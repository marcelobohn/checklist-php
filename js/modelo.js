var Aplicativo = "modelo";

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
	//ajuste manual necessário
	var pNome = '';
	pIdModelo = document.getElementById("idModelo").value;	
	pNome = escape(document.getElementById("nome").value); //$("#nome").val();

	var arquivo = Aplicativo+".model.php?acao=grava&idModelo="+pIdModelo+"&nome="+pNome+"";
	$("#divConteudo").load(arquivo);	
}

function excluir() {
	$('input:checked').each(function() {
		//var url = "apaga.php?id="+$(this).val();
		var arquivo = Aplicativo+".model.php?acao=apaga&id="+$(this).val();
		$("#divConteudo").load(arquivo);	
     });
	 
	lista();	
}

function listaPergunta() {
	var pId = document.getElementById("idModelo").value;
	var arquivo = Aplicativo+".pergunta.lista.php?id="+pId;
	$("#divPergunta").load(arquivo);
}

function incluiPergunta(modelo, pergunta) {
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
		var url = "modelo.pergunta.inclui.php?idModelo="+modelo+"&idPergunta="+pergunta;
		xmlhttp.open("GET",url,true);
		xmlhttp.send();	   
	}
}

function apagaPergunta(modelo, pergunta) {
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
		var url = "modelo.pergunta.apaga.php?idModelo="+modelo+"&idPergunta="+pergunta;
		xmlhttp.open("GET",url,true);
		xmlhttp.send();	   
	}
}
