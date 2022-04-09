<?php

use \App\Http\Response;
use \App\Controller\Visitor;

//ROTA de Listagem de atendimentos
$obRouter->get('/visitor/atendimentos',[
		'middlewares' => [
				'require-visitor-login'
		],
		
		
		function ($request){
			return new Response(200, Visitor\Atendimento::getAtendimentos($request));
			
		}
		]);

//ROTA de Novo Atendimento do paciente selecionado
$obRouter->get('/visitor/atendimentos/{id}/view',[
		'middlewares' => [
				'require-visitor-login'
		],
		
		
		function ($request,$id){
			return new Response(200, Visitor\Atendimento::getAtendimentos($request,$id));
		}
		]);


//ROTA de Novo Atendimento do paciente selecionado
$obRouter->post('/visitor/atendimentos/{id}/atendimento',[
		'middlewares' => [
				'require-visitor-login'
		],
		
		
		function ($request,$id){
			return new Response(200, Visitor\Atendimento::getInsertAtendimento($request,$id));
		}
		]);


//ROTA de Edição do Atendimento selecionado
$obRouter->get('/visitor/atendimentos/{codPronto}/atendimento/{id}/edit',[
		'middlewares' => [
				'require-visitor-login'
		],
		
		
		function ($request,$codPronto,$id){
			return new Response(200, Visitor\Atendimento::getEditAtendimento($request,$codPronto, $id));
		}
		]);

//ROTA de POST de Edição do Atendimento selecionado
$obRouter->post('/visitor/atendimentos/{codPronto}/atendimento/{id}/edit',[
		'middlewares' => [
				'require-visitor-login'
		],
		
		
		function ($request,$codPronto,$id){
			return new Response(200, Visitor\Atendimento::setEditAtendimento($request,$codPronto, $id));
		}
		]);

//ROTA de Exclusão de um de Atendimento
$obRouter->get('/visitor/atendimentos/{id}/delete',[
		'middlewares' => [
				'require-visitor-login'
		],
		
		
		function ($request,$id){
			return new Response(200, Visitor\Atendimento::getDeleteAtendimento($request,$id));
		}
		]);


//ROTA de Exclusão de um de Atendimento (POST)
$obRouter->post('/visitor/atendimentos/{id}/delete',[
		'middlewares' => [
				'require-visitor-login'
		],
		
		
		function ($request,$id){
			return new Response(200, Visitor\Atendimento::setDeleteAtendimento($request,$id));
		}
		]);

//ROTA de Relatórios de atendimentos
$obRouter->get('/visitor/atendimentos/relatorios',[
		'middlewares' => [
				'require-visitor-login'
		],
		
		
		function ($request){
			return new Response(200, Visitor\Atendimento::getProducao($request));
		}
		]);

//ROTA de Relatórios de atendimentos
$obRouter->post('/visitor/atendimentos/relatorios',[
		'middlewares' => [
				'require-visitor-login'
		],
		
		
		function ($request){
			return new Response(200, Visitor\Atendimento::getProducao($request));
		}
		]);




