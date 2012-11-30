<?php
class templateParser
{
	private $output;

	//construtor faz a carga do template
	function templateParser( $templateFile='template.html' ){
		(file_exists($templateFile)) ? $this->output=file_get_contents($templateFile) : die('Erro: Arquivo '.$templateFile.' nгo encontrado');
	}

	//faz a substituiзгo
	function parseTemplate($tags=array()){
		if(count($tags)>0){
			foreach($tags as $tag=>$data){
				$data = (file_exists($data)) ? $this->parseFile($data) : $data;
				$this->output = str_replace('{'.$tag.'}',$data, $this->output);
			}
		}
		else {
			die('Erro: nгo encontramos o arquivo ou texto');
		}
	}

	//Enquanto o buffer de saнda estiver ativo, nгo й enviada a saнda do script
	function parseFile($file){
		//Ativar o buffer de saнda.
		ob_start();
		include($file);
		//O conteъdo deste buffer interno й copiado na variбvel $content
		$content=ob_get_contents();
		//descartar o conteъdo do buffer.
		ob_end_clean();
		return $content;
	}

	//Exibe o tempalte
	function display(){
		return $this->output;
	}
}
?>