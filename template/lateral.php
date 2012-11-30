<?php
//$EnderecoBase = "http://10.1.1.105:8080/workspace/checklist/checklist/";
$EnderecoBase = "http://localhost:80/checklist/";

if ($_SESSION['modo']=='de') {
	?>
	<ul class="menu">
<li><a href=<?php echo $EnderecoBase?>>Inicio</a></li>
<li><a href=<?php echo $EnderecoBase.'pergunta/'?>>Cadastro de perguntas</a></li>
<li><a href=<?php echo $EnderecoBase.'modelo/'?>>Cadastro de modelos</a></li>
<li><a href=<?php echo $EnderecoBase.'registro/'?>>Registro de check list</a></li>
<li><a href=<?php echo $EnderecoBase.'consulta/'?>>Consulta de check list</a></li>
</ul>
<hr />
<?php echo "Usuário logado: ".$_SESSION['usuario']; ?>
<ul class="menu">
<li><a href=<?php echo $EnderecoBase.'logout.php'?>>Logout</a></li>
<?php echo ($_SESSION['perfil']=='adm')?('<li><a href=\''.$EnderecoBase.'usuario/\'>Cadastra usuários</a></li>'):('')?>
</ul>
	<?php
}
?>
