<?php

namespace App\Controller\Pages;

use \App\Utils\View;
use App\Model\Entity\Generica;

class Page extends Generica{
	
	
	//metodo responsavel por renderizar o topo da pagina
	private static function getHeader(){
		
		
		return View::render('pages/header',[
				'menuActive' => 'active'
				
		]);
		
	}
	
	//metodo responsavel por renderizar o rodapé da pagina
	private static function getFooter(){
		
		return View::render('pages/footer');
		
	}
	
	//Método responsavel por retornar com um link da paginação
	private static function getPaginationLink($queryParams,$page,$url,$label=null){
		//Altera a página
		$queryParams['page'] = $page['page'];
		
		//Link
		$link = $url.'?'.http_build_query($queryParams);
		
		//view
		return View::render('pages/pagination/link',[
				'page' => $label ?? $page['page'],
				'link' => $link,
				'active' => $page['current'] ? 'active' : ''
		]);
	}
	
	//Método responsvel por renderizar o layout de paginação
	public static function getPagination($request,$obPagination){
		//Páginas
		$pages = $obPagination->getPages();
		
		//Verifica a quantidade de páginas
		if(count($pages) <=1) return '';
		
		//Links
		$links = '';
		
		//url atual (sem gets)
		$url = $request->getRouter()->getCurrentUrl();
		
		//GET
		$queryParams = $request->getQueryParams();
	
		//Página ataual
		$currentPage = $queryParams['page'] ?? 1;
		
		//Limite de páginas
		$limit = getenv('PAGINATION_LIMIT');
		
		//Meio da paginação
		$middle = ceil($limit/2);
		
		//Início da paginação
		$start = $middle > $currentPage ? 0 : $currentPage - $middle;
		
		//Ajusta o final da paginação
		$limit = $limit + $start;
		
		//Ajusta o início da paginação
		if($limit > count($pages)){
			$diff = $limit - count($pages);
			$start = $start - $diff;
		}
		
	//Link Inicial
		if($start > 0){
			$links .= self::getPaginationLink($queryParams,reset($pages),$url,'<<');
		}
		
		
		//Renderiza os Links
		foreach ($pages as $page){
			//Verifica o start da paginação
			if($page['page'] <= $start) continue;
			
			//Verifica o limite de paginação
			if($page['page'] > $limit){
				$links .= self::getPaginationLink($queryParams,end($pages),$url,'>>');
				
				break;
			}
			
			$links .= self::getPaginationLink($queryParams,$page,$url);			

		}
		
	
	
		
		//Renderiza box de paginação
		return View::render('pages/pagination/box',[
				'links' => $links
				
		]);
		
		
		
		
	}
	
	
	
	//retorna o conteudo (view) da nossa página genérica
	public static function getPage($title, $content){
		return View::render('pages/page',[
				'title' => $title,
				'header' => self::getHeader(),
				'content' => $content,
				'footer' => self::getFooter()
				
		]);
		
	}
	
}