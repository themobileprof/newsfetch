<?php
namespace App;
////////////////////////////////////
class rssGrab  {

	 /**
	* PDO object
	* @var \PDO
	*/
	private $pdo;
	 
	public $doc;
	
	private $sourceid;
	
	/**
	 * Get a web file (HTML, XHTML, XML, image, etc.) from a URL.  Return an
	 * array containing the HTTP server response header fields and content.
	 */
	public function __construct ($pdo){

		/**
		* connect to the SQLite database
		*/
		$this->pdo = $pdo;

	}

	function getSources (){
		// Get all RSS feeds stored in database
		$stmt = $this->pdo->query("SELECT `id`,`rss`,`catId` FROM sources WHERE activ = '1'");


		$sources = [];

		while ($rows = $stmt->fetch(\PDO::FETCH_ASSOC)) {
			//echo "RSS: ".$rows['rss']."<br />\n";
			$sources[] = $rows;
		}
		
		
		return $sources;
		
		
		/*
		$Smplf[0]['id'] = '65';
		$Smplf[0]['rss'] = "feeds/businessday_feed.php";
		$Smplf[0]['catId'] = '1';
		
		$Smplf[1]['id'] = '17';
		$Smplf[1]['rss'] = "feeds/guardian_feed.php";
		$Smplf[1]['catId'] = '1';
		
		$Smplf[2]['id'] = '48';
		$Smplf[2]['rss'] = "feeds/punch_feed.htm";
		$Smplf[2]['catId'] = '4';
		
		$Smplf[3]['id'] = '6';
		$Smplf[3]['rss'] = "feeds/vanguard_feed.htm";
		$Smplf[3]['catId'] = '1';
		
		$Smplf[4]['id'] = '54';
		$Smplf[4]['rss'] = "feeds/daily_trust_rss.php";
		$Smplf[4]['catId'] = '1';
		
		foreach ($Smplf as $rows){
			$this->get_rss_upd ($rows['rss'],$rows['id']);
			$this->add_db ($this->dblink, $rows['catId']);
		}
		*/
	}
	
	function getFeed ($rss_feed){
		//echo "Get_RSS_Upd: ".$rss_feed;
		$ch = curl_init($rss_feed);
		curl_setopt($ch, CURLOPT_TIMEOUT, 3);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURL_HTTP_VERSION_1_1, true);
		curl_setopt($ch, CURLOPT_ENCODING, "gzip, deflate");
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.3) Gecko/2008092417 Firefox/3.0.3");
		$data = curl_exec($ch);
		curl_close($ch);
		
		//print_r($data);
		//exit();
		
		$use_errors = libxml_use_internal_errors(true);
		try {
			$this->doc = new \SimpleXmlElement($data, LIBXML_NOCDATA);
			if(isset($this->doc->channel)):
				// $this->parseRSS($doc);
				return 'rss';
			elseif(isset($this->doc->entry)):
				// $this->parseAtom($doc);
				return 'atom';
			endif;


		} catch (Exception $e) {
			$this->failedURL();
			return FALSE;
		}
		// echo "this: ";
		// print_r($doc); //debug
		// exit(); //////////////
		
		
	}
	
	// Returns the error message on improper XML
	function isXML($xml){
		libxml_use_internal_errors(true);
	
		$doc = new DOMDocument('1.0', 'utf-8');
		$doc->loadXML($xml);
	
		$errors = libxml_get_errors();
	
		if(empty($errors)){
			return true;
		}
	
		$error = $errors[0];
		if($error->level < 3){
			return true;
		}
	
		$explodedxml = explode("r", $xml);
		$badxml = $explodedxml[($error->line)-1];
	
		$message = $error->message . ' at line ' . $error->line . '. Bad XML: ' . htmlentities($badxml);
		return $message;
	}
	
	function parseRSS(){
		$xml = $this->doc;
		$cnt = count($xml->channel->item);
		
		for($i=0; $i<$cnt; $i++){
		 
			$d['title'] = $xml->channel->item[$i]->title;
			$d['description'] = $xml->channel->item[$i]->description;
			$d['pubdate'] = date("Y-m-d",strtotime($xml->channel->item[$i]->pubDate));
			$d['guid'] = stripslashes(htmlentities($xml->channel->item[$i]->guid,ENT_QUOTES,'UTF-8'));
			$d['url'] = htmlentities($xml->channel->item[$i]->link,ENT_QUOTES,'UTF-8');
			$d['img'] = stripslashes($xml->channel->item[$i]->img,ENT_QUOTES,'UTF-8');
			$d['sourceid'] = $this->sourceid;
			
			// Get Image if image field is empty
			if (empty($d['img'])){
				$d['img'] = $this->get_first_image($d['description']);
			}
			
			//Cleanse description
			$d['description'] = $this->proc_desc ($d['description']);
			
			
			$this->rssPosts[]=$d;
		}
	}
	
	function parseAtom(){
		$xml = $this->doc;
		$cnt = count($xml->entry);
		
		for($i=0; $i<$cnt; $i++){
			
			if (!empty($xml->entry[$i]->link[4])):
				$urlAtt = $xml->entry[$i]->link[4]->attributes();
			else:
				$urlAtt = $xml->entry[$i]->link[0]->attributes();
			endif;
			
			
			$d['title'] = stripslashes(htmlentities($xml->entry[$i]->title,ENT_QUOTES,'UTF-8'));
			$d['description'] = $xml->entry[$i]->content;
			$d['pubdate'] = date("Y-m-d",strtotime($xml->entry[$i]->updated));
			$d['guid'] = stripslashes(htmlentities($xml->entry[$i]->id,ENT_QUOTES,'UTF-8'));
			
			if (strpos($urlAtt['href'],"OqshX") !== false): //set link for LindaIkeja
				$d['url'] = "http://lindaikeji.blogspot.com/".date("Y/m/",strtotime($xml->entry[$i]->updated)).end(explode("/",$urlAtt['href']));
			else:
				$d['url'] = htmlentities($urlAtt['href'],ENT_QUOTES,'UTF-8');
			endif;
			
			$d['img'] = $this->get_first_image($d['description']);
			$d['sourceid'] = $this->sourceid;
			
			//Cleanse description
			$d['description'] = $this->proc_desc ($d['description']);
			
			
			$this->rssPosts[]=$d;
		}
	}
	
	function failedURL (){
		$this->pdo->exec("UPDATE `sources` SET `fail` = `fail`+1 WHERE `id` = '$this->sourceid' LIMIT 1");
	}
	
	function cleans_1($d){
		
		$d = strip_tags($d,'<p>');
		$d = str_replace("<p>","",$d);
		$d = str_replace("</p>","",$d);
		return $d;
	}
	
	function get_first_image($htmlStr){
		$html = new DOMDocument();        
		$html->loadHTML($htmlStr);
	   //get the first image tag from the description HTML
		$imgTag = $html->getElementsByTagName('img');
		$img = ($imgTag->length==0)?'':$imgTag->item(0)->getAttribute('src');
		
		return $img;
		
		/*/////////////////////////////
		require_once('simple_html_dom.php');
		
		foreach ($feed->get_items() as $item)
		{
		 $description =  $item->get_description();
		 $desc_dom = str_get_html($description);
		 $image = $desc_dom->find('img', 0);
		 $image_url = $image->src;
		}
		
		require_once('../includes/simple_html_dom.php');
	
		$post_html = str_get_html($html);
	
		if (!empty($post_html)) {
			$first_img = $post_html->find('img', 0);
				
			if($first_img !== null) {
				return $first_img->src;
			}
		}
	
	
		return null;
		/////////////////////////////*/
	}
	
	function addDb ($link, $type){
		if(is_array($this->rssPosts)):
			// Includes for related articles
			//require_once '../includes/class.stemmer.inc';
			//require_once '../includes/cleansearch.php';
			//require_once '../includes/related.php';
			
			foreach($this->rssPosts as $post){
				//Make article code
				$artid = random_str(10);
				
				$sql = "INSERT INTO articles SET
				id = '$artid',
				guId = '$post[guid]',
				sourceId = '$post[sourceid]',
				title = '$post[title]',
				description = '$post[description]',
				img = '$post[img]',
				url = '$post[url]',
				articleDate = '$post[pubdate]',
				catId = '$type'";
				//echo $sql;
				if ($this->pdo->exec($sql)):
					//Create shortcode
					//$this->addshortcode ($this->dblink,$artid);
					
					
					
					/*/////////////////////////////////////////////////////////////
					//////////////////////////////////////////////////////////////
					// CREATE RELATED ARTICLES
					//////////////////////////////////////////////////////////////
					$stemmer = new Stemmer;
					$clean_string = new cleaner();
					
					// Prepare for alternative search
					$split = split(" ",$post['title']);
					  foreach ($split as $array => $value) {
						  if (strlen($value) < 3) {
							  continue;
						  }
						  $stemmed_string = $clean_string->parseString($value);
						  $stemmed_string = $stemmer->stem($stemmed_string);
						  
						  $new_string .= $stemmed_string.' ';
					  }
					  
					$new_string=substr($new_string,0,(strLen($new_string)-1));
					
					
					$search_news = new related();
					
					// Search with full text search //////////////////////////////
					if (!$search_news->fulltext($post['title'], $search_news->dblink)):
						// If full text fails, proceed to the next level
						//Search with alternative text search ////////////////////
						$showr = $search_news->altsearch($new_string, $link);
					elseif (count($search_news->rel) < 10): // if results are less than 10
						$remaining = 10 - count($search_news->rel);
						//Search with alternative text search ////////////////////
						$showr = $search_news->altsearch($new_string, $link, $remaining);
					endif;
					
					// Add to database
					$search_news->add2db ($artid, $search_news->dblink);
					echo "Seen related add";
					
					/////////////////////////////////////////////////////////////////////////////////
					/////////////////////////////////////////////////////////////////////////////////
					///////////////////////////////////////////////////////////////////////////////*/
				else:
					echo "No add";
				endif;
			}
		endif;
	}
	
	function mk_code (){
		return substr(md5(time()), 0, 10);
	}
	
	function proc_desc ($body){
		$body = htmlentities(strip_tags($body));
		
		return $body;
		
		
		/*///////////////////////////////////////
		if (strlen($body) > 300):
			$pos = strrpos($body, ".");
			if ($pos !== false):
			   $body = substr($body, 0, $pos);
			else:
				$pos1 = strrpos($body, " ");
				if ($pos1 !== false):
					$body = substr($body, 0, $pos1);
				endif;
			endif;
			
			$body .= "...";
		endif;
		
		
		return $body;
		/////////////////////////////////////*/
	}
	
	function addshortcode ($link,$art){
		$i = 0;
		while ($i < 20){ //create a loop that ensures that the code is created
			$code = ci_rand (5);
			$sql = "INSERT INTO shortcode SET
			code = '$code',
			newsId = '$art',
			legend = 'general'";
			//echo $sql;
			if ($this->pdo->exec($sql)):
				return TRUE;
			endif;
		}
	}
} //End class get_rss


?>
