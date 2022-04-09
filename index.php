<?php

require __DIR__.'/includes/app.php';

use \App\Http\Router;

//Inicia o router
$obRouter = new Router(URL);

//Inclui as rotas de páginas
include __DIR__.'/routes/pages.php';

//Inclui as rotas do Painel
include __DIR__.'/routes/admin.php';


//Inclui as rotas de Usuários
include __DIR__.'/routes/visitor.php';


//Inclui as da API
include __DIR__.'/routes/api.php';

//Inclui as da API
include __DIR__.'/routes/login.php';


//Imprime o response da Rota
$obRouter->run()
				->sendResponse();