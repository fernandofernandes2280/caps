<?php

use \App\Http\Response;
use \App\Controller\Admin;


//ROTA de Listage de substancias
$obRouter->get('/admin/substancias',[
		'middlewares' => [
				'require-admin-login'
		],
		
		
		function ($request){
			return new Response(200, Admin\Substancia::getSubstancias($request));
		}
		]);


//ROTA de Cadastro de um Novo de Substancias
$obRouter->get('/admin/substancias/new',[
		'middlewares' => [
				'require-admin-login'
		],
		
		
		function ($request){
			return new Response(200, Admin\Substancia::getNewSubstancia($request));
		}
		]);

//ROTA de Cadastro de um Novo de Substancias (POST)
$obRouter->post('/admin/substancias/new',[
		'middlewares' => [
				'require-admin-login'
		],
		
		
		function ($request){
			return new Response(200, Admin\Substancia::setNewSubstancia($request));
		}
		]);

//ROTA de Edição de um de Substancias
$obRouter->get('/admin/substancias/{id}/edit',[
		'middlewares' => [
				'require-admin-login'
		],
		
		
		function ($request,$id){
			return new Response(200, Admin\Substancia::getEditSubstancia($request,$id));
		}
		]);

//ROTA de Edição de um de Substancias (POST)
$obRouter->post('/admin/substancias/{id}/edit',[
		'middlewares' => [
				'require-admin-login'
		],
		
		
		function ($request,$id){
			return new Response(200, Admin\Substancia::setEditSubstancia($request,$id));
		}
		]);

//ROTA de Exclusão de um de Substancias
$obRouter->get('/admin/substancias/{id}/delete',[
		'middlewares' => [
				'require-admin-login'
		],
		
		
		function ($request,$id){
			return new Response(200, Admin\Substancia::getDeleteSubstancia($request,$id));
		}
		]);
//ROTA de Exclusão de um de Substancias (POST)
$obRouter->post('/admin/substancias/{id}/delete',[
		'middlewares' => [
				'require-admin-login'
		],
		
		
		function ($request,$id){
			return new Response(200, Admin\Substancia::setDeleteSubstancia($request,$id));
		}
		]);

