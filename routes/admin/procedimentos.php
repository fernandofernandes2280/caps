<?php

use \App\Http\Response;
use \App\Controller\Admin;


//ROTA de Listage de Procedimentos
$obRouter->get('/admin/procedimentos',[
		'middlewares' => [
				'require-admin-login'
		],
		
		
		function ($request){
			return new Response(200, Admin\Procedimento::getProcedimentos($request));
		}
		]);


//ROTA de Cadastro de um Novo de Usuário
$obRouter->get('/admin/procedimentos/new',[
		'middlewares' => [
				'require-admin-login'
		],
		
		
		function ($request){
			return new Response(200, Admin\Procedimento::getNewProcedimento($request));
		}
		]);

//ROTA de Cadastro de um Novo de Usuário (POST)
$obRouter->post('/admin/procedimentos/new',[
		'middlewares' => [
				'require-admin-login'
		],
		
		
		function ($request){
			return new Response(200, Admin\Procedimento::setNewProcedimento($request));
		}
		]);

//ROTA de Edição de um de Cid10
$obRouter->get('/admin/procedimentos/{id}/edit',[
		'middlewares' => [
				'require-admin-login'
		],
		
		
		function ($request,$id){
			return new Response(200, Admin\Procedimento::getEditProcedimento($request,$id));
		}
		]);

//ROTA de Edição de um de Cid10 (POST)
$obRouter->post('/admin/procedimentos/{id}/edit',[
		'middlewares' => [
				'require-admin-login'
		],
		
		
		function ($request,$id){
			return new Response(200, Admin\Procedimento::setEditProcedimento($request,$id));
		}
		]);

//ROTA de Exclusão de um de Usuário
$obRouter->get('/admin/procedimentos/{id}/delete',[
		'middlewares' => [
				'require-admin-login'
		],
		
		
		function ($request,$id){
			return new Response(200, Admin\Procedimento::getDeleteProcedimento($request,$id));
		}
		]);
//ROTA de Exclusão de um de Usuário (POST)
$obRouter->post('/admin/procedimentos/{id}/delete',[
		'middlewares' => [
				'require-admin-login'
		],
		
		
		function ($request,$id){
			return new Response(200, Admin\Procedimento::setDeleteProcedimento($request,$id));
		}
		]);

