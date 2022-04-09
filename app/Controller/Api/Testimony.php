<?php

namespace App\Controller\Api;

use \App\Model\Entity\Testimony as EntityTestimony;
use \WilliamCosta\DatabaseManager\Pagination;


class Testimony extends Api{
	
	
	//Método responsavel por obter a renderização dos itens de depoimentos para a página
	private static function getTestimonyItems($request, &$obPagination){
		//Depoimentos
		$itens = [];
		
		//Quantidade total de registros
		$quantidadetotal =  EntityTestimony::getTestimonies(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;
		
		//Página atual
		$queryParams = $request->getQueryParams();
		$paginaAtual = $queryParams['page'] ?? 1;
		
		//Instancia de paginacao
		$obPagination = new Pagination($quantidadetotal,$paginaAtual,5);
		
		//Resultados da Página
		$results = EntityTestimony::getTestimonies(null, 'id DESC',$obPagination->getLimit());
		
		//Renderiza o item
		while ($obTestimony = $results->fetchObject(EntityTestimony::class)) {
			
			//View de depoimentos
			$itens[]= [
					'id' => (int)$obTestimony->id,
					'nome' => $obTestimony->nome,
					'mensagem' => $obTestimony->mensagem,
					'data' => $obTestimony->data
					
			];
		}
		
		
		//Retorna os depoimentos
		return $itens;
	}
	
	//Método responsavel por retonar os depoimentos cadastrados
	public static function getTestimonies($request){
		return [
				'depoimentos' => self::getTestimonyItems($request, $obPagination),
				'paginacao' => parent::getPagination($request,$obPagination)
		];
		
	}
	
	//Método responsavel por retornar os detalhes de um depoimento
	public static function getTestimony($request,$id){
		
	//Valida o id do depoimento
		if(!is_numeric($id)){
			throw new \Exception("O id '".$id."' não é válido.", 400);
		}
		
	//Busca depoimento
	$obTestimony = EntityTestimony::getTestimonyById($id);
	
	//Valida se o depoimento existe
	if(!$obTestimony instanceof EntityTestimony){
		throw new \Exception("O depoimento ".$id." não foi encontrado.", 404);
	}
	
	//Retorna os detalhes do depoimento
	return [
			'id' => (int)$obTestimony->id,
			'nome' => $obTestimony->nome,
			'mensagem' => $obTestimony->mensagem,
			'data' => $obTestimony->data
			
	];
		
	}
	
	//Método responsável por cadastrar um novo depoimento
	public static function setNewTestimony($request){
		
		return [
			'sucesso' => true	
		];
		
	}
	
}













