<?php

/*********************************

	Simple script, which counts downloads of a specific file
	based on the download URL

	Matthias Strubel  - matthias.strubel@aod-rpg.de
	(C) 2013 - GPL3


**********************************/


include "dl_statistics.func.php";

$redirect_url = $_GET['DL_URL'] ;

$db = __do_db_connect();

$sel_sth = $db->prepare("SELECT url, counter FROM dl_statistics WHERE url = :url");

if (! $sel_sth->execute(array(':url' => $redirect_url))) {
	die ("Error getting stat. line: ". $sel_sthi->errorInfo());
}

$up_sth = "";
$cnt    = 0;
if ($row = $sel_sth->fetch(PDO::FETCH_ASSOC)) {
	$cnt = $row['counter'] + 1;
	$up_sth = $db->prepare("UPDATE dl_statistics SET counter = :cnt WHERE url = :url");
}
else {
	// Seems no hit, so we try to insert it.
	$cnt = 1;
	$up_sth = $db->prepare("INSERT INTO dl_statistics (url , counter) VALUES (:url, :cnt) ");
}

if (! $up_sth->execute(array(':url' => $redirect_url, ':cnt' => $cnt))) {
	die ("Error updateing table with counter $cnt ". $up_sth->errorInfo());
}

header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
// 307 Temporary Redirect
header("Location: $redirect_url",TRUE,307);

?>
