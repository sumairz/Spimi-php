<?php
error_reporting(0);
ini_set('max_execution_time', 1500);

$time_start = microtime(true);

include('vars.php');
include('functions.php');

$docID = 21;
foreach($reuters as $val)
{
	$doc = parseDocument($val);
		
	$tokensResult = Array();
	
	if(is_array($doc))
	{
		$tokensResult = makeTokens($doc[1],$doc[2]);
		
		$saved = saveToFile($tokensResult,$docID);
		
		if($saved == true)
			echo $docID.".json file saved <br />";
		else
			echo "Failed saving data to file <br />";
			
		$docID = $docID + 1;
	}
	else
		echo "Invalid Data.";
}

if(mergeBlocks())
	echo "Merging done and final index is saved";
else
	echo "Issue with merging. Try again";

	
$time_end = microtime(true);
$time = $time_end - $time_start;

echo "Parsing execution time is : <b> ".round($time)." microseconds</b>";
?>