<?php
$requi = $_SERVER["HTTP_REFERER"];
$requi= strtolower("/$requi/"); //
$server = $_SERVER['SERVER_NAME'];
$server= strtolower("/$server/");
if(preg_match($server, $requi) == 0){
	//header("Location: http://www.SeuSite.com.br");
	//header("HTTP/1.0 404 Not Found");
	header('HTTP/1.0 404 Not Found');
    echo "<h1>404 Not Found</h1>";
    echo "The page that you have requested could not be found.";	
	die();
} 
?>