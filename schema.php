<?php


require 'vendor/autoload.php';
 
use App\Connection as Connection;
use App\dbTables as dbTables;

$dbSchema = new dbTables((new Connection())->connect());

$sqlfile = ($dbSchema->dbType() == "MySQL") ? file_get_contents('sql/mysql_afrikina.sql') : file_get_contents('sql/sqlite_afrikina.sql');

// create new tables
$dbSchema->createTables($sqlfile);
 
// Populate Tables
$dbSchema->populateTable();


echo "complete";
