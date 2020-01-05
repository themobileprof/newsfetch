<?php
//$st_time = microtime();
error_reporting(E_ERROR); 
//ini_set( 'display_errors','1');

require_once 'db.php';
require_once '../includes/common.php';
require_once '../includes/smail/PHPMailerAutoload.php';
//require_once '../includes/lib/swift_required.php';
require_once '../includes/adverts.php';
//////////////////////////////////////////
class sendnews {
	private $dblink;
	private $friend;
	public $errors="";
	private $allmails;
	private $bcc;
	private $sendername;
	private $mailbody;
	private $mailbody2;
	private $percycle = '10';
	private $subj;
	private $listname='newsletter';
	private $sm_num = 1;
	///////////////////////////////////////////
	// The following are for testing the newsletter
	///////////////////////////////////////////
	private $test = FALSE;
	///////////////////////////////////////////
	
	
	function __construct (){
		
		//////////////////////////////////////////////////
		//global $st_time;
		global $link;
		$this->dblink = $link;
		
		///////////////////////////////
		//$this->mailbody = file_get_contents("../includes/newslettermail.html");
		$this->mailbody = file_get_contents("../includes/newslettermail.html");
		$this->mailbody2 = file_get_contents("../includes/newslettermail2.html");
		$this->prep_body($this->dblink);
		///////////////////////
		
		////////////////////////
		// Retrieve records
		$result = $this->get_list ($this->dblink);
		//statement for updating
		$upd_wh = "";
		
		
		// Replace body parts        // Adverts
		$this->mailbody = str_replace ("{campain}", $this->listname, $this->mailbody);
		$this->mailbody2 = str_replace ("{campain}", $this->listname, $this->mailbody2);
		$this->mailbody = str_replace ("{ad1}", get_adv(), $this->mailbody);
		$this->mailbody2 = str_replace ("{ad1}", get_adv(), $this->mailbody2);
		$this->mailbody = str_replace ("{ad2}", get_adv(), $this->mailbody);
		$this->mailbody2 = str_replace ("{ad2}", get_adv(), $this->mailbody2);
		$this->mailbody = str_replace ("{ad3}", get_adv(), $this->mailbody);
		$this->mailbody2 = str_replace ("{ad3}", get_adv(), $this->mailbody2);
		
		while ($rows=mysqli_fetch_assoc($result)){
			
			
			//if this email has been sent to in this iteration
			if (!empty($this->allmails[$rows['email']])):
				continue;
			else:
				$this->allmails[$rows['email']]=1;
				
				// Replace body parts
				$body = str_replace ("{email}", $this->listname, $this->mailbody);
				$altbody = str_replace ("{email}", $this->listname, $this->mailbody2);
				
				// Send mail
				$mail = new PHPMailer();
				$mail->IsMail();
				$mail->IsHTML(true);
				$mail->From = 'admin@newsng.com';
				$mail->FromName = 'Nigerian News';
				$mail->Subject = $this->subj;
				$mail->MsgHTML($body);
				$mail->AltBody = $altbody;
				$mail->AddAddress($rows['email']);
				
				
				////////////////////////////////////////////
				if ($mail->Send()):
				// if ($this->count_send($this->dblink, $rows['email'])):
				//if (mail($to, $this->subj, $full_body, $headers)):
				//if ($mailer->send($message)):
					$success = 1;
					$this->count_send($this->dblink, $rows['email']);
				else:
					$this->errors .= "<span style=\"color:#FF0000\">This email was not successfully sent: <strong>$rows[email]</strong></span><br />
					Mail Error: $mail->ErrorInfo";
				endif;
				////////////////////////////////////////////*/
				
				$upd_wh .= "$rows[id], ";
			endif;
		}
		
		//Clean up $upd_wh query
		$upd_wh = substr($upd_wh,0,-2);
		
		if (!empty($success)):
			$this->update_list ($this->dblink, $upd_wh); // mark as sent
			$this->errors .= "<span style=\"color:#00FF00; font-size: 14px;\"><strong>This newsletter has been successfully sent to $this->percycle more subscribers</strong></span>";
		endif;
		
		//echo $this->errors;
		//exit(); // For debugging purposes
	}
	
	
	function prep_body ($link){
		require_once '../includes/newsletter_highlight.php';
		///////////////////////////////
		$news = new get_news("News",$this->listname);
		if (empty($this->subj) && !empty($news->title1)): // Set newsletter subject
			$this->subj = $news->title1;
		endif;
		
		$politics = new get_news("Politics",$this->listname);
		if (empty($this->subj) && !empty($politics->title1)): // Set newsletter subject
			$this->subj = $politics->title1;
		endif;
		
		$business = new get_news("Business",$this->listname);
		if (empty($this->subj) && !empty($business->title1)): // Set newsletter subject
			$this->subj = $business->title1;
		endif;
		
		$sports = new get_news("Sports",$this->listname);
		if (empty($this->subj) && !empty($sports->title1)): // Set newsletter subject
			$this->subj = $sports->title1;
		endif;
		
		$fashion = new get_news("Fashion",$this->listname);
		if (empty($this->subj) && !empty($fashion->title1)): // Set newsletter subject
			$this->subj = $fashion->title1;
		endif;
		
		$ent = new get_news("Entertainment",$this->listname);
		if (empty($this->subj) && !empty($ent->title1)): // Set newsletter subject
			$this->subj = $ent->title1;
		endif;
		
		$health = new get_news("Health",$this->listname);
		if (empty($this->subj) && !empty($health->title1)): // Set newsletter subject
			$this->subj = $health->title1;
		endif;
		
		$tech = new get_news("Technology",$this->listname);
		if (empty($this->subj) && !empty($tech->title1)): // Set newsletter subject
			$this->subj = $tech->title1;
		endif;
		////////////////////////////////////////////////////
		$this->mailbody = str_replace ("{News}", $news->html, $this->mailbody);
		$this->mailbody2 = str_replace ("{News}", $news->nonhtml, $this->mailbody2);
		
		$this->mailbody = str_replace ("{Politics}", $politics->html, $this->mailbody);
		$this->mailbody2 = str_replace ("{Politics}", $politics->nonhtml, $this->mailbody2);
		
		$this->mailbody = str_replace ("{Business}", $business->html, $this->mailbody);
		$this->mailbody2 = str_replace ("{Business}", $business->nonhtml, $this->mailbody2);
		
		$this->mailbody = str_replace ("{Sports}", $sports->html, $this->mailbody);
		$this->mailbody2 = str_replace ("{Sports}", $sports->nonhtml, $this->mailbody2);
		
		$this->mailbody = str_replace ("{Fashion}", $fashion->html, $this->mailbody);
		$this->mailbody2 = str_replace ("{Fashion}", $fashion->nonhtml, $this->mailbody2);
		
		$this->mailbody = str_replace ("{Entertainment}", $ent->html, $this->mailbody);
		$this->mailbody2 = str_replace ("{Entertainment}", $ent->nonhtml, $this->mailbody2);
		
		$this->mailbody = str_replace ("{Health}", $health->html, $this->mailbody);
		$this->mailbody2 = str_replace ("{Health}", $health->nonhtml, $this->mailbody2);
		
		$this->mailbody = str_replace ("{Technology}", $tech->html, $this->mailbody);
		$this->mailbody2 = str_replace ("{Technology}", $tech->nonhtml, $this->mailbody2);
	}
	
	function update_list ($link, $upd){
		$sql = "UPDATE `mailing_list` SET
		`sent` = '1',
		`lastupdate` = '".date('Y-m-d')."'
		WHERE `id` IN (".$upd.")";
		//echo $sql;
		if (@mysqli_query($link, $sql)):
			mysqli_close($link);
			return TRUE;
		else:
			mysqli_close($link);
			return FALSE;
		endif;
	}
	
	
	function get_list ($link){
		// For testing purposes
		if ($this->test){
			$wh = "AND `testing` = '1'";
		} else {
			//$wh = "AND `lastupdate` != '".date('Y-m-d')."'";
			$wh = "";
		}
		
		///////////////////////////////////////////////////////////////////////////////////////
		
		$sql = "SELECT `id`, `email` 
		FROM `mailing_list`
		WHERE `subscription` = '1'
		AND `sent` = '0'
		$wh
		ORDER BY rand()
		LIMIT $this->percycle";
		//echo $sql;
		//exit();
		$result = @mysqli_query($link, $sql);
		
		$d_num_rows = @mysqli_num_rows($result);
		
		//mysqli_close($link);
		
		if ($d_num_rows > 0):
			return $result;
		elseif ($d_num_rows == 0):
			// Change the whole mailing list, and start again
			$this->reset_sent($this->dblink);
			
			$_SESSION[] = array();
			session_destroy();
			exit(); //stop script when there is nothing more to process
		else:
			exit();
		endif;
		
	}
	
	function count_send ($link, $email){
		$sql = "UPDATE `mailing_list` SET sendCount = sendCount+1 WHERE `email` = '".$email."'";
		if (@mysqli_query($link, $sql)){
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	function reset_sent ($link){
		$sql = "UPDATE `mailing_list` SET `sent` = '0'";
		@mysqli_query($link, $sql);
		mysqli_close($link);
	}
} //end class

$newsletter = new sendnews;
//echo microtime()-$st_time;
echo $newsletter->errors;

?>