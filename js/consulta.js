var Aplicativo = "consulta";

function lista() {
	
}

function mostra() {
	$("#registro").load('consulta.monta.php?registro=' + $("#idRegistro").val() );		
}

function limpa() {
	$("#registro").html('');
}

function filtro(){
	$("#filtro").load('consulta.lista.filtro.php?cliente=' + $("#cliente").val() + '&tarefa=' + $("#tarefa").val() );		
}