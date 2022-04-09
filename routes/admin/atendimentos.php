<?php

use \App\Http\Response;
use \App\Controller\Admin;

//ROTA de Listagem de atendimentos
$obRouter->get('/admin/atendimentos',[
		'middlewares' => [
				'require-admin-login'
		],
		
		
		function ($request){
			return new Response(200, Admin\Atendimento::getAtendimentos($request));
			
		}
		]);

//ROTA de Novo Atendimento do paciente selecionado
$obRouter->get('/admin/atendimentos/{id}/atendimento',[
		'middlewares' => [
				'require-admin-login'
		],
		
		
		function ($request,$id){
			return new Response(200, Admin\Atendimento::getAtendimentos($request,$id));
		}
		]);


//ROTA de Novo Atendimento do paciente selecionado
$obRouter->post('/admin/atendimentos/{id}/atendimento',[
		'middlewares' => [
				'require-admin-login'
		],
		
		
		function ($request,$id){
			return new Response(200, Admin\Atendimento::getInsertAtendimento($request,$id));
		}
		]);


//ROTA de Edição do Atendimento selecionado
$obRouter->get('/admin/atendimentos/{codPronto}/atendimento/{id}/edit',[
		'middlewares' => [
				'require-admin-login'
		],
		
		
		function ($request,$codPronto,$id){
			return new Response(200, Admin\Atendimento::getEditAtendimento($request,$codPronto, $id));
		}
		]);

//ROTA de POST de Edição do Atendimento selecionado
$obRouter->post('/admin/atendimentos/{codPronto}/atendimento/{id}/edit',[
		'middlewares' => [
				'require-admin-login'
		],
		
		
		function ($request,$codPronto,$id){
			return new Response(200, Admin\Atendimento::setEditAtendimento($request,$codPronto, $id));
		}
		]);

//ROTA de Exclusão de um de Atendimento
$obRouter->get('/admin/atendimentos/{id}/delete',[
		'middlewares' => [
				'require-admin-login'
		],
		
		
		function ($request,$id){
			return new Response(200, Admin\Atendimento::getDeleteAtendimento($request,$id));
		}
		]);


//ROTA de Exclusão de um de Atendimento (POST)
$obRouter->post('/admin/atendimentos/{id}/delete',[
		'middlewares' => [
				'require-admin-login'
		],
		
		
		function ($request,$id){
			return new Response(200, Admin\Atendimento::setDeleteAtendimento($request,$id));
		}
		]);

//ROTA de Relatórios de atendimentos
$obRouter->get('/admin/atendimentos/relatorios',[
		'middlewares' => [
				'require-admin-login'
		],
		
		
		function ($request){
			return new Response(200, Admin\Atendimento::getProducao($request));
		}
		]);

//ROTA de Relatórios de atendimentos
$obRouter->post('/admin/atendimentos/relatorios',[

		
		function ($request){
			return new Response(200, Admin\Atendimento::getProducao($request));
		}
		]);

//ROTA de Relatórios de atendimentos
$obRouter->get('/admin/atendimentos/reativarPaciente',[
		'middlewares' => [
				'require-admin-login'
		],
		
		function ($request){
			return new Response(200, Admin\Atendimento::getReativaPaciente($request));
		}
		]);
//ROTA de Relatórios de atendimentos
$obRouter->post('/admin/atendimentos/reativarPaciente',[
		'middlewares' => [
				'require-admin-login'
		],
		
		function ($request){
			return new Response(200, Admin\Atendimento::setReativaPaciente($request));
		}
		]);

//ROTA de atendimentos Avulso
$obRouter->get('/admin/atendimentos/avulso',[
		'middlewares' => [
				'require-admin-login'
		],
		
		
		function ($request,$id){
			return new Response(200, Admin\Atendimento::getAtendimentosAvulso($request,$id));
			
		}
		]);


//ROTA de POST atendimentos Avulso
$obRouter->post('/admin/atendimentos/avulso',[
		'middlewares' => [
				'require-admin-login'
		],
		
		
		function ($request){
			return new Response(200, Admin\Atendimento::getInsertAtendimentoAvulso($request));
			
		}
		]);


//ROTA de Edição do Atendimento Avulso selecionado
$obRouter->get('/admin/atendimentos/avulso/{id}/edit',[
		'middlewares' => [
				'require-admin-login'
		],
		
		
		function ($request,$id){
			return new Response(200, Admin\Atendimento::getEditAtendimentoAvulso($request,$id));
		}
		]);

//ROTA de POST de Edição do Atendimento Avulso selecionado
$obRouter->post('/admin/atendimentos/avulso/{id}/edit',[
		'middlewares' => [
				'require-admin-login'
		],
		
		
		function ($request,$id){
			return new Response(200, Admin\Atendimento::setEditAtendimentoAvulso($request,$id));
		}
		]);

//ROTA de Exclusão de um de Atendimento
$obRouter->get('/admin/atendimentos/{id}/delete/avulso',[
		'middlewares' => [
				'require-admin-login'
		],
		
		
		function ($request,$id){
			return new Response(200, Admin\Atendimento::getDeleteAtendimentoAvulso($request,$id));
		}
		]);
//ROTA de Exclusão de um de Atendimento
$obRouter->post('/admin/atendimentos/{id}/delete/avulso',[
		'middlewares' => [
				'require-admin-login'
		],
		
		
		function ($request,$id){
			return new Response(200, Admin\Atendimento::setDeleteAtendimentoAvulso($request,$id));
		}
		]);

