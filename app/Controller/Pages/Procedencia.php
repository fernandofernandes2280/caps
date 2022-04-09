<?php

namespace App\Controller\Pages;

use \App\Utils\View;
use \App\Model\Entity\Procedencia as EntityProcedencia;
use \WilliamCosta\DatabaseManager\Pagination;
use App\Http\Request;

class Procedencia extends Page{
	
	private static $qtdTotal ;
	
	
	//Método responsavel por obter a rendereizacao dos pacientes para a página
	private static function getProcedenciaItems($request, &$obPagination){
	$resultados = '';
	
	//Pagina Atual
	$queryParams = $request->getQueryParams();
	$paginaAtual = $queryParams['page'] ?? 1;
	
	 
	//Armazena valor busca pelo nome do paciente
	 $busca = $queryParams['buscaProcedencia'] ?? '';
	 
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
	
	 $quantidadeTotal = EntityProcedencia::getProcedencia($where, null,null,'COUNT(*) as qtd')->fetchObject()->qtd;
	 self::$qtdTotal = $quantidadeTotal;
	 //Instancia de paginação
	 $obPagination = new Pagination($quantidadeTotal,$paginaAtual,5);
	 #############################################
	 
	 
	//Obtem os pacientes
	 $results = EntityProcedencia::getProcedencias($where, 'id ASC', $obPagination->getLimit());
	 
	 //var_dump($where);exit;
	 
	//Renderiza
	 while ($ob = $results -> fetchObject(EntityProcedencia::class)) {
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
public static function getProcedencias($request){
		$resultados = '';
		$results =  EntityProcedencia::getProcedencias(null,'nome asc',null);
		
		
		//Renderiza
		while ($ob = $results -> fetchObject(EntityProcedencia::class)) {

			//View de Bairros
			$resultados .= View::render('pages/paciente/itemSelect',[
					
					'nome' => $ob->nome
					
			]);
		}
		//Retorna os bairros
		return $resultados;
		
	}
	
	
	//Método responsavel por cadastrar um paciente
	public static function insertProcedencia($request){

		//dados do post
		$postVars = $request->getPostVars();
	
		//Nova instância de paciente
		$ob = new EntityProcedencia;
		$ob->nome = $postVars['nome'];
		$ob->cadastrar();
		
		
		//retorna a página de listagem de pacientes
		return self::getProcedencias($request);
	
	}
	
}