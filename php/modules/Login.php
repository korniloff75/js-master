<?php
\H::protectScript(basename(__FILE__));

/**
 * При удалении Login::LOGIN_PATH
 * данные, посланные в форме
 * становятся новым админ-доступом.
 *
 * При удалении Login::TRYDB_PATH
 * разблокируются все IP
 */

class Login

{
	private const
		TRY_ADMIN = 3,
		LOGIN_PATH = 'db/login.json',
		TRYDB_PATH = 'db/tryDB.json';

	function __construct()
	{
		extract($_REQUEST);
		$this->uip = \H::realIP();

		$this->tryDB = \H::json(self::TRYDB_PATH)[$this->uip] ?? 0;

		if($login === 'logout')
		{
			$action = 'logout';
			$_SESSION['auth'] = 0;
			session_destroy();
		}
		elseif(!isset($login) && empty($action))
		{
			$this->tryLogin(1);
			throw new Exception('В запросе нет $action' . "\nIP - {$this->uip}" , 1);
		}


		switch ($action) {
			case 'authorize':
				if(empty($pswd)) throw new Exception('В запросе нет $pswd', 1);
				$this->DB = \H::json(self::LOGIN_PATH);

				# Если в базе нет админа - создаём нового
				if(empty($this->DB['admin']))
				{
					$this->DB['admin'] = password_hash($pswd, PASSWORD_DEFAULT);

					\H::json(self::LOGIN_PATH, ['admin' => $this->DB['admin']]);
					// var_dump($this->DB);
					// die;
				}

				$login = empty(trim($login)) ? 'admin' : $login;

				$this->auth($login, $pswd);
				break;

			case 'logout':
				header('Location: ' . $_SERVER['HTTP_REFERER']);
				break;

			default:
				$this->form();
				break;
		}

	} // __construct


	private function auth(string $login, string $pswd)
	{
		// var_dump($login);
		\H::$tmp['db'] = $this->DB;
		\H::$tmp['login'] = $login;
		\H::$tmp['pswd'] = $pswd;
		\H::log([
			'echo "\$login = " . self::$tmp[\'login\'] . "; \$pswd = " . self::$tmp[\'pswd\']',
			// 'echo "\$this->DB[\$login] = {$db[$login]}"',
			'echo "password_verify(\$pswd, \$this->DB[\$login]) = "',
			'var_dump(password_verify(self::$tmp[\'pswd\'], self::$tmp[\'db\'][self::$tmp[\'login\']]))'
		], __FILE__, __LINE__);
		if(
			$this->tryDB <= self::TRY_ADMIN
			&& !empty($this->DB[$login])
			&& password_verify($pswd, $this->DB[$login])
		)
		{
			# Success
			$_SESSION['auth'] = [
				'group' => $login === 'admin' ? 'admin' : 'users',
				'IP' => $this->uip,
				'login' => $login,
			];

			$adm_db = \H::json('db/adm.json', $this->uip) ?? [
				'attempts' => 0,
			];
			++$adm_db['attempts'];
			array_push($adm_db, date(\CF['date']['format']));

			$adm_db = \H::json('db/adm.json', [$this->uip => $adm_db] );

			if($this->tryDB)
				$this->tryLogin(0);
		}
		else
		{
			# Fail
			session_destroy();
			$this->tryLogin(1);
			\H::shead(401);
		}
	}


	protected function tryLogin($bool)
	{
		if($bool)
			++$this->tryDB;
		elseif(!empty($_SESSION['auth']['login']))
			$this->tryDB = 0;

		// var_dump($this->tryDB, $this->uip);

		\H::json(self::TRYDB_PATH, [
			$this->uip => $this->tryDB
		]);

	}


	public function form()
	{
		$_REQUEST['module'] = 'templates/login/index.html';
	}

} // Login

