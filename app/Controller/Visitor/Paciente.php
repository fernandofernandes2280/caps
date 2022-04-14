<?php

namespace App\Controller\Visitor;

use \App\Utils\View;
use \App\Utils\Funcoes;
use \App\Model\Entity\Paciente as EntityPaciente;
use \App\Model\Entity\Bairro as EntityBairro;
use \App\Model\Entity\Cidade as EntityCidade;
use \App\Model\Entity\Escolaridade as EntityEscolaridade;
use \App\Model\Entity\EstadoCivil as EntityEstadoCivil;
use \App\Model\Entity\Procedencia as EntityProcedencia;
use \App\Model\Entity\MotivoInativo as EntityMotivoInativo;
use \App\Model\Entity\Cid10 as Entitycid10;
use \App\Model\Entity\Substancia as EntitySubstancia;
use \App\Controller\Admin\Alert;

use \WilliamCosta\DatabaseManager\Pagination;
use App\Controller\Admin\Logs;


class Paciente extends Page{
	
	//Armazena quantidade total de pacientes listados
	private static $qtdTotal ;
	
	//esconde busca rápida de prontuário no navBar
	private static $hidden = '';
	
	//Método responsavel por obter a rendereizacao dos pacientes para a página
	private static function getPacienteItems($request, &$obPagination){
		$resultados = '';
		
		//Pagina Atual
		$queryParams = $request->getQueryParams();
		$paginaAtual = $queryParams['page'] ?? 1;
		
		$postVars = $request->getPostVars();
		//Armazena valor busca pelo nome do paciente
		$nome = $queryParams['nome'] ?? '';
		
		@$postVars['pront'] ? $pront = @$postVars['pront'] : $pront = @$queryParams['pront'];
	//	$pront = $queryParams['pront'] ?? '';
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
				
				strlen($nome) ? 'nome LIKE "%'.str_replace(' ', '%', $nome).'%"' : null,
				strlen($pront) ? 'codPronto LIKE "'.$pront.'%"' : null,
				strlen($filtroStatus) ? 'status = "'.$filtroStatus.'" ' : null,
				strlen($filtroTipo) ? 'tipo = "'.$filtroTipo.'" ' : null
		];
		
		//Remove posições vazias
		$condicoes = array_filter($condicoes);
		
		//cláusula where
		$where = implode(' AND ', $condicoes);
		
		
		//Quantidade total de registros
		// $quantidadeTotal = EntityPaciente::getPacientes($where, null,null,'COUNT(*) as qtd')->fetchObject()->qtd;
		
		self::$qtdTotal = EntityPaciente::getPacientes($where, null,null,'COUNT(*) as qtd')->fetchObject()->qtd;
		
		//Instancia de paginação
		$obPagination = new Pagination(self::$qtdTotal,$paginaAtual,5);
		#############################################
		
		
		//Verifica se existe pesquisa, se sim, ordena pelo ulltimo pac cadastrado, se nao, ordena pelo Prontuário
		empty($queryParams) ? $order = 'id DESC' : $order = 'codPronto ASC';
		
		
		
		//Obtem os pacientes
		$results = EntityPaciente::getPacientes($where, $order, $obPagination->getLimit());
		
		
		
		//Renderiza
		while ($obPaciente = $results -> fetchObject(EntityPaciente::class)) {
			
			//View de pacientes
			$resultados .= View::render('visitor/modules/pacientes/item',[
					//muda cor do texto do status para azul(ativo) ou vermelho(inativo)
					$obPaciente->status == 'Ativo' ? $cor = 'text-success' : $cor = 'text-danger',
					$obPaciente->status == 'Ativo' ? $titleStatus = 'Ativo' : $titleStatus = 'Inativo',
					$obPaciente->tipo == 'Ad' ? $titleTipo = 'Álcool e/ou drogas ' : $titleTipo = 'Transtormo mental',
					'codPronto' => str_pad($obPaciente->codPronto,4,"0",STR_PAD_LEFT),
					'nome' => $obPaciente->nome,
					'cartaoSus' => EntityPaciente::mask($obPaciente->cartaoSus, '# | # | # | # | # | # | # | # | # | # | # | # | # | # | #') ,
					'tipo' => $obPaciente->tipo,
					'status' => $obPaciente->status,
					'cor' => $cor,
					'id' => $obPaciente->id,
					'titleStatus'=> $titleStatus,
					'titleTipo'=> $titleTipo
					
					
					
			]);
			
		}
		
		
		
		//Retorna os pacientes
		return $resultados;
		
	}
	
	
	
	//Método responsavel por renderizar a view de Listagem de Pacientes
	public static function getPacientes($request){
		$selectedAtivo = '';
		$selectedInativo = '';
		$selectedAtIn = '';
		$selectedAd = '';
		$selectedTm = '';
		$selectedAdTm = '';
		//Recebe os parâmetros da requisição
		$queryParams = $request->getQueryParams();
		
		
		
		if (isset($queryParams['tipo'])) {
			if($queryParams['tipo'] == 'TM')$selectedTm = 'selected';
			else if($queryParams['tipo'] == 'AD') $selectedAd = 'selected';
			else $selectedAdTm = 'selected';
		}
		
		if (isset($queryParams['status'])) {
			if($queryParams['status'] == 'ATIVO')$selectedAtivo = 'selected';
			else if($queryParams['status'] == 'INATIVO') $selectedInativo = 'selected';
			else $selectedAtIn = 'selected';
		}
		
		
		//Conteúdo da Home
		$content = View::render('visitor/modules/pacientes/index',[
				
				'itens' => self::getPacienteItems($request,$obPagination),
				'pagination' => parent::getPagination($request, $obPagination),
				'nome' =>  $queryParams['nome'] ?? '',
				'pront' =>  $queryParams['pront'] ?? '',
				'totalPacientes' => self::$qtdTotal,
				'selectedAtivo' =>  $selectedAtivo,
				'selectedInativo' =>  $selectedInativo,
				'selectedAdTm' => $selectedAdTm,
				'selectedAd' =>  $selectedAd,
				'selectedTm' =>  $selectedTm,
				'selectedAtIn' => $selectedAtIn,
				'statusMessage' => ''
			
				
				
		]);
		
		//Retorna a página completa
		return parent::getPanel('Pacientes > SISCAPS', $content,'pacientes', self::$hidden);
		
	}
	

	//Metodo responsávelpor retornar o formulário com os dados de um Paciente
	public static function getViewPaciente($request,$codPronto){
		
		$queryParams = $request->getQueryParams();
		
		$postVars = $request->getPostVars();
		
	//	var_dump($postVars);exit;
		@$postVars['pront'] ? $codPronto = $postVars['pront'] : $codPronto = $codPronto; 
		
		
		//obtém o Paciente do banco de dados
		$obPaciente = EntityPaciente::getPacienteByCodPronto($codPronto);
		
		//Valida a instancia
		if(!$obPaciente instanceof EntityPaciente){
			$request->getRouter()->redirect('/visitor/pacientes');
		}
		
		//Conteúdo do Formulário
		$content = View::render('visitor/modules/pacientes/form',[
				'title' => str_pad($obPaciente->codPronto,4,"0",STR_PAD_LEFT),
				'prontuario' => $obPaciente->codPronto,
				'nome' => $obPaciente->nome,
				'cep' => $obPaciente->cep,
				'endereco' => $obPaciente->endereco,
				'cartaoSus' => $obPaciente->cartaoSus,
				'naturalidade' => $obPaciente->naturalidade,
				'fone1' => Funcoes::mask($obPaciente->fone1,'#### ####'),
				'fone2' => Funcoes::mask($obPaciente->fone2,'#### ####'),
				'mae' => $obPaciente->mae,
				'obs' => $obPaciente->obs,
				'bairro' => EntityBairro::getBairroById($obPaciente->bairro)->nome,
				'cidade' => $obPaciente->cidade,
				'uf' => $obPaciente->uf,
				'dataNasc' => date('d-m-Y', strtotime($obPaciente->dataNasc)),
				'dataCad' => date('d-m-Y', strtotime($obPaciente->dataCad)),
				'selectedSexo' => $obPaciente->sexo,
				'optionEscolaridade' => is_null($obPaciente->escolaridade) ? 'Não Informado' : EntityEscolaridade::getEscolaridadeById($obPaciente->escolaridade)->nome,
				'optionEstadoCivil' => is_null($obPaciente->estadoCivil) ? 'Não Informado' : EntityEstadoCivil::getEstadoCivilById($obPaciente->estadoCivil)->nome,
				'optionProcedencia' => is_null($obPaciente->procedencia) ? 'Não Informado' : EntityProcedencia::getProcedenciaById($obPaciente->procedencia)->nome, 
				'selectedStatus' => $obPaciente->status,
				'optionMotivoInativo' => is_null($obPaciente->motivoInativo) ? 'Não Informado' : EntityMotivoInativo::getMotivoInativoById($obPaciente->motivoInativo)->nome,
				'selectedTipo' => $obPaciente->tipo,
				'optionCid10-1' => is_null($obPaciente->cid1) ? 'Não Informado' : Entitycid10::getCid10ById($obPaciente->cid1)->nome,
				'optionCid10-2' => is_null($obPaciente->cid2) ? 'Não Informado' : Entitycid10::getCid10ById($obPaciente->cid2)->nome,
				'optionSubstanciaPri' => is_null($obPaciente->substanciaPri) ? 'Não Informado' : EntitySubstancia::getSubstanciaById($obPaciente->substanciaPri)->nome,
				'optionSubstanciaSec' => is_null($obPaciente->substanciaSec) ? 'Não Informado' : EntitySubstancia::getSubstanciaById($obPaciente->substanciaSec)->nome,
				
				
				
				
				
		]);
	//	Logs::setNewLog($request);
		//Retorna a página completa
		return parent::getPanel('Editar Paciente > SISCAPS', $content,'pacientes', self::$hidden);
		
	}
	
	

	
	
	
}