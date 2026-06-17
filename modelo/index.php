<?php
include('../template/start.php');
require_once(__DIR__ . '/../block.php');

//instancia a classe
$tp = new \App\TemplateParser('../template/modelo.php');

//define os parâmetros da classe
$tags = array(
            'Titulo' => $Titulo,
            'cabecalho' => 'cabecalho.php',
            'Menu' => '../template/cadastro.menu.php',
            'MenuLateral' => '../template/lateral.php',							
            'IntoHead' => $head
        );
 
//faz a substituição
$tp->parseTemplate($tags);
 
// exibe a page
echo $tp->display();

?>