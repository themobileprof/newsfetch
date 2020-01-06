<?php
 
require 'vendor/autoload.php';
 
use App\SQLiteConnection as SQLiteConnection;
use App\rssGrab as rssGrab;

$grabber = new rssGrab((new SQLiteConnection())->connect());
// create new tables
$sources = $grabber->getSources();
 
foreach ($sources as $source){
	
	$sourceid = $source['id'];
	$feedtype = $grabber->getFeed($source['rss']); 
	if($feedtype == 'rss'){
		echo 'rss';
		$grabber->parseRSS($sourceid);
	} else if ($feedtype == 'atom') {
		echo 'atom';
		$grabber->parseAtom($sourceid);
	} else {
		echo 'None';
	}
	$grabber->addDb ($source->pdo, $source['catId']);
}

