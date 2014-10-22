<?php

// This function parse the document using Regex and read all content from <Body> tag and return a string 
function parseDocument($fileName)
{
	global $reutersFolder;
	if(file_exists($reutersFolder.$fileName))
	{
		$myfile = fopen($reutersFolder.$fileName, "r") or die("Unable to open file!");
		$tempContent = fread($myfile,filesize($reutersFolder.$fileName));
		fclose($myfile);
		
		//regular expression to get document ID from NEWID tag
		preg_match_all('/\<REUTERS[^>]*NEWID=\"([0-9]+)\"\>(.*?)<\/REUTERS>/is', $tempContent, $doc_ids);
		
		unset($doc_ids[1]);
		unset($doc_ids[2]);
		
		$cn = count($doc_ids[0]);
		for($i=0;$i<=$cn;$i++)
		{
			preg_match("/<body[^>]*>(.*?)<\/body>/is", $doc_ids[0][$i], $b);
			
			if(empty($b))
			{
				unset($doc_ids[0][$i]);
			}
		}
		
		$ncn = count($doc_ids[0]);
		for($i=0;$i<=$cn;$i++)
		{
			preg_match('/\<REUTERS[^>]*NEWID=\"([0-9]+)\"\>/is', $doc_ids[0][$i], $n);
			preg_match("/<body[^>]*>(.*?)<\/body>/is", $doc_ids[0][$i], $b);
			$ft[1][] = $b[1];
			$ft[2][] = $n[1]; 			
		}
				
		return $ft;
	}
	else
		return "File not found.";
}


// This function makes tokens using space and remove StopWords, apply lowercase, remove numeric keywords and remove stop words
function makeTokens($content,$doc_ids)
{
	$arrayCount = count($content);
	$resultArray = Array();
	$main = Array();
	$c =0;
	
	for($i=0;$i<=$arrayCount;$i++)
	{
		$temp_did = $doc_ids[$i];
		
		// applying Case Folding
		$content[$i] = applyLowerCase($content[$i]); 		
		
		// Separating words on basis of whitespace
		$tokens = explode(" ",$content[$i]);	
				
		//removing whitespaces
		$finalTokens = removeWhiteSpaces($tokens);  
				
		// removing unwanted characters
		$finalTokens = stripPunctuations($finalTokens); 
				
		// removing strop words
		$finalTokens = removeStopWords($finalTokens); 
						
		// removing Numerical tokens
		$finalTokens = removeNumericTokens($finalTokens);  
		
		$cnt = count($finalTokens);
		for($k=0;$k<=$cnt;$k++)
		{
			$terms[$finalTokens[$k]] = $temp_did;
		}
		
		foreach ($terms as $key=>$val)
		{
			if($key != NULL || $key != '')
			{
				if(array_key_exists($key, $main) == TRUE)
				{
					if(checkValuesExist($main[$key], $val) == FALSE)
						$main[$key] = $main[$key].",".$val; 
					}
				else
				{
					$main[$key] = $val;
				}
			}
		}
	}//END of arrayCount loop
	
	// tokens array
	$result['tokens'] =  $main; 		
	
	// total number of tokens generated in the document
	$result['total_count'] = count($main);
	 
	return $result;
}



function removeDuplicateElements($content)
{
	return array_unique($content);
}


// This function convert uppercase characters to lowercase
function applyLowerCase($content)
{
	return strtolower($content);
}


// This function is removing whitespaces in terms
function removeWhiteSpaces($content)
{
	return array_map('trim', $content);
}


function stripPunctuations($content)
{
	$whatToStrip = array("?","!",",",";",'"','"...');
	
	$tokensCount = count($content);	// counting tokens
		
	// Performing actions of each token
	for($k=0;$k<=$tokensCount;$k++)
	{
		$content[$k] = str_replace($whatToStrip, "", $content[$k]);
	}//END of tokenCount loop
	
	return $content;	
}


// This function check if the given word is stropword or not
function removeStopWords($tokens)
{
	// 177 stop words
	$stopWords = Array("will", "i", "a", "about", "above", "after", "again", "against", "all", "am", "an", "and", "any", "are", "aren't", "as", "at", "be", "because", "been", "before", "being", "below", "between", "both", "but", "by", "can't", "cannot", "could", "couldn't", "did", "didn't", "do", "does", "doesn't", "doing", "don't", "down", "during", "each", "few", "for", "from", "further", "had", "hadn't", "has", "hasn't", "have", "haven't", "having", "he", "he'd", "he'll", "he's", "her", "here", "here's", "hers", "herself", "him", "himself", "his", "how", "how's", "i", "i'd", "i'll", "i'm", "i've", "if", "in", "into", "is", "isn't", "it", "it's", "its", "itself", "let's", "me", "more", "most", "mustn't", "my", "myself", "no", "nor", "not", "of", "off", "on", "once", "only", "or", "other", "ought", "our", "ours", "ourselves", "out", "over", "own", "same", "shan't", "she", "she'd", "she'll", "she's", "should", "shouldn't", "so", "some", "such", "than", "that", "that's", "the", "their", "theirs", "them", "themselves", "then", "there", "there's", "these", "they", "they'd", "they'll", "they're", "they've", "this", "those", "through", "to", "too", "under", "until", "up", "very", "was", "wasn't", "we", "we'd", "we'll", "we're", "we've", "were", "weren't", "what", "what's", "when", "when's", "where", "where's", "which", "while", "who", "who's", "whom", "why", "why's", "with", "won't", "would", "wouldn't", "you", "you'd", "you'll", "you're", "you've", "your", "yours", "yourself", "yourselves", ' ');
	
	$tokensCount = count($tokens);	// counting tokens
		
	// Performing actions of each token
	for($k=0;$k<=$tokensCount;$k++)
	{
		$word = $tokens[$k];
		if(in_array($word, $stopWords))
		{
			unset($tokens[$k]);
		} // END if			
	}//END of tokenCount loop
	
	$tokens = array_values($tokens);
	return $tokens;
}


// This function remove numerical words from tokens
function removeNumericTokens($tokens)
{
	$tokensCount = count($tokens);	// counting tokens
		
	// Performing actions of each token
	for($k=0;$k<=$tokensCount;$k++)
	{
		$word = $tokens[$k];
		if(is_numeric($word) || empty($word) || !ctype_alpha($word))
		{
			unset($tokens[$k]);
		} // END if			
	}//END of tokenCount loop
	
	$tokens = array_values($tokens);
	return $tokens;
}


// This function save the data to a file. Used for saving postings list
function saveToFile($data,$fileName)
{
	if(file_put_contents('blocks/'.$fileName.'.json', json_encode($data)))
		return true;
	else
		return false;
}

// This function will merge all the blocks and create final index
function mergeBlocks()
{
	$final_file = "final_index.json";
	
	$a1 = file_get_contents("blocks/1.json");
	$a2 = file_get_contents("blocks/2.json");
	$a3 = file_get_contents("blocks/3.json");
	$a4 = file_get_contents("blocks/4.json");
	$a5 = file_get_contents("blocks/5.json");
	$a6 = file_get_contents("blocks/6.json");
	$a7 = file_get_contents("blocks/7.json");
	$a8 = file_get_contents("blocks/8.json");
	$a9 = file_get_contents("blocks/9.json");
	$a10 = file_get_contents("blocks/10.json");
	$a11 = file_get_contents("blocks/11.json");
	$a12 = file_get_contents("blocks/12.json");
	$a13 = file_get_contents("blocks/13.json");
	$a14 = file_get_contents("blocks/14.json");
	$a15 = file_get_contents("blocks/15.json");
	$a16 = file_get_contents("blocks/16.json");
	$a17 = file_get_contents("blocks/17.json");
	$a18 = file_get_contents("blocks/18.json");
	$a19 = file_get_contents("blocks/19.json");
	$a20 = file_get_contents("blocks/20.json");
	$a21 = file_get_contents("blocks/21.json");
	
	
	$arr1 =  json_decode($a1,true);
	$arr2  = json_decode($a2,true);
	$arr3  = json_decode($a3,true);
	$arr4  = json_decode($a4,true);
	$arr5  = json_decode($a5,true);
	$arr6  = json_decode($a6,true);
	$arr7  = json_decode($a7,true);
	$arr8  = json_decode($a8,true);
	$arr9  = json_decode($a9,true);
	$arr10  = json_decode($a10,true);
	$arr11  = json_decode($a11,true);
	$arr12 = json_decode($a12,true);
	$arr13  = json_decode($a13,true);
	$arr14  = json_decode($a14,true);
	$arr15  = json_decode($a15,true);
	$arr16  = json_decode($a16,true);
	$arr17  = json_decode($a17,true);
	$arr18  = json_decode($a18,true);
	$arr19  = json_decode($a19,true);
	$arr20  = json_decode($a20,true);
	$arr21  = json_decode($a21,true);
	
	
	$final_index = array();
	$final_index = mergingBlocks($final_index, $arr1['tokens']);
	$final_index = mergingBlocks($final_index, $arr2['tokens']);
	$final_index = mergingBlocks($final_index, $arr3['tokens']);
	$final_index = mergingBlocks($final_index, $arr4['tokens']);
	$final_index = mergingBlocks($final_index, $arr5['tokens']);
	$final_index = mergingBlocks($final_index, $arr6['tokens']);
	$final_index = mergingBlocks($final_index, $arr7['tokens']);
	$final_index = mergingBlocks($final_index, $arr8['tokens']);
	$final_index = mergingBlocks($final_index, $arr9['tokens']);
	$final_index = mergingBlocks($final_index, $arr10['tokens']);
	$final_index = mergingBlocks($final_index, $arr11['tokens']);
	$final_index = mergingBlocks($final_index, $arr12['tokens']);
	$final_index = mergingBlocks($final_index, $arr13['tokens']);
	$final_index = mergingBlocks($final_index, $arr14['tokens']);
	$final_index = mergingBlocks($final_index, $arr15['tokens']);
	$final_index = mergingBlocks($final_index, $arr16['tokens']);
	$final_index = mergingBlocks($final_index, $arr17['tokens']);
	$final_index = mergingBlocks($final_index, $arr18['tokens']);
	$final_index = mergingBlocks($final_index, $arr19['tokens']);
	$final_index = mergingBlocks($final_index, $arr20['tokens']);
	$final_index = mergingBlocks($final_index, $arr21['tokens']);
		
	ksort($final_index);
		
	if(file_put_contents($final_file, json_encode($final_index)))
		return true;
	else
		return false;
}


function mergingBlocks($main,$temp)
{
	if(empty($main))	
	{
		$main = array_merge($main,$temp);
	}
	else
	{
		foreach ($temp as $key=>$val)
		{
			if(array_key_exists($key, $main) == TRUE)
			{
				if(checkValuesExist($main[$key], $val) == FALSE)
				{
					$main[$key] = $main[$key].",".$val;
				} 
			}
			else
			{
				$main[$key] = $val;
			}
		}
	}
	return $main;		
}


function checkValuesExist($main,$temp)
{
	$ex = explode(",", $main);
	
	if(!empty($ex))
	{
		foreach ($ex as $val)
		{
			if($val == $temp)
				return true;
		}
	}
	return false;	
}


function makeQueryTokens($query)
{
	$t = explode(" ", $query);
	return $t;
}


function doSearch($word)
{
	$final_index = file_get_contents("final_index.json");
	$final_arr = json_decode($final_index,true);

	if(array_key_exists($word, $final_arr))
	{
		return $final_arr[$word];
	}
	else
	{
		return false;
	}
	
}
?>