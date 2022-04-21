<?php

use \App\Http\Response;
use \App\Controller\Admin;
use \App\File;

//ROTA DE LISTAGEM DE PACIENTE
$obRouter->get('/admin/pacientes',[
		'middlewares' => [
				'require-admin-login'
		],
		
		
		function ($request){
			return new Response(200, Admin\Paciente::getPacientes($request));  
		}
		]);

//ROTA DE UPLOAD DE IMAGEM DO ADMIN
$obRouter->post('/admin/pacientes',[
		'middlewares' => [
				'require-admin-login'
		],
		
		
		function ($request){
			return new Response(200, File\Upload::setUploadImages($request));
		}
		]);


//ROTA DE CADASTRO DE UM NOVO PACIENTE
$obRouter->get('/admin/pacientes/new',[
    'middlewares' => [
        'require-admin-login'
    ],
    
    
    function ($request){
        return new Response(200, Admin\Paciente::getNewPaciente($request));
    }
    ]);

//ROTA DE CADASTRO DE UM NOVO PACIENTE (POST)
$obRouter->post('/admin/pacientes/new',[
    'middlewares' => [
        'require-admin-login'
    ],
    
    
    function ($request){
        return new Response(200, Admin\Paciente::setNewPaciente($request));
    }
    ]);


//ROTA GET DE LME DE PACIENTE
$obRouter->get('/admin/pacientes/{codPronto}/lme',[
    'middlewares' => [
        'require-admin-login'
    ],
    
    
    function ($request,$codPronto){
        return new Response(200, Admin\Lme::getLme($request,$codPronto));
        
    }
    ]);

//ROTA POST DE LME DE PACIENTE
$obRouter->post('/admin/pacientes/{codPronto}/lme',[
    'middlewares' => [
        'require-admin-login'
    ],
    
    
    function ($request){
        return new Response(200, Admin\Lme::getLmePrintPdf($request));
        
    }
    ]);


//ROTA de Edição de um de Paciente
$obRouter->get('/admin/pacientes/{codPronto}/edit',[
    'middlewares' => [
        'require-admin-login'
    ],
    
    
    function ($request,$codPronto){
        return new Response(200, Admin\Paciente::getEditPaciente($request,$codPronto));
    }
    ]);

//ROTA de Edição de um de Paciente (POST)
$obRouter->post('/admin/pacientes/{codPronto}/edit',[
    'middlewares' => [
        'require-admin-login'
    ],
    
    
    function ($request,$codPronto){
        return new Response(200, Admin\Paciente::setEditPaciente($request,$codPronto));
    }
    ]);


//ROTA de Exclusão de um de Paciente
$obRouter->get('/admin/pacientes/{codPronto}/delete',[
    'middlewares' => [
        
        'require-admin-login'
        
        
    ],
    
    
    function ($request,$codPronto){
        //apenas administrador pode excluir paciente
        if($_SESSION['admin']['usuario']['tipo'] == 'Admin')
            return new Response(200, Admin\Paciente::getDeletePaciente($request,$codPronto));
        else 
            return new Response(200, 'Você não tem permissão. Contate o Administrador! <a href="javascript:history.back()">Voltar</a>');
    }
    ]);


//ROTA de Exclusão de um de Paciente (POST)
$obRouter->post('/admin/pacientes/{codPronto}/delete',[
    'middlewares' => [
        'require-admin-login'
    ],
    
    
    function ($request,$codPronto){
        return new Response(200, Admin\Paciente::setDeletePaciente($request,$codPronto));
    }
    ]);


//ROTA  DE CAPA DE PRONTUÁRIO
$obRouter->get('/admin/pacientes/{codPronto}/capa',[
    'middlewares' => [
        'require-admin-login'
    ],
    
    
    function ($request,$codPronto){
        return new Response(200, Admin\Paciente::getCapaProntuario($request,$codPronto));
    }
    ]);

//ROTA DE IMPRESSÃO DE CAPA DE PRONTUÁRIO
$obRouter->get('/admin/pacientes/{codPronto}/capa/imprimir',[
    'middlewares' => [
        'require-admin-login'
    ],
    
    
    function ($request,$codPronto){
        return new Response(200, Admin\Paciente::getImprimirCapaProntuario($request,$codPronto));
    }
    ]);






