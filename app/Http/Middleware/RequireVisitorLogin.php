<?php

namespace App\Http\Middleware;

use \App\Session\Visitor\Login as SessionVisitorLogin;


class RequireVisitorLogin{
	
	//Método responsavel por executar o middleware
	public function handle($request, $next){
		
		
		//Verifica se o usuario está logado
		if(!SessionVisitorLogin::isLogged()){
			$request->getRouter()->redirect('/visitor/login');
		}
		
		
		//Continua a execução
		return $next($request);
		
		
	}
	
}