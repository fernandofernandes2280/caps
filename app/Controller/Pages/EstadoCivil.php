<?php

namespace App\Controller\Pages;

use \App\Utils\View;
use \App\Model\Entity\EstadoCivil as EntityEstadoCivil;
use \WilliamCosta\DatabaseManager\Pagination;
use App\Http\Request;

class EstadoCivil extends Page{
	
	private static $qtdTotal ;
	
	
	//Método responsavel por obter a rendereizacao dos pacientes para a página
	private static function getEstadoCivilItems($request, &$obPagination){
	$resultados = '';
	
	//Pagina Atual
	$queryParams = $request->getQueryParams();
	$paginaAtual = $queryParams['page'] ?? 1;
	
	 
	//Armazena valor busca pelo nome do paciente
	 $busca = $queryParams['buscaEstadoCivil'] ?? '';
	 
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
	
	 $quantidadeTotal = EntityEstadoCivil::getEstadoCivil($where, null,null,'COUNT(*) as qtd')->fetchObject()->qtd;
	 self::$qtdTotal = $quantidadeTotal;
	 //Instancia de paginação
	 $obPagination = new Pagination($quantidadeTotal,$paginaAtual,5);
	 #############################################
	 
	 
	//Obtem os pacientes
	 $results = EntityEstadoCivil::getEstadoCivils($where, 'id ASC', $obPagination->getLimit());
	 
	 //var_dump($where);exit;
	 
	//Renderiza
	 while ($ob = $results -> fetchObject(EntityEstadoCivil::class)) {
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
public static function getEstadoCivils($request){
		$resultados = '';
		$results =  EntityEstadoCivil::getEstadoCivils(null,'nome asc',null);
		
		
		//Renderiza
		while ($ob = $results -> fetchObject(EntityEstadoCivil::class)) {

			//View de Bairros
			$resultados .= View::render('pages/paciente/itemSelect',[
					
					'nome' => $ob->nome
					
			]);
		}
		//Retorna os bairros
		return $resultados;
		
	}
	
	
	//Método responsavel por cadastrar um paciente
	public static function insertEstadoCivil($request){

		//dados do post
		$postVars = $request->getPostVars();
	
		//Nova instância de paciente
		$ob = new EntityEstadoCivil;
		$ob->nome = $postVars['nome'];
		$ob->cadastrar();
		
		
		//retorna a página de listagem de pacientes
		return self::getEstadoCivils($request);
	
	}
	
}