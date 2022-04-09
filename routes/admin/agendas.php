<?php

use \App\Http\Response;
use \App\Controller\Admin;

//ROTA de Listagem de Agendas
$obRouter->get('/admin/agendas',[
				'middlewares' => [
						'require-admin-login'
				],
				function ($request){
					return new Response(200, Admin\Agenda::getAgendas($request));
					
				}
		]);

//ROTA de POST de Agendas
$obRouter->post('/admin/agendas',[
		'middlewares' => [
				'require-admin-login'
		],
		function ($request){
			return new Response(200, Admin\Agenda::setAgendas($request));
			
		}
		]);

//ROTA de Get de Edição de Agendas
$obRouter->get('/admin/agendas/{id}/edit',[
		'middlewares' => [
				'require-admin-login'
		],
		function ($request,$id){
			return new Response(200, Admin\Agenda::getEditAgenda($request,$id));
			
		}
		]);

//ROTA de Post de Edição de Agendas
$obRouter->post('/admin/agendas/{id}/edit',[
		'middlewares' => [
				'require-admin-login'
		],
		function ($request, $id){
			return new Response(200, Admin\Agenda::setEditAgenda($request, $id));
			
		}
		]);

//ROTA de Get de Adição de pacientes na agenda
$obRouter->get('/admin/agendas/{id}/view',[
		'middlewares' => [
				'require-admin-login'
		],
		function ($request, $id){
			return new Response(200, Admin\Agenda::getAgendasView($request, $id));
			
		}
		]);




//ROTA de post para salvar  alteração no procedimento e na presença do paciente
$obRouter->post('/admin/agendas/{id}/view',[
		'middlewares' => [
				'require-admin-login'
		],
		function ($request){
			return new Response(200, Admin\Agenda::setAgendaAlteraProcedimento($request));
			
		}
		]);



//ROTA de Get para listar pacientes para add na agenda
$obRouter->get('/admin/agendas/{idAgenda}/addPaciente',[
		'middlewares' => [
				'require-admin-login'
		],
		function ($request,$idAgenda){
			return new Response(200, Admin\Agenda::getAgendaAddPaciente($request,$idAgenda));
			
		}
		]);

//ROTA de get de adicionar paciente na Agenda
$obRouter->get('/admin/agendas/{idPac}/add/{idAgenda}',[
		'middlewares' => [
				'require-admin-login'
		],
		function ($request,$idPac, $idAgenda){
			return new Response(200, Admin\Agenda::setAgendaAddPaciente($request, $idPac, $idAgenda));
			
		}
		]);

//ROTA de get de remover paciente na Agenda
$obRouter->get('/admin/agendas/{idPac}/remove/{idAgenda}',[
		'middlewares' => [
				'require-admin-login'
		],
		function ($request,$idPac, $idAgenda){
			return new Response(200, Admin\Agenda::setAgendaRemovePaciente($request, $idPac, $idAgenda));
			
		}
		]);

//ROTA de get de remover paciente na Agenda
$obRouter->get('/admin/agendas/{idPac}/removelist/{idAgenda}',[
		'middlewares' => [
				'require-admin-login'
		],
		function ($request,$idPac, $idAgenda){
			return new Response(200, Admin\Agenda::setAgendaRemovePacienteList($request, $idPac, $idAgenda));
			
		}
		]);


//ROTA de get de nova Agenda
$obRouter->get('/admin/agendas/new',[
		'middlewares' => [
				'require-admin-login'
		],
		function ($request){
			return new Response(200, Admin\Agenda::getAgendasNew($request));
			
		}
		]);

//ROTA de POST de nova Agenda
$obRouter->post('/admin/agendas/new',[
		'middlewares' => [
				'require-admin-login'
		],
		function ($request){
			return new Response(200, Admin\Agenda::setAgendasNew($request));
			
		}
		]);

//ROTA de get para deletar Agenda
$obRouter->get('/admin/agendas/{id}/delete',[
		'middlewares' => [
				'require-admin-login'
		],
		function ($request, $id){
			return new Response(200, Admin\Agenda::getAgendasDelete($request, $id));
			
		}
		]);
//ROTA de post para deletar Agenda
$obRouter->post('/admin/agendas/{id}/delete',[
		'middlewares' => [
				'require-admin-login'
		],
		function ($request, $id){
			return new Response(200, Admin\Agenda::setAgendasDelete($request, $id));
			
		}
		]);


