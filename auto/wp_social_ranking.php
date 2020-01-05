<?php
//error_reporting(E_ERROR); 
//ini_set( 'display_errors','1');
///////////////////////////////////
$link = mysqli_connect ("localhost", "person41_newsng", "tummymouse", "person41_newsng_wp") or die ("Connection Error: " . mysqli_connect_error());
////////////////////////////////////
function get_tot ($link, $timframe){
	/*
	$sql = "SELECT SUBSTRING_INDEX((SUBSTRING_INDEX((SUBSTRING_INDEX(`r`.`meta_value`, '://', -1)), '/', 1)), '.', -2) as domain, 
	SUM(`b`.`meta_value`) AS `tot` 
	FROM `wp_postmeta` `b`
	INNER JOIN `wp_postmeta` `r` 
	ON `b`.`post_id` = `r`.`post_id`
	AND (`meta_key` = 'Buzz' OR `meta_key` = 'rss_pi_source_url')";
	*/
	if (!empty($timframe)) { //this should come as number of the month
		$timframe = str_pad($timframe, 2, '0', STR_PAD_LEFT);
	} else {
		$timframe = str_pad(date("m"), 2, '0', STR_PAD_LEFT);
	}
	
	$st_date = date("Y-").$timframe."-01 00:00:00";
	$en_date = date("Y-").$timframe."-".date("t",date("Y-").$timframe."-31")." 23:59:59";
	$wh = " AND WHERE `wp_posts`.`post_date` >= '".$st_date."' AND `wp_posts`.`post_date` <= '".$en_date."'";
	
	$sql = "SELECT `wp_posts`.`ID` AS `id`, `wp_postmeta`.`meta_key` AS `mkey`, `wp_postmeta`.`meta_value` AS `mval`, `wp_posts`.`post_date` AS `pdat`
	FROM `wp_postmeta`, `wp_posts` 
	WHERE `mkey` = 'Buzz' OR `mkey` = 'rss_pi_source_url' 
	AND `wp_posts`.`ID` = `wp_postmeta`.`post_id`".$wh;
	
	$result = @mysqli_query($link, $sql);
	
	while ($rows = @mysqli_fetch_assoc($result)){
		if ($rows['mkey'] == 'Buzz'){
			$det[$rows['id']]['buzz'] = $rows['mval'];
		} elseif ($rows['mkey'] == 'rss_pi_source_url'){
			$det[$rows['id']]['url'] = $rows['mval'];
		}
	}
	
	// Reassign array
	foreach ($det as $pid=>$val_array){
		// Get Paper name based on URL
		
		if (strpos($val_array['url'], 'punchng') !== false){
			$paper = '1';
		} elseif (strpos($val_array['url'], 'vanguard') !== false){
			$paper = '2';
		} elseif (strpos($val_array['url'], 'guardian') !== false){
			$paper = '3';
		} elseif (strpos($val_array['url'], 'thisdayonline') !== false){
			$paper = '4';
		} elseif (strpos($val_array['url'], 'businessday') !== false){
			$paper = '5';
		} elseif (strpos($val_array['url'], 'independent') !== false){
			$paper = '6';
		} elseif (strpos($val_array['url'], 'sun') !== false){
			$paper = '7';
		} elseif (strpos($val_array['url'], 'tribune') !== false){
			$paper = '8';
		} elseif (strpos($val_array['url'], 'nation') !== false){
			$paper = '9';
		} elseif (strpos($val_array['url'], 'trust') !== false){
			$paper = '10';
		}
		
		if (!isset($url_tot[$burl])){
			$url_tot[$paper] = $val_array['Buzz'];
		}else{
			$url_tot[$paper] += $val_array['Buzz'];
		}
	}
	
	// Echo Graph line
	$paper_arr = 'i:0;a:11:{i:0;s:7:"'.date("F",$st_date).'";';
	
	foreach ($url_tot as $k=>$v){
		$paper_arr .= 'i:'.$k.';d:'.$v.';';
	}
	
	$paper_arr .= '}';
	
	return $paper_arr;
	
}


$array_month  = array("January","February","March","April","May","June","July","August","September","October","November","December");
$array_paper = array("Punch","Vanguard","Guardian","Thisday","Business Day","Daily Independent","Daily Sun","Nigerian Tribune","The Nation","Daily Trust");


$rep_data = 'a:12:{';
$rep_data .= get_tot ($link, '1');
$rep_data .= get_tot ($link, '2');
$rep_data .= get_tot ($link, '3');
$rep_data .= get_tot ($link, '4');
$rep_data .= get_tot ($link, '5');
$rep_data .= get_tot ($link, '6');
$rep_data .= get_tot ($link, '7');
$rep_data .= get_tot ($link, '8');
$rep_data .= get_tot ($link, '9');
$rep_data .= get_tot ($link, '10');
$rep_data .= get_tot ($link, '11');
$rep_data .= get_tot ($link, '12');
$rep_data .= '}';

?>