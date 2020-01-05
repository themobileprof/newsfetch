<?php
error_reporting(E_ERROR); 
//ini_set( 'display_errors','1');
///////////////////////////////////
require_once 'db.php';
require_once '../includes/common.php';
////////////////////////////////////
//class get_rss extends add_event {
class get_social  {
	private $dblink;
	private $num2process = 500;
	
	function __construct (){
		global $link;
		$this->dblink = $link1 = $link;
		
		$stop = $this->get_last_stop ($this->dblink);
		
		// Get all RSS feeds stored in database
		//$sql = "SELECT `id`,`url` FROM articles WHERE id = 'cd55814df7'"; //For Debug purposes
		$sql = "SELECT `id`,`url` FROM articles ORDER BY `article_date` ASC LIMIT ".$stop.", ".$this->num2process."";
		$result = @mysqli_query($link1, $sql);
		if (@mysqli_num_rows($result) > 0){
			while ($rows = @mysqli_fetch_assoc ($result)){
				$this->add_db ($this->dblink, $rows['id'], $rows['url']);
				echo "Saw RSS";
			}
		} else {
			$this->update_last_stop ($this->dblink, 0);
		}
		
		// Update top
		$new_stop = $stop + $this->num2process;
		$this->update_last_stop ($this->dblink, $new_stop);
	}
	
	function get_last_stop ($link){
		$sql = "SELECT * FROM `auto_stop` WHERE `job` = 'socialgrab' LIMIT 1";
		$result = @mysqli_query($link, $sql);
		$row = @mysqli_fetch_assoc ($result);
		echo "Stop Number: ".$row['stopNumber'];
		return $row['stopNumber'];
	}
	
	function update_last_stop ($link, $stop){
		$sql = "UPDATE `auto_stop` SET
		`stopNumber` = '".$stop."'
		WHERE `job` = 'socialgrab'
		LIMIT 1";
		@mysqli_query($link, $sql);
		echo "Seen Update last stop";
	}
	
	function add_db ($link, $artid, $url){
		$url = urlencode($url);
		
		//get FB score
		$fb_score = $this->get_fb_score ($url);
		
		//get Twitter Score
		$tw_score = $this->get_tw_score ($url);
		
		//get LinkedIn Score
		$lin_score = $this->get_lin_score ($url);
		
		//get Google+ Score
		$gplus_score = $this->get_gplus_score ($url);
		
		$sql = "UPDATE articles SET
		fb_score = '$fb_score',
		tw_score = '$tw_score',
		lin_score = '$lin_score',
		gplus_score = '$gplus_score',
		social_score = (fb_score+tw_score+lin_score+gplus_score)
		WHERE id = '$artid'";
		//echo $sql;
		//exit();
		@mysqli_query($link, $sql);
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