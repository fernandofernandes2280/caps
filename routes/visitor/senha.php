<?php

use \App\Http\Response;
use \App\Controller\Visitor;

//ROTA Admin
$obRouter->get('/visitor/trocarSenha',[
		'middlewares' => [
				'require-visitor-login'
		],
		function ($request){
			return new Response(200, Visitor\Senha::getTrocarSenha($request));
			//return new Response(200, Visitor\Home::getHome($request));
		}
		]);


$obRouter->post('/visitor/trocarSenha',[
		'middlewares' => [
				'require-visitor-login'
		],
		function ($request){
			return new Response(200, Visitor\Senha::setTrocarSenha($request));
			//return new Response(200, Visitor\Home::getHome($request));
		}
		]);