<?php
 
namespace App;
 
/**
 * SQLite Create Table Demo
 */
class dbTables {
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

	public function dbType(){
	    return Config::DATABASE_TYPE;
	}

	public function createTables($sql) {
		$this->pdo->exec($sql);

	}

	public function populateTable (){
		$this->pdo->exec("INSERT INTO `news_sources`(rss, source) VALUES('https://m.guardian.ng/category/business-services/business/feed/','Guardian Nigeria')");
		$this->pdo->exec("INSERT INTO `news_sources`(rss, source) VALUES('http://businessnews.com.ng/feed/','Business News')");
		$this->pdo->exec("INSERT INTO `news_sources`(rss, source) VALUES('https://www.vanguardngr.com/category/business/feed/','Vanguard Nigeria')");
		$this->pdo->exec("INSERT INTO `news_sources`(rss,source) VALUES('https://www.premiumtimesng.com/category/business/business-news/feed','Premium Times')");
		$this->pdo->exec("INSERT INTO `news_sources`(rss) VALUES('https://tribuneonlineng.com/business/feed/')");
		$this->pdo->exec("INSERT INTO `news_sources`(rss, source) VALUES('https://www.africanbusinesscentral.com/feed/','African Business Central')");
		$this->pdo->exec("INSERT INTO `news_sources`(rss, source) VALUES('http://venturesafrica.com/feed/', 'Ventures Africa')");
		// $this->pdo->exec("INSERT INTO `sources`(rss) VALUES('')");
	// $this->pdo->exec("INSERT INTO `sources`(rss) VALUES('')");
		// $this->pdo->exec("INSERT INTO `sources`(rss) VALUES('')");
		// $this->pdo->exec("INSERT INTO `sources`(rss) VALUES('')");
		//$this->pdo->exec("INSERT INTO `sources`(rss) VALUES('')");
		// $this->pdo->exec("INSERT INTO `sources`(rss) VALUES('')");
 	}


}
