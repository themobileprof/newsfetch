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
	
	function __construct (){
		global $link;
		$this->dblink = $link1 = $link;
		
		// Get all RSS feeds stored in database
		//$sql = "SELECT `id`,`url` FROM articles WHERE id = 'cd55814df7'"; //For Debug purposes
		$sql = "SELECT `id`,`url` FROM articles WHERE article_date = '".date("Y-m-d")."'";
		$result = @mysqli_query($link1, $sql);
		while ($rows = @mysqli_fetch_assoc ($result)){
			//echo "Got some news";
			$this->add_db ($this->dblink, $rows['id'], $rows['url']);
		}
	}
	
	function add_db ($link, $artid, $url){
		//$url = urlencode("http://punchng.com/fayose-aregbesola-disagree-sale-national-assets/");
		
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
		$app_id = '361222975195';
		$app_secret = '5cf538069c3e9b8c8201fee2eff4924b';
		
		$urls = 'https://graph.facebook.com/v2.7/?id='. urlencode($url) . '&access_token='. $app_id . '|' . $app_secret;
		$string = @file_get_contents( $urls );
		if($string) {
			$fan_count = json_decode( $string,true );
			return intval($fan_count['share']['share_count']);
		}
	}
	
	function get_tw_score ($url){
		require_once('TwitterAPIExchange.php');
		 
		/** Set access tokens here - see: https://dev.twitter.com/apps/ **/
		$settings = array(
			'oauth_access_token' => "15249547-dsZWUduygigvPGzocSHXGsoDT8yCq0Tcr3qbkbwxk",
			'oauth_access_token_secret' => "IQSxE9JYtFCLjmCWZAYndW63cXUNuif6TjQRwCvOC1zok",
			'consumer_key' => "byquokpviVkaFpFeupr3TpEIm",
			'consumer_secret' => "4osITXYAm6q3ZfrvxrXxjmWa2IxyhlopOkTFOPCCM6QGFNaQAC"
		);
		
		//$requestMethod = "GET";
		 
		//$twitter = new TwitterAPIExchange($settings);
		
		//return '3'; //Test only
		$loc_url = "http://urls.api.twitter.com/1/urls/count.json?url=".$url;
		
		$ch = curl_init();		
		curl_setopt($ch, CURLOPT_URL, $loc_url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		$data = curl_exec($ch);
		
		curl_close($ch);
		
		$json = json_decode ($data);
		
		//print_r($json);
		
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