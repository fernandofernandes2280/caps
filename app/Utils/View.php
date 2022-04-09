<?php

namespace App\Utils;

class View{
	
	//Variáveis padrões da view
	private static $vars = [];
	
	//Método responsavel por definir os dados iniciais da classe
	public static function init($vars = []){
		
		self::$vars = $vars;
		
	}
	
	//metodo responsavel por retornar o conteudo de uma view
	private static function getContentView($view){
		
		$file = __DIR__.'/../../resources/view/'.$view.'.html';  
		return file_exists($file) ? file_get_contents($file) : '';
	}
	
	//metodo responsavel por retornar o conteudo renderizado de uma view
	public static function render($view, $vars = []){
		//conteudo da view
		$contentView = self::getContentView($view);
		
		//Merge de variáveis da view
		$vars = array_merge(self::$vars, $vars);
		
		//chaves dos arrays
		$keys = array_keys($vars);
		$keys = array_map(function($item){
			return '{{'.$item.'}}';
		}, $keys);
		
		return str_replace($keys, array_values($vars), $contentView);
		
	}
	
}