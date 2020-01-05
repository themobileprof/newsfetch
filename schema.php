<?php
 
require 'vendor/autoload.php';
 
use App\SQLiteConnection as SQLiteConnection;
use App\sourcesTable as sourcesTable;
use App\articlesTable as articlesTable;

$src = new sourcesTable((new SQLiteConnection())->connect());
// create new tables
// $src->createTables();
 
// Populate Tables
$src->populateTable();

$art = new articlesTable((new SQLiteConnection())->connect());
// create new tables
$art->createTables();
 
// Populate Tables
// $src->populateTable();

echo "complete";
