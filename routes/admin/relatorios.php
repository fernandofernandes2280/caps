<?php

use \App\Http\Response;
use \App\Controller\Admin;


//ROTA de Listage de Relatorio de Atendimento
$obRouter->post('/admin/relatorios',[
		'middlewares' => [
				'require-admin-login'
		],
		
		function ($request){
			return new Response(200, Admin\Relatorio::getRelatorio($request));
		}
		]);


