<?php

require "classes/ZookeeperService.php";

// Usage zookeeper service with command line arguments
if ($argc < 3) {
    die("Usage: php zkStart.php <connectionString> <operation> Valid operation: create, get, update and delete\n");
}

$zkConnString = $argv[1];
$zkOp = $argv[2];
switch ($zkOp) {
case "create":
	if ($argc < 5) {
		die("Usage: php zkStart.php <connectionString> create <nodePath> <nodeData>\n");
	}
	$nodePath = $argv[3];
	$nodeData = $argv[4];

	$zkService = ZookeeperService::getZookeeperService($zkConnString);
	$zkService->createZNode($nodePath, $nodeData);
	break;
case "get":
	if ($argc < 4) {
		die("Usage: php zkStart.php <connectionString> get <nodePath>\n");
	}
	$nodePath = $argv[3];
	$zkService = ZookeeperService::getZookeeperService($zkConnString);
	$zkService->getZNode($nodePath);
	break;
case "update":
	if ($argc < 5) {
		die("Usage: php zkStart.php <connectionString> update <nodePath> <newData>\n");
	}
	$nodePath = $argv[3];
	$newData = $argv[4];

	$zkService = ZookeeperService::getZookeeperService($zkConnString);
	$zkService->updateZNode($nodePath, $newData);
	break;
case "delete":
	if ($argc < 4) {
		die("Usage: php zkStart.php <connectionString> delete <nodePath>\n");
	}
	$nodePath = $argv[3];
	$zkService = ZookeeperService::getZookeeperService($zkConnString);
	$zkService->deleteZNode($nodePath);
	break;
default:
	echo "Operation ". $zkOp . " not supported\n\n";
	break;
}
