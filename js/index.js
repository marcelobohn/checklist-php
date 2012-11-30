var Aplicativo = "index";

function lista() {
	//$("#divConteudo").text("");
	//$("#divConteudo").text("lista de modelos");	
	//$("#divConteudo").append("<a href='pergunta/'>Cadastro de perguntas</a><br/>");
	//$("#divConteudo").append("<a href='modelo/'>Cadastro de modelos</a><br/>");
	//$("#divConteudo").append("<a href='registro/'>Registro de check list</a><br/>");
}

function login() {
	verificaCampo('usuario');
	verificaCampo('senha');
	document.forms['frmLogin'].action = 'login.php';
	document.forms['frmLogin'].submit();	
}