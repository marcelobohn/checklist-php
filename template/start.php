<?php
session_start(); 
//header("Content-Type: text/html; charset=ISO-8859-1",true)

include("config.php");

//inclui a classe
require_once($Acesso.'template/class.template.php');
//include_once("template.php");

$head = "<link rel='stylesheet' type='text/css' href='".$Acesso."css/style.css'/>\n".
		"<script type='text/javascript' src='".$Acesso."js/jquery-1.7.1.min.js'></script>\n".
		//"<script type='text/javascript' src='".$Acesso."js/iphone-style-checkboxes.js'></script>\n".
		"<script type=\"text/javascript\" src=\"".$Acesso."js/util.js\"></script>\n".
		"<script type=\"text/javascript\" src=\"".$Acesso."js/".$ArquivoJS."\"></script>\n";
		
?>