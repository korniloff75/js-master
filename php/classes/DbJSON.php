<?php
class DbJSON {
	private
		$path = '',
		$json; # String

	public
		# DataBase
		$db = []; # Array


	public function __construct(string $path=null)
	{
		if(empty($path)) throw new LogicException("Отсутствует \$path", 1);

		$this->path = $path;
		$this->json = @file_get_contents($this->path);
		$this->db = json_decode($this->json, true) ?? [];

	}

	/**
	 * @id optional <string|int>
	 */
	public function get($id=null)
	{
		return empty($id)
		? $this->db
		: (
			$this->db[$id] ?? null
		);
	}

	/**
	 * @param data <array>
	 */
	public function set(array $data)
	{
		$this->db = array_replace_recursive($this->db, $data);
		$this->db['change']= 1;

		return $this;
	}


	# Плоский массив из многомерного
	public function getFlat()
	{
		return array_values(iterator_to_array(
			new \RecursiveIteratorIterator(
				new \RecursiveArrayIterator($this->db)
			)
		));
	}


	# Массив в JSON
	public static function toJSON(array $arr)
	{
		return json_encode(($arr), JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
	}


	public function __destruct()
	{
		if(empty($this->db['change'])) return;

		unset($this->db['change']);

		file_put_contents($this->path, self::toJSON($this->db), LOCK_EX);
	}
} // DbJSON