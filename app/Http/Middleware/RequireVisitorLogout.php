<?php

namespace App\Http\Middleware;

use \App\Session\Visitor\Login as SessionVisitorLogin;


class RequireVisitorLogout{
	
	//Método responsavel por executar o middleware
	public function handle($request, $next){
		
		
		//Verifica se o usuario está logado
		if(SessionVisitorLogin::isLogged()){
			$request->getRouter()->redirect('/visitor');
		}
		
		
		//Continua a execução
		return $next($request);
		
		
	}
	
}