<?php

namespace App\Http\Middleware;

class Api{
	
	//Método responsavel por executar o middleware
	public function handle($request, $next){
	
		
		//Altera o content type para json
		$request->getRouter()->setContentType('application/json');
		
		//Executa o proximo nível do middleware
		return $next($request);
		
	}
	
}