<?php
namespace App;
 
/**
 * Database connnection
 */
class Connection {
    /**
     * PDO instance
     * @var type 
     */
    private $pdo;

    public function __construct(){
	    switch(Config::DATABASE_TYPE){
		case "MySQL":
			//echo "Connect MySQL";
			$this->connectMysql();
			break;
		default:
			//echo "Connect SQLite";
			$this->connectSqlite();
	    }
    }

    public function connect (){
	    return $this->pdo;
    }

    /**
     * return in instance of the PDO object that connects to the SQLite database
     * @return \PDO
     */
    public function connectSqlite() {
        if ($this->pdo == null) {
            $this->pdo = new \PDO("sqlite:" . Config::PATH_TO_SQLITE_FILE);
        }
        //return $this->pdo;
    }

    /**
     * return in instance of the PDO object that connects to the MySql database
     * @return \PDO
     */
    public function connectMysql() {
        if ($this->pdo == null) {
		$dsn = "mysql:host=".Config::MYSQL_HOST.";dbname=".Config::MYSQL_DB.";";
		try {
			$this->pdo = new \PDO($dsn, Config::MYSQL_USERNAME, Config::MYSQL_PASSWORD);
		} catch (\PDOException $e) {
		     throw new \PDOException($e->getMessage(), (int)$e->getCode());
		}
	}
        return $this->pdo;
    }
}
