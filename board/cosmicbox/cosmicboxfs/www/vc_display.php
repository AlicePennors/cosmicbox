<?php

/******* Display Visitor - statistics *****

VERSION 0.1 - Matthias Strubel  (c) 2013 - GPL3

Very simple script to get access to the statistic data.

Following GET-Options are possible:

  sortOrder   = ASC / DESC  - Ascendening or decsending sort order
  sortBy      =
  list_type   = "all"  display all data ; "top" - limit display with top n entries
  top_max     = Limit entry list in "top" mode by that value
  output_type = none or html resulsts in a simple html output
               "json" results in a json structure

The HTML output is based on a file pointed in  "vc.conf.php" to.
That file lays on librarybox in the content folder
  http://librarybox.us/content/....
  which is in reality on the USB stick. That file can simply exchanged without the need
  of touching the logic behind.

Currently I don't have the path filter programmed in that. script


CHANGELOG:
  0.1 RELEASE

********************************************/

require_once  "vc.conf.php";
include "vc.func.php";

$config = vc_get_config();

$sort        = $config["sortOrder"];
$sortBy      = $config["sortBy"];
$top_max     = $config["top_max"];
$output_type = $config["output_type"];
$list_type   = $config["list_type"];

// get sortOrder param if any
if (isset($_GET['sortOrder'])) {

	if ($_GET['sortOrder'] == 'ASC') {
		$sort = 'ASC';
	}
	else {
		$sort = 'DESC';
	}

}

// get sortBy param if any
if (isset($_GET['sortBy'])) {

	if ($_GET["sortBy"] == "url") {
		$sortBy = "url";
	}
	elseif ($_GET["sortBy"] == "counter") {
		$sortBy = "counter";
	}
}

# get top_max param if any
if (isset($_GET['top_max'])) {
	$top_max = $_GET['top_max'];
}

# get output_type param if any
if (isset($_GET['output_type'])) {

	if ($_GET["output_type"] == "json") {
		$output_type = "json";
	}
	elseif ( $_GET["output_type"] == "html" ) {
		$output_type = "html";
	}
	elseif ($_GET["output_type"] == "debug") {
		$output_type = "debug";
	}
}

# get list_type param if any
if (isset( $_GET['list_type'])) {
	$list_type= $_GET['list_type'];
}

// detect which statement
$result = vc_read_stat_sum_per_day('%', $sortBy, $sort, $list_type, $top_max);

// display results if any
if (is_array($result)) {

	if ($output_type == "html") {

		# Template file for HTML output
		include $config["HTML_TEMPLATE_FILE"];
		output_html ( $result, array (
				'list_type' => $list_type,
				'top_max'   => $top_max,
				"sortBy"    =>  $sortBy,
				"sortOrder" => $sort,
				"filter_path" => false));
	}
	elseif ( $output_type == "json" ) {
		header('Content-Type: application/json');
		print json_encode ( $result );
	}
	elseif ( $output_type == "debug" ) {
		print_r($result);
	}

}

?>
