<?php

namespace App\Controller\Api;

class Api{
	
	//Método responsavel por retonar os detalhes da API
	public static function getDetails($request){
		return [
				'nome' => 'API -WDEV',
				'versao' => 'V1.0.0',
				'autor' => 'Willim Costa'
		];
		
	}
	
	//Mètodo reposonsavel por retornar os detalhes da paginação
	protected static function getPagination($request,$obPagination){
		
		//Query Params
		$queryParams = $request->getQueryParams();
		
		
		
		//Página
		$pages = $obPagination->getPages();
		
		//Retorno
		return [
				'paginaAtual' => isset($queryParams['page']) ? (int)$queryParams['page'] : 1,
				'quantidadePaginas' => !empty($pages) ? count($pages) : 1
		];
	}
	
	
}