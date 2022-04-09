<?php

namespace App\Http;
use \Closure;
use \Exception;
use \ReflectionFunction;
use \App\Http\Middleware\Queue as MiddlewareQueue;
class Router{

		/**
	 * URL COMPLETA DO PROJETO
	 * @var string
	 */
	private $url = '';

	//Prefixo de todas as rotas
	private $prefix = '';

	//Índice de rotas
	private $routes = [];

	//Instancia de Request
	private $request;
	
	//Content type padrão do response
	private $contentType = 'text/html';

	//Método responsavel por iniciar a classe
	public function __construct($url){
		$this->request = new Request($this);
		$this->url     = $url;
		$this->setPrefix();
	}
	
	//Método responsavel por altear o valor do content type
	public function setContentType($contentType){
		$this->contentType = $contentType;
	}

	//Método responsavel por definir o prefixo das rotas
	private function setPrefix(){
		//IMformacoes da URL atual
		$parseURL = parse_url($this->url);
		//Define o prefeixo
		$this->prefix = $parseURL['path'] ?? '';
	}

	//Método responavel por adicionar uma rota na classe
	private function addRoute($method, $route, $params = []){

		//Validação dos parametros
		foreach ($params as $key => $value) {
			if($value instanceof Closure){
				$params['controller'] = $value;
				unset($params[$key]);
				continue;
			}

		}
		
		//MIddlewares da Rota
		$params['middlewares'] = $params['middlewares']  ?? [];
		
		
		//Variáveis da Rota
		$params['variables'] = [];

		//Padrao de Validação dsa Variáveis das Rotas
		$patternVariable = '/{(.*?)}/';
		if(preg_match_all($patternVariable, $route, $matches)){

			$route = preg_replace($patternVariable, '(.*?)', $route);
			$params['variables'] = $matches[1];
		}

		//Padrão de validação da url
		$patternRoute = '/^'.str_replace('/','\/' , $route).'$/';



		//Adciona a rota dentro da classe
		$this->routes[$patternRoute][$method] = $params;

	}

	//Método responsável por definir uma rota de GET
	public function get($route, $params = []) {
		return $this->addRoute('GET', $route,$params);
	}

	//Método responsável por definir uma rota de POST
	public function post($route, $params = []) {
		return $this->addRoute('POST', $route,$params);
	}

	//Método responsável por definir uma rota de PUT
	public function put($route, $params = []) {
		return $this->addRoute('PUT', $route,$params);
	}

	//Método responsável por definir uma rota de DELETE
	public function delete($route, $params = []) {
		return $this->addRoute('DELETE', $route,$params);
	}

	//Método responsavel por retornar a URI desconsiderando o prefixo
	private function getUri(){
		//URI da request
		$uri = $this->request->getUri();

		//Fatia a URI com o prefixo
		$xUri = strlen($this->prefix) ? explode($this->prefix, $uri) : [$uri];

			
		//retorna a URI sem prefixo;
		return rtrim(end($xUri),'/');
		//return end($xUri);
	}

	//Método responsavel por retonrar os dados da rota atual
	private function getRoute(){
		//URI
		$uri = $this->getUri();
		//Method
		$httpMethod = $this->request->getHttpMethod();

		//Valida as rotas
		foreach ($this->routes as $patternRoutes => $methods) {
			//Verifica se a uri bate com o padrão
			if(preg_match($patternRoutes, $uri, $matches)){
				//Verifica o método
				if (isset($methods[$httpMethod])) {

					//Remove a primeira posição
					unset($matches[0]);

					//Variáveis processadas
					$keys = $methods[$httpMethod]['variables'];
					$methods[$httpMethod]['variables'] = array_combine($keys, $matches);
					$methods[$httpMethod]['variables']['request'] = $this->request;

					//Retorn dos parametros da rota
					return $methods[$httpMethod];

				}
				//Método não permitido/ definido
				throw new Exception("Método não é permitido", 405);
			}

		}
		//URL não encontrada
		throw new Exception("URL não encontrada", 404);
	}

	//Método responsavel pro executar a rota atual
	public function run(){
		try {
			//Obtem a rota atual
			$route = $this->getRoute();


			//Verifica o controlador
			if(!isset($route['controller'])){
				throw new Exception("A URL não pôde ser processada",500);
			}
			//argumentos da função
			$args = [];

			//Reflection
			$reflection = new ReflectionFunction($route['controller']);
			foreach ($reflection->getParameters() as $parameter ){
				$name = $parameter->getName();
				$args[$name] = $route['variables'][$name] ?? '';

			}

		
			
			//Retorna a execução da fila de Middlewares			
			return (new MiddlewareQueue($route['middlewares'],$route['controller'],$args))->next($this->request);


		//	throw new Exception();
		} catch (Exception $e) {
			return new Response($e->getCode(), $this->getErrorMessage($e->getMessage()),$this->contentType);
		}
	}
	
	//Método responsavel por retonar a mensagem de errro de acordo com o content type
	private function getErrorMessage($message){
		
		switch ($this->contentType) {
			case 'application/json':
				return [
					'error' => $message
				];
			break;
			
			default:
				return $message;
			break;
		}
		
	}
	
	//Método responsavel por retornar a url atual
	public function getCurrentUrl(){
		return $this->url.$this->getUri();
	}
	
	//Método responsavel por redirecionar a URL
	public function redirect($route) {
		//URL
		$url = $this->url.$route;
		
		//executa o redirect
		header('location: '.$url);
		exit;
		
	}

}
