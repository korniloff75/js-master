<?php

// var_dump($_REQUEST);

$inputData = json_decode(file_get_contents('php://input'), 1);

foreach($inputData as $name=>$val)
{
	echo "$name = ". json_encode($val) . "\n";
}

echo json_encode([
	'_GET'=>$_GET,
	'_POST'=>$_POST,
	'_REQUEST'=>$_REQUEST,
	'inputData'=>$inputData,
]);

require_once __DIR__ . '/EntryPointGraph.class.php';

class fetchHandler
{
	const CACHE_PATH = __DIR__ . "/../cache.json";

	public function __construct()
	{
		$this->json = json_decode(file_get_contents(EntryPointGraph::CACHE_PATH), 1);
	}
}