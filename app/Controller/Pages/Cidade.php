<?php

namespace App\Controller\Pages;

use \App\Utils\View;
use \App\Model\Entity\Cidade as EntityCidade;
use \WilliamCosta\DatabaseManager\Pagination;
use App\Http\Request;

class Cidade extends Page{
	
	private static $qtdTotal ;
	
	
	//Método responsavel por obter a rendereizacao dos pacientes para a página
	private static function getCidadeItems($request, &$obPagination){
	$resultados = '';
	
	//Pagina Atual
	$queryParams = $request->getQueryParams();
	$paginaAtual = $queryParams['page'] ?? 1;
	
	 
	//Armazena valor busca pelo nome do paciente
	 $busca = $queryParams['buscaCidade'] ?? '';
	 
	 //Condições SQL
	 $condicoes = [
	 		
	 		strlen($busca) ? 'nome LIKE "%'.str_replace(' ', '%', $busca).'%"' : null
	 ];
	 
	 //Remove posições vazias
	 $condicoes = array_filter($condicoes);
	 
	 //cláusula where
	 $where = implode(' AND ', $condicoes);
	 
	 
	 //Quantidade total de registros
	// $quantidadeTotal = EntityPaciente::getPacientes($where, null,null,'COUNT(*) as qtd')->fetchObject()->qtd;
	
	 $quantidadeTotal = EntityCidade::getCidades($where, null,null,'COUNT(*) as qtd')->fetchObject()->qtd;
	 self::$qtdTotal = $quantidadeTotal;
	 //Instancia de paginação
	 $obPagination = new Pagination($quantidadeTotal,$paginaAtual,5);
	 #############################################
	 
	 
	//Obtem os pacientes
	 $results = EntityCidade::getCidades($where, 'id ASC', $obPagination->getLimit());
	 
	 //var_dump($where);exit;
	 
	//Renderiza
	 while ($ob = $results -> fetchObject(EntityCidade::class)) {
		//var_dump($obPaciente);exit;
		//View de pacientes
		$resultados .= View::render('pages/paciente/itemSelect',[

				'nome' => $ob->nome
				
		]);
	}
	//Retorna os pacientes
	
	return $resultados;
}
	

	
	//retorna o conteudo (view) de Bairros
	public static function getCidades($request){
		$resultados = '';
		$results =  EntityCidade::getCidades(null,'nome asc',null);
		//Renderiza
		while ($ob = $results -> fetchObject(EntityCidade::class)) {

			//View de Bairros
			$resultados .= View::render('pages/paciente/itemSelect',[
					
					'nome' => $ob->nome
					
			]);
		}
		//Retorna os bairros
		return $resultados;
		
	}
	
	
	//Método responsavel por cadastrar um paciente
	public static function insertCidade($request){

		//dados do post
		$postVars = $request->getPostVars();
	
		//Nova instância de paciente
		$obBairro = new EntityCidade;
		$obBairro->nome = $postVars['nome'];
		$obBairro->cadastrar();
		
		
		//retorna a página de listagem de pacientes
		return self::getCidades($request);
	
	}
	
}