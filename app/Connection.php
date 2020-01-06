<?php
namespace App;
 
/**
 * SQLite connnection
 */
class SQLiteConnection {
    /**
     * PDO instance
     * @var type 
     */
    private $pdo;
 
    /**
     * return in instance of the PDO object that connects to the SQLite database
     * @return \PDO
     */
    public function connect() {
        if ($this->pdo == null) {
            $this->pdo = new \PDO("sqlite:" . Config::PATH_TO_SQLITE_FILE);
        }
        return $this->pdo;
    }
}


// MySql Connection

class MysqlConnection {
    /**
     * PDO instance
     * @var type 
     */
    private $pdo;
 
    /**
     * return in instance of the PDO object that connects to the SQLite database
     * @return \PDO
     */
    public function connect() {
        if ($this->pdo == null) {
		$host = 'localhost';
		$db   = 'afrikina';
		$user = 'afrikina';
		$pass = 'tummy654';

		$dsn = "mysql:host=$host;dbname=$db;";
		try {
			$this->pdo = new \PDO($dsn, $user, $pass);
		} catch (\PDOException $e) {
		     throw new \PDOException($e->getMessage(), (int)$e->getCode());
		}
	}
        return $this->pdo;
    }
}
