<?php
include('../template/start.php');

//instancia a classe
$tp = &new templateParser('../template/modelo.php');

//define os par�metros da classe
$tags = array(
            'Titulo' => $Titulo,
            'cabecalho' => 'cabecalho.php',
            'Menu' => '../template/cadastro.menu.php',
            'MenuLateral' => '../template/lateral.php',							
            'IntoHead' => $head
        );
 
//faz a substitui��o
$tp->parseTemplate($tags);
 
// exibe a page
echo $tp->display();

?>