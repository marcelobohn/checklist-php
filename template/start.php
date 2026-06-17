<?php
session_start();
//header("Content-Type: text/html; charset=UTF-8",true)

// Token CSRF da sessão (gerado uma vez por sessão).
if (empty($_SESSION['csrf'])) {
	$_SESSION['csrf'] = bin2hex(random_bytes(32));
}

include("config.php");

//inclui a classe
require_once($Acesso.'template/class.template.php');
//include_once("template.php");

// Cache-busting: anexa ?v=<filemtime> ao asset para que o browser busque a
// versão nova sempre que o arquivo mudar (ver issue #18). O caminho de
// filesystem é resolvido por __DIR__ (robusto a partir de qualquer módulo).
$asset = function (string $rel) use ($Acesso) {
	$fsPath = __DIR__ . '/../' . $rel;
	$ver = is_file($fsPath) ? filemtime($fsPath) : '';
	return $Acesso . $rel . ($ver !== '' ? '?v=' . $ver : '');
};

$head = "<link rel='stylesheet' type='text/css' href='".$asset('css/style.css')."'/>\n".
		"<script type='text/javascript' src='".$asset('js/jquery-3.7.1.min.js')."'></script>\n".
		//"<script type='text/javascript' src='".$Acesso."js/iphone-style-checkboxes.js'></script>\n".
		"<script type=\"text/javascript\" src=\"".$asset('js/util.js')."\"></script>\n".
		"<script type=\"text/javascript\" src=\"".$asset('js/'.$ArquivoJS)."\"></script>\n".
		// expõe o token CSRF e o anexa automaticamente a toda requisição AJAX
		"<script type=\"text/javascript\">\n".
		"var CSRF_TOKEN = \"".$_SESSION['csrf']."\";\n".
		"if (window.jQuery) { jQuery.ajaxPrefilter(function(options){ options.url += (options.url.indexOf('?') >= 0 ? '&' : '?') + 'csrf=' + encodeURIComponent(CSRF_TOKEN); }); }\n".
		"</script>\n";

?>