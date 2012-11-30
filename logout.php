<?php
session_start(); 
session_destroy();
//header( 'Location: http://localhost:8080/php/checklist/index.php' ) ;
header( 'Location: index.php' ) ;
?>