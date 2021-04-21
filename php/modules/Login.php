<?php
\Site::protectScript(basename(__FILE__));

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

	private
		$attempts;

	function __construct()
	{
		extract($_REQUEST);

		if(empty($_SESSION)) session_start();

		$this->IP = \Site::realIP();

		$this->attempts = new DbJSON(self::TRYDB_PATH);
		$this->DB = new DbJSON(self::LOGIN_PATH);

		if($login === 'logout')
		{
			$action = 'logout';
			$_SESSION['auth'] = 0;
			session_destroy();
		}
		elseif(!isset($login) && empty($action))
		{
			$this->tryLogin(1);
			throw new Exception('В запросе нет $action' . "\nIP - {$this->IP}" , 1);
		}


		switch ($action) {
			case 'authorize':
				if(empty($pswd)) throw new Exception('В запросе нет $pswd', 1);

				//* Если в базе нет админа - создаём нового
				if(empty($this->DB->admin))
				{
					$this->DB->set(['admin' => password_hash($pswd, PASSWORD_DEFAULT)]);
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


	protected function getAttempts()
	{
		return is_array($this->attempts->{$this->IP})? $this->attempts->{$this->IP}[1]: $this->attempts->{$this->IP} ?? 0;
	}


	private function auth(string $login, string $pswd)
	{
		$attempts= $this->getAttempts();

		tolog(__METHOD__,null,[
			'$login'=>$login, '$this->DB->{$login}'=>$this->DB->{$login}, empty($this->DB->{$login}), '$pswd'=>$pswd, 'password_verify($pswd, $this->DB->{$login})'=>password_verify($pswd, $this->DB->{$login}), ($attempts <= self::TRY_ADMIN), !empty($this->DB->{$login})
		]);

		if(
			$attempts <= self::TRY_ADMIN
			&& !empty($this->DB->{$login})
			&& password_verify($pswd, $this->DB->{$login})
		)
		{
			//* Success
			$_SESSION['auth'] = [
				'group' => $login === 'admin' ? 'admin' : 'users',
				'IP' => $this->IP,
				'login' => $login,
			];

			tolog(['session'=>$_SESSION]);

			if($attempts)
				$this->tryLogin(0);
		}
		else
		{
			# Fail
			session_destroy();
			$this->tryLogin(1);
			\Site::shead(401);
		}
	}


	protected function tryLogin($bool)
	{
		$attempt= $this->getAttempts();

		if($bool)
			++$attempt;
		elseif(is_adm())
			$attempt = 0;

		// *Записываем поптыки авторизации
		$this->attempts->set([$this->IP=>[time(),$attempt]]);

	}


	public function form()
	{
		$_REQUEST['module'] = 'templates/login/index.php';
	}

} // Login

