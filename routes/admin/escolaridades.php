<?php

use \App\Http\Response;
use \App\Controller\Admin;


//ROTA de Listage de escolaridades
$obRouter->get('/admin/escolaridades',[
		'middlewares' => [
				'require-admin-login'
		],
		
		
		function ($request){
			return new Response(200, Admin\Escolaridade::getEscolaridades($request));
		}
		]);


//ROTA de Cadastro de um Novo de escolaridades
$obRouter->get('/admin/escolaridades/new',[
		'middlewares' => [
				'require-admin-login'
		],
		
		
		function ($request){
			return new Response(200, Admin\Escolaridade::getNewEscolaridade($request));
		}
		]);

//ROTA de Cadastro de um Novo de escolaridades (POST)
$obRouter->post('/admin/escolaridades/new',[
		'middlewares' => [
				'require-admin-login'
		],
		
		
		function ($request){
			return new Response(200, Admin\Escolaridade::setNewEscolaridade($request));
		}
		]);

//ROTA de Edição de um de escolaridades
$obRouter->get('/admin/escolaridades/{id}/edit',[
		'middlewares' => [
				'require-admin-login'
		],
		
		
		function ($request,$id){
			return new Response(200, Admin\Escolaridade::getEditEscolaridade($request,$id));
		}
		]);

//ROTA de Edição de um de escolaridades (POST)
$obRouter->post('/admin/escolaridades/{id}/edit',[
		'middlewares' => [
				'require-admin-login'
		],
		
		
		function ($request,$id){
			return new Response(200, Admin\Escolaridade::setEditEscolaridade($request,$id));
		}
		]);

//ROTA de Exclusão de um de escolaridades
$obRouter->get('/admin/escolaridades/{id}/delete',[
		'middlewares' => [
				'require-admin-login'
		],
		
		
		function ($request,$id){
			return new Response(200, Admin\Escolaridade::getDeleteEscolaridade($request,$id));
		}
		]);
//ROTA de Exclusão de um de escolaridades (POST)
$obRouter->post('/admin/escolaridades/{id}/delete',[
		'middlewares' => [
				'require-admin-login'
		],
		
		
		function ($request,$id){
			return new Response(200, Admin\Escolaridade::setDeleteEscolaridade($request,$id));
		}
		]);

