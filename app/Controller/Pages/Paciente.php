<?php

namespace App\Controller\Pages;

use \App\Utils\View;
use \App\Model\Entity\Paciente as EntityPaciente;
use \WilliamCosta\DatabaseManager\Pagination;
use App\Http\Request;
use \App\Controller\Pages;


class Paciente extends Page{
	
	private static $qtdTotal ;
	
	
	//Método responsavel por obter a rendereizacao dos pacientes para a página
	private static function getPacienteItems($request, &$obPagination){
	$resultados = '';
	
	//Pagina Atual
	$queryParams = $request->getQueryParams();
	$paginaAtual = $queryParams['page'] ?? 1;
	
	 
	//Armazena valor busca pelo nome do paciente
	 $busca = $queryParams['busca'] ?? '';
	 //Filtro Status
	 $filtroStatus = $queryParams['status'] ?? '';
	 //Filtro recebe apenas os valores possíveis(s ou n) caso contrário recebe vazio.
	 $filtroStatus = in_array($filtroStatus, ['ATIVO','INATIVO']) ? $filtroStatus : '';
	 //Filtro Status
	 $filtroTipo = $queryParams['tipo'] ?? '';
	 //Filtro recebe apenas os valores possíveis(s ou n) caso contrário recebe vazio.
	 $filtroTipo = in_array($filtroTipo, ['AD','TM']) ? $filtroTipo : '';
	 
	 //Condições SQL
	 $condicoes = [
	 		
	 		strlen($busca) ? 'nome LIKE "%'.str_replace(' ', '%', $busca).'%"' : null,
	 		strlen($filtroStatus) ? 'status = "'.$filtroStatus.'" ' : null,
	 		strlen($filtroTipo) ? 'tipo = "'.$filtroTipo.'" ' : null
	 ];
	 
	 //Remove posições vazias
	 $condicoes = array_filter($condicoes);
	 
	 //cláusula where
	 $where = implode(' AND ', $condicoes);
	 
	 
	 //Quantidade total de registros
	// $quantidadeTotal = EntityPaciente::getPacientes($where, null,null,'COUNT(*) as qtd')->fetchObject()->qtd;
	
	 $quantidadeTotal = EntityPaciente::getPacientes($where, null,null,'COUNT(*) as qtd')->fetchObject()->qtd;
	 self::$qtdTotal = $quantidadeTotal;
	 //Instancia de paginação
	 $obPagination = new Pagination($quantidadeTotal,$paginaAtual,5);
	 #############################################
	 
	 
	//Obtem os pacientes
	 $results = EntityPaciente::getPacientes($where, 'codPronto ASC', $obPagination->getLimit());
	 
	 //var_dump($where);exit;
	 
	//Renderiza
	while ($obPaciente = $results -> fetchObject(EntityPaciente::class)) {
		//var_dump($obPaciente);exit;
		//View de pacientes
		$resultados .= View::render('pages/paciente/itemPaciente',[
		//muda cor do texto do status para azul(ativo) ou vermelho(inativo)
				$obPaciente->status == 'Ativo' ? $cor = 'text-success' : $cor = 'text-danger',
				'codPronto' => str_pad($obPaciente->codPronto,4,"0",STR_PAD_LEFT),
				'nome' => $obPaciente->nome,
				'cartaoSus' => $obPaciente->cartaoSus,
				'tipo' => $obPaciente->tipo,
				'status' => $obPaciente->status,
				'cor' => $cor
				
		]);
	}
	//Retorna os pacientes
	return $resultados;
}
	
	
	
	
	//retorna o conteudo (view) de Pacientes
	public static function getPacientes($request){
		$obBairro = new Bairro;
		$obCidade = new Cidade;
		$obEscolaridade = new Escolaridade;
		$obEstadoCivil = new EstadoCivil;
		$obProcedencia = new Procedencia;
		$obMotivoInativo = new MotivoInativo;
		$obSubstancia = new Substancia;
		$obCid10 = new Cid10;
		
		//Recebe parâmetros do GET
		$queryParams = $request->getQueryParams();
		$selectedAtIn = $queryParams['status'] ?? '';
		$selectedAtivo = $queryParams['status'] ?? '';
		$selectedInativo = $queryParams['status'] ?? '';
		$selectedAtIn == 'ATIVO' ? $selectedAtivo = 'selected' : '';
		$selectedInativo == 'INATIVO' ? $selectedInativo = 'selected' : '';
		$selectedAtIn == '' ? $selectedAtIn = 'selected' : '';
		$selectedAd = $queryParams['tipo'] ?? '';
		$selectedTm = $queryParams['tipo'] ?? '';
		$selectedAdTm = $queryParams['tipo'] ?? '';
		$selectedAd == 'AD' ? $selectedAd = 'selected' : '';
		$selectedTm == 'TM' ? $selectedTm = 'selected' : '';
		$selectedAdTm == '' ? $selectedAdTm = 'selected' : '';
		
		//View de pacientes
		$content = View::render('pages/pacientes',[
				'itens' => self::getPacienteItems($request,$obPagination),
				'pagination' => parent::getPagination($request, $obPagination),
				'buscaNome' =>  $queryParams['busca'] ?? '',
				'selectedAtivo' =>  $selectedAtivo,
				'selectedInativo' =>  $selectedInativo,
				'selectedAd' =>  $selectedAd,
				'selectedTm' =>  $selectedTm,
				'selectedAtIn' => $selectedAtIn,
				'totalPacientes' => self::$qtdTotal,
				'optionBairro' => $obBairro->getBairros($request),
				'optionCidade' => $obCidade->getCidades($request),
				'optionEscolaridade' => $obEscolaridade->getEscolaridades($request),
				'optionEstadoCivil' => $obEstadoCivil->getEstadoCivils($request),
				'optionProcedencia' => $obProcedencia->getProcedencias($request),
				'optionMotivoInativo' => $obMotivoInativo->getMotivoInativos($request),
				'optionSubstancia' => $obSubstancia->getSubstancias($request),
				'optionCid10' => $obCid10->getCid10s($request)
				
				
		]);
		
		return parent::getPage('PACIENTES > SISCAPS', $content);
		
	}
	
	
	//Método responsavel por cadastrar um paciente
	public static function insertPaciente($request){

		//dados do post
		$postVars = $request->getPostVars();
	
		//Nova instância de paciente
		$obPaciente = new EntityPaciente;
		$obPaciente->codPronto = 77777;
		$obPaciente->nome = $postVars['nome'];
		$obPaciente->endereco = $postVars['endereco'];
		$obPaciente->cadastrar();
		
		
		//retorna a página de listagem de pacientes
		return self::getPacientes($request);
	
	}
	
}