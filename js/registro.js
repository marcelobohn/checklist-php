var Aplicativo = "registro";

function modelos() {

}

function lista() {
/*
	$("#divConteudo").text("");		
	//$("#divConteudo").text("lista de modelos");	
	$("#divConteudo").append("lista de modelos<br />");
	
	if (window.XMLHttpRequest)  {// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	} else  {// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange=function()   {
		if (xmlhttp.readyState==4 && xmlhttp.status==200)     {
			//document.getElementById("divConteudo").innerHTML=xmlhttp.responseText;
			$("#divConteudo").append(xmlhttp.responseText);
		}
	}

	var url = "registro.modelo.lista.php";
	xmlhttp.open("GET",url,true);
	xmlhttp.send();	 	
*/	
}

function gera() {
	$("#registro").load('registro.monta.php?modelo=' + $("#idModelo").val() );		
}

function valida(){
	verificaCampo('usuario');
	verificaCampo('versao');
	verificaCampo('base');
	verificaCampo('tarefa');
	verificaInt('tarefa');
	verificaCampo('cliente');
	verificaInt('cliente');
	
	var lista = [];
	var last = "";
	$('input:radio').each(function() {
		if ((last == "") || (last !=this.name)) {
			lista.push(this.name);
			last = this.name;
		}
    });
	var pendencia = [];
	var resposta = new Array();
	for (var item in lista) {
		var pergunta = lista[item];
		var questionNo = document.getElementsByName(pergunta);
		var verifica = false;
		for (i=0; i<questionNo.length; i++) {
			verifica = (verifica) || (questionNo[i].checked==true)  ;
			if (questionNo[i].checked==true) {
				p = pergunta.substr(2,10);
				r = questionNo[i].value;
				resposta.push( { "perg" : p, "resp" : r } );
			}
		}
		temp = 'p_'+pergunta.substr(2,10);
		if (!verifica) {
			document.getElementById(temp).style.borderColor = 'red';
			pendencia.push(temp);
		} else {
			document.getElementById(temp).style.borderColor = 'black';
		}
	}
	
	if (pendencia.length > 0) {
		alert( 'Existem perguntas sem resposta: \n' + pendencia );
	} else {	
		//gravar(resposta);
		gravar();
	}
	
}

function cancela() {
	$("#registro").html('<br/>Cancelado');
}

//function gravar(lista){
function gravar(){
	document.forms['checklist'].action = 'registro.grava.php?idModelo='+$("#idModelo").val();
	document.forms['checklist'].submit();
	
	/*
	for(var item in lista) {
		//var pergunta = lista[item][0];
		//var resposta = lista[item][1];
		//alert('pergunta: ' + pergunta + ' | resposta' + resposta);
		alert(lista[item].resp);
	}
	*/
	//alert(lista.length);
}