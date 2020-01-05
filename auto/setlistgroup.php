<?php
require_once 'db.php';
require_once '../includes/common.php';
//////////////////////////////////////
$link2 = $link;


// Get current group
$sql = "SELECT `group` FROM `currlist` LIMIT 1";
$result = @mysqli_query($link, $sql);
$row = @mysqli_fetch_assoc($result);

// Update group for next newsletter
if ($row['group'] >= 3):
	$next = 1;
else:
	$next = $row['group'] + 1;
endif;

$sql = "UPDATE `currlist` SET `group` = '$next' LIMIT 1";
@mysqli_query($link2, $sql);
?>