<?php

use \App\Http\Response;
use \App\Controller\Admin;


//ROTA de Relatórios de atendimentos
$obRouter->post('/admin/producao',[

		
		function ($request){
			return new Response(200, Admin\Producao::getProducao($request));
		}
		]);
