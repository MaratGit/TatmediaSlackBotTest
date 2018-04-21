<?php
namespace slackbot;

include 'slackbot_config.php';

class SlackBotModel {
    private $connection = null;
	private $errno = null;
    
    public function __get($name) {
    	return $name == "errno" ? $this->errno : null;
	}
    
    public function __construct() {
    	$this->ConnectToDB();
    }
    
	public function __destruct() {
		if ($this->connection != null) {
			$this->connection->close();
		}

		$this->connection = null;
	}

	public function AddItem($item_guid) {
		if ($this->errno != null) {
			return;
		}

		if ($statement = $this->connection->prepare("INSERT INTO " . DB_TABLE . " (item_guid) VALUES (?)")) {
			$statement->bind_param("s", $item_guid);
			$statement->execute();
			$statement->close();
			return $this->connection->insert_id;
		}
	}

	public function ItemExists($item_guid) {
		$sql = "SELECT id, item_guid FROM " . DB_TABLE . " WHERE item_guid = ? LIMIT 1";

		$statement = $this->connection->prepare($sql);
		$statement->bind_param("s", $item_guid);
		$statement->execute();
		$statement->store_result();
		
		$result = ($statement->num_rows > 0);

		$statement->close();

		return $result;
	}

	private function ConnectToDB() {
		$this->connection = new \mysqli(DB_HOST, DB_USER, DB_PASSWORD);

		if ($this->connection->connect_error) {
			$this->errno = $this->connection->connect_errno;
			return;
		}
		
		$db_selected = $this->connection->select_db(DB_NAME);
		if (!$db_selected) {
			if ($this->CreateDB() === TRUE) {
				$this->connection->select_db(DB_NAME);
				$this->CreateTable();
			} else {
				$this->errno = $this->connection->errno;
			}
		}
	}
	
	private function CreateDB() {
		$sql = "CREATE DATABASE " . DB_NAME;
		return $this->connection->query($sql);
	}
    
    private function CreateTable() {
		$sql = "CREATE TABLE " . DB_TABLE . " (
			id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
			item_guid VARCHAR(255) NOT NULL
		)";

		if ($this->connection->query($sql) !== TRUE) {
			$this->errno = $this->connection->errno;
		}
	}
}