<?php


?>


<html>

<head>
<title>Reuters search engine</title>
</head>
<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css" />
<link rel="stylesheet" type="text/css" href="style.css">

<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/autocomplete.js"></script>

<script type="text/javascript">

function getResult() {
	
	var searchTerm = $("#tags").val();
	
	if(searchTerm.length > 0)
	{
		$("#result").html('<img src="loader.gif">');
		
		var stringData = "keyword="+searchTerm;
			
		$.ajax({
			type: 'POST',
			url: "queryManager.php",
			data: stringData,
			success: function(data) 
			{
				$("#result").html('<p>'+data+'</p>');
			},
			error: function(data)
			{
				$("#result").html('<p style="color:red;">Error: '+data+'</p>');
			}
		});
	}
	else
	{
		$("#result").html('<p style="color:red;">Enter a query to search</p>');
	}
}
</script>

<body>

<h2>Reuters Search Engine</h2>
<div class="ui-widget">
<input id="tags" size="50" name="query" type="text"/>
</div>
<span id="faux" style="display:none;"></span><br>
<!-- <input type="text" name="query" id="query"> --> 
<input type="submit" name="Search" id="submit" value="Search" onclick="getResult()">

<div id="result"></div>
</body>
</html>