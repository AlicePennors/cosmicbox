<?php

require_once  "vc.conf.php";

function vc_do_db_connect() {

	$config = vc_get_config();

	if (! $db = new PDO($config['SQLITE_FILE'])) {
		print_r($db->errorInfo());
		die ("Error, couldn't open database");
	}

	$sth = $db->prepare('CREATE TABLE IF NOT EXISTS vc_statistics(day text, visitor text, PRIMARY KEY (day ,visitor) ON CONFLICT IGNORE)');
	if (! $sth) {
		print_r($db->errorInfo());
		die ("statement error");
	}

	if (! $sth->execute ())
		die ("Error creating table: ". $sth->errorInfo ());

	// $sth = $db->prepare ( 'DELETE FROM vc_statistics');
	// 	if ( ! $sth->execute () )
	// 	          die ( "Error Deleting: ". $sth->errorInfo ());

	return $db;
}


function vc_save_visitor($visitor_key, $visitor_date, $debug = false) {

	if (! $debug) {
		error_reporting(0);
	}

	$db = vc_do_db_connect();

	$sth = $db->prepare('INSERT OR IGNORE INTO vc_statistics (day , visitor) VALUES (:day , :visitor)');

	if (! $sth) {
		print_r($db->errorInfo());
		die ("statement error");
	}

	$sth->bindParam(':day', $visitor_date);
	$sth->bindParam(':visitor', $visitor_key);

	if (! $sth->execute()) {
		if ($debug)
			print_r($sth->errorInfo());
	}
}

function vc_read_stat_sum_per_day_only($day = "%") {

	$config = vc_get_config();

	return vc_read_stat_sum_per_day($day, $config["sortBy"], $config["sortOrder"], "all", $config["top_max"]);
}

function vc_read_stat_sum_per_day($day="%", $sortBy, $sort, $type="all", $limit) {

	$config = vc_get_config();

	if (! isset($sortBy))
		$sortBy = $config["sortBy"];

	if (! isset($sort))
		$sort=$config["sortOrder"];

	if (! isset($limit))
		$limit=$config["top_max"];

	$db = vc_do_db_connect();

	if ($type == "all") {
		$sth = $db->prepare ("SELECT day, count(visitor) as counter FROM vc_statistics WHERE day LIKE :day GROUP BY day ORDER by $sortBy $sort");
	}
	elseif ($type == "top") {
		$sth = $db->prepare("SELECT day, count(visitor) as counter FROM vc_statistics WHERE day LIKE :day GROUP BY day ORDER by $sortBy $sort LIMIT 0, :max");
		$sth->bindParam(':max', $limit, PDO::PARAM_INT);
	}

	if ($sth) {

		$generic_day = "";
		if ($day == "%") {
			$generic_day = "%" ;
		}
		else {
			$generic_day =  $day.'%' ;
		}

		$sth->bindParam(':day',  $generic_day);
		if (! $sth->execute()) {
			print_r($sth->errorInfo());
			die ("Error executing statement");
		}

		$result = $sth->fetchAll();
		# Tidy array up, I only want named keys
		foreach ($result as &$line) {
			unset ($line[0]);
			unset ($line[1]);
			$date_arr = date_parse($line['day'] . " 00:00:00");
			$line['day'] = date("F j, Y", mktime(0, 0, 0, $date_arr['month'], $date_arr['day'], $date_arr['year']));
		}

		return $result;

	}
	else {
		print_r($db->errorInfo());
		die ("\n no valid statement could be found");
	}

}

?>
