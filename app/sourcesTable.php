<?php
 
namespace App;
 
/**
 * SQLite Create Table Demo
 */
class sourcesTable {
	    /**
     * PDO object
     * @var \PDO
     */
    private $pdo;
 
    /**
     * connect to the SQLite database
     */
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
 
    /**
     * create tables 
     */
    public function createTables() {
	$this->pdo->exec("DROP TABLE IF EXISTS `sources`");

	$this->pdo->exec("CREATE TABLE `sources` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    `rss` TEXT,
    `catId` INTEGER,
    `fail` INTEGER DEFAULT 0,
    `activ` TEXT DEFAULT 1
	)");
	}

	public function populateTable (){
		$this->pdo->exec("INSERT INTO `sources`(rss) VALUES('https://m.guardian.ng/category/business-services/business/feed/')");
		$this->pdo->exec("INSERT INTO `sources`(rss) VALUES('http://businessnews.com.ng/feed/')");
		$this->pdo->exec("INSERT INTO `sources`(rss) VALUES('https://www.vanguardngr.com/category/business/feed/')");
		$this->pdo->exec("INSERT INTO `sources`(rss) VALUES('https://www.premiumtimesng.com/category/business/business-news/feed')");
		$this->pdo->exec("INSERT INTO `sources`(rss) VALUES('https://tribuneonlineng.com/business/feed/')");
		$this->pdo->exec("INSERT INTO `sources`(rss) VALUES('https://www.africanbusinesscentral.com/feed/')");
		$this->pdo->exec("INSERT INTO `sources`(rss) VALUES('http://venturesafrica.com/feed/')");
		// $this->pdo->exec("INSERT INTO `sources`(rss) VALUES('')");
	// $this->pdo->exec("INSERT INTO `sources`(rss) VALUES('')");
		// $this->pdo->exec("INSERT INTO `sources`(rss) VALUES('')");
		// $this->pdo->exec("INSERT INTO `sources`(rss) VALUES('')");
		//$this->pdo->exec("INSERT INTO `sources`(rss) VALUES('')");
		// $this->pdo->exec("INSERT INTO `sources`(rss) VALUES('')");
 	}


}
