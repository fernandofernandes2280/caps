<?php

use \App\Http\Response;
use \App\Controller\Visitor;

//ROTA Admin
$obRouter->get('/visitor',[
		'middlewares' => [
				'require-visitor-login'
		],
		function ($request,$codPronto){
			return new Response(200, Visitor\Paciente::getPacientes($request,$codPronto));
			//return new Response(200, Visitor\Home::getHome($request));
		}
		]);