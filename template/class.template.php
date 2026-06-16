<?php
class templateParser
{
	private $output;

	//construtor faz a carga do template
	function __construct( $templateFile='template.html' ){
		(file_exists($templateFile)) ? $this->output=file_get_contents($templateFile) : die('Erro: Arquivo '.$templateFile.' n�o encontrado');
	}

	//faz a substitui��o
	function parseTemplate($tags=array()){
		if(count($tags)>0){
			foreach($tags as $tag=>$data){
				$data = (file_exists($data)) ? $this->parseFile($data) : $data;
				$this->output = str_replace('{'.$tag.'}',$data, $this->output);
			}
		}
		else {
			die('Erro: n�o encontramos o arquivo ou texto');
		}
	}

	//Enquanto o buffer de sa�da estiver ativo, n�o � enviada a sa�da do script
	function parseFile($file){
		//Ativar o buffer de sa�da.
		ob_start();
		include($file);
		//O conte�do deste buffer interno � copiado na vari�vel $content
		$content=ob_get_contents();
		//descartar o conte�do do buffer.
		ob_end_clean();
		return $content;
	}

	//Exibe o tempalte
	function display(){
		return $this->output;
	}
}
?>