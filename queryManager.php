<?php 
$time_start = microtime(true);
error_reporting(0);
include 'functions.php';

$intersect = Array();

$query = trim(htmlspecialchars($_POST['keyword']));

$query = applyLowerCase($query);

$tokens = makeQueryTokens($query);

$tokens = removeStopWords($tokens);

$tokens = removeNumericTokens($tokens);

foreach ($tokens as $val)
{
	$result = doSearch($val);
	
	if($result)
	{
		echo "<div id='queryWord'>".$val."</div>";
		echo "<div id='queryResult'>".$result."</div>";
		$intersect[] = $result;
	}
	else 
		echo "<p style='color:red;'>No result found for ".$val."</p>";
}

if(count($intersect) == 2)
{
	$int1 = explode(",", $intersect[0]);
	$int2 = explode(",", $intersect[1]);
	$union = array_unique(array_merge($int1,$int2));
	$int_final = array_intersect($int1, $int2);	
}

if(count($intersect) == 3)
{
	$int1 = explode(",", $intersect[0]);
	$int2 = explode(",", $intersect[1]);
	$int3 = explode(",", $intersect[2]);
	
	$union_temp = array_unique(array_merge($int1,$int2));
	$union = array_unique(array_merge($union_temp,$int3));
	
	$int_temp = array_intersect($int1, $int2);
	$int_final = array_intersect($int_temp, $int3);	
}

if(!empty($int_final)){
	echo "<div id='queryWord'>".$query." (AND)</div>";
	foreach ($int_final as $value) {
		echo $value. "<br />";
	}
}

if(!empty($union)) {
	echo "<div id='queryWord'>".$query." (OR)</div>";
	foreach ($union as $value) {
		echo $value. "<br />";
	}
}

$time_end = microtime(true);
$time = $time_end - $time_start;

echo "<br /><br />Search time is : <b> ".round($time,2)." microseconds</b>";

?>