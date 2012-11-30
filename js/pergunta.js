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
		//$("#divResposta").append("<li>" + temp + "");
		$("#divResposta ul li:last").append("<li>" + temp + "</li>");
		
		if (window.XMLHttpRequest)  {// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
		} else  {// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
  
		xmlhttp.onreadystatechange=function()   {
			if (xmlhttp.readyState==4 && xmlhttp.status==200)     {
				document.getElementById("resp").innerHTML=xmlhttp.responseText;
			}
		}

		var url = "pergunta.resposta.inclui.php?idPergunta="+pergunta+"&descricao="+temp;
		xmlhttp.open("GET",url,true);
		xmlhttp.send();	   
	}
}

function apagaResposta(pergunta, resposta) {
	if (pergunta != null) {
		if (window.XMLHttpRequest)  {// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
		} else  {// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange=function()   {
			if (xmlhttp.readyState==4 && xmlhttp.status==200)     {
				document.getElementById("resp").innerHTML=xmlhttp.responseText;
			}
		}
		var url = "pergunta.resposta.apaga.php?idPergunta="+pergunta+"&idResposta="+resposta;
		xmlhttp.open("GET",url,true);
		xmlhttp.send();	   
	}
}
