<?php

class ZookeeperService
{
    private $zkObj;

    public function __construct($connString)
    {
        $this->zkObj = new Zookeeper($connString);

        while ($this->zkObj->getState() !== Zookeeper::CONNECTED_STATE) {
            usleep(10000); // sleep for 10 milliseconds
        }

        echo "Connected to ZooKeeper\n";
    }

    public static function getZookeeperService($connString)
    {
	return new ZookeeperService($connString);
    }

    public function createZNode($path, $data)
    {
        $acl = array(
            array(
                'perms' => Zookeeper::PERM_ALL,
                'scheme' => 'world',
                'id' => 'anyone',
            ),
	);

        $this->zkObj->create($path, $data, $acl);

        echo "Node created: $path\n";
    }

    public function getZNode($path)
    {
	// Read data from the node
	$nodeData = $this->zkObj->get($path);
	echo "Data from $path: $nodeData\n";
    }

    public function updateZNode($path, $newData)
    {
	// New data for the node
	$this->zkObj->set($path, $newData);
	echo "Data in $path updated\n";
    }

    public function deleteZNode($path)
    {
	// Delete the node
	$this->zkObj->delete($path);
	echo "Node deleted: $path\n";
    }
}

// Usage example with command line arguments
if ($argc < 3) {
    die("Usage: php zkService.php <connectionString> <operation> Valid operation: create, get, update and delete\n");
}

$zkConnString = $argv[1];
$zkOp = $argv[2];
switch ($zkOp) {
case "create":
	if ($argc < 5) {
		die("Usage: php zkService.php <connectionString> <operation> <nodePath> <nodeData>\n");
	}
	$nodePath = $argv[3];
	$nodeData = $argv[4];

	$zkService = ZookeeperService::getZookeeperService($zkConnString);
	$zkService->createZNode($nodePath, $nodeData);
	break;
case "get":
	if ($argc < 4) {
		die("Usage: php zkService.php <<connectionString> operation> <nodePath>\n");
	}
	$nodePath = $argv[3];
	$zkService = ZookeeperService::getZookeeperService($zkConnString);
	$zkService->getZNode($nodePath);
	break;
case "update":
	if ($argc < 5) {
		die("Usage: php zkService.php <connectionString> <operation> <nodePath> <newData>\n");
	}
	$nodePath = $argv[3];
	$newData = $argv[4];

	$zkService = ZookeeperService::getZookeeperService($zkConnString);
	$zkService->updateZNode($nodePath, $newData);
	break;
case "delete":
	if ($argc < 4) {
		die("Usage: php zkService.php <connectionString> <operation> <nodePath>\n");
	}
	$nodePath = $argv[3];
	$zkService = ZookeeperService::getZookeeperService($zkConnString);
	$zkService->deleteZNode($nodePath);
	break;
default:
	echo "Operation ". $zkOp . " not supported\n\n";
	break;
}
