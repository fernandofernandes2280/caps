<?php

namespace App\Controller\Visitor;

use \App\Utils\View;
use \App\Model\Entity\Paciente as EntityPaciente;
use \App\Model\Entity\Atendimento as EntityAtendimento;
use \App\Model\Entity\Profissional as EntityProfissional;
use \App\Model\Entity\Procedimento as EntityProcedimento;
use \App\Model\Entity\Cid10 as EntityCid10;
use \WilliamCosta\DatabaseManager\Pagination;
use \Dompdf\Dompdf;

class Atendimento extends Page{
	
	//Armazena quantidade total de pacientes listados
	private static $qtdTotal ;
	private static $totalGeralBpac = 0;
	
	//esconde busca rápida de prontuário no navBar
	private static $hidden = '';
	
	//Método responsavel por obter a rendereizacao dos pacientes para a página
	private static function getAtendimentosItems($request, &$obPagination, $id){
		$resultados = '';
		
		//Pagina Atual
		$queryParams = $request->getQueryParams();
		$paginaAtual = $queryParams['page'] ?? 1;
		
		//Armazena valor busca pelo nome do paciente
		$busca = $queryParams['busca'] ?? '';
		//Filtro Status
		//$filtroStatus = $queryParams['status'] ?? '';
		//Filtro recebe apenas os valores possíveis(s ou n) caso contrário recebe vazio.
		//$filtroStatus = in_array($filtroStatus, ['ATIVO','INATIVO']) ? $filtroStatus : '';
		//Filtro Status
		//$filtroTipo = $queryParams['tipo'] ?? '';
		//Filtro recebe apenas os valores possíveis(s ou n) caso contrário recebe vazio.
		//$filtroTipo = in_array($filtroTipo, ['AD','TM']) ? $filtroTipo : '';
		
		//Condições SQL
		$condicoes = [
				
				strlen($busca) ? 'codPronto "'.$id.'" ' : null,
			//	strlen($filtroStatus) ? 'status = "'.$filtroStatus.'" ' : null,
			//	strlen($filtroTipo) ? 'tipo = "'.$filtroTipo.'" ' : null
		];
		
		//Remove posições vazias
		$condicoes = array_filter($condicoes);
		
		//cláusula where
		//$where = implode(' AND ', $condicoes);
		$where = 'codPronto = '.$id.' ';
	
		//Quantidade total de registros
		// $quantidadeTotal = EntityPaciente::getPacientes($where, null,null,'COUNT(*) as qtd')->fetchObject()->qtd;
		
		self::$qtdTotal = EntityAtendimento::getAtendimentos($where, 'id DESC',null,'COUNT(*) as qtd')->fetchObject()->qtd;
		
		//Instancia de paginação
		$obPagination = new Pagination(self::$qtdTotal,$paginaAtual,5);
		#############################################
		
		
		//Obtem os pacientes
		$results = EntityAtendimento::getAtendimentos($where, 'data DESC', $obPagination->getLimit());
		
		
	//	var_dump($results); exit;
		
		//Renderiza
		while ($obAtendimento = $results -> fetchObject(EntityAtendimento::class)) {

			//View de pacientes
			$resultados .= View::render('visitor/modules/atendimentos/item',[
				
				
					'codPronto' => str_pad($obAtendimento->codPronto,4,"0",STR_PAD_LEFT),
					'data' =>  date('d/m/Y', strtotime($obAtendimento->data)),
					'idProfissional' => EntityProfissional::getProfissionalById($obAtendimento->idProfissional)->nome,
					'idProcedimento' => EntityProcedimento::getProcedimentoById($obAtendimento->idProcedimento)->nome,
					'status' => $obAtendimento->status,
					'id' => $obAtendimento->id,
					'idade' => $obAtendimento->idade
				
					
					
					
			]);
		}
		//Retorna os pacientes
		return $resultados;
		//var_dump($where);exit;
	}
	
	

	//Método responsavel por renderizar a view de Listagem de Atendimentos
	public static function getAtendimentos($request,$id){
		
			
		//Post Vars
		$postVars = $request->getPostVars();
		
		$queryParams = $request->getQueryParams();
		
		@$queryParams['pront'] ? $codPronto = $queryParams['pront'] : $codPronto = $id  ;	
		
//		var_dump($request);exit;
		//obtém o Paciente do banco de dados
		$obPaciente = EntityPaciente::getPacienteByCodPronto($codPronto);
		
		//Valida a instancia
		if(!$obPaciente instanceof EntityPaciente){
			$request->getRouter()->redirect('/user/pacientes');
		}
		
		//obtém o  do banco de dados
		$obAtendimento = EntityAtendimento::getAtendimentoByCodPronto($codPronto);
		
		//Valida a instancia
		if(!$obAtendimento instanceof EntityAtendimento){
			$request->getRouter()->redirect('/visitor/pacientes');
		}
		
		//Conteúdo da Home
		$content = View::render('visitor/modules/atendimentos/index',[
				
				'itens' => self::getAtendimentosItems($request,$obPagination,$codPronto),
				'pagination' => parent::getPagination($request, $obPagination),
				'totalAtendimentos' => self::$qtdTotal,
				'nome' => $obPaciente->nome,
				'prontuario' => str_pad($obPaciente->codPronto,4,"0",STR_PAD_LEFT),
				'statusMessage' => '',
				'data' => $postVars['data'] ?? '',
				'profissional' => $postVars['profissional'] ?? '',
				'procedimento' => $postVars['procedimento'] ?? '',
				
				
		]);
		
		
		
		//Retorna a página completa
		return parent::getPanel('Atendimentos', $content,'atendimentos', self::$hidden);
		
	}
	
	
}

