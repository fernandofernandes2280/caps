<?php

use \App\Http\Response;
use \App\Controller\Admin;


//ROTA de Listage de Depoimentos
$obRouter->get('/admin/testimonies',[
		'middlewares' => [
				'require-admin-login'
		],
		
		
		function ($request){
			return new Response(200, Admin\Testimony::getTestimonies($request));
		}
		]);


//ROTA de Cadastro de um Novo de Depoimentos
$obRouter->get('/admin/testimonies/new',[
		'middlewares' => [
				'require-admin-login'
		],
		
		
		function ($request){
			return new Response(200, Admin\Testimony::getNewTestimony($request));
		}
		]);

//ROTA de Cadastro de um Novo de Depoimentos (POST)
$obRouter->post('/admin/testimonies/new',[
		'middlewares' => [
				'require-admin-login'
		],
		
		
		function ($request){
			return new Response(200, Admin\Testimony::setNewTestimony($request));
		}
		]);

//ROTA de Edição de um de Depoimentos
$obRouter->get('/admin/testimonies/{id}/edit',[
		'middlewares' => [
				'require-admin-login'
		],
		
		
		function ($request,$id){
			return new Response(200, Admin\Testimony::getEditTestimony($request,$id));
		}
		]);

//ROTA de Edição de um de Depoimentos (POST)
$obRouter->post('/admin/testimonies/{id}/edit',[
		'middlewares' => [
				'require-admin-login'
		],
		
		
		function ($request,$id){
			return new Response(200, Admin\Testimony::setEditTestimony($request,$id));
		}
		]);

//ROTA de Exclusão de um de Depoimentos
$obRouter->get('/admin/testimonies/{id}/delete',[
		'middlewares' => [
				'require-admin-login'
		],
		
		
		function ($request,$id){
			return new Response(200, Admin\Testimony::getDeleteTestimony($request,$id));
		}
		]);
//ROTA de Exclusão de um de Depoimentos (POST)
$obRouter->post('/admin/testimonies/{id}/delete',[
		'middlewares' => [
				'require-admin-login'
		],
		
		
		function ($request,$id){
			return new Response(200, Admin\Testimony::setDeleteTestimony($request,$id));
		}
		]);

