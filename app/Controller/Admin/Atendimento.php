<?php

namespace App\Controller\Admin;

use \App\Utils\View;
use \App\Model\Entity\Paciente as EntityPaciente;
use \App\Model\Entity\Atendimento as EntityAtendimento;
use \App\Model\Entity\AtendimentoAvulso as EntityAtendimentoAvulso;
use \App\Model\Entity\Profissional as EntityProfissional;
use \App\Model\Entity\Procedimento as EntityProcedimento;
use \WilliamCosta\DatabaseManager\Pagination;

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
			$resultados .= View::render('admin/modules/atendimentos/item',[
				
				
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
	
	
	//Método responsavel por retornar a mensagem de status
	private static function getStatus($request){
		//Query PArams
		$queryParams = $request->getQueryParams();
		
		//Status
		if(!isset($queryParams['statusMessage'])) return '';
		
		//Mensagens de status
		switch ($queryParams['statusMessage']) {
			case 'created':
				return Alert::getSuccess('Atendimento criado com sucesso!');
				break;
			case 'updated':
				return Alert::getSuccess('Atendimento atualizado com sucesso!');
				break;
			case 'deleted':
				return Alert::getSuccess('Atendimento excluído com sucesso!');
				break;
			case 'duplicad':
				return Alert::getError('Atendimento duplicado!');
				break;
			case 'notFound':
				return Alert::getError('Paciente não encontrado!');
				break;
			case 'deletedfail':
				return Alert::getError('Você não tem permissão para Excluir Atendimentos de outro profissional! Contate o administrador.');
				break;
		}
	}
	
	//Método responsavel por renderizar a view de Listagem de Atendimentos
	public static function getAtendimentos($request,$id){
		
		$queryParams = $request->getQueryParams();
		
		if(isset($queryParams['pront'])){
			$request->getRouter()->redirect('/admin/atendimentos/'.$queryParams['pront'].'/atendimento');
		}
		
		//Post Vars
		$postVars = $request->getPostVars();
		
		
		
		$idProf = $queryParams['idProf'] ?? null;	
		if(isset($queryParams['data'])){
			$data = date('Y-m-d',strtotime($queryParams['data']));}else{$data = null;}
		$proced = $queryParams['proced'] ?? null;
	
		
		
		
		//obtém o Paciente do banco de dados
		$codPronto = $id;
		$obPaciente = EntityPaciente::getPacienteByCodPronto($codPronto);
		
		//Valida a instancia
		if(!$obPaciente instanceof EntityPaciente){
			$request->getRouter()->redirect('/admin/atendimentos/0001/atendimento?statusMessage=notFound');
		}
		
		//obtém o  do banco de dados
	//	$obAtendimento = EntityAtendimento::getAtendimentoByCodPronto($id);
		
		//Valida a instancia
	//	if(!$obAtendimento instanceof EntityAtendimento){
	//		$request->getRouter()->redirect('/admin/pacientes');
	//	}
		$obPaciente->status == 'Ativo' ? $alert = 'success' : $alert = 'danger';
		//Conteúdo da Home
		$content = View::render('admin/modules/atendimentos/index',[
				
				'itens' => self::getAtendimentosItems($request,$obPagination,$id),
				'pagination' => parent::getPagination($request, $obPagination),
				'totalAtendimentos' => self::$qtdTotal,
				'nome' => $obPaciente->nome,
				'prontuario' => str_pad($obPaciente->codPronto,4,"0",STR_PAD_LEFT), 
				'status' => $obPaciente->status,
		        'tipoAlert' => $alert,
				'id'=>$obPaciente->id,
				'statusMessage' => self::getStatus($request),
				'data' => $data,
				//'profissional' => $postVars['profissional'] ?? '',
				//'procedimento' => $postVars['procedimento'] ?? '',
				'selectedP' => '',
				'selectedF' => '',
				'optionProfissional' => self::getProfissionais($idProf,'status = 1'),//status = 1 => funcionários ativos
				'optionProcedimento' => self::getProcedimentos($proced),
				'acao' => 'Novo',
				'selectedP' => 'selected',

		]);
		
		
		
		//Retorna a página completa
		return parent::getPanel('Atendimentos > CAPS', $content,'atendimentos', self::$hidden);
		
	}
	
	
	public static function calcularIdade($data){
		$idade = 0;
		$data_nascimento = date('Y-m-d', strtotime($data));
		$data = explode("-",$data_nascimento);
		$anoNasc    = $data[0];
		$mesNasc    = $data[1];
		$diaNasc    = $data[2];
		
		$anoAtual   = date("Y");
		$mesAtual   = date("m");
		$diaAtual   = date("d");
		
		$idade      = $anoAtual - $anoNasc;
		if ($mesAtual < $mesNasc){
			$idade -= 1;
		} elseif ( ($mesAtual == $mesNasc) && ($diaAtual <= $diaNasc) ){
			$idade -= 1;
		}
		
		return $idade;
	}
	
	
	//Metodo responsável por gravar a Reativar Paciente
	public static function getReativaPaciente($codPronto){
	    //obtém o deopimento do banco de dados
	    $obPaciente = EntityPaciente::getPacienteByCodPronto($codPronto);
	    
	    //Atualiza a instância
	   $obPaciente->status = 'Ativo';
       $obPaciente->atualizar();
	    
	}
	
	
	public static function getInsertAtendimento($request){
		
		//Post vars
		$postVars = $request->getPostVars();
		
	//	var_dump($postVars);exit;
		
		//redireciona caso seja feita busca rápida pelo prontuário
		if(@$postVars['pront']){
			$request->getRouter()->redirect('/admin/atendimentos/'.@$postVars['pront'].'/atendimento');
		}
		
		$data = implode('-', array_reverse(explode('/', $postVars['data'])));
		//Nova instância de paciente
		$obAtendimento = new EntityAtendimento;
		$obAtendimento->codPronto = $postVars['codPronto'];
		$obAtendimento->data =$data;
		$obAtendimento->idProfissional = $postVars['profissional'];
		$obAtendimento->idProcedimento = $postVars['procedimento'];
		$obAtendimento->status = strtoupper($postVars['status']);
		$obAtendimento->idade = self::calcularIdade(date('d/m/Y',strtotime(EntityPaciente::getPacienteByCodPronto($postVars['codPronto'])->dataNasc)));
		
		//Verifica se o atendimento está existe no banco de dados
		$duplicado = EntityAtendimento::getAtendimentoDuplicado(date('Y-m-d',strtotime($postVars['data'])), $postVars['codPronto'], $postVars['profissional'], $postVars['procedimento']);
		if($duplicado instanceof EntityAtendimento){
			//Redireciona o usuário em caso de existir
			$request->getRouter()->redirect('/admin/atendimentos/'.$obAtendimento->codPronto.'/atendimento?statusMessage=duplicad&idProf='.$obAtendimento->idProfissional.'&data='.$data.'&proced='.$postVars['procedimento']);
		}
		

		$obAtendimento->cadastrar();
		
		
		//Reativa o paciente se estivar Inativo
		if (EntityPaciente::getPacienteByCodPronto($obAtendimento->codPronto)->status == 'Inativo' ){
		    self::getReativaPaciente($obAtendimento->codPronto);
		}
		
	
		//Redireciona o usuário
		$request->getRouter()->redirect('/admin/atendimentos/'.$obAtendimento->codPronto.'/atendimento?statusMessage=created&idProf='.$obAtendimento->idProfissional.'&datas='.$data.'');
	}
	
	public static function getEditAtendimento($request, $codPronto, $id){
		//obtém o deopimento do banco de dados
		$obAtendimento = EntityAtendimento::getAtendimentoById($id);
		//Post Vars
		$postVars = $request->getPostVars();
		
		
	
		//Valida a instancia
		if(!$obAtendimento instanceof EntityAtendimento){
			$request->getRouter()->redirect('/admin/pacientes');
		}
		
		
		//obtém o Paciente do banco de dados
		$obPaciente = EntityPaciente::getPacienteByCodPronto($codPronto);
		
		//Valida a instancia
		if(!$obPaciente instanceof EntityPaciente){
			$request->getRouter()->redirect('/admin/pacientes');
		}
		strtoupper($obAtendimento->status) == 'P' ? $selectedP = 'selected' : $selectedP = '';
		strtoupper($obAtendimento->status) == 'F' ? $selectedF = 'selected' : $selectedF = '';
		
		
		//Conteúdo da Home
		$content = View::render('admin/modules/atendimentos/index',[
				
				'itens' => self::getAtendimentosItems($request,$obPagination,$codPronto),
				'status' => $obPaciente->status,
				'pagination' => parent::getPagination($request, $obPagination),
				'totalAtendimentos' => self::$qtdTotal,
				'nome' => $obPaciente->nome,
				'prontuario' => $obAtendimento->codPronto,
				'statusMessage' => self::getStatus($request),
				'data' => date('Y-m-d',strtotime($obAtendimento->data)),
				//'procedimento' => EntityProcedimento::getProcedimentoById($obAtendimento->idProcedimento)->nome,
				'selectedP' => $selectedP,
				'selectedF' => $selectedF,
				'optionProfissional' => self::getProfissionais($obAtendimento->idProfissional, null),
				'optionProcedimento' => self::getProcedimentos($obAtendimento->idProcedimento),
				'acao' => 'Editar',
				'navBar'=>View::render('admin/navBar',[])
				
		]);
		
		
		
		//Retorna a página completa
		return parent::getPanel('Atendimentos > CAPS', $content,'atendimentos', self::$hidden);
		
	//	var_dump($obAtendimento);exit;
	//	$obPaciente->atualizar();
		
		//Redireciona o usuário
	//	$request->getRouter()->redirect('/admin/pacientes/'.$obAtendimento->id.'/edit?statusMessage=updated');
		
	}
	
	//Metodo responsável por gravar a atualização de um Atendimento
	public static function setEditAtendimento($request,$codPronto,$id){
		//obtém o deopimento do banco de dados
		$obAtendimento = EntityAtendimento::getAtendimentoById($id);
		//Valida a instancia
		if(!$obAtendimento instanceof EntityAtendimento){
			$request->getRouter()->redirect('/admin/pacientes');
		}
		
		//Post Vars
    	$postVars = $request->getPostVars();
			//Atualiza a instância
			
    
    	$data = implode('-', array_reverse(explode('/', $postVars['data'])));
    	$obAtendimento->data = $data;
		$obAtendimento->idProfissional = $postVars['profissional'];
		$obAtendimento->idProcedimento = $postVars['procedimento'];
		$obAtendimento->status = strtoupper($postVars['status']);
		
	
		
		$obAtendimento->atualizar();
		
		//Redireciona o usuário
		$request->getRouter()->redirect('/admin/atendimentos/'.$obAtendimento->codPronto.'/atendimento?statusMessage=updated');
	}
	
	//Método responsavel por listar os Profissionais 
	public static function getProfissionais($id,$status){
		$resultados = '';
		$results =  EntityProfissional::getProfissionais($status,'nome asc',null);
		//verifica se o id não é nulo e obtém a Escolaridade do banco de dados
		if (!is_null($id)) {
			$selected = '';
			while ($obProfissional = $results -> fetchObject(EntityProfissional::class)) {
				
				//seleciona as Escolaridades do paciente
				$obProfissional->id == $id ? $selected = 'selected' : $selected = '';
				//View de as Escolaridades
				$resultados .= View::render('admin/modules/pacientes/itemSelect',[
						'id' => $obProfissional ->id,
						'nome' => $obProfissional->nome,
						'selecionado' => $selected
				]);
			}
			//retorna os as Escolaridades
			return $resultados;
		}else{ //se as Escolaridades for nulo, lista todos e seleciona um em branco
			while ($obProfissional = $results -> fetchObject(EntityProfissional::class)) {
			//	$obProfissional->nome == 'Não Informado' ? $selected = 'selected' : $selected = '';
				$resultados .= View::render('admin/modules/pacientes/itemSelect',[
						'id' => $obProfissional ->id,
						'nome' => $obProfissional->nome,
						'selecionado' => ''
				]);
			}
			//retorna os as Escolaridades
			return $resultados;
		}
	}
	
	
	//Método responsavel por listar os Procedimentos
	public static function getProcedimentos($id){
		$resultados = '';
		$results =  EntityProcedimento::getprocedimentos(null,'nome asc',null);
		//verifica se o id não é nulo e obtém a Procedimento do banco de dados
		if (!is_null($id)) {
			$selected = '';
			while ($obProcedimento = $results -> fetchObject(EntityProcedimento::class)) {
				
				//seleciona o procedimento do atendimento
				$obProcedimento->id == $id ? $selected = 'selected' : $selected = '';
				//View de as Escolaridades
				$resultados .= View::render('admin/modules/pacientes/itemSelect',[
						'id' => $obProcedimento ->id,
						'nome' => $obProcedimento->nome,
						'selecionado' => $selected
				]);
			}
			//retorna os Procedimentos
			return $resultados;
		}else{ //se o procedimento for nulo, lista todos e seleciona um em branco
			while ($obProcedimento = $results -> fetchObject(EntityProcedimento::class)) {
				//	$obProfissional->nome == 'Não Informado' ? $selected = 'selected' : $selected = '';
				$resultados .= View::render('admin/modules/pacientes/itemSelect',[
						'id' => $obProcedimento ->id,
						'nome' => $obProcedimento->nome,
						'selecionado' => ''
				]);
			}
			//retorna os as Escolaridades
			return $resultados;
		}
	}
	
	//Metodo responsávelpor retornar o formulário de Exclusão de um Paciente
	public static function getDeleteAtendimento($request,$id){
		
		//obtém o deopimento do banco de dados
		$obAtendimento = EntityAtendimento::getAtendimentoById($id);
		
		//Valida a instancia
		if(!$obAtendimento instanceof EntityAtendimento){
			$request->getRouter()->redirect('/admin/atendimentos/'.$obAtendimento->codPronto.'/atendimento');
		}
		
		
		//Conteúdo do Formulário
		$content = View::render('admin/modules/atendimentos/delete',[
				'data' => date('d/m/Y', strtotime($obAtendimento->data)),
				'profissional' => EntityProfissional::getProfissionalById($obAtendimento->idProfissional)->nome,
				'procedimento' => EntityProcedimento::getProcedimentoById($obAtendimento->idProcedimento)->nome,
				'codPronto' => $obAtendimento->codPronto,
				'status_qtd' => 'status',
				'status_qtd_val' => $obAtendimento->status,
				'title' => 'Excluir',
				'voltar' => '/admin/atendimentos/'.$obAtendimento->codPronto.'/atendimento',
				'statusMessage' => self::getStatus($request)
				
				
		]);
		
		//Retorna a página completa
		return parent::getPanel('Excluir Atendimento', $content,'atendimentos', 'hidden');
		
	}
	
	//Metodo responsável por Excluir um Paciente
	public static function setDeleteAtendimento($request,$id){
		
		
		//obtém o paciente do banco de dados
		$obAtendimento = EntityAtendimento::getAtendimentoById($id);
		
		//armazena o cpf do usuário que está logado
		$cpfLogado = ($_SESSION['admin']['usuario']['cpf']);
		
		//Valida a instancia
		if(!$obAtendimento instanceof EntityAtendimento){
			$request->getRouter()->redirect('/admin/atendimentos/'.$obAtendimento->codPronto.'/atendimento');
		}

		//Verifica se o usuário logado é o mesmo do atendimento que será excluído (se não for não conclui a exclusão )
		if($_SESSION['admin']['usuario']['tipo'] == 'Operador'){
			if($cpfLogado != EntityProfissional::getProfissionalById($obAtendimento->idProfissional)->cpf){
				$request->getRouter()->redirect('/admin/atendimentos/'.$id.'/delete?statusMessage=deletedfail');
			}
		}
		
		//Exclui o depoimento
		$obAtendimento->excluir();
		
		//Redireciona o usuário
		$request->getRouter()->redirect('/admin/atendimentos/'.$obAtendimento->codPronto.'/atendimento?statusMessage=deleted');
		
		
	}
	
	
	//Método responsavel por obter a rendereizacao dos pacientes para a página
	private static function getAtendimentosItemsAvulsos($request, &$obPagination){
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
		$where = implode(' AND ', $condicoes);
	
		
		//Quantidade total de registros
		// $quantidadeTotal = EntityPaciente::getPacientes($where, null,null,'COUNT(*) as qtd')->fetchObject()->qtd;
		
		self::$qtdTotal = EntityAtendimentoAvulso::getAtendimentosAvulsos($where, 'id DESC',null,'COUNT(*) as qtd')->fetchObject()->qtd;
		
		//Instancia de paginação
		$obPagination = new Pagination(self::$qtdTotal,$paginaAtual,5);
		#############################################
		
		
		//Obtem os pacientes
		$results = EntityAtendimentoAvulso::getAtendimentosAvulsos($where, 'data DESC', $obPagination->getLimit());
		
		
		//	var_dump($results); exit;
		
		//Renderiza
		while ($obAtendimento = $results -> fetchObject(EntityAtendimentoAvulso::class)) {
			
			//View de pacientes
			$resultados .= View::render('admin/modules/atendimentos/itemAvulso',[
					
					'data' =>  date('d/m/Y', strtotime($obAtendimento->data)),
					'idProfissional' => EntityProfissional::getProfissionalById($obAtendimento->idProfissional)->nome,
					'idProcedimento' => EntityProcedimento::getProcedimentoById($obAtendimento->idProcedimento)->nome,
					'qtd' => $obAtendimento->qtd,
					'id' => $obAtendimento->id,
			]);
		}
		//Retorna os pacientes
		return $resultados;
		//var_dump($where);exit;
	}
	
	
	//Método responsavel por renderizar a view de Listagem de Atendimentos
	public static function getAtendimentosAvulso($request){
		
		//Post Vars
		$postVars = $request->getPostVars();
		$idProf = $queryParams['idProf'] ?? null;
		if(isset($queryParams['data'])){
			$data = date('Y-m-d',strtotime($queryParams['data']));}else{$data = null;}
			$proced = $queryParams['proced'] ?? null;
		
		
				//Conteúdo da paǵina avulsos
				$content = View::render('admin/modules/atendimentos/avulso',[
						
						'itens' => self::getAtendimentosItemsAvulsos($request,$obPagination),
						'pagination' => parent::getPagination($request, $obPagination),
						'totalAtendimentos' => self::$qtdTotal,
						'statusMessage' => self::getStatus($request),
						'selectedP' => '',
						'selectedF' => '',
						'optionProfissional' => self::getProfissionais($idProf,'status = 1'),//status = 1 => funcionários ativos
						'optionProcedimento' => self::getProcedimentoAvulso($proced),
						'acao' => 'Novo Avulso',
						
						'qtd' => ''
				]);
				
				
				
				//Retorna a página completa
				return parent::getPanel('Atendimentos > CAPS', $content,'atendimentos', self::$hidden);
				
	}

	public static function getInsertAtendimentoAvulso($request){
		
		$postVars = $request->getPostVars();
		
		$data = implode('-', array_reverse(explode('/', $postVars['data'])));
		//Nova instância de paciente
		$obAtendimentoAvulso = new EntityAtendimentoAvulso();

		$obAtendimentoAvulso->data =$data;
		$obAtendimentoAvulso->idProfissional = $postVars['profissional'];
		$obAtendimentoAvulso->idProcedimento = $postVars['procedimento'];
		$obAtendimentoAvulso->qtd = $postVars['qtd'];
		$obAtendimentoAvulso->cadastrar();
		
		//Redireciona o usuário
		$request->getRouter()->redirect('/admin/atendimentos/avulso?statusMessage=created');
	}
	
	
	public static function getEditAtendimentoAvulso($request, $id){
		//obtém o deopimento do banco de dados
		$obAtendimentoAvulso = EntityAtendimentoAvulso::getAtendimentoAvulsoById($id);
		//Post Vars
		$postVars = $request->getPostVars();
		
	
		
		//Valida a instancia
		if(!$obAtendimentoAvulso instanceof EntityAtendimentoAvulso){
			$request->getRouter()->redirect('/admin/atendimentos/avulso');
		}
		
		
		
		//Conteúdo da Home
		$content = View::render('admin/modules/atendimentos/avulso',[
				
				'itens' => self::getAtendimentosItemsAvulsos($request,$obPagination),
				'pagination' => parent::getPagination($request, $obPagination),
				'totalAtendimentos' => self::$qtdTotal,
				'statusMessage' => self::getStatus($request),
				'data' => date('Y-m-d',strtotime($obAtendimentoAvulso->data)),
				'optionProfissional' => self::getProfissionais($obAtendimentoAvulso->idProfissional, null),
				'optionProcedimento' => self::getProcedimentos($obAtendimentoAvulso->idProcedimento),
				'acao' => 'Editar Avulso',
				'qtd' => $obAtendimentoAvulso->qtd
		]);
		
		//Retorna a página completa
		return parent::getPanel('Atendimentos > CAPS', $content,'atendimentos', self::$hidden);
		
	}
	
	//Metodo responsável por gravar a atualização de um Atendimento Avulso
	public static function setEditAtendimentoAvulso($request,$id){
		//obtém o deopimento do banco de dados
		$obAtendimentoAvulso = EntityAtendimentoAvulso::getAtendimentoAvulsoById($id);
		
		//Valida a instancia
		if(!$obAtendimentoAvulso instanceof EntityAtendimentoAvulso){
			$request->getRouter()->redirect('/admin/atendimentos/avulso');
		}
		
		//Post Vars
		$postVars = $request->getPostVars();
	
		//Atualiza a instância
		$data = implode('-', array_reverse(explode('/', $postVars['data'])));
		$obAtendimentoAvulso->data = $data;
		$obAtendimentoAvulso->idProfissional = $postVars['profissional'];
		$obAtendimentoAvulso->idProcedimento = $postVars['procedimento'];
		$obAtendimentoAvulso->qtd = $postVars['qtd'];
		$obAtendimentoAvulso->atualizar();
		
		//Redireciona o usuário
		$request->getRouter()->redirect('/admin/atendimentos/avulso/'.$obAtendimentoAvulso->id.'/edit?statusMessage=updated');
	}
	
	//Metodo responsávelpor retornar o formulário de Exclusão de um Atendimento Avulso
	public static function getDeleteAtendimentoAvulso($request,$id){
		
		//obtém o Atendimento avulso do banco de dados
		$obAtendimentoAvulso = EntityAtendimentoAvulso::getAtendimentoAvulsoById($id);
		
		//Valida a instancia
		if(!$obAtendimentoAvulso instanceof EntityAtendimentoAvulso){
			$request->getRouter()->redirect('/admin/atendimentos/avulso');
		}
		
		
		//Conteúdo do Formulário
		$content = View::render('admin/modules/atendimentos/delete',[
				'data' => date('d/m/Y', strtotime($obAtendimentoAvulso->data)),
				'profissional' => EntityProfissional::getProfissionalById($obAtendimentoAvulso->idProfissional)->nome,
				'procedimento' => EntityProcedimento::getProcedimentoById($obAtendimentoAvulso->idProcedimento)->nome,
				'status_qtd' => 'qtd',
				'codPronto' => '',
				'status_qtd_val' => $obAtendimentoAvulso->qtd,
				'title' => 'Excluir Avulso',
				'voltar' => '/admin/atendimentos/avulso'
				
				
		]);
		
		//Retorna a página completa
		return parent::getPanel('Excluir Atendimento Avulso', $content,'atendimentos', 'hidden');
		
	}
	
	//Metodo responsável por Excluir um Paciente
	public static function setDeleteAtendimentoAvulso($request,$id){
		
		//obtém o paciente do banco de dados
		$obAtendimentoAvulso = EntityAtendimentoAvulso::getAtendimentoAvulsoById($id);
		
		//Valida a instancia
		if(!$obAtendimentoAvulso instanceof EntityAtendimentoAvulso){
			$request->getRouter()->redirect('/admin/modules/atendimentos/avulso');
		}
		
		//Exclui o depoimento
		$obAtendimentoAvulso->excluir($id);
		
		//Redireciona o usuário
		$request->getRouter()->redirect('/admin/atendimentos/avulso?statusMessage=deleted');
		
		
	}
	
	//Método responsavel por listar os Procedimentos
	public static function getProcedimentoAvulso($id){
		$resultados = '';
		$results =  EntityProcedimento::getprocedimentos('id = 16','nome asc',null);
		//verifica se o id não é nulo e obtém a Procedimento do banco de dados
		if (!is_null($id)) {
			$selected = '';
			while ($obProcedimento = $results -> fetchObject(EntityProcedimento::class)) {
				
				//seleciona o procedimento do atendimento
				$obProcedimento->id == $id ? $selected = 'selected' : $selected = '';
				//View de as Escolaridades
				$resultados .= View::render('admin/modules/pacientes/itemSelect',[
						'id' => $obProcedimento ->id,
						'nome' => $obProcedimento->nome,
						'selecionado' => $selected
				]);
			}
			//retorna os Procedimentos
			return $resultados;
		}else{ //se o procedimento for nulo, lista todos e seleciona um em branco
			while ($obProcedimento = $results -> fetchObject(EntityProcedimento::class)) {
				//	$obProfissional->nome == 'Não Informado' ? $selected = 'selected' : $selected = '';
				$resultados .= View::render('admin/modules/pacientes/itemSelect',[
						'id' => $obProcedimento ->id,
						'nome' => $obProcedimento->nome,
						'selecionado' => ''
				]);
			}
			//retorna os as Escolaridades
			return $resultados;
		}
	}
	
}

