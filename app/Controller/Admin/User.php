<?php

namespace App\Controller\Admin;

use \App\Utils\View;
use \App\Model\Entity\User as EntityUser;
use \WilliamCosta\DatabaseManager\Pagination;
use Bissolli\ValidadorCpfCnpj\CPF;
use \App\Utils\Funcoes;
use \App\Model\Entity\Profissional as EntityProfissional;

class User extends Page{
	
	//esconde busca rápida de prontuário no navBar
	private static $hidden = 'hidden';
	
	//Método responsavel por obter a renderização dos itens de usuários para a página
	private static function getUserItems($request, &$obPagination){
		//Usuários
		$itens = '';
		
		//Quantidade total de registros
		$quantidadetotal =  EntityUser::getUsers(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;
		
		//Página atual
		$queryParams = $request->getQueryParams();
		$paginaAtual = $queryParams['page'] ?? 1;
		
		//Instancia de paginacao
		$obPagination = new Pagination($quantidadetotal,$paginaAtual,5);
		
		//Resultados da Página
		$results = EntityUser::getUsers(null, 'id DESC',$obPagination->getLimit());
		
		//Renderiza o item
		while ($obUser = $results->fetchObject(EntityUser::class)) {
		
			//View de depoimentos
			$itens.= View::render('admin/modules/users/item',[
					'id' => $obUser->id,
					'nome' => $obUser->nome,
					'email' => $obUser->email,
					'cpf' => Funcoes::mask($obUser->cpf, '###.###.###-##') ,
					'tipo' => $obUser->tipo,
			]);
		}
		
		
		//Retorna os depoimentos
		return $itens;
		
	}
	
	
	//Método responsavel por renderizar a view de Listagem de USuários
	public static function getUsers($request){
		
		//Conteúdo da Home
		$content = View::render('admin/modules/users/index',[
				'itens' => self::getUserItems($request, $obPagination),
				'pagination' => parent::getPagination($request, $obPagination),
				'status' => self::getStatus($request),
				'navBar'=>View::render('admin/navBar',[]),
				'footer'=>View::render('admin/modules/pacientes/footer',[]),
				'statusMessage' => ''
				
		]);
		
		//Retorna a página completa
		return parent::getPanel('Usuários > SISCAPS', $content,'users', self::$hidden);
		
	}
	
	//Metodo responsávelpor retornar o formulário de cadastro de um novo usuário
	public static function getNewUser($request,$id){

	    
	    if($id != '')
	    $obFunc = EntityProfissional::getProfissionalById($id);
	    
	    
		//Conteúdo do Formulário
		$content = View::render('admin/modules/users/form',[
				'title' => 'Cadastrar usuário',
				'nome' => $obFunc->nome ?? '',
		        'email' => $obFunc->email ?? '',
		        'cpf' => Funcoes::mask(@$obFunc->cpf, '###.###.###-##')  ?? '',
		        'senha' => $obFunc->cpf ?? '',
				'statusMessage' => self::getStatus($request),
		        'selectedVisitante'=> 'selected'
				
		]);
		
		//Retorna a página completa
		return parent::getPanel('Cadastrar Usuário > SISCAPS', $content,'users', self::$hidden);
		
	}
	
	
	//Metodo responsávelpor por cadastrar um usuário no banco
	public static function setNewUser($request,$id){
		//Post vars
		$postVars = $request->getPostVars();
	
		$nome = $postVars['nome'] ?? '';
		$email = $postVars['email'] ?? '';
		$senha = $postVars['senha'] ?? '';
		$cpf = $postVars['cpf'] ?? '';
		$tipo = $postVars['tipo'] ?? '';
		
		
		//instancia classe pra verificar CPF
		$validaCpf = new CPF($cpf);
		
		//verifica se é válido o cpf
		if (!$validaCpf->isValid()){
			
			$request->getRouter()->redirect('/admin/users/new?status=cpfInvalido');
		}
		
		
		//busca usuário pelo CPF sem a maskara
		$obUser = EntityUser::getUserByCPF($validaCpf->getValue());
		
		if($obUser instanceof EntityUser){
		    if($id != '')
		      
		        $request->getRouter()->redirect('/admin/users/'.$obUser->id.'/edit?status=duplicated');
		    else

		        $request->getRouter()->redirect('/admin/users/'.$obUser->id.'/edit?status=duplicated');
		}
		
		//Valida o email do usuário
		$obUserEmail = EntityUser::getUserByEmail($email);
		
		if($obUserEmail instanceof EntityUser ){
		    if($id != '')
		        $request->getRouter()->redirect('/admin/users/'.$obUserEmail->id.'/edit?status=emailDuplicated');
		        else
		    $request->getRouter()->redirect('/admin/users/'.$obUserEmail->id.'/edit?status=emailDuplicated');
		}
				
		//Nova instancia de Usuário
		$obUser = new EntityUser;
		$obUser->nome = $nome;
		$obUser->email = $email;
		$obUser->cpf = $validaCpf->getValue(); //cpf sem formatação
		$obUser->tipo = $tipo;
		$obUser->senha = password_hash($senha,PASSWORD_DEFAULT);
		$obUser->cadastrar();
		
		//Redireciona o usuário
		$request->getRouter()->redirect('/admin/users/'.$obUser->id.'/edit?status=created');
		
	}
	
	//Método responsavel por retornar a mensagem de status
	private static function getStatus($request){
		//Query PArams
		$queryParams = $request->getQueryParams();
		
		//Status
		if(!isset($queryParams['status'])) return '';
		
		//Mensagens de status
		switch ($queryParams['status']) {
			case 'created':
				return Alert::getSuccess('Usuário criado com sucesso!');
			break;
			case 'updated':
				return Alert::getSuccess('Usuário atualizado com sucesso!');
				break;
			case 'deleted':
				return Alert::getSuccess('Usuário excluído com sucesso!');
				break;
			case 'duplicated':
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
	
	
	//Metodo responsávelpor retornar o formulário de Edição de um Usuário
	public static function getEditUser($request,$id){
		
				
		//obtém o usuário do banco de dados
		$obUser = EntityUser::getUserById($id);
		
		//Valida a instancia
		if(!$obUser instanceof EntityUser){
			$request->getRouter()->redirect('/admin/users');
		}
		
		$obUser->tipo == 'Admin' ? $selectedAdmin = 'selected' : $selectedAdmin = '' ;
		$obUser->tipo == 'Visitante' ? $selectedVisitante = 'selected' : $selectedVisitante = '' ;
		$obUser->tipo == 'Operador' ? $selectedOperador = 'selected' : $selectedOperador = '' ;
		
		//Conteúdo do Formulário
		$content = View::render('admin/modules/users/form',[
				'title' => 'Editar usuário',
				'nome' => $obUser->nome,
				'email' => $obUser->email,
				'senha' => $obUser->senha,
				'cpf' => Funcoes::mask($obUser->cpf, '###.###.###-##'), 
				'selectedAdmin'=> $selectedAdmin,
				'selectedVisitante'=> $selectedVisitante,
				'selectedOperador'=> $selectedOperador,
				'statusMessage' => self::getStatus($request),
				'navBar'=>View::render('admin/navBar',[]),
				'footer'=>View::render('admin/modules/pacientes/footer',[]),
				
				
		]);
		
		//Retorna a página completa
		return parent::getPanel('Editar Usuário > SISCAPS', $content,'users', self::$hidden);
		
	}
	
	//Metodo responsável por gravar a atualizacao de um usuário
	public static function setEditUser($request,$id){
		//Post Vars
		$postVars = $request->getPostVars();
		$nome = $postVars['nome'] ?? '';
		$email = $postVars['email'] ?? '';
		$senha = $postVars['senha'] ?? '';
		$tipo = $postVars['tipo'] ?? '';
		$cpf = $postVars['cpf'] ?? '';
		
		//obtém o usuário do banco de dados
		$obUser = EntityUser::getUserById($id);
		
		//Valida a instancia
		if(!$obUser instanceof EntityUser){
			$request->getRouter()->redirect('/admin/users');
		}
		
		//instancia classe pra verificar CPF
		$validaCpf = new CPF($cpf);
		
		//verifica se é válido o cpf
		if (!$validaCpf->isValid()){
			
			$request->getRouter()->redirect('/admin/users/'.$id.'/edit?status=cpfInvalido');
		}
		
		
		//busca usuário pelo CPF sem a maskara
		$obUser = EntityUser::getUserByCPF($validaCpf->getValue());
		
		if($obUser instanceof EntityUser && $obUser->id != $id){
			$request->getRouter()->redirect('/admin/users/'.$id.'/edit?status=duplicated');
		}
		
		
		
		//Valida o email do usuário
		$obUserEmail = EntityUser::getUserByEmail($email);
		
		if($obUserEmail instanceof EntityUser && $obUserEmail->id != $id){
			$request->getRouter()->redirect('/admin/users/'.$id.'/edit?status=emailDuplicated');
		}
		
		
		
		//Atualiza a instância
		$obUser->nome = $nome;
		
		$obUser->email = $email;
		$obUser->tipo = $tipo;
		$obUser->cpf = $validaCpf->getValue(); //cpf sem formatação
		if($senha != '') $obUser->senha = password_hash($senha,PASSWORD_DEFAULT);
		$obUser->atualizar();
		
		//Redireciona o usuário
		$request->getRouter()->redirect('/admin/users/'.$obUser->id.'/edit?status=updated');
		
		
	}
	
	
	//Metodo responsávelpor retornar o formulário de Exclusão de um usuário
	public static function getDeleteUser($request,$id){
		//obtém o usuário do banco de dados
		$obUser = EntityUser::getUserById($id);
		
		//Valida a instancia
		if(!$obUser instanceof EntityUser){
			$request->getRouter()->redirect('/admin/users');
		}
		
		
		
		//Conteúdo do Formulário
		$content = View::render('admin/modules/users/delete',[
				'nome' => $obUser->nome,
				'email' => $obUser->email
				
				
		]);
		
		//Retorna a página completa
		return parent::getPanel('Excluir Usuário > SISCAPS', $content,'users', self::$hidden);
		
	}
	
	//Metodo responsável por Excluir um usuário
	public static function setDeleteUser($request,$id){
		//obtém o usuário do banco de dados
		$obUser = EntityUser::getUserById($id);
		
		//Valida a instancia
		if(!$obUser instanceof EntityUser){
			$request->getRouter()->redirect('/admin/users');
		}
		
			
		//Exclui o usuário
		$obUser->excluir($id);
		
		//Redireciona o usuário
		$request->getRouter()->redirect('/admin/users?status=deleted');
		
		
	}
	
	
}