<?php

namespace App\Http\Middleware;

class Queue{
	
	//Mapeamento de middleware
	private static $map = [];
	
	//Mapeamento de middlewares que serão carregados em todas as rotas
	private static $default = [];
	
	//fila de midlleware a serem executados
	private $midllewares = [];
	
	//Função de execução do controlador (clousure)
	private $controller;
	
	//Argumentos da função do controlador
	private $controllerArgs = [];
	
	//Método responsavel por construir a classe de fila do midlleware
	public function __construct($midllewares,$controller,$controllerArgs){
		$this->midllewares = array_merge(self::$default,$midllewares);
		$this->controller = $controller;
		$this->controllerArgs = $controllerArgs;
		
	}
	//Método responsavel por definir o mapeamento de middleware
	public static function setMap($map){
		self::$map =$map;
		
	}
	
	//Método responsavel por definir o mapeamento de middleware padrões
	public static function setDefault($default){
		self::$default =$default;
		
	}
	
	
	//Método responsavel por executar o proximo nível da fila de middlewares
	public function next($request){
		//Verifica se a fila está vazia
		if(empty($this->midllewares)) return call_user_func_array($this->controller, $this->controllerArgs);
		
	
		//Middleware
		$middleware = array_shift($this->midllewares);
		
		//Verifica o mapeamento
		if(!isset(self::$map[$middleware])){
			throw new \Exception("Problemas ao processar o middleware da requisição",500);
		}
	
		//Next
		$queue = $this;
		$next = function($request) use ($queue){
			return $queue->next($request);
		};
		//Executa o middlewarw
		return (new self::$map[$middleware])->handle($request,$next);
		
		
	}
	
	
}