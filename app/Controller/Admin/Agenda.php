<?php

namespace App\Controller\Admin;

use \App\Utils\View;
use \App\Model\Entity\Paciente as EntityPaciente;
use \App\Model\Entity\Atendimento as EntityAtendimento;
use \App\Model\Entity\AtendimentoAvulso as EntityAtendimentoAvulso;
use \App\Model\Entity\Profissional as EntityProfissional;
use \App\Model\Entity\Procedimento as EntityProcedimento;
use \WilliamCosta\DatabaseManager\Pagination;
use \App\Model\Entity\Agenda as EntityAgenda;
use \App\Model\Entity\AgendaStatus as EntityAgendaStatus;
use \App\Model\Entity\AgendaItems as EntityAgendaItems;
use \App\Model\Entity\AgendaPresenca as EntityAgendaPresenca;

class Agenda extends Page{
	
	//Armazena quantidade total de pacientes listados
	private static $qtdTotal ;
	private static $totalGeralBpac = 0;
	//esconde busca rápida de prontuário no navBar
	private static $hidden = '';
	
	//Método responsavel por obter a rendereizacao dos pacientes para a página
	private static function getAgendasItems($request, &$obPagination){
		$resultados = '';
		
		//Pagina Atual
		$queryParams = $request->getQueryParams();
		$paginaAtual = $queryParams['page'] ?? 1;
	//	var_dump($queryParams);exit;
		//Armazena valor do profissional
		$profissional = $queryParams['profissional'] ?? '';
		//Filtro Status
		$filtroStatus = $queryParams['status'] ?? '';

		if (isset($queryParams['data']) && $queryParams['data'] != '' ){
			$filtroData = date('Y-m-d',strtotime($queryParams['data']));
		}else{
			$filtroData = '';
		}
		
		
		//Condições SQL
		$condicoes = [
				
				strlen($profissional) ? 'idProfissional = '.$profissional.' ' : null,
				strlen($filtroStatus) ? 'status = "'.$filtroStatus.'" ' : null,
				strlen($filtroData) ? 'data = "'.$filtroData.'" ' : null
		];
		
		//Remove posições vazias
		$condicoes = array_filter($condicoes);
		
		//cláusula where
		$where = implode(' AND ', $condicoes);
	//	$where = 'id = 2 ';
	//var_dump($where);exit;
		//Quantidade total de registros
		// $quantidadeTotal = EntityPaciente::getPacientes($where, null,null,'COUNT(*) as qtd')->fetchObject()->qtd;
		
		self::$qtdTotal = EntityAgenda::getAgendas($where, 'id DESC',null,'COUNT(*) as qtd')->fetchObject()->qtd;
		
		//Instancia de paginação
		$obPagination = new Pagination(self::$qtdTotal,$paginaAtual,5);
		#############################################
		
		
		//Obtem os pacientes
		$results = EntityAgenda::getAgendas($where, 'data DESC', $obPagination->getLimit());
		
		//Renderiza
		while ($obAgenda = $results -> fetchObject(EntityAgenda::class)) {
			
			//retorna a qtd de pacientes de cada agenda
			$qtdPacAgenda = EntityAgendaItems::getAgendaItems('idAgenda = '.$obAgenda->id.' ', 'id DESC',null,'COUNT(*) as qtd')->fetchObject()->qtd;

			//View de Agendas
			$resultados .= View::render('admin/modules/agendas/item',[

					'id' => $obAgenda->id,
					'data' =>  date('d/m/Y', strtotime($obAgenda->data)),
					'idProfissional' => EntityProfissional::getProfissionalById($obAgenda->idProfissional)->nome,
					'status' =>EntityAgendaStatus::getAgendaStatusById($obAgenda->status)->nome,
					'qtdPac' => $qtdPacAgenda
			]);
		}
		//Retorna as agendas
		return $resultados;

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
				return Alert::getSuccess('Agenda criada com sucesso!');
				break;
			case 'updated':
				return Alert::getSuccess('Agenda atualizada com sucesso!');
				break;
			case 'deleted':
				return Alert::getSuccess('Agenda excluída com sucesso!');
				break;
			case 'duplicad':
				return Alert::getError('Agenda duplicada!');
				break;
			case 'notFound':
				return Alert::getError('Agenda não encontrada!');
				break;
			case 'add':
				return Alert::getSuccess('Paciente adicionado com sucesso!');
				break;
			case 'removed':
				return Alert::getSuccess('Paciente removido com sucesso!');
				break;
			case 'alter':
				return Alert::getSuccess('Alterações realizadas com sucesso!');
				break;
			case 'alterDuplo':
				return Alert::getSuccess('Alterações realizadas com sucesso, exceto registros com mesmo atendimento!');
				break;
			case 'errorDate':
				return Alert::getError('Agenda não pode ser transferida para a mesma data!');
				break;
			case 'transfer':
				return Alert::getSuccess('Agenda transferida com sucesso!');
				break;
			case 'deletedfail':
				return Alert::getError('Você não tem permissão para Excluir! Contate o administrador.');
				break;
		}
	}
	
	//Método responsavel por renderizar a view de Listagem de Atendimentos
	public static function getAgendas($request){
		
		//Recebe os parâmetros da requisição
		$queryParams = $request->getQueryParams();
		
	//	var_dump($queryParams); exit;
		
		$idProfissional = @$queryParams['profissional'] ?? null; 
		$status = @$queryParams['status'] ?? null;
		$data = @$queryParams['data'];
		//Conteúdo da Home
		$content = View::render('admin/modules/agendas/index',[
				
				'itens' => self::getAgendasItems($request,$obPagination),
				'pagination' => parent::getPagination($request, $obPagination),
				'totalAtendimentos' => self::$qtdTotal,
				'statusMessage' => self::getStatus($request),
				'optionProfissional' => self::getProfissionais($idProfissional,'status = 1'),//status = 1 => funcionários ativos
				'optionStatus' => self::getAgendaStatus($status),
				'acao' => 'Pesquisa',
				'data' => $data
				
		]);
		
		//Retorna a página completa
		return parent::getPanel('Agendas > CAPS', $content,'agendas', 'hidden');
	}
	
	
	//Método responsavel por renderizar a view de Nova agenda
	public static function getAgendasNew($request){
		
			//Conteúdo da Home
			$content = View::render('admin/modules/agendas/new',[
					
					'itens' => self::getAgendasItems($request,$obPagination),
					'pagination' => parent::getPagination($request, $obPagination),
					'totalAtendimentos' => self::$qtdTotal,
					'statusMessage' => self::getStatus($request),
					'optionProfissional' => self::getProfissionais(null,'status = 1'),//status = 1 => funcionários ativos
					'acao' => 'Nova',
			]);
			
			//Retorna a página completa
			return parent::getPanel('Agendas > CAPS', $content,'agendas', 'hidden');
	}
	
	//Método responsável por salvar uma agenda no banco
	public static function setAgendasNew($request){
		
		//Post vars
		$postVars = $request->getPostVars();
		
		$data = implode('-', array_reverse(explode('/', $postVars['data'])));
		
		//Nova instância de Agenda
		$obAgenda = new EntityAgenda;
		$obAgenda->data =$data;
		$obAgenda->idProfissional = $postVars['profissional'];
		$obAgenda->status = strtoupper($postVars['status']);
		
		//Verifica se a agenda já está existe no banco de dados
		$duplicado = EntityAgenda::getAgendaDuplicada(date('Y-m-d',strtotime($postVars['data'])), $postVars['profissional']);
		
		
		if($duplicado instanceof EntityAgenda){
			//Redireciona o usuário em caso de existir
			$request->getRouter()->redirect('/admin/agendas?statusMessage=duplicad');
		}
		
		$obAgenda->cadastrar();
		
		//Redireciona o usuário
		$request->getRouter()->redirect('/admin/agendas?statusMessage=created');
	}
	
	

	//Método responsavel por renderizar a view de Edição de agendas
	public static function getEditAgenda($request, $id){
			
		//obtém o agenda  do banco de dados
		$obAgenda = EntityAgenda::getAgendaById($id);
		
		//retorna a qtd de pacientes da agenda
		$qtdPacAgenda = EntityAgendaItems::getAgendaItems('idAgenda = '.$obAgenda->id.' ', 'id DESC',null,'COUNT(*) as qtd')->fetchObject()->qtd;
		
		
		//Valida a instancia
		if(!$obAgenda instanceof EntityAgenda){
				$request->getRouter()->redirect('/admin/agendas');
		}
				
				//Renderiza o conteúdo
				$content = View::render('admin/modules/agendas/edit',[
						
						'itens' => self::getAgendasItems($request,$obPagination),
						'pagination' => parent::getPagination($request, $obPagination),
						'totalAtendimentos' => self::$qtdTotal,
						'statusMessage' => self::getStatus($request),
						'data' => date('Y-m-d',strtotime($obAgenda->data)),
						'optionProfissional' => self::getProfissionais($obAgenda->idProfissional,'status = 1'),//status = 1 => funcionários ativos
						'optionStatus' => self::getAgendaStatus($obAgenda->status),
						'acao' => 'Editar',
						'qtdPac' => $qtdPacAgenda
				]);
				
				//Retorna a página completa
				return parent::getPanel('Agendas > CAPS', $content,'agendas', 'hidden');
	}
	
	
	//Metodo responsável por gravar a edição de uma agenda
	public static function setEditAgenda($request, $id){
	
			
		//obtém a agenda do banco de dados
		$obAgenda = EntityAgenda::getAgendaById($id);
		//Valida a instancia
		if(!$obAgenda instanceof EntityAgenda){
			$request->getRouter()->redirect('/admin/agendas');
		}
		
		//Post Vars
		$postVars = $request->getPostVars();

		//Atualiza a instância
		$data = implode('-', array_reverse(explode('/', $postVars['data'])));
		
		// código executado caso o status seja transferida(id 4)
		if ($postVars['status'] == 4){
			
			//verifica se a data antiga e data nova são iguais
			if($obAgenda->data == $data){
				//Redireciona o usuário
				$request->getRouter()->redirect('/admin/agendas/'.$obAgenda->id.'/edit?statusMessage=errorDate');
			}
			
			//Nova instância de Agenda
			$obNovaAgenda = new EntityAgenda;
			$obNovaAgenda->data =$data;
			$obNovaAgenda->idProfissional = $postVars['profissional'];
			$obNovaAgenda->status = 1; //agenda aberta
			
			//Verifica se a agenda já está existe no banco de dados
			$duplicado = EntityAgenda::getAgendaDuplicada(date('Y-m-d',strtotime($postVars['data'])), $postVars['profissional']);
			if($duplicado instanceof EntityAgenda){
				//Redireciona o usuário em caso de existir
				$request->getRouter()->redirect('/admin/agendas/'.$obAgenda->id.'/edit?statusMessage=duplicad');
			}
			
			$obNovaAgenda->cadastrar();
			
			//obtem agenda de destino
			$obAgendaTransferida = EntityAgenda::getAgendaDuplicada(date('Y-m-d',strtotime($postVars['data'])), $postVars['profissional']);
			
			
			//Obtem os pacientes da agenda de origem
			$results = EntityAgendaItems::getAgendaItems('idAgenda = '.$id.'');
			
			//cadastra os pacientes na agenda de destino
			while ($obPacAgendaOrigem = $results -> fetchObject(EntityAgendaItems::class)) {
				//Nova instância de itens da Agenda
				$obAdicionarPaciente = new EntityAgendaItems();
				$obAdicionarPaciente->idPaciente = $obPacAgendaOrigem->idPaciente;
				$obAdicionarPaciente->idAgenda = $obAgendaTransferida->id;
				$obAdicionarPaciente->idProcedimento = $obPacAgendaOrigem->idProcedimento;
				$obAdicionarPaciente->idPresenca = $obPacAgendaOrigem->idPresenca;
				$obAdicionarPaciente->cadastrar();
				
				
			}
			//atualiza o status da agenda de origem para "transferida"
			$obAgenda->status = strtoupper($postVars['status']);
			$obAgenda->atualizar();
			
			//Redireciona o usuário
			$request->getRouter()->redirect('/admin/agendas/'.$obAgenda->id.'/edit?statusMessage=transfer');
		}
		
		
		$obAgenda->data = $data;
		$obAgenda->idProfissional = $postVars['profissional'];
		$obAgenda->status = strtoupper($postVars['status']);
		$obAgenda->atualizar();
		
		//Redireciona o usuário
		$request->getRouter()->redirect('/admin/agendas/'.$obAgenda->id.'/edit?statusMessage=updated');
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
	
	//Método responsavel por listar os Status da Agenda
	public static function getAgendaStatus($id){
		$resultados = '';
		$results =  EntityAgendaStatus::getAgendasStatus(null,'nome asc',null);
		
		//verifica se o id não é nulo e obtém a Procedimento do banco de dados
		if (!is_null($id)) {
			$selected = '';
			while ($obAgendaStatus = $results -> fetchObject(EntityAgendaStatus::class)) {
				
				//seleciona o procedimento do atendimento
				$obAgendaStatus->id == $id ? $selected = 'selected' : $selected = '';
				//View de as Escolaridades
				$resultados .= View::render('admin/modules/pacientes/itemSelect',[
						'id' => $obAgendaStatus ->id,
						'nome' => $obAgendaStatus->nome,
						'selecionado' => $selected
				]);
			}
			//retorna os Procedimentos
			return $resultados;
		}else{ //se o procedimento for nulo, lista todos e seleciona um em branco
			while ($obAgendaStatus = $results -> fetchObject(EntityAgendaStatus::class)) {
				//	$obProfissional->nome == 'Não Informado' ? $selected = 'selected' : $selected = '';
				$resultados .= View::render('admin/modules/pacientes/itemSelect',[
						'id' => $obAgendaStatus ->id,
						'nome' => $obAgendaStatus->nome,
						'selecionado' => ''
				]);
			}
			//retorna os as Escolaridades
			return $resultados;
		}
	}
	
	//Método responsavel por Presença ou Falta na agenda
	public static function getAgendaPresenca($id){
		$resultados = '';
		$results =  EntityAgendaPresenca::getAgendasPresenca(null,'nome asc',null);
		
		//verifica se o id não é nulo e obtém p ou f do banco de dados
		if (!is_null($id)) {
			$selected = '';
			while ($obAgendaPresenca = $results -> fetchObject(EntityAgendaPresenca::class)) {
				
				//seleciona p ou f
				$obAgendaPresenca->id == $id ? $selected = 'selected' : $selected = '';
				//View de as Escolaridades
				$resultados .= View::render('admin/modules/pacientes/itemSelect',[
						'id' => $obAgendaPresenca ->id,
						'nome' => $obAgendaPresenca->nome,
						'selecionado' => $selected
				]);
			}
			//retorna
			return $resultados;
		}else{ //se o p ou f for nulo, lista todos e seleciona um em branco
			while ($obAgendaPresenca = $results -> fetchObject(EntityAgendaPresenca::class)) {
				$resultados .= View::render('admin/modules/pacientes/itemSelect',[
						'id' => $obAgendaPresenca ->id,
						'nome' => $obAgendaPresenca->nome,
						'selecionado' => ''
				]);
			}
			
			return $resultados;
		}
	}
	

	
	
	//Método responsavel por renderizar a view da Agenda
	public static function getAgendasView($request, $id){
		
		//obtém o agenda  do banco de dados
		$obAgenda = EntityAgenda::getAgendaById($id);
		
		//Valida a instancia
		if(!$obAgenda instanceof EntityAgenda){
			$request->getRouter()->redirect('/admin/agendas');
		}
		
		//Renderiza o conteúdo
		$content = View::render('admin/modules/agendas/view',[
				
				'itens' => self::getAgendasViewItems($request,$obPagination, $id),
				'pagination' => parent::getPagination($request, $obPagination),
				'totalAtendimentos' => self::$qtdTotal,
				'statusMessage' => self::getStatus($request),
				'data' => date('d/m/Y',strtotime($obAgenda->data)),
				'acao' => 'Visualizar',
				'idAgenda' => $id,
				'profissional' => EntityProfissional::getProfissionalById($obAgenda->idProfissional)->nome,
				'status' => EntityAgendaStatus::getAgendaStatusById($obAgenda->status)->nome,
				'funcao' => EntityProfissional::getProfissionalById($obAgenda->idProfissional)->funcao
		]);
		
		//Retorna a página completa
		return parent::getPanel('Agendas > Adicionar Paciente', $content,'agendas', 'hidden');
	}

	//Método responsavel por obter a rendereizacao dos pacientes para a página
	private static function getAgendasViewItems($request, &$obPagination, $id){
		$resultados = '';
		
		//Pagina Atual
		$queryParams = $request->getQueryParams();
		$paginaAtual = $queryParams['page'] ?? 1;
		
		//Armazena valor busca pelo nome do paciente
		$busca = $queryParams['busca'] ?? '';
		
		//Condições SQL
		$condicoes = [
				
				strlen($busca) ? 'codPronto "'.$id.'" ' : null,
				//	strlen($filtroStatus) ? 'status = "'.$filtroStatus.'" ' : null,
				//	strlen($filtroTipo) ? 'tipo = "'.$filtroTipo.'" ' : null
		];
		
		//Remove posições vazias
		$condicoes = array_filter($condicoes);
		
		//cláusula where
	//	$where = implode(' AND ', $condicoes);
		$where = 'idAgenda = '.$id.' ';
		
		//Quantidade total de registros
		self::$qtdTotal = EntityAgendaItems::getAgendaItems($where, 'id DESC',null,'COUNT(*) as qtd')->fetchObject()->qtd;
		
		//Instancia de paginação
		$obPagination = new Pagination(self::$qtdTotal,$paginaAtual,5);
		#############################################
		
	//	var_dump($where);exit;
		//Obtem os pacientes
		$results = EntityAgendaItems::getAgendaItems($where, 'idPaciente' , $obPagination->getLimit());
		
		
		//	var_dump($results); exit;
		$num=0;
		//Renderiza
		while ($obAgendaItems = $results -> fetchObject(EntityAgendaItems::class)) {
			$num++;
			//View de Agendas
			$resultados .= View::render('admin/modules/agendas/viewItem',[
					
					'codPronto' => EntityPaciente::getPacienteById($obAgendaItems->idPaciente)->codPronto,
					'paciente' =>EntityPaciente::getPacienteById($obAgendaItems->idPaciente)->nome,
					'idPac' => $obAgendaItems->idPaciente,
					'idProc' =>$obAgendaItems->idProcedimento,
					'optionProcedimento' => self::getProcedimentos($obAgendaItems->idProcedimento),
					'optionPresenca' => self::getAgendaPresenca($obAgendaItems->idPresenca),
					'idAgendaItems' =>$obAgendaItems->id,
					'num' => $num
			]);
		}
		//Retorna as agendas
		return $resultados;
		
	}
	
	
	//Método responsavel por renderizar a view de Listagem de Pacientes para add na agenda
	public static function getAgendaAddPaciente($request,$idAgenda){
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
	//	var_dump($idAgenda);exit;
		//esconde busca rápida de prontuário no navBar
		$hidden = '';
		//Conteúdo da Home
		$content = View::render('admin/modules/agendas/addPaciente',[
				'title' => 'Pesquisar Pacientes para Adicionar na Agenda',
				'itens' => self::getAgendaAddPacienteItems($request,$obPagination, $idAgenda),
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
				'idAgenda' => $idAgenda
				
				
		]);
		
		//Retorna a página completa
		return parent::getPanel('Pacientes > Siscaps', $content,'agendas', self::$hidden);
		
	}
	
	
	
	// Método responsável por listar os pacientes da agenda
	private static function getAgendaAddPacienteItems($request, &$obPagination, $idAgenda ){
		
		$resultados = '';
		
		//Pagina Atual
		$queryParams = $request->getQueryParams();
		$paginaAtual = $queryParams['page'] ?? 1;
		
		
		//Armazena valor busca pelo nome do paciente
		$nome = $queryParams['nome'] ?? '';
		
		$pront = $queryParams['pront'] ?? '';
		//retira zeros à esquerda
		if($pront != '') $pront += 0;
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
		$order = 'id DESC' ;
		
		
		
		//Obtem os pacientes
		$results = EntityPaciente::getPacientes($where, $order, $obPagination->getLimit());
		
		
		
		//Renderiza
		while ($obPaciente = $results -> fetchObject(EntityPaciente::class)) {
			
			//conta  a qtd de vezes que o paciente foi adicionado na agenda (o paciente pode receber mais de um atendimento por agenda)
			$qtdAtend = EntityAgendaItems::getAgendaItems('idAgenda = '.$idAgenda.' and  idPaciente = '.$obPaciente->id.'  ', null,null,'COUNT(*) as qtd')->fetchObject()->qtd;
			
			
			//View de pacientes da lista
			$resultados .= View::render('admin/modules/agendas/addPacienteItem',[
					//muda cor do texto do status para azul(ativo) ou vermelho(inativo)
					$obPaciente->status == 'Ativo' ? $cor = 'text-success' : $cor = 'text-danger',
					$obPaciente->status == 'Ativo' ? $titleStatus = 'Ativo' : $titleStatus = 'Inativo',
					$obPaciente->tipo == 'Ad' ? $titleTipo = 'Álcool e/ou drogas ' : $titleTipo = 'Transtormo mental',
					'codPronto' => str_pad($obPaciente->codPronto,4,"0",STR_PAD_LEFT),
					'nome' => $obPaciente->nome,
					'cartaoSus' => $obPaciente->cartaoSus,
					'tipo' => $obPaciente->tipo,
					'status' => $obPaciente->status,
					'cor' => $cor,
					'idPac' => $obPaciente->id,
					'idAgenda' => $idAgenda,
					'titleStatus'=> $titleStatus,
					'titleTipo'=> $titleTipo,
					'title' =>'Adicionar na agenda',
					'icone' => 'group_add',
					'link'=> '/admin/agendas/'.$obPaciente->id.'/add/{{idAgenda}}',
					'qtdAtend' => $qtdAtend
					
					
			]);
			
		}
		
		//Grava o Log do usuário
	//	if(!empty($queryParams)) Logs::setNewLog('pacientes', 'Pesquisa' , implode(", ", $condicoes));
		
		//Retorna os pacientes
		return $resultados;
		
		
		
	}
	
	
	//Método responsavel por Adicionar Paciente na Agenda
	public static function setAgendaAddPaciente($request, $idPac, $idAgenda){
		
				
		//Nova instância de itens da Agenda
		$obAdicionarPaciente = new EntityAgendaItems();
		$obAdicionarPaciente->idPaciente = $idPac;
		$obAdicionarPaciente->idAgenda = $idAgenda;
		//atribui o procedimento individual como padrão
		$obAdicionarPaciente->idProcedimento = 2;
		//atribui falta como padrão
		$obAdicionarPaciente->idPresenca = 2;
		$obAdicionarPaciente->cadastrar();
		
		//Redireciona o usuário
		$request->getRouter()->redirect('/admin/agendas/'.$idAgenda.'/addPaciente?statusMessage=add');
	}
	

	//Método responsavel por Atualizar o procedimento e a presença do Paciente na Agenda
	public static function setAgendaAlteraProcedimento($request){
		
		$postVars = $request->getPostVars();
			
	//	var_dump($postVars);exit;
		$idAgendaItems = $postVars['idAgendaItems'];
		$idProcedimento = $postVars['procedimento'];
		$idPresenca = $postVars['presenca'];
		
		$duplo = false;
		for($i = 1; $i <= count($idAgendaItems); $i++) {
			
			//obtém o item da agenda do banco de dados
			$obAgendaItems = EntityAgendaItems::getAgendaItemsById($idAgendaItems[$i]);
			//Atualiza a instância
			$obAgendaItems->idProcedimento = $idProcedimento[$i];
			$obAgendaItems->idPresenca = $idPresenca[$i];
			$obAgendaItems->atualizar();
		}
			
		
			
			$campos = 'idAgenda , idPaciente , idProcedimento, idPresenca, Count(*)';
			$group = 'idAgenda, idPaciente, idProcedimento, idPresenca' ;
			$having = 'Count(*) > 1';
			$results = EntityAgendaItems::getAgendaItems2(null, $group, $having, null, null, $campos);
		//	var_dump($results -> fetchObject(EntityAgendaItems::class));exit;
			while ($ob = $results -> fetchObject(EntityAgendaItems::class)) {
				
				if($ob->idPresenca == 1){
					
				$duploResults = EntityAgendaItems::getAgendaItems('idAgenda = '.$ob->idAgenda.' and idPaciente = '.$ob->idPaciente.' and idProcedimento = '.$ob->idProcedimento.' ',null,null);	
				$obDuploResults = $duploResults -> fetchObject(EntityAgendaItems::class);
				
				//2 falta
				$obDuploResults->idPresenca = 2;
				$obDuploResults->atualizar();
				$duplo = true;
				}
				
			}
			
			
			
		
		if($duplo){
			//Redireciona o usuário
			$request->getRouter()->redirect('/admin/agendas/'.$postVars['idAgenda'].'/view?statusMessage=alterDuplo');
		}else{
			$request->getRouter()->redirect('/admin/agendas/'.$postVars['idAgenda'].'/view?statusMessage=alter');
		}
		
	}
	
	
	//Método responsavel por Remover Paciente da Agenda
	public static function setAgendaRemovePaciente($request, $idPac, $idAgenda){
		
		//obtém o paciente da agenda
		$obAgendaItems = EntityAgendaItems::getAgendaItemsByIdAgendaPaciente($idAgenda, $idPac);
		
		//Remove o paciente
		$obAgendaItems->excluir();
		
		//Redireciona o usuário
		$request->getRouter()->redirect('/admin/agendas/'.$idAgenda.'/addPaciente?statusMessage=removed');
	}
	
	//Método responsavel por Remover Paciente da Agenda na Lista
	public static function setAgendaRemovePacienteList($request, $idPac, $idAgenda){
		
		//obtém o paciente da agenda
		$obAgendaItems = EntityAgendaItems::getAgendaItemsByIdAgendaPaciente($idAgenda, $idPac);
		
		//Remove o paciente
		$obAgendaItems->excluir();
		
		//Redireciona o usuário
		$request->getRouter()->redirect('/admin/agendas/'.$idAgenda.'/addPacienteLista?statusMessage=removed');
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
	public static function getAgendasDelete($request,$id){
		
		
		
		//obtém o deopimento do banco de dados
		$obAgenda = EntityAgenda::getAgendaById($id);
		
		//Valida a instancia
		if(!$obAgenda instanceof EntityAgenda){
			$request->getRouter()->redirect('/admin/agendas');
		}
		
		//retorna a qtd de pacientes da agenda
		$qtdPacAgenda = EntityAgendaItems::getAgendaItems('idAgenda = '.$obAgenda->id.' ', 'id DESC',null,'COUNT(*) as qtd')->fetchObject()->qtd;
		
		//Conteúdo do Formulário
		$content = View::render('admin/modules/agendas/delete',[
				'title' => 'Excluir',
				'data' => date('d/m/Y', strtotime($obAgenda->data)),
				'profissional' => EntityProfissional::getProfissionalById($obAgenda->idProfissional)->nome,
				'status' => EntityAgendaStatus::getAgendaStatusById($obAgenda->status)->nome,
				'qtdPac' => $qtdPacAgenda
				
				
				
		]);
		
		//Retorna a página completa
		return parent::getPanel('Excluir Atendimento', $content,'atendimentos', 'hidden');
		
	}
	
	//Metodo responsável por Excluir um Paciente
	public static function setAgendasDelete($request,$id){
		
		//apenas o administrador pode excluir
		if ($_SESSION['admin']['usuario']['tipo'] == 'Operador'){
			//Redireciona o usuário
			$request->getRouter()->redirect('/admin/agendas?statusMessage=deletedfail');
		}
				
		
		//obtém o paciente do banco de dados
		$obAgenda = EntityAgenda::getAgendaById($id);
		
		//Valida a instancia
		if(!$obAgenda instanceof EntityAgenda){
			$request->getRouter()->redirect('/admin/agendas');
		}
		
		//Exclui o depoimento
		$obAgenda->excluir();
		
		//Redireciona o usuário
		$request->getRouter()->redirect('/admin/agendas?statusMessage=deleted');
		
		
	}
	


	
}

