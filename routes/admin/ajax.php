<?php

use \App\Http\Response;
use \App\Controller\Admin;
use App\Http\Request;


//ROTA de Listage de Cid10
$obRouter->post('/admin/delete',[
		'middlewares' => [
				'require-admin-login'
		],
		
		
		function ($request){
			return new Response(200, Admin\Ajax::create($request));
		}
		]);

