function ajustaTela() {
	// Em telas estreitas o layout e responsivo via CSS (media query, max-width:
	// 700px). Nao impomos larguras/alturas fixas por JS: apenas limpamos as que
	// porventura existam, deixando o CSS empilhar as colunas.
	if (window.matchMedia('(max-width: 700px)').matches) {
		$('#divCorpo, #divConteudo').css({ width: '', height: '' });
		return;
	}
	$('#divCorpo').width($(window).width()-80);
	$('#divCorpo').height($(window).height()-60);
	$('#divConteudo').width($(window).width()-360);
	$('#divConteudo').height($(window).height()-180);
}

$(document).ready(function(){
	//alert('corregado');
	ajustaTela();
	lista();
    //$(':checkbox').iphoneStyle();
	//$("select, input:checkbox, input:radio, input:file").uniform();
});

$(window).resize(function() {
	ajustaTela();
});

$.ajaxSetup ({
    // Disable caching of AJAX responses
    cache: false
});

function verificaCampo(campo) {
	//if ($('#usuario').val() == "") {
	if (document.getElementById(campo).value == "") {
		alert('Campo deve ser preenchido');
		document.getElementById(campo).focus();
		exit();
	}
}

function verificaInt(campo) {
	if (!parseInt(document.getElementById(campo).value)) {
		alert('Campo deve ser preenchido com números');
		document.getElementById(campo).focus();
		exit();
	}
}

function clearDefault(el) {
	if (el.defaultValue==el.value) el.value = "";
	else
	if (el.value=="") el.value = el.defaultValue;
}
