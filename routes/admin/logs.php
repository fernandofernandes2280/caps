<?php

use \App\Http\Response;
use \App\Controller\Admin;


//ROTA de Listage de Usuários
$obRouter->get('/admin/logs',[
		'middlewares' => [
				'require-admin-login'
		],
		
		
		function ($request){
		    //apenas administrador pode excluir paciente
		    if($_SESSION['admin']['usuario']['tipo'] == 'Admin')
		        return new Response(200, Admin\Logs::getLogs($request));
		          else
		        return new Response(200, 'Você não tem permissão. Contate o Administrador! <a href="javascript:history.back()">Voltar</a>');
		}
		]);


