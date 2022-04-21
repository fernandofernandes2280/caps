<?php

use \App\Http\Response;
use \App\Controller\Admin;
use \App\File;

//ROTA DE LISTAGEM DE PROFISSIONAL
$obRouter->get('/admin/profissionais',[
		'middlewares' => [
				'require-admin-login'
		],
		
		
		function ($request){
		    return new Response(200, Admin\Profissional::getProfissionais($request));
		    
		}
		]);


//ROTA GET DE EDIT DE PROFISSIONAL
$obRouter->get('/admin/profissionais/{id}/edit',[
    'middlewares' => [
        'require-admin-login'
    ],
    
    
    function ($request,$id){
        return new Response(200, Admin\Profissional::getEditProfissional($request,$id));
        
    }
    ]);

//ROTA DE POST DE EDIT PROFISSIONAL
$obRouter->post('/admin/profissionais/{id}/edit',[
    'middlewares' => [
        'require-admin-login'
    ],
    
    
    function ($request,$id){
        return new Response(200, Admin\Profissional::setEditProfissional($request,$id));
        
    }
    ]);

//ROTA GET DE ACESSO AO SISTEMA PELO PROFISSIONAL
$obRouter->get('/admin/profissionais/{id}/acesso',[
    'middlewares' => [
        'require-admin-login'
    ],
    
    
    function ($request,$id){
        return new Response(200, Admin\User::getNewUser($request,$id));
        
    }
    ]);

//ROTA POST DE ACESSO AO SISTEMA PELO PROFISSIONAL
$obRouter->post('/admin/profissionais/{id}/acesso',[
    'middlewares' => [
        'require-admin-login'
    ],
    
    
    function ($request,$id){
        return new Response(200, Admin\User::setNewUser($request,$id));
        
    }
    ]);

//ROTA GET DE NOVO PROFISSIONAL
$obRouter->get('/admin/profissionais/new',[
    'middlewares' => [
        'require-admin-login'
    ],
    
    
    function ($request){
        return new Response(200, Admin\Profissional::getNewProfissional($request));
        
    }
    ]);

//ROTA POST DE NOVO PROFISSIONAL
$obRouter->post('/admin/profissionais/new',[
    'middlewares' => [
        'require-admin-login'
    ],
    
    
    function ($request){
        return new Response(200, Admin\Profissional::setNewProfissional($request));
        
    }
    ]);


//ROTA GET DE EXCLUIR PROFISSIONAL
$obRouter->get('/admin/profissionais/{id}/delete',[
    'middlewares' => [
        'require-admin-login'
    ],
    
    
    function ($request, $id){
        //apenas administrador pode excluir paciente
        if($_SESSION['admin']['usuario']['tipo'] == 'Admin')
                return new Response(200, Admin\Profissional::getDeleteProfissional($request, $id));
            else
                return new Response(200, 'Você não tem permissão. Contate o Administrador! <a href="javascript:history.back()">Voltar</a>');
    }
    ]);

//ROTA POST DE EXCLUIR PROFISSIONAL
$obRouter->post('/admin/profissionais/{id}/delete',[
    'middlewares' => [
        'require-admin-login'
    ],
    
    
    function ($request, $id){
        return new Response(200, Admin\Profissional::setDeleteProfissional($request, $id));
        
    }
    ]);
