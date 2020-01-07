<?php
 
namespace App;
 
/**
 * SQLite Create Table Demo
 */
class articlesTable {
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
	$this->pdo->exec("DROP TABLE IF EXISTS `articles`");

	$this->pdo->exec("CREATE TABLE `articles` (
    `id` INTEGER PRIMARY KEY AUTO_INCREMENT NOT NULL,
    `guId` TEXT UNIQUE,
    `sourceId` INTEGER,
    `title` TEXT,
    `description` TEXT,
    `img` TEXT,
    `url` TEXT,
    `articleDate` TEXT,
    `catId` INTEGER DEFAULT 1,
    FOREIGN KEY (`sourceId`) REFERENCES `news_sources`(`id`)
)");
	}

	public function populateTable (){
		// $this->pdo->exec("INSERT INTO `sources`(rss) VALUES('')");
	// $this->pdo->exec("INSERT INTO `sources`(rss) VALUES('')");
		// $this->pdo->exec("INSERT INTO `sources`(rss) VALUES('')");
		// $this->pdo->exec("INSERT INTO `sources`(rss) VALUES('')");
		//$this->pdo->exec("INSERT INTO `sources`(rss) VALUES('')");
		// $this->pdo->exec("INSERT INTO `sources`(rss) VALUES('')");
 	}


}
