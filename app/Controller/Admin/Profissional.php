<?php

namespace App\Controller\Admin;

use \App\Utils\View;
use \App\Model\Entity\Paciente as EntityPaciente;
use \App\Model\Entity\Bairro as EntityBairro;
use \App\Model\Entity\Escolaridade as EntityEscolaridade;
use \App\Model\Entity\EstadoCivil as EntityEstadoCivil;
use \App\Model\Entity\Procedencia as EntityProcedencia;
use \App\Model\Entity\MotivoInativo as EntityMotivoInativo;
use \App\Model\Entity\Cid10 as Entitycid10;
use \App\Model\Entity\Substancia as EntitySubstancia;
use \App\Utils\Funcoes;

use \App\Model\Entity\Profissional as EntityProfissional;

use \WilliamCosta\DatabaseManager\Pagination;
use Dompdf\Dompdf;


class Profissional extends Page{
	
	//Armazena quantidade total de pacientes listados
	private static $qtdTotal ;
	//esconde busca rápida de prontuário no navBar (''->exibe  'hidden'->esconde)
	private static $buscaRapidaPront = 'hidden';
	
	//Método responsavel por obter a rendereizacao dos pacientes para a página
	private static function getProfissionaisItems($request, &$obPagination){
		
		
		
		$resultados = '';
		
		//Pagina Atual
		$queryParams = $request->getQueryParams();
		$paginaAtual = $queryParams['page'] ?? 1;
		
		
		//Armazena valor busca pelo nome do paciente
		$nome = $queryParams['nome'] ?? '';
		
		//Filtro Status
		$filtroStatus = $queryParams['status'] ?? '';
		//Filtro recebe apenas os valores possíveis(s ou n) caso contrário recebe vazio.
		$filtroStatus = in_array($filtroStatus, ['ATIVO','INATIVO']) ? $filtroStatus : '';
		
		//Condições SQL
		$condicoes = [
				
				strlen($nome) ? 'nome LIKE "%'.str_replace(' ', '%', $nome).'%"' : null,
				strlen($filtroStatus) ? 'status = "'.$filtroStatus.'" ' : null,
		];
		
		//Remove posições vazias
		$condicoes = array_filter($condicoes);
		
		//cláusula where
		$where = implode(' AND ', $condicoes);
		
	
		//Quantidade total de registros
		// $quantidadeTotal = EntityPaciente::getPacientes($where, null,null,'COUNT(*) as qtd')->fetchObject()->qtd;
		
		self::$qtdTotal = EntityProfissional::getProfissionais($where, null,null,'COUNT(*) as qtd')->fetchObject()->qtd;
		
		//Instancia de paginação
		$obPagination = new Pagination(self::$qtdTotal,$paginaAtual,5);
		#############################################
		
		
		//Verifica se existe pesquisa, se sim, ordena pelo ulltimo pac cadastrado, se nao, ordena pelo Prontuário
		$order = 'status DESC, nome' ;
		
		
		
		//Obtem os pacientes
		$results = EntityProfissional::getProfissionais($where, $order, $obPagination->getLimit());
		
		
		
		//Renderiza
		while ($obProfissional = $results -> fetchObject(EntityProfissional::class)) {
			 
			//View de pacientes
			$resultados .= View::render('admin/modules/profissionais/item',[
			
			//muda cor do texto do status para azul(ativo) ou vermelho(inativo)
			    $obProfissional->status == 1 ? $cor = 'text-success' : $cor = 'text-danger',
			    $obProfissional->status == 1 ? $titleStatus = 'Ativo' : $titleStatus = 'Inativo',

			    'nome' => $obProfissional->nome,
			    'cartaoSus' => $obProfissional->cartaoSus,
			    'status' => $obProfissional->status,
			    'id' => $obProfissional->id,
			    'titleStatus'=> $titleStatus,
			    'cor' => $cor,
			]);
			
		}
	
		//Grava o Log do usuário
//		if(!empty($queryParams)) Logs::setNewLog('pacientes', 'Pesquisa' , implode(", ", $condicoes));

		//Retorna os pacientes
		return $resultados;
		
		
		
	}
	

	
	//Método responsavel por renderizar a view de Listagem de Pacientes
	public static function getProfissionais($request){
		$selectedAtivo = '';
		$selectedInativo = '';
		$selectedAtIn = '';
		$selectedAd = '';
		$selectedTm = '';
		$selectedAdTm = '';
		//Recebe os parâmetros da requisição
		$queryParams = $request->getQueryParams();

	//	var_dump('ola');exit;

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
		
		//esconde busca rápida de prontuário no navBar
		$hidden = '';
		//Conteúdo da Home
		$content = View::render('admin/modules/profissionais/index',[
				'title' => 'Pesquisar Profissionais',
				'itens' => self::getProfissionaisItems($request,$obPagination),
				'pagination' => parent::getPagination($request, $obPagination),
				'statusMessage' => self::getStatus($request),
				'nome' =>  $queryParams['nome'] ?? '',
				'pront' =>  $queryParams['pront'] ?? '',
				'totalPacientes' => self::$qtdTotal,
				'selectedAtivo' =>  $selectedAtivo,
				'selectedInativo' =>  $selectedInativo,
				'selectedAdTm' => $selectedAdTm,
				'selectedAd' =>  $selectedAd,
				'selectedTm' =>  $selectedTm,
				'selectedAtIn' => $selectedAtIn,
				
				
		]);
		
		//Retorna a página completa
		return parent::getPanel('Profissionais > Siscaps', $content,'profissionais', self::$buscaRapidaPront);
		
	}
	
	
	
	//Método responsavel por renderizar a Capa de Prontuário do Paciente
	public static function getCapaProntuario($request,$codPronto){
	
		//esconde busca rápida de prontuário no navBar
		$hidden = '';
		
		
		//obtém o Paciente do banco de dados
		$obPaciente = EntityPaciente::getPacienteByCodPronto($codPronto);
		
		//Valida a instancia
		if(!$obPaciente instanceof EntityPaciente){
			$request->getRouter()->redirect('/admin/pacientes');
		}
		
		//Conteúdo da Home
		$content = View::render('admin/modules/pacientes/capa',[
			
				'codPronto' => $obPaciente->codPronto,
				'tipo' => $obPaciente->tipo,
				'nome' => $obPaciente->nome,
				'endereco' => $obPaciente->endereco.' - '.EntityBairro::getBairroById($obPaciente->bairro)->nome.' - '.$obPaciente->cidade.' / '.$obPaciente->uf,
				'dataNasc' => date('d/m/Y', strtotime($obPaciente->dataNasc)),
				'dataCad' => date('d/m/Y', strtotime($obPaciente->dataCad)),
				'estadoCivil' =>EntityEstadoCivil::getEstadoCivilById($obPaciente->estadoCivil)->nome,
				'escolaridade' =>EntityEscolaridade::getEscolaridadeById($obPaciente->escolaridade)->nome,
				'sexo' => $obPaciente->sexo,
				'naturalidade' => $obPaciente->naturalidade,
				'mae' => $obPaciente->mae,
				'cartaoSus' =>Funcoes::mask($obPaciente->cartaoSus,'# | # | # | # | # | # | # | # | # | # | # | # | # | # | #') ,
				'fone1' =>$obPaciente->fone1,
				'fone2' =>$obPaciente->fone2,
				'procedencia' =>EntityProcedencia::getProcedenciaById($obPaciente->procedencia)->nome,
				'cid' =>Entitycid10::getCid10ById($obPaciente->cid1)->nome
				
				
				
		]);
		
		//Retorna a página completa
		//return parent::getPanel('Pacientes > Siscaps', $content,'pacientes', self::$hidden);
		return $content;
		
	}
	
	//Método responsavel por gerar o PDF da Capa de Prontuário do Paciente
	public static function getImprimirCapaProntuario($request, $codPronto){
		
		//instância a classe
		$dompdf = new Dompdf(["enable_remote" => true]);
		$options = $dompdf->getOptions();
		$options->setDefaultFont('Courier');
		$dompdf->setOptions($options);
		//abre a sessão de cache
	//	ob_start();
		//caminho do arquivo
	//	require '{{URL}}../../resources/view/admin/modules/pacientes/capa.html';
		//recebe o conteudo entre as tags ob_start e ob_get_clean
	//	$pdf = ob_get_clean();
		
		$pdf = self::getCapaProntuario($request, $codPronto);
		
		//carrega o conteúdo do arquivo .php
		$dompdf->loadHtml($pdf);
		
		
		
		//Configura o tamanho do papel
		$dompdf->setPaper("A4");
		
		$dompdf->render();
		
		$dompdf->stream("capaProntuario.php", ["Attachment" => false]);
		
	}
	
	
	

	
	//Método que gera o Codigo do pontuario do paciente
	public static function geraCodPronto(){
		
		$resultado = [];
		$results =  EntityPaciente::getPacientes(null,'codPronto asc',null,'id, codPronto');
		//verifica se o id não é nulo e obtém a Escolaridade do banco de dados

		while ($obPaciente = $results -> fetchObject(EntityPaciente::class)) {
				
			$id[] = $obPaciente->id;
			$codPronto[] = $obPaciente->codPronto;
			}
			//retorna a diferença entre os arrays
			$resultado = (array_diff($id, $codPronto));
			
			if(count($resultado) > 0){
				sort($resultado);
				//recebe o primeiro valor do último codPronto que está faltando
				$resultado = $resultado[0];
				
			}else{
				//recebe a ultima posicao acrescida de 1
				$resultado = ($codPronto[count($codPronto)-1]+1);
			}

			return $resultado;
			
		}
		
		
		
	//Metodo responsávelpor retornar o formulário de cadastro de um novo Paciente
	public static function getNewPaciente($request){
		$dataAtual = date('Y-m-d');
		//Conteúdo do Formulário
		$content = View::render('admin/modules/pacientes/form',[
				'title' => 'Novo',
				'prontuario' =>str_pad(self::geraCodPronto(),4,"0",STR_PAD_LEFT) ,
				'hidden' =>'hidden', //esconde botão atendimentos
				'nome' => '',
				'endereco' => '',
				'cep' => '',
				'optionBairros' => EntityBairro::getSelectBairros(null),
				'cidade' => 'Santana',
				'uf' => 'Ap',
				'dataNasc' => '',
				'dataCad' => $dataAtual,
				'naturalidade' => '',
				'fone1' => '',
				'fone2' => '',
				'mae' => '',
				'cartaoSus' => '',
				'obs' => '',
				'statusMessage' => '',
				'optionEscolaridade' => EntityEscolaridade::getSelectEscolaridade(null),
				'optionEstadoCivil' => EntityEstadoCivil::getSelectEstadoCivil(null),
				'optionProcedencia' => EntityProcedencia::getSelectProcedencia(null),
				'optionMotivoInativo' => EntityMotivoInativo::getSelectMotivoInativo(null),
				'optionCid10-1' => Entitycid10::getSelectCid10(null),
				'optionCid10-2' => Entitycid10::getSelectCid10(null),
				'optionSubstanciaPri' => EntitySubstancia::getSelectSubstancia(null),
				'optionSubstanciaSec' => EntitySubstancia::getSelectSubstancia(null),
				'selectedStatusA' => 'selected',
				'selectedTipoA' => 'selected',
				
		]);
		
		//Retorna a página completa
		return parent::getPanel('Cadastrar Pacientes > WDEV', $content,'pacientes', self::$hidden);
		
	}
	
	
	//Metodo responsávelpor por cadastrar um Paciente no banco
	public static function setNewPaciente($request){
		//Post vars
		$postVars = $request->getPostVars();

		//Nova instância de paciente
		$obPaciente = new EntityPaciente;
		
		$obPaciente->codPronto = self::geraCodPronto();
		$obPaciente->nome = Funcoes::convertePriMaiuscula($postVars['nome']);
		$obPaciente->cep = $postVars['cep'];
		$obPaciente->endereco = Funcoes::convertePriMaiuscula($postVars['endereco']);
		$obPaciente->bairro = Funcoes::convertePriMaiuscula($postVars['bairro']);
		$obPaciente->cidade = Funcoes::convertePriMaiuscula($postVars['cidade']);
		$obPaciente->uf = strtoupper($postVars['uf']);
		$obPaciente->dataNasc = implode("-",array_reverse(explode("/",$postVars['dataNasc']))); 
		$obPaciente->dataCad = $postVars['dataCad'];
		$obPaciente->sexo = $postVars['sexo'];
		$obPaciente->naturalidade = Funcoes::convertePriMaiuscula($postVars['naturalidade']);
		$obPaciente->escolaridade = $postVars['escolaridade'] ?? null;
		$obPaciente->fone1 = $postVars['fone1'];
		$obPaciente->fone2 = $postVars['fone2'];
		$obPaciente->mae = Funcoes::convertePriMaiuscula($postVars['mae']);
		$obPaciente->estadoCivil = $postVars['estadoCivil'] ?? null;
		$obPaciente->procedencia = $postVars['procedencia'] ?? null;
		$obPaciente->status = $postVars['status'];
		$obPaciente->motivoInativo = $postVars['motivoInativo'] ?? null;
		$obPaciente->cartaoSus = $postVars['cartaoSus'];
		$obPaciente->tipo = $postVars['tipo'];
		$obPaciente->cid1 = $postVars['cid1'] ?? null;
		$obPaciente->cid2 = $postVars['cid2'] ?? null;
		$obPaciente->substanciaPri = $postVars['substanciaPri'] ?? null;
		$obPaciente->substanciaSec = $postVars['substanciaSec'] ?? null;
		$obPaciente->obs = Funcoes::convertePriMaiuscula($postVars['obs']) ?? '';
		
		
		//Verifica se o Paciente já está cadastrado no Banco
		$duplicado = EntityPaciente::getPacienteDuplicado(date('Y-m-d',strtotime($postVars['dataNasc'])), $postVars['nome']);
		
		if($duplicado instanceof EntityPaciente){
			//Redireciona para o paciente já existente
			$request->getRouter()->redirect('/admin/pacientes/'.$duplicado->codPronto.'/edit?statusMessage=duplicad');
		}
		
		$obPaciente->cadastrar();
	
		//Grava o Log do usuário
		Logs::setNewLog('pacientes', 'Novo' , $obPaciente->codPronto.' '.$obPaciente->nome);
		
		//Redireciona o usuário
		$request->getRouter()->redirect('/admin/pacientes/'.$obPaciente->codPronto.'/edit?statusMessage=created');
		
	}
	
	
	
	//Metodo responsávelpor retornar o formulário de Edição de um Paciente
	public static function getEditPaciente($request,$codPronto){

		//obtém o Paciente do banco de dados
		$obPaciente = EntityPaciente::getPacienteByCodPronto($codPronto);
		
		//Valida a instancia
		if(!$obPaciente instanceof EntityPaciente){
			$request->getRouter()->redirect('/admin/pacientes');
		}

		//Conteúdo do Formulário
		$content = View::render('admin/modules/pacientes/form',[
		        'prontuario'=> str_pad($obPaciente->codPronto,4,"0",STR_PAD_LEFT),
				'id' => $obPaciente->id,
				'hidden' =>'', //exibe botão atendimentos
				'title' => 'Editar',
				'nome' => $obPaciente->nome,
				'cep' => $obPaciente->cep,
				'endereco' => $obPaciente->endereco,
				'cartaoSus' => $obPaciente->cartaoSus,
				'statusMessage' => self::getStatus($request),
				'naturalidade' => $obPaciente->naturalidade,
				'fone1' => $obPaciente->fone1,
				'fone2' => $obPaciente->fone2,
				'mae' => $obPaciente->mae,
				'obs' => $obPaciente->obs,
				'bairro' => $obPaciente->bairro,
				'cidade' => $obPaciente->cidade,
				'uf' => $obPaciente->uf,
				'dataNasc' => date('Y-m-d', strtotime($obPaciente->dataNasc)),
				'dataCad' => date('Y-m-d', strtotime($obPaciente->dataCad)),
				'selectedSexoM' => $obPaciente->sexo === 'MAS' ? 'selected' : '',
				'selectedSexoF' => $obPaciente->sexo === 'FEM' ? 'selected' : '',
				'optionEscolaridade' => EntityEscolaridade::getSelectEscolaridade($obPaciente->escolaridade),
				'optionEstadoCivil' => EntityEstadoCivil::getSelectEstadoCivil($obPaciente->estadoCivil),
				'optionProcedencia' => EntityProcedencia::getSelectProcedencia($obPaciente->procedencia),
				'selectedStatusA' => $obPaciente->status === 'Ativo' ? 'selected' : '',
				'selectedStatusI' => $obPaciente->status === 'Inativo' ? 'selected' : '',
				'optionMotivoInativo' => EntityMotivoInativo::getSelectMotivoInativo($obPaciente->motivoInativo),
				'selectedTipoA' => $obPaciente->tipo === 'Ad' ? 'selected' : '',
				'selectedTipoT' => $obPaciente->tipo === 'Tm' ? 'selected' : '',
				'optionCid10-1' => Entitycid10::getSelectCid10($obPaciente->cid1),
				'optionCid10-2' => Entitycid10::getSelectCid10($obPaciente->cid2),
				'optionSubstanciaPri' => EntitySubstancia::getSelectSubstancia($obPaciente->substanciaPri),
				'optionSubstanciaSec' => EntitySubstancia::getSelectSubstancia($obPaciente->substanciaSec),
				'optionBairros' => EntityBairro::getSelectBairros($obPaciente->bairro),
				

				
				
		]);
		
		//Retorna a página completa
		return parent::getPanel('Editar Paciente > WDEV', $content,'pacientes', self::$hidden);
		
	}
	
	//Metodo responsável por gravar a atualização de um Paciente
	public static function setEditPaciente($request,$codPronto){
		//obtém o deopimento do banco de dados
		$obPaciente = EntityPaciente::getPacienteByCodPronto($codPronto);
		
		//Valida a instancia
		if(!$obPaciente instanceof EntityPaciente){
			$request->getRouter()->redirect('/admin/pacientes');
		}
		
		//Post Vars
		$postVars = $request->getPostVars();

		//redireciona caso seja feita busca rápida pelo prontuário
		if(@$postVars['pront']){
			$request->getRouter()->redirect('/admin/pacientes/'.@$postVars['pront'].'/edit');
		}
		
		
		//Atualiza a instância
		$obPaciente->nome = Funcoes::convertePriMaiuscula($postVars['nome']) ?? $obPaciente->nome;
		$obPaciente->cep = $postVars['cep'] ?? $obPaciente->cep;
		$obPaciente->endereco = $postVars['endereco'] ?? $obPaciente->endereco;
		$obPaciente->bairro =  $postVars['bairro'] ?? $obPaciente->bairro;
		$obPaciente->cidade = $postVars['cidade'] ?? $obPaciente->cidade;
		$obPaciente->uf = $postVars['uf'] ?? $obPaciente->uf;
		$obPaciente->dataNasc = implode("-",array_reverse(explode("/",$postVars['dataNasc']))); 
		$obPaciente->dataCad = implode("-",array_reverse(explode("/",$postVars['dataCad'])));
		$obPaciente->sexo = $postVars['sexo'] ?? $obPaciente->sexo;
		$obPaciente->naturalidade = $postVars['naturalidade'] ?? $obPaciente->naturalidade;
		$obPaciente->escolaridade = $postVars['escolaridade'] ?? $obPaciente->escolaridade;
		$obPaciente->fone1 = $postVars['fone1'] ?? $obPaciente->fone1;
		$obPaciente->fone2 = $postVars['fone2'] ?? $obPaciente->fone2;
		$obPaciente->mae = $postVars['mae'] ?? $obPaciente->mae;
		$obPaciente->estadoCivil = $postVars['estadoCivil'] ?? $obPaciente->estadoCivil;
		$obPaciente->procedencia = $postVars['procedencia'] ?? $obPaciente->procedencia;
		$obPaciente->status = $postVars['status'] ?? $obPaciente->status;
		$obPaciente->motivoInativo = $postVars['motivoInativo'] ?? $obPaciente->motivoInativo;
		$obPaciente->cartaoSus = $postVars['cartaoSus'] ?? $obPaciente->cartaoSus;
		$obPaciente->tipo = $postVars['tipo'] ?? $obPaciente->tipo;
		$obPaciente->cid1 = $postVars['cid1'] ?? $obPaciente->cid1;
		$obPaciente->cid2 = $postVars['cid2'] ?? $obPaciente->cid2;
		$obPaciente->substanciaPri = $postVars['substanciaPri'] ?? $obPaciente->substanciaPri;
		$obPaciente->substanciaSec = $postVars['substanciaSec'] ?? $obPaciente->substanciaSec;
		$obPaciente->obs = $postVars['obs'] ?? $obPaciente->obs;
		$obPaciente->atualizar();
		
	//	Logs::setNewLog($request);
		
		//Redireciona o usuário
		$request->getRouter()->redirect('/admin/pacientes/'.$obPaciente->codPronto.'/edit?statusMessage=updated');
		
	}
	
	//Método responsavel por retornar a mensagem de status
	private static function getStatus($request){
		//Query PArams
		$queryParams = $request->getQueryParams();
		
		//Status
		if(!isset($queryParams['statusMessage'])) return '';
		
		//Mensagens de status
		switch ($queryParams['statusMessage']) {
			case 'created':
				return Alert::getSuccess('Paciente criado com sucesso!');
				break;
			case 'updated':
				return Alert::getSuccess('Paciente atualizado com sucesso!');
				break;
			case 'deleted':
				return Alert::getSuccess('Paciente excluído com sucesso!');
				break;
			case 'duplicad':
				return Alert::getError('Paciente Já cadastrado!');
				break;
			case 'deletedfail':
				return Alert::getError('Você não tem permissão para Excluir! Contate o administrador.');
				break;
		}
	}
	
	
	//Metodo responsávelpor retornar o formulário de Exclusão de um Paciente
	public static function getDeletePaciente($request,$codPronto){
		//obtém o deopimento do banco de dados
		$obPaciente = EntityPaciente::getPacienteByCodPronto($codPronto);
		
		//Valida a instancia
		if(!$obPaciente instanceof EntityPaciente){
			$request->getRouter()->redirect('/admin/pacientes');
		}
		
		
		//Conteúdo do Formulário
		$content = View::render('admin/modules/pacientes/delete',[
				'nome' => $obPaciente->nome
			
				
		]);
		
		//Retorna a página completa
		return parent::getPanel('Excluir Paciente > WDEV', $content,'pacientes', self::$hidden);
		
	}
	
	//Metodo responsável por Excluir um Paciente
	public static function setDeletePaciente($request,$codPronto){
		
		
		//apenas o administrador pode excluir
		if ($_SESSION['admin']['usuario']['tipo'] == 'Operador'){
			//Redireciona o usuário
			$request->getRouter()->redirect('/admin/pacientes?statusMessage=deletedfail');
		}
		
		
		//obtém o paciente do banco de dados
		$obPaciente = EntityPaciente::getPacienteByCodPronto($codPronto);
		
		//Valida a instancia
		if(!$obPaciente instanceof EntityPaciente){
			$request->getRouter()->redirect('/admin/pacientes');
		}
		
		//Exclui o depoimento
		$obPaciente->excluir();
		
		//Redireciona o usuário
		$request->getRouter()->redirect('/admin/pacientes?statusMessage=deleted');
		
		
	}
	
	
	
}