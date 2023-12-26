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
