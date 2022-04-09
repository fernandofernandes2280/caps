<?php

use \App\Http\Response;
use \App\Controller\Admin;


//ROTA de Listage de Usuários
$obRouter->get('/admin/logs',[
		'middlewares' => [
				'require-admin-login'
		],
		
		
		function ($request){
			return new Response(200, Admin\Logs::getLogs($request));
		}
		]);


