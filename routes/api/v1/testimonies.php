<?php

use \App\Http\Response;
use \App\Controller\Api;


//Rota de listagem de depoimentos
$obRouter->get('/api/v1/testimonies',[
		'middlewares' => [
				'api'
		],
		function ($request){
			return new Response(200, Api\Testimony::getTestimonies($request),'application/json');
		}
]);


//Rota de consulta individual de depoimentos
$obRouter->get('/api/v1/testimonies/{id}',[
		'middlewares' => [
				'api'
		],
		function ($request,$id){
			return new Response(200, Api\Testimony::getTestimony($request,$id),'application/json');
		}
		]);

//Rota de cadastro de depoimentos
$obRouter->post('/api/v1/testimonies',[
		'middlewares' => [
				'api'
		],
		function ($request,$id){
			return new Response(201, Api\Testimony::setNewTestimony($request,$id),'application/json');
		}
		]);