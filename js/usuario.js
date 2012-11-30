var Aplicativo = "usuario";

function lista(pag) {
	var pPesquisa = escape(document.getElementById("edtPesquisa").value);
	var arquivo = Aplicativo + ".view.php?acao=pesquisa&p=" + pPesquisa
			+ "&pag=" + pag;
	$("#divConteudo").load(arquivo);
}

function incluir() {
	var arquivo = Aplicativo + ".view.php?acao=form";
	$("#divConteudo").load(arquivo);
}

function altera(id) {
	var arquivo = Aplicativo + ".view.php?acao=form&id=" + id;
	$("#divConteudo").load(arquivo);
}

function grava() {
	// ajuste manual necessário
	var pNome = '';
	pIdUsuario = document.getElementById("idUsuario").value;
	pNome = escape(document.getElementById("nome").value); // $("#nome").val();
	pSenha = escape(document.getElementById("senha").value); // $("#nome").val();
	pAdmin = document.getElementById("admin").checked == true ? "S" : "N";

	var arquivo = Aplicativo + ".model.php?acao=grava&idUsuario=" + pIdUsuario
			+ "&nome=" + pNome + "&senha=" + pSenha + "&admin=" + pAdmin;
	$("#divConteudo").load(arquivo);
}

function excluir() {
	$('input:checked').each(function() {
		// var url = "apaga.php?id="+$(this).val();
		var arquivo = Aplicativo + ".model.php?acao=apaga&id=" + $(this).val();
		$("#divConteudo").load(arquivo);
	});

	lista();
}