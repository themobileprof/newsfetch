<?php
//error_reporting(E_ERROR); 
//ini_set( 'display_errors','1');
///////////////////////////////////
$link = mysqli_connect ("localhost", "person41_newsng", "tummymouse", "person41_newsng_wp") or die ("Connection Error: " . mysqli_connect_error());
////////////////////////////////////
//class get_rss extends add_event {
class get_social  {
	private $dblink;
	
	function __construct (){
		global $link;
		$this->dblink = $link1 = $link;
		
		
		// Get all RSS feeds stored in database
		//$sql = "SELECT `id`,`url` FROM articles WHERE id = 'cd55814df7'"; //For Debug purposes
		$sql = "SELECT `wp_posts`.`ID` AS `id`, `wp_postmeta`.`meta_value` AS `url`  FROM `wp_posts`,`wp_postmeta` WHERE `meta_key`='rss_pi_source_url' AND `wp_posts`.`ID` = `wp_postmeta`.`post_id` AND `wp_posts`.`post_date` >= DATE_ADD(CURDATE(), INTERVAL -1 DAY)";
		//echo $sql;
		
		$result = @mysqli_query($link1, $sql);
		while ($rows = @mysqli_fetch_assoc ($result)){
			$this->add_db ($this->dblink, $rows['id'], $rows['url']);
		}
	}
	
	function chkadd ($link, $post_id){
		$sql = "SELECT * FROM `wp_postmeta` WHERE `post_id` = '$post_id' AND `meta_key` = 'Buzz'";
		//echo $sql;
		//exit();
		$result = @mysqli_query($link, $sql);
		if (@mysqli_num_rows($result) > 0):
			return FALSE;
		else:
			return TRUE; 
		endif;
	}
	
	function add_db ($link, $artid, $url){
		$link2 = $link;
		$url = urlencode($url);
		
		//get FB score
		$fb_score = $this->get_fb_score ($url);
		
		//get Twitter Score
		$tw_score = $this->get_tw_score ($url);
		
		//get LinkedIn Score
		$lin_score = $this->get_lin_score ($url);
		
		//get Google+ Score
		$gplus_score = $this->get_gplus_score ($url);
		
		// Collate Buzz
		$buzz = $fb_score+$tw_score+$lin_score+$gplus_score;
		
		$upd_sql = "UPDATE `wp_postmeta` SET
		`meta_value` = '$buzz'
		WHERE `post_id` = '$artid'
		AND `meta_key` = 'Buzz'
		AND `meta_value` <= '$buzz'";
		
		@mysqli_query($link, $upd_sql);
		
		if (@mysqli_affected_rows($link) <= 0){
			//If UPDATE DOESN'T WORK, THEN insert
			
			if ($this->chkadd ($link2, $artid)): // Don't add if record already exists 
				$sql = "INSERT INTO `wp_postmeta` SET
				`post_id` = '$artid',
				`meta_key` = 'Buzz',
				`meta_value` = '$buzz'";
				echo " | New 1: ".$artid;
				@mysqli_query($link2, $sql);
			else:
				echo " | Leave 1: ".$artid;
			endif;
		}else{
			echo " | Updated 1: ".$artid;
		}
		
		;
	}
	///////////////////////////////////////////////////
	
	function get_fb_score ($url){
		//echo "Seen FB";
		//return '4'; //Test only
		$loc_url = "https://api.facebook.com/method/fql.query?query=SELECT%20total_count%20FROM%20link_stat%20WHERE%20url=%22".$url."%22";
		//echo $loc_url;
		
		$ch = curl_init();		
		curl_setopt($ch, CURLOPT_URL, $loc_url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		$data = curl_exec($ch);
		
		curl_close($ch);
		
		$xml = simplexml_load_string($data);
		
		//print_r($xml);
		
		return intval( $xml->link_stat->total_count );
	}
	
	function get_tw_score ($url){
		//return '3'; //Test only
		$loc_url = "http://urls.api.twitter.com/1/urls/count.json?url=".$url;
		
		$ch = curl_init();		
		curl_setopt($ch, CURLOPT_URL, $loc_url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		$data = curl_exec($ch);
		
		curl_close($ch);
		
		$json = json_decode ($data);
		
		return intval( $json->count );
	}
	
	function get_lin_score($url) {
		//return '1'; //Test only
		$loc_url = "http://www.linkedin.com/countserv/count/share?url=$url&format=json";
		
		$ch = curl_init();		
		curl_setopt($ch, CURLOPT_URL, $loc_url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$data = curl_exec($ch);
		curl_close($ch);
		
		//print_r($data);
		
		$json = json_decode ($data, true);
		
		return intval( $json->count );
	}

	
	function get_gplus_score($url) {
		return '0'; // Google Plus Ranking Not Ready yet
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, "https://clients6.google.com/rpc");
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"' . $url . '","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
		$curl_results = curl_exec ($curl);
		curl_close ($curl);
		//print_r($curl_results);
		$json = json_decode($curl_results, true);
		return intval( $json[0]['result']['metadata']['globalCounts']['count'] );
	}

	
	
} //End class get_rss

$report = new get_social;
?>