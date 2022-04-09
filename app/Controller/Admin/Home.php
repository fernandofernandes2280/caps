<?php

namespace App\Controller\Admin;

use \App\Utils\View;


class Home extends Page{
	
	//Método responsavel por renderizar a view de Home do Painel
	public static function getHome($request){
		
		//Conteúdo da Home
		$content = View::render('admin/modules/home/index',[
				
		]);
		
		//Retorna a página completa
		return parent::getPanel('Home > SisCaps', $content,'home');
		
	}
	
	
	
}