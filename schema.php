<?php


require 'vendor/autoload.php';
 
use App\Connection as Connection;
use App\sourcesTable as sourcesTable;
use App\articlesTable as articlesTable;

$src = new sourcesTable((new Connection())->connect());
// create new tables
// $src->createTables();
 
// Populate Tables
$src->populateTable();

$art = new articlesTable((new Connection())->connect());
// create new tables
$art->createTables();
 
// Populate Tables
// $src->populateTable();

echo "complete";
