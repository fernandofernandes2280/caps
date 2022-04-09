<?php

namespace App\Http;

class Request{
	
	//Instancia do router
	private $router;
	
	//método HTTP da requisição
	private $httpMethod;
	
	//URI da página
	private $uri;
	
	//Parâmetros da URL ($_GET)
	private $queryParams = [];
	
	//Variáveis recebidas no POST da página
	private $postVars = [];
	
	//Variáveis recebidas no FILE da página
	private $fileVars = [];
	
	
	//Cabeçalho da requiscao
	private $header = [];
	
	
	//Construtor da classe
	public function __construct($router){
		$this->router = $router;
		$this->queryParams = $_GET ?? []; //se nao existir passa vazio
		$this->postVars = $_POST ?? [];
		$this->fileVars = $_FILES ?? [];
		$this->header = getallheaders(); //esta função obtem todos os headers recebidos
		$this->httpMethod = $_SERVER['REQUEST_METHOD'] ?? '';
		$this->setURI();
		
			
	}
	
	//Método responsavel por definir a URI
	private function setURI(){
		//Uri completa (com gets)
		$this->uri = $_SERVER['REQUEST_URI'] ?? '';
		
		//Remove gets da URI
		$xURI = explode('?', $this->uri);
		$this->uri = $xURI[0];
	}
	
	//Método responsavel por retornar a instância de Router
	public function getRouter(){
		return $this->router;
	}
	
	//Método responsável por retornar o metodo Http da requisicao
	public function getHttpMethod(){
		return $this->httpMethod;
	}
	//Método responsável por retornar a URI da requisicao
	public function getUri(){
		return $this->uri;
	}
	//Método responsável por retornar os headers da requisicao
	public function getHeaders(){
		return $this->header;
	}
	//Método responsável por retornar os parâmetros da requisicao
	public function getQueryParams(){
		return $this->queryParams;
	}
	//Método responsável por retornar as váriaveis POST da requisicao
	public function getPostVars(){
		return $this->postVars;
	}
	//Método responsável por retornar as váriaveis POST da requisicao
	public function getFileVars(){
		return $this->fileVars;
	}
}









