<?php
 
namespace App;
 
/**
 * SQLite Create Table Demo
 */
class makeTable {
	    /**
     * PDO object
     * @var \PDO
     */
    private $pdo;
 
    /**
     * connect to the database
     */
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
 
    /**
     * create tables 
     */
    public function SQLiteCreateTables($sql) {
	$this->pdo->exec($sql);

    }

    public function MysqlCreateTables($sql) {
	$this->pdo->exec($sql);

	}

	public function populateTableSources (){
		$this->pdo->exec("INSERT INTO `news_sources`(rss) VALUES('https://m.guardian.ng/category/business-services/business/feed/')");
		$this->pdo->exec("INSERT INTO `news_sources`(rss) VALUES('http://businessnews.com.ng/feed/')");
		$this->pdo->exec("INSERT INTO `news_sources`(rss) VALUES('https://www.vanguardngr.com/category/business/feed/')");
		$this->pdo->exec("INSERT INTO `news_sources`(rss) VALUES('https://www.premiumtimesng.com/category/business/business-news/feed')");
		$this->pdo->exec("INSERT INTO `news_sources`(rss) VALUES('https://tribuneonlineng.com/business/feed/')");
		$this->pdo->exec("INSERT INTO `news_sources`(rss) VALUES('https://www.africanbusinesscentral.com/feed/')");
		$this->pdo->exec("INSERT INTO `news_sources`(rss) VALUES('http://venturesafrica.com/feed/')");
		// $this->pdo->exec("INSERT INTO `sources`(rss) VALUES('')");
	// $this->pdo->exec("INSERT INTO `sources`(rss) VALUES('')");
		// $this->pdo->exec("INSERT INTO `sources`(rss) VALUES('')");
		// $this->pdo->exec("INSERT INTO `sources`(rss) VALUES('')");
		//$this->pdo->exec("INSERT INTO `sources`(rss) VALUES('')");
		// $this->pdo->exec("INSERT INTO `sources`(rss) VALUES('')");
 	}


}
