<?php
class conexaoBD
{
  	var $con;
	var $status_online = false;
  	
  	//** Construtor que abre conexao
  	function conexaoBD()
  	{
	    $this->con = mysql_connect( 'localhost:3306' , 'root' , '123' );
	    if( !$this->con )
	    {
		  	echo("Erro ao conectar no Bando de Dados.");
		  	exit;
		}
	    mysql_select_db( 'checklist' , $this->con );
	}
	
	//** Fecha conexao
	function fechaBd()
	{
		mysql_close( $this->con );
  	}
	
	function chkOnLine($texto) {
		if (!$status_online) {
			return $texto;
		}
	}

	function limpaTabela($tabela) {
		$sql = "truncate table ".$tabela;
		$result = mysql_query( $sql );			
	}	
	
}
?>
