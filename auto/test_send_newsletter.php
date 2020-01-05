<?php
require_once 'db.php';
require_once '../includes/common.php';
require_once '../includes/smail/class.phpmailer.php';
//////////////////////////////////////////
class sendnews {
	private $dblink;
	private $friend;
	public $errors="";
	private $bcc;
	private $sendername;
	private $mailbody;
	private $percycle = '2';
	
	
	function __construct (){
		
		//////////////////////////////////////////////////
		global $link;
		$this->dblink = $link;
		
		if (empty($_SESSION['tot_list'])):
			$this->totals('sub',$this->dblink);
		endif;
		
		if (empty($_SESSION['rem'])):
			$_SESSION['rem'] = $_SESSION['tot_list'];
		endif;
		
		///////////////////////////////
		$this->mailbody = file_get_contents("../includes/newslettermail.html");
		$this->prep_body($this->dblink);
		///////////////////////
		if (empty($_SESSION['ini_nl'])):
			$this->reset_list ($this->dblink); // indicates in the database that this particular newsletter has not been sent before
		endif;
		
		////////////////////////
		// Retrieve records
		$result = $this->get_list ($this->dblink);
		while ($rows=mysqli_fetch_assoc($result)){
			//replace firstname and lastname
			$this->mailbody = str_replace ("{firstname}", $rows['firstname'], $this->mailbody);
			$this->mailbody = str_replace ("{lastname}", $rows['lastname'], $this->mailbody);
			$this->mailbody = str_replace ("{email}", $rows['email'], $this->mailbody);
			$this->mailbody = str_replace ("{sub_id}", $rows['id'], $this->mailbody);
			
			//Debug
			//echo $this->mailbody;
			//exit();
			
			/*
			$mail = new PHPMailer();
							$mail->IsMail();
							$mail->IsHTML(true);
							$mail->From = 'register@amstelmaltaboxoffice.com';
							$mail->FromName = "www.amstelmaltaboxoffice.com";
							$mail->AddAddress("$em");
							$mail->Subject = 'Amstel Malta Box Office 5 Contestant Selection';
							$mail->Body  = $body;
			*/
			// Send mail
			$mail = new PHPMailer();
			$mail->IsMail();
			$mail->IsHTML(true);
			$mail->From = 'admin@newsng.com';
			$mail->FromName = "Nigerian News";
			$mail->AddAddress("$rows[email]");
			$mail->Subject = 'Up-to-date Nigerian news from www.newsng.com';
			$mail->Body  = $this->mailbody;
	
			if ($mail->Send()):
				$success = 1;
			else:
				$this->errors .= "<span style=\"color:#FF0000\">This email was not successfully sent: <strong>$rows[email]</strong></span><br />
				Mail Error: $mail->ErrorInfo";
				$success = 0;
			endif;
		}
		
		if (!empty($success)):
			$_SESSION['rem'] = $_SESSION['rem'] - $this->percycle;
			$this->update_list ($this->dblink); // mark as sent
			$this->errors .= "<span style=\"color:#00FF00; font-size: 14px;\"><strong>This newsletter has been successfully sent to $this->percycle more subscribers, remaining <span style=\"color:#FFFFFF\">".$_SESSION['rem']."</span></strong></span>";
		endif;
	}
	
	function prep_body ($link){
		require_once '../includes/newsletter_highlight.php';
		require_once '../includes/pub_date.php';
		/////////////////////////////////////////
		//$pubdate = default_date($link);
		$pubdate = date("Y-m-d");
		///////////////////////////////
		$news = new get_news("News",$pubdate);
		$politics = new get_news("Politics",$pubdate);
		$business = new get_news("Business",$pubdate);
		$sports = new get_news("Sports",$pubdate);
		$fashion = new get_news("Fashion",$pubdate);
		$ent = new get_news("Entertainment",$pubdate);
		////////////////////////////////////////////////////
		$this->mailbody = str_replace ("{News}", $news->html, $this->mailbody);
		$this->mailbody = str_replace ("{Politics}", $politics->html, $this->mailbody);
		$this->mailbody = str_replace ("{Business}", $business->html, $this->mailbody);
		$this->mailbody = str_replace ("{Sports}", $sports->html, $this->mailbody);
		$this->mailbody = str_replace ("{Fashion}", $fashion->html, $this->mailbody);
		$this->mailbody = str_replace ("{Entertainment}", $ent->html, $this->mailbody);
	}
	
	function totals ($type,$link){
		if ($type == 'sub'):
			$sql = "SELECT COUNT(email) AS num FROM test_mailing_list WHERE subscription='1'";
		elseif ($type == 'unsub'):
			$sql = "SELECT COUNT(email) AS num FROM test_mailing_list WHERE subscription='0'";
		else:
			return false;
		endif;
		$result = mysqli_query ($link, $sql);
		$row = @mysqli_fetch_assoc ($result);
		$_SESSION['tot_list'] = $row['num'];
	}
	
	function reset_list ($link){
		$sql = "UPDATE test_mailing_list 
		SET sent = '0'";
		if (@mysqli_query($link, $sql)):
			$_SESSION['ini_nl'] = '1'; //makes sure it only resets after session has ended
			return TRUE;
		else:
			return FALSE;
		endif;
	}
	
	function update_list ($link){
		$sql = "UPDATE test_mailing_list 
		SET sent='1' 
		WHERE sent='0' 
		ORDER BY id ASC
		LIMIT $this->percycle";
		if (@mysqli_query($link, $sql)):
			return TRUE;
		else:
			return FALSE;
		endif;
	}
	
	function get_list ($link){
		$sql = "SELECT id, firstname, lastname, email 
		FROM test_mailing_list
		WHERE subscription = '1'
		AND sent = '0'
		ORDER BY id ASC
		LIMIT $this->percycle";
		$result = @mysqli_query($link, $sql);
		return $result;
	}
} //end class
//////////////////////////////////////////
if (empty($_SESSION['ini_nl'])):
	$newsletter = new sendnews;
endif;

if ($_SESSION['rem'] > 0):
	$newsletter = new sendnews;
	usleep(2000000);
	header ("Location: test_send_newsletter.php");
else:
	$_SESSION[] = array();
	session_destroy();
endif;

echo $newsletter->errors;
echo $_SESSION['rem'];
?>