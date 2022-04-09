<?php

use \App\Http\Response;
use \App\Controller\Visitor;


//ROTA de Listage de Relatorio de Atendimento
$obRouter->post('/visitor/relatorios',[
		'middlewares' => [
				'require-visitor-login'
		],
		
		function ($request){
			return new Response(200, Visitor\Relatorio::getRelatorio($request));
		}
		]);


