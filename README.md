Spimi-php
=========

Spimi implementation in php using reuters corpus. For course Comp479 

Implementation of Spimi in php is not perfect but it is following rules of spimi. I could not implement the memory blocks features
You will find porter stemmer which I found on internet, it is not implemented in the project.

To run spimi
<your path>/spimi.php

To run search query
<your path>/index.php

The search is breaking each word and searching each word in the final inverted index. 
The resulted array are intersect to perform 'AND' operation and perform union to perform 'OR' on the resulted document ids.

