<?php

$time_start = microtime(true);

include('vars.php');
include('functions.php');


if(mergeBlocks())
	echo "Merging done and final index is saved";
else
	echo "Issue with merging. Try again";


$time_end = microtime(true);
$time = $time_end - $time_start;

echo "<br /><br />Parsing execution time is : <b> ".round($time)." microseconds</b>";
?>