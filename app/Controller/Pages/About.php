<?php

namespace App\Controller\Pages;

use \App\Utils\View;
use \App\Model\Entity\Organization;

class About extends Page{
	
	//retorna o conteudo (view) da nossa página de sobre
	public static function getAbout(){
		
		//Organização
		$obOrganization = new Organization();
		
		//View da Sobre
		$content = View::render('pages/about',[
				'name' => $obOrganization->name,
				'description' => $obOrganization->description,
				'site' => $obOrganization->site
				
				
		]);
		
		//retorna a view da página
		return parent::getPage('SOBRE > WDEV ', $content);
		
	}
	
}