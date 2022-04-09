<?php

use \App\Http\Response;
use \App\Controller\Admin;


//ROTA de Listage de Cid10
$obRouter->get('/admin/cid10',[
		'middlewares' => [
				'require-admin-login'
		],
		
		
		function ($request){
			return new Response(200, Admin\Cid10::getCid10($request));
		}
		]);


//ROTA de Cadastro de um Novo de Usuário
$obRouter->get('/admin/cid10/new',[
		'middlewares' => [
				'require-admin-login'
		],
		
		
		function ($request){
			return new Response(200, Admin\Cid10::getNewCid10($request));
		}
		]);

//ROTA de Cadastro de um Novo de Usuário (POST)
$obRouter->post('/admin/cid10/new',[
		'middlewares' => [
				'require-admin-login'
		],
		
		
		function ($request){
			return new Response(200, Admin\Cid10::setNewCid10($request));
		}
		]);

//ROTA de Edição de um de Cid10
$obRouter->get('/admin/cid10/{id}/edit',[
		'middlewares' => [
				'require-admin-login'
		],
		
		
		function ($request,$id){
			return new Response(200, Admin\Cid10::getEditCid10($request,$id));
		}
		]);

//ROTA de Edição de um de Cid10 (POST)
$obRouter->post('/admin/cid10/{id}/edit',[
		'middlewares' => [
				'require-admin-login'
		],
		
		
		function ($request,$id){
			return new Response(200, Admin\Cid10::setEditCid10($request,$id));
		}
		]);

//ROTA de Exclusão de um de Usuário
$obRouter->get('/admin/cid10/{id}/delete',[
		'middlewares' => [
				'require-admin-login'
		],
		
		
		function ($request,$id){
			return new Response(200, Admin\Cid10::getDeleteCid10($request,$id));
		}
		]);
//ROTA de Exclusão de um de Usuário (POST)
$obRouter->post('/admin/cid10/{id}/delete',[
		'middlewares' => [
				'require-admin-login'
		],
		
		
		function ($request,$id){
			return new Response(200, Admin\Cid10::setDeleteCid10($request,$id));
		}
		]);

