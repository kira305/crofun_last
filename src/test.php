<?php 

    $myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
	$txt = "huy hung\n".rand(0,1000);
	fwrite($myfile, $txt);
	$txt = "huy nam\n".rand(0,1000);
	fwrite($myfile, $txt);
	fclose($myfile);

?>