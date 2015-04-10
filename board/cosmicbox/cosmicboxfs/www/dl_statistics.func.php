<?php

require_once  "dl_statistics.conf.php";

function __do_db_connect() {

	$config = dl_get_config();

	if (! $db = new PDO($config['SQLITE_FILE'])) {
		print_r($db->errorInfo()) ;
		die ("Error, couldn't open database");
	}

	$sth = $db->prepare('CREATE TABLE IF NOT EXISTS dl_statistics (url text PRIMARY KEY ASC, counter int)');
	if (! $sth->execute())
		die ("Error creating table: ".$sth->errorInfo ());

	return $db;
}


function dl_read_stat_per_path_only($path="%") {
	$config = dl_get_config();

	return dl_read_stat_per_path($path, $config["sortBy"], $config["sortOrder"], $config["list_type"], $config["top_max"]);
}

function dl_read_stat_per_path($path="%", $sortBy, $sort, $listType, $limit) {

	$config = dl_get_config();

	if (! isset($sortBy))
		$sortBy = $config["sortBy"];

	if (! isset($sort))
		$sort = $config["sortOrder"];

	if (! isset($limit))
		$limit = $config["top_max"];

	$db = __do_db_connect();

	$sth = $db->prepare("SELECT url, counter FROM dl_statistics WHERE url LIKE :path ORDER by $sortBy $sort");

	if ($sth) {

		$generic_path = "";
		if ($path == "%") {
			$generic_path = "%";
		}
		else {
			$generic_path = $path.'%';
		}

		$sth->bindParam(':path',  $generic_path);
		if (! $sth->execute()) {
			print_r($sth->errorInfo());
			die ("Error executing statement");
		}
		$result =  $sth->fetchAll();

		# Tidy array up, I only want named keys
		$full_result = array();
		foreach($result as $elem => &$line) {
			if (file_exists('/mnt/usb/LibraryBox' . $line['url'])) {
				unset($line[0]);
				unset($line[1]);
				$url_expl = explode('/', $line['url']);
				$line['filename'] = end($url_expl);
				$full_result[] = $line;
			}
		}
		if ($listType == "top") {
			return array_slice($full_result, 0, $limit);
		}
		return $full_result;

	}
	else {
		print_r($db->errorInfo());
		die ("\n no valid statement could be found");
	}

}

?>
