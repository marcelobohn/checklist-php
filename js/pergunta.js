var Aplicativo = "pergunta";

function listaResposta(ligado) {
	if (ligado) {
		var pId = document.getElementById("idPergunta").value;
		var arquivo = Aplicativo+".resposta.lista.php?id="+pId;
		$("#divResposta").load(arquivo);
	} else {
		$("#divResposta").text('');
	}
}

function lista(pag) {
	var pPesquisa = escape(document.getElementById("edtPesquisa").value);
	var arquivo = Aplicativo+".view.php?acao=pesquisa&p="+pPesquisa+"&pag="+pag;
	$("#divConteudo").load(arquivo);	
}

function incluir() {
	var arquivo = Aplicativo+".view.php?acao=form";
	$("#divConteudo").load(arquivo);	
}

function altera(id) {
	var arquivo = Aplicativo+".view.php?acao=form&id=" + id;
	$("#divConteudo").load(arquivo);	
}
function grava() {
	verificaCampo('descricao');
	if ((!document.getElementById("marcar").checked) && (!document.getElementById("resposta").checked)) {
		alert('Informe Marcar ou Resposta');
		exit();
	}
	
	var pDescricao = '';
	pIdPergunta = document.getElementById("idPergunta").value;	
	pDescricao = escape(document.getElementById("descricao").value); //$("#nome").val();
	pMarcar = document.getElementById("marcar").checked==true?"S":"N";
	pResposta = document.getElementById("resposta").checked==true?"S":"N";
	var arquivo = Aplicativo+".model.php?acao=grava&idPergunta="+pIdPergunta+"&descricao="+pDescricao+"&marcar="+pMarcar+"&resposta="+pResposta+"";
	$("#divConteudo").load(arquivo);	
	
	lista();
}

function excluir() {
	$('input:checked').each(function() {
		//var url = "apaga.php?id="+$(this).val();
		var arquivo = Aplicativo+".model.php?acao=apaga&id="+$(this).val();
		$("#divConteudo").load(arquivo);	
    });
	 
	lista();	
}

function incluiResposta(pergunta) {
	var temp = prompt("Digite a resposta", "");
	if (temp != null) {
		// Via jQuery para o ajaxPrefilter anexar o token CSRF (ver template/start.php).
		// Atualiza a lista SÓ após a inclusão concluir (no callback), evitando a race.
		var url = "pergunta.resposta.inclui.php?idPergunta="+pergunta+"&descricao="+encodeURIComponent(temp);
		$.get(url, function() { listaResposta(true); });
	}
}

function apagaResposta(pergunta, resposta) {
	if (pergunta != null) {
		var url = "pergunta.resposta.apaga.php?idPergunta="+pergunta+"&idResposta="+resposta;
		$.get(url, function() { listaResposta(true); });
	}
}
