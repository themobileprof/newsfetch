<?php
 
require 'vendor/autoload.php';
 
use App\SQLiteConnection as SQLiteConnection;
use App\rssGrab as rssGrab;

$grabber = new rssGrab((new SQLiteConnection())->connect());
// create new tables
$sources = $grabber->getSources();
 
foreach ($sources as $source){
	
	$sourceid = $source['id'];
	// echo $sourceid;
	$feedType = $grabber->getFeed($source['rss']); 
	if($feedtype == 'rss'){
		$grabber->parseRSS();
	} else if ($feedtype == 'atom') {
		$grabber->parseAtom();
	}
	$grabber->addDb ($source->pdo, $source['catId']);
}

