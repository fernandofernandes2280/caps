<?php

namespace App\Controller\Pages;

use \App\Utils\View;
use \App\Model\Entity\Bairro as EntityBairro;
use \WilliamCosta\DatabaseManager\Pagination;
use App\Http\Request;

class Bairro extends Page{
	
	private static $qtdTotal ;
	
	
	//Método responsavel por obter a rendereizacao dos pacientes para a página
	private static function getBairroItems($request, &$obPagination){
	$resultados = '';
	
	//Pagina Atual
	$queryParams = $request->getQueryParams();
	$paginaAtual = $queryParams['page'] ?? 1;
	
	 
	//Armazena valor busca pelo nome do paciente
	 $busca = $queryParams['buscaBairro'] ?? '';
	 
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
	
	 $quantidadeTotal = EntityBairro::getBairros($where, null,null,'COUNT(*) as qtd')->fetchObject()->qtd;
	 self::$qtdTotal = $quantidadeTotal;
	 //Instancia de paginação
	 $obPagination = new Pagination($quantidadeTotal,$paginaAtual,5);
	 #############################################
	 
	 
	//Obtem os pacientes
	 $results = EntityBairro::getBairros($where, 'id ASC', $obPagination->getLimit());
	 
	 //var_dump($where);exit;
	 
	//Renderiza
	 while ($ob = $results -> fetchObject(EntityBairro::class)) {
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
	public static function getBairros($request){
		$resultados = '';
		$results =  EntityBairro::getBairros(null,'nome asc',null);
		//Renderiza
		while ($ob = $results -> fetchObject(EntityBairro::class)) {

			//View de Bairros
			$resultados .= View::render('pages/paciente/itemSelect',[
					
					'nome' => $ob->nome
					
			]);
		}
		//Retorna os bairros
		return $resultados;
		
	}
	
	
	//Método responsavel por cadastrar um paciente
	public static function insertBairro($request){

		//dados do post
		$postVars = $request->getPostVars();
	
		//Nova instância de paciente
		$obBairro = new EntityBairro;
		$obBairro->nome = $postVars['nome'];
		$obBairro->cadastrar();
		
		
		//retorna a página de listagem de pacientes
		return self::getBairros($request);
	
	}
	
}