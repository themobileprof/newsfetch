<?php
 
require 'vendor/autoload.php';
 
use App\Connection as Connection;
use App\rssGrab as rssGrab;

$grabber = new rssGrab((new Connection())->connect());
// create new tables
$sources = $grabber->getSources();
//print_r($sources);
//exit();
 
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

