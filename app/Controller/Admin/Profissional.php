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
			    'email' => $obProfissional->email,
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
	
	
	//Metodo responsávelpor retornar o formulário de Cadastro de um novo Profissional
	public static function getNewProfissional($request){
	    
	    //Conteúdo do Formulário
	    $content = View::render('admin/modules/profissionais/form',[
	        
	        'title' => 'Novo',
	        'nome' => '',
	        'cep' => '',
	        'endereco' => '',
	        'cartaoSus' => '',
	        'statusMessage' => self::getStatus($request),
	        'fone' => '',
	        'cidade' => 'Santana',
	        'uf' => 'AP',
	        'cbo' => '',
	        'cpf' => '',
	        'funcao' => '',
	        'dataNasc' => '',
	        'selectedStatusA' => 'selected',
	        'selectedStatusI' => '',
	        'optionBairros' => EntityBairro::getSelectBairros(null),
	        'email' => '',
	        'escondeBotaoAcesso' => 'hidden'
	        
	    ]);
	    
	    //Retorna a página completa
	    return parent::getPanel('Novo Profissional > SISCAPS', $content,'profissionais', self::$buscaRapidaPront);
	    
	}
	
	
	//Metodo responsávelpor retornar o formulário de Edição de um Profissional
	public static function getEditProfissional($request,$id){
	    
	    //obtém o Profissional do banco de dados
	    $obProfissional = EntityProfissional::getProfissionalById($id);
	    
	    //Valida a instancia
	    if(!$obProfissional instanceof EntityProfissional){
	        $request->getRouter()->redirect('/admin/profissionais');
	    }
	    
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
	        'escondeBotaoAcesso' => ''
	        
	    ]);
	    
	    //Retorna a página completa
	    return parent::getPanel('Editar Profissional > SISCAPS', $content,'profissionais', self::$buscaRapidaPront);
	    
	}
	
	//Metodo responsável por gravar um Novo Funcionário
	public static function setNewProfissional($request){
	    
	    //Post Vars
	    $postVars = $request->getPostVars();
	    
	    //instancia classe pra verificar CPF
	    $validaCpf = new CPF($postVars['cpf']);
	    
	    //verifica se é válido o cpf
	    if (!$validaCpf->isValid()){
	        
	        $request->getRouter()->redirect('/admin/profissionais/new?status=cpfInvalido');
	    }
	    
	    
	    //busca usuário pelo CPF sem a maskara
	    $obProfissional = EntityProfissional::getUserByCPF($validaCpf->getValue());
	    
	    if($obProfissional instanceof EntityProfissional){
	        $request->getRouter()->redirect('/admin/profissionais/new?status=cpfDuplicated');
	    }
	
	    
	    //Nova instancia de Usuário
	    $obProfissional = new EntityProfissional();
	    $obProfissional->nome = Funcoes::convertePriMaiuscula($postVars['nome']);
	    $obProfissional->cep = $postVars['cep'];
	    $obProfissional->endereco = $postVars['endereco'];
	    $obProfissional->bairro =  $postVars['bairro'];
	    $obProfissional->cidade = $postVars['cidade'];
	    $obProfissional->uf = $postVars['uf'];
	    $obProfissional->cartaoSus = $postVars['cartaoSus'];
	    $obProfissional->cbo = $postVars['cbo'];
	    $obProfissional->funcao = $postVars['funcao'];
	    $obProfissional->dataNasc = implode("-",array_reverse(explode("/",$postVars['dataNasc'])));
	    $obProfissional->cpf = $validaCpf->getValue(); //cpf sem formatação
	    $obProfissional->fone = $postVars['fone'];
	    $obProfissional->status = $postVars['status'];
	    $obProfissional->email = $postVars['email'];
	    $obProfissional->cadastrar();
	    
	    //	Logs::setNewLog($request);
	    
	    //Redireciona o usuário
	    $request->getRouter()->redirect('/admin/profissionais/'.$obProfissional->id.'/edit?statusMessage=created');
	    
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
	    $obProfissional->email = $postVars['email'] ?? $obProfissional->email;
	    $obProfissional->atualizar();
	    
	    //	Logs::setNewLog($request);
	    
	    //Redireciona o usuário
	    $request->getRouter()->redirect('/admin/profissionais/'.$obProfissional->id.'/edit?statusMessage=updated');
	    
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
	public static function getDeleteProfissional($request,$id){
	    
		//obtém o profissional do banco de dados
		$obProfissional = EntityProfissional::getProfissionalById($id);
		
		//Valida a instancia
		if(!$obProfissional instanceof EntityProfissional){
			$request->getRouter()->redirect('/admin/profissionais');
		}
		
		
		//Conteúdo do Formulário
		$content = View::render('admin/modules/profissionais/delete',[
		    'nome' => $obProfissional->nome
			
				
		]);
		
		//Retorna a página completa
		return parent::getPanel('Excluir Profissional > SISCAPS', $content,'profissionais', self::$buscaRapidaPront);
		
	}
	
	//Metodo responsável por Excluir um Paciente
	public static function setDeleteProfissional($request,$id){
		
		//obtém o paciente do banco de dados
		$obProfissional = EntityProfissional::getProfissionalById($id);
		
		//Valida a instancia
		if(!$obProfissional instanceof EntityProfissional){
			$request->getRouter()->redirect('/admin/profissionais');
		}
		
		//Exclui o depoimento
		$obProfissional->excluir();
		
		//Redireciona o usuário
		$request->getRouter()->redirect('/admin/profissionais?statusMessage=deleted');
		
		
	}
	
	
	
}