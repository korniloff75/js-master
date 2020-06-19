<?php

// var_dump($_REQUEST);


require_once __DIR__ . '/EntryPointGraph.class.php';

class fetchHandler
{
	// const CACHE_PATH = __DIR__ . "/../cache.json";

	public function __construct()
	{
		$this->inputData = json_decode(file_get_contents('php://input'), 1);

		echo "<hr>";

		foreach($this->inputData as $name=>$val)
		{
			// echo "$name = ". print_r($val) . "\n";
			// echo "$name = ". json_encode($val) . "\n";
		}

		echo "<hr>";

		echo json_encode([
			'_GET'=>$_GET,
			'_POST'=>$_POST,
			'_REQUEST'=>$_REQUEST,
			// 'inputData'=>$inputData,
		]);

		echo "<hr>";
		$this->json = json_decode(file_get_contents(EntryPointGraph::CACHE_PATH), 1);
		$this->cols= &$this->json['columns'];

		$this->handleInputData();
	}

	protected function handleInputData()
	{
		$this->json = json_decode(file_get_contents(EntryPointGraph::CACHE_PATH), 1);
		$this->cols= &$this->json['columns'];

		foreach($this->inputData as $name=>&$angles)
		{
			foreach($angles as $ca=>&$a)
			echo "{$name}[$ca] = ".implode(' / ',$a)."\n";
		}
	}
}

new fetchHandler();