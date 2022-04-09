<?php

namespace App\Controller\Pages;

use \App\Utils\View;
use \App\Model\Entity\Testimony as EntityTestimony;
use \WilliamCosta\DatabaseManager\Pagination;

class Testimony extends Page{
	
	
	//Método responsavel por obter a renderização dos itens de depoimentos para a página
	private static function getTestimonyItems($request, &$obPagination){
		//Depoimentos
		$itens = '';
		
		//Quantidade total de registros
		$quantidadetotal =  EntityTestimony::getTestimonies(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;
		
		//Página atual
		$queryParams = $request->getQueryParams();
		$paginaAtual = $queryParams['page'] ?? 1;
		
		//Instancia de paginacao
		$obPagination = new Pagination($quantidadetotal,$paginaAtual,3);
		
		
		//Resultados da Página
		$results = EntityTestimony::getTestimonies(null, 'id DESC',$obPagination->getLimit());
		
		//Renderiza o item
		while ($obTestimony = $results->fetchObject(EntityTestimony::class)) {
			//View de depoimentos
			$itens.= View::render('pages/testimony/item',[
					'nome' => $obTestimony->nome,
					'mensagem' => $obTestimony->mensagem,
					'data' => date('d/m/Y H:i:s', strtotime($obTestimony->data))
					
			]);
		}
		
		
		//Retorna os depoimentos
		return $itens;
		
	}
	
	
	//retorna o conteudo (view) da nossa home
	public static function getTestimonies($request){
		
		
		//View de depoimentos
		$content = View::render('pages/testimonies',[
				'itens' => self::getTestimonyItems($request,$obPagination),
				'pagination' => parent::getPagination($request,$obPagination)
			
		]);
		
		return parent::getPage('DEPOIMENTOS > WDEV', $content);
		
	}
	
	//Método responsavel por cadastrar um depoimento
	public static function insertTestimony($request){
		//Dados do Post
		$postVars = $request->getPostVars();
		
		//Nova instancia de depoimento
		$obTestimony = new EntityTestimony();
		$obTestimony->nome = $postVars['nome'];
		$obTestimony->mensagem = $postVars['mensagem'];
		$obTestimony->cadastrar();
		
		//Retorna a página de listagem de depoimentos
		return self::getTestimonies($request);
	}
	
}