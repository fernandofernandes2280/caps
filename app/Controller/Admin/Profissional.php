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
use Bissolli\ValidadorCpfCnpj\CPF;


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
		$filtroStatus = in_array($filtroStatus, ['1','0']) ? $filtroStatus : '';
		
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
			if($queryParams['status'] == '1')$selectedAtivo = 'selected';
			else if($queryParams['status'] == '0') $selectedInativo = 'selected';
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
	
	
	//Metodo responsávelpor retornar o formulário de Edição de um Profissional
	public static function getEditProfissional($request,$id){
	    
	    //obtém o Profissional do banco de dados
	    $obProfissional = EntityProfissional::getProfissionalById($id);
	    
	    //Valida a instancia
	    if(!$obProfissional instanceof EntityProfissional){
	        $request->getRouter()->redirect('/admin/profissionais');
	    }
	    
	    $obProfissional->tipo == 'Admin' ? $selectedAdmin = 'selected' : $selectedAdmin = '' ;
	    $obProfissional->tipo == 'Visitante' ? $selectedVisitante = 'selected' : $selectedVisitante = '' ;
	    $obProfissional->tipo == 'Operador' ? $selectedOperador = 'selected' : $selectedOperador = '' ;
	    
	    
	    //Conteúdo do Formulário
	    $content = View::render('admin/modules/profissionais/form',[
	       
	        'id' => $obProfissional->id,
	        'title' => 'Editar',
	        'nome' => $obProfissional->nome,
	        'cep' => $obProfissional->cep,
	        'endereco' => $obProfissional->endereco,
	        'cartaoSus' => $obProfissional->cartaoSus,
	        'statusMessage' => self::getStatus($request),
	        'fone' => $obProfissional->fone,
	        'bairro' => $obProfissional->bairro,
	        'cidade' => $obProfissional->cidade,
	        'uf' => $obProfissional->uf,
	        'cbo' => $obProfissional->cbo,
	        'cpf' => Funcoes::mask($obProfissional->cpf, '###.###.###-##') ,
	        'funcao' => $obProfissional->funcao,
	        'dataNasc' => date('Y-m-d', strtotime($obProfissional->dataNasc)),
	        'selectedStatusA' => $obProfissional->status == 1 ? 'selected' : '',
	        'selectedStatusI' => $obProfissional->status == 0 ? 'selected' : '',
	        'optionBairros' => EntityBairro::getSelectBairros($obProfissional->bairro),
	        'email' => $obProfissional->email,
	        'senha' => $obProfissional->senha,
	        'selectedAdmin'=> $selectedAdmin,
	        'selectedVisitante'=> $selectedVisitante,
	        'selectedOperador'=> $selectedOperador,
	        
	    ]);
	    
	    //Retorna a página completa
	    return parent::getPanel('Editar Profissional > SISCAPS', $content,'profissionais', self::$buscaRapidaPront);
	    
	}
	
	//Metodo responsável por gravar a atualização de um Funcionário
	public static function setEditProfissional($request,$id){
	    
	    //obtém o funcionário do banco de dados
	    $obProfissional = EntityProfissional::getProfissionalById($id);
	    
	    //Valida a instancia
	    if(!$obProfissional instanceof EntityProfissional){
	        $request->getRouter()->redirect('/admin/profissionais');
	    }
	    
	    //Post Vars
	    $postVars = $request->getPostVars();
	    
	    //instancia classe pra verificar CPF
	    $validaCpf = new CPF($postVars['cpf']);
	    
	    //verifica se é válido o cpf
	    if (!$validaCpf->isValid()){
	        
	        $request->getRouter()->redirect('/admin/profissionais/'.$id.'/edit?status=cpfInvalido');
	    }
	    
	    
	    //busca usuário pelo CPF sem a maskara
	    $obProfissional = EntityProfissional::getUserByCPF($validaCpf->getValue());
	    
	    if($obProfissional instanceof EntityProfissional && $obProfissional->id != $id){
	        $request->getRouter()->redirect('/admin/profissionais/'.$id.'/edit?status=cpfDuplicated');
	    }
	    
	    
	    //Atualiza a instância
	    $obProfissional->nome = Funcoes::convertePriMaiuscula($postVars['nome']) ?? $obProfissional->nome;
	    $obProfissional->cep = $postVars['cep'] ?? $obProfissional->cep;
	    $obProfissional->endereco = $postVars['endereco'] ?? $obProfissional->endereco;
	    $obProfissional->bairro =  $postVars['bairro'] ?? $obProfissional->bairro;
	    $obProfissional->cidade = $postVars['cidade'] ?? $obProfissional->cidade;
	    $obProfissional->uf = $postVars['uf'] ?? $obProfissional->uf;
	    $obProfissional->cartaoSus = $postVars['cartaoSus'] ?? $obProfissional->cartaoSus;
	    $obProfissional->cbo = $postVars['cbo'] ?? $obProfissional->cbo;
	    $obProfissional->funcao = $postVars['funcao'] ?? $obProfissional->funcao;
	    $obProfissional->dataNasc = implode("-",array_reverse(explode("/",$postVars['dataNasc'])));
	    $obProfissional->cpf = $validaCpf->getValue(); //cpf sem formatação
	    $obProfissional->fone = $postVars['fone'] ?? $obProfissional->fone;
	    $obProfissional->status = $postVars['status'] ?? $obProfissional->status;
	    $obProfissional->atualizar();
	    
	    //	Logs::setNewLog($request);
	    
	    //Redireciona o usuário
	    $request->getRouter()->redirect('/admin/profissionais/'.$obProfissional->id.'/edit?statusMessage=updated');
	    
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
		return parent::getPanel('Cadastrar Pacientes > SISCAPS', $content,'pacientes', self::$hidden);
		
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
	
	

	
	
	//Método responsavel por retornar a mensagem de status
	private static function getStatus($request){
		//Query PArams
		$queryParams = $request->getQueryParams();
		
		//Status
		if(!isset($queryParams['statusMessage'])) return '';
		
		//Mensagens de status
		switch ($queryParams['statusMessage']) {
			case 'created':
				return Alert::getSuccess('Profissional criado com sucesso!');
				break;
			case 'updated':
				return Alert::getSuccess('Profissional atualizado com sucesso!');
				break;
			case 'deleted':
				return Alert::getSuccess('Profissional excluído com sucesso!');
				break;
			case 'duplicad':
				return Alert::getError('Profissional Já cadastrado!');
				break;
			case 'cpfDuplicated':
			    return Alert::getError('CPF já está sendo utilizado por outro usuário!');
			    break;
			case 'cpfInvalido':
			    return Alert::getError('CPF Inválido!');
			    break;
			case 'emailDuplicated':
			    return Alert::getError('E-mail já está sendo utilizado!');
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
		return parent::getPanel('Excluir Paciente > SISCAPS', $content,'pacientes', self::$hidden);
		
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