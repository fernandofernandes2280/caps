<?php

namespace App\Controller\Pages;

use \App\Utils\View;
use \App\Model\Entity\Escolaridade as EntityEscolaridade;
use \WilliamCosta\DatabaseManager\Pagination;
use App\Http\Request;

class Escolaridade extends Page{
	
	private static $qtdTotal ;
	
	
	//Método responsavel por obter a rendereizacao dos pacientes para a página
	private static function getEscolaridadeItems($request, &$obPagination){
	$resultados = '';
	
	//Pagina Atual
	$queryParams = $request->getQueryParams();
	$paginaAtual = $queryParams['page'] ?? 1;
	
	 
	//Armazena valor busca pelo nome do paciente
	 $busca = $queryParams['buscaEscolaridade'] ?? '';
	 
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
	
	 $quantidadeTotal = EntityEscolaridade::getEscolaridade($where, null,null,'COUNT(*) as qtd')->fetchObject()->qtd;
	 self::$qtdTotal = $quantidadeTotal;
	 //Instancia de paginação
	 $obPagination = new Pagination($quantidadeTotal,$paginaAtual,5);
	 #############################################
	 
	 
	//Obtem os pacientes
	 $results = EntityEscolaridade::getEscolaridades($where, 'id ASC', $obPagination->getLimit());
	 
	 //var_dump($where);exit;
	 
	//Renderiza
	 while ($ob = $results -> fetchObject(EntityEscolaridade::class)) {
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
public static function getEscolaridades($request){
		$resultados = '';
		$results =  EntityEscolaridade::getEscolaridades(null,'nome asc',null);
		
		
		//Renderiza
		while ($ob = $results -> fetchObject(EntityEscolaridade::class)) {

			//View de Bairros
			$resultados .= View::render('pages/paciente/itemSelect',[
					
					'nome' => $ob->nome
					
			]);
		}
		//Retorna os bairros
		return $resultados;
		
	}
	
	
	//Método responsavel por cadastrar um paciente
	public static function insertEscolaridade($request){

		//dados do post
		$postVars = $request->getPostVars();
	
		//Nova instância de paciente
		$ob = new EntityEscolaridade;
		$ob->nome = $postVars['nome'];
		$ob->cadastrar();
		
		
		//retorna a página de listagem de pacientes
		return self::getEscolaridades($request);
	
	}
	
}