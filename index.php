<?php
include('template/start.php');

if ($_SESSION['modo']=='de') {
	//instancia a classe
	$tp = &new templateParser('template/modelo.php');
	//define os par�metros da classe
	$tags = array(
            'Titulo' => $Titulo,
            'cabecalho' => 'cabecalho.php',
            'Menu' => '',
            'Conteudo' => 'Acesso',
            'MenuLateral' => 'template/lateral.php',
            'IntoHead' => $head
        );
	//faz a substitui��o
	$tp->parseTemplate($tags);
	// exibe a page
	echo $tp->display();
} else {
	//instancia a classe
	$tp = &new templateParser('template/acesso.php');
	//define os par�metros da classe
	$tags = array(
            'Titulo' => $Titulo,
            'Login' => 'dlgLogin.php',
            'IntoHead' => $head	    
        );
	//faz a substitui��o
	$tp->parseTemplate($tags);
	// exibe a page
	echo $tp->display();
}
?>