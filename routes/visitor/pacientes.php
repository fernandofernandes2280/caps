<?php

use \App\Http\Response;
use \App\Controller\Visitor;
use \App\Controller\Admin;
use \App\File;
//ROTA DE LISTAGEM DE PACIENTE
$obRouter->get('/visitor/pacientes',[
		'middlewares' => [
				'require-visitor-login'
		],
		
		
		function ($request,$codPronto){
			return new Response(200, Visitor\Paciente::getPacientes($request,$codPronto));
		}
		]);



//ROTA DE UPLOAD DE IMAGEM DO VISITANTE
$obRouter->post('/visitor/pacientes',[
		'middlewares' => [
				'require-visitor-login'
		],
		
		
		function ($request){
			return new Response(200, File\Upload::setUploadImages($request));
		}
		]);



//ROTA de Edição do Atendimento selecionado
$obRouter->get('/visitor/pacientes/{codPronto}/view',[
		'middlewares' => [
				'require-visitor-login'
		],
		
		
		function ($request,$codPronto){
			return new Response(200, Visitor\Paciente::getViewPaciente($request, $codPronto));
		}
		]);


//ROTA de Edição do Atendimento selecionado
$obRouter->post('/visitor/pacientes/{codPronto}/view',[
		'middlewares' => [
				'require-visitor-login'
		],
		
		
		function ($request,$codPronto){
			return new Response(200, Visitor\Paciente::getViewPaciente($request, $codPronto));
		}
		]);



//ROTA DE CADASTRO DE UM NOVO PACIENTE
$obRouter->get('/visitor/pacientes/new',[
		'middlewares' => [
				'require-visitor-login'
		],
		
		
		function ($request){
			return new Response(200, Visitor\Paciente::getNewPaciente($request));
		}
		]);

//ROTA DE CADASTRO DE UM NOVO PACIENTE (POST)
$obRouter->post('/visitor/pacientes/new',[
		'middlewares' => [
				'require-visitor-login'
		],
		
		
		function ($request){
			return new Response(200, Visitor\Paciente::setNewPaciente($request));
		}
		]);


//ROTA de Edição de um de Paciente
$obRouter->get('/visitor/pacientes/{id}/edit',[
		'middlewares' => [
				'require-visitor-login'
		],
		
		
		function ($request,$id){
			return new Response(200, Visitor\Paciente::getEditPaciente($request,$id));
		}
		]);

//ROTA de Edição de um de Paciente (POST)
$obRouter->post('/visitor/pacientes/{id}/edit',[
		'middlewares' => [
				'require-visitor-login'
		],
		
		
		function ($request,$id){
			return new Response(200, Visitor\Paciente::setEditPaciente($request,$id));
		}
		]);


//ROTA de Exclusão de um de Paciente
$obRouter->get('/visitor/pacientes/{id}/delete',[
		'middlewares' => [
				'require-visitor-login'
		],
		
		
		function ($request,$id){
			return new Response(200, Visitor\Paciente::getDeletePaciente($request,$id));
		}
		]);


//ROTA de Exclusão de um de Paciente (POST)
$obRouter->post('/visitor/pacientes/{id}/delete',[
		'middlewares' => [
				'require-visitor-login'
		],
		
		
		function ($request,$id){
			return new Response(200, Visitor\Paciente::setDeletePaciente($request,$id));
		}
		]);

//ROTA DE IMPRESSÃO DE CAPA DE PRONTUÁRIO
$obRouter->get('/visitor/pacientes/{codPronto}/capa/imprimir',[
		'middlewares' => [
				'require-visitor-login'
		],
		
		
		function ($request,$codPronto){
			return new Response(200, Admin\Paciente::getImprimirCapaProntuario($request,$codPronto));
		}
		]);

//ROTA GET DE LME DE PACIENTE
$obRouter->get('/visitor/pacientes/{codPronto}/lme',[
		'middlewares' => [
				'require-visitor-login'
		],
		
		
		function ($request,$codPronto){
			return new Response(200, Visitor\Lme::getLme($request,$codPronto));
			
		}
		]);

//ROTA POST DE LME DE PACIENTE
$obRouter->post('/visitor/pacientes/{codPronto}/lme',[
		'middlewares' => [
				'require-visitor-login'
		],
		
		
		function ($request){
			return new Response(200, Visitor\Lme::getLmePrintPdf($request));
			
		}
		]);








