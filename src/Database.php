<?php

namespace App;

use mysqli;

class Database {
	private mysqli $connection;

	public function __construct($host, $user, $password, $database)
	{
		$this->connection = new mysqli($host, $user, $password, $database);

		if ($this->connection->connect_error) {
			die("Connection failed: " . $this->connection->connect_error);
		}
	}

	public function getConnection(): mysqli
	{
		return $this->connection;
	}
}
