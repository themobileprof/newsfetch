<?php
 
require 'vendor/autoload.php';
 
use App\SQLiteConnection as SQLiteConnection;
use App\sourcesTable as sourcesTable;

$src = new sourcesTable((new SQLiteConnection())->connect());
// create new tables
// $src->createTables();
 
// Populate Tables
$src->populateTable();

echo "complete";
