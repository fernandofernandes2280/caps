<?php

namespace App\Http;

class Response{
	
	//Código do status HTTP do Response
	private $httpCode = 200;
	
	//cabeçalho do Response
	private $headers = [];
	
	//Tipo de conteúdo que estás sendo retornado
	private $contentType = 'text/html';
	
	//Guarda o conteúdo do response
	private $content;
	
	
	// Método responsável por iniciar a classe e definir os valores
	public function __construct($httpCode, $content, $contentType = 'text/html'){
		$this->httpCode = $httpCode;
		$this->content = $content;
		$this->setContentType($contentType);
		
	}
	
	//método responsavel por alterar o Content Type do response
	public function setContentType($contentType){
		$this->contentType = $contentType;
		$this->addHeader('Content-Type', $contentType);
		
	}
	//Método responsavel por adicionar um registro no cabeçalho do response
	public function addHeader($key, $value){
		$this->headers[$key] = $value;
	}
	
	
	//Método responsável por enviar os headers para o navegador
	private function sendHeaders(){
		//Status
		http_response_code($this->httpCode);
		
		//Enviar todos os headres
		foreach ($this->headers as $key=>$value) {
			header($key.': '.$value);
		}
	}
	
	//Método responsavel por enviar a resposta para o usuário
	public function sendResponse(){
		//envia os headers
		$this->sendHeaders();
		
		//Imprime o conteúdo
		switch ($this->contentType) {
			case 'text/html':
				echo $this->content;
			exit;
			case 'application/json':
				echo json_encode($this->content,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
				exit;
		}
		
	}
		
	
	
}