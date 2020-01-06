<?php
 
require 'vendor/autoload.php';
 
// use App\SQLiteConnection as SQLiteConnection;
use App\MysqlConnection as MysqlConnection;
use App\sourcesTable as sourcesTable;
use App\articlesTable as articlesTable;

$src = new sourcesTable((new MysqlConnection())->connect());
// create new tables
// $src->createTables();
 
// Populate Tables
$src->populateTable();

$art = new articlesTable((new MysqlConnection())->connect());
// create new tables
$art->createTables();
 
// Populate Tables
// $src->populateTable();

echo "complete";
