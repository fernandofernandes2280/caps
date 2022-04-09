<?php

namespace App\Controller\Admin;

use \App\Utils\View;
use \App\Model\Entity\Cid10 as EntityCid10;
use \WilliamCosta\DatabaseManager\Pagination;

class Cid10 extends Page{
	
	//esconde busca rápida de prontuário no navBar
	private static $hidden = 'hidden';
	
	//Método responsavel por obter a renderização da listagem dos registros do banco
	private static function getCid10Items($request, &$obPagination){

		$itens = '';
		
		//Quantidade total de registros
		$quantidadetotal =  EntityCid10::getCid10s(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;
		
		//Página atual
		$queryParams = $request->getQueryParams();
		$paginaAtual = $queryParams['page'] ?? 1;
		
		//Instancia de paginacao
		$obPagination = new Pagination($quantidadetotal,$paginaAtual,5);
		
		//Resultados da Página
		$results = EntityCid10::getCid10s(null, 'nome',$obPagination->getLimit());
		
		//Renderiza o item
		while ($ob = $results->fetchObject(EntityCid10::class)) {
		
			//View de listagem
			$itens.= View::render('admin/modules/cid10/item',[
					'id' => $ob->id,
					'nome' => $ob->nome,
					'descricao' => $ob->descricao
			]);
		}
		
		
		//Retorna a listagem
		return $itens;
		
	}
	
	
	//Método responsavel por renderizar a view de Listagem
	public static function getCid10($request){
		
		//Conteúdo da Home
		$content = View::render('admin/modules/cid10/index',[
				'itens' => self::getCid10Items($request, $obPagination),
				'pagination' => parent::getPagination($request, $obPagination),
				'status' => self::getStatus($request),
				'title' => 'CID10',
				'statusMessage' => ''
		]);
		
		//Retorna a página completa
		return parent::getPanel('Cid10 > Siscaps', $content,'cid10', self::$hidden);
		
	}
	
	//Metodo responsávelpor retornar o formulário de cadastro 
	public static function getNewCid10($request){
		
		//Conteúdo do Formulário
		$content = View::render('admin/modules/cid10/form',[
				'title' => 'Cadastrar Cid10',
				'nome' => '',
				'descricao' => '',
				'statusMessage' => ''

				
				
		]);
		
		//Retorna a página completa
		return parent::getPanel('Cadastrar Cid10 > Siscaps', $content,'cid10', self::$hidden);
		
	}
	
	
	//Metodo responsávelpor por cadastrar no banco
	public static function setNewCid10($request){
		//Post vars
		$postVars = $request->getPostVars();
		
		$nome = $postVars['nome'] ?? '';
		$descricao = $postVars['descricao'] ?? '';
		
		//Nova instancia
		$ob = new EntityCid10;
		$ob->nome = $nome;
		$ob->descricao = $descricao;
		
		$ob->cadastrar();
		
		//Redireciona o usuário
		$request->getRouter()->redirect('/admin/cid10/'.$ob->id.'/edit?status=created');
		
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
				return Alert::getSuccess('Cid10 criado com sucesso!');
			break;
			case 'updated':
				return Alert::getSuccess('Cid10 atualizado com sucesso!');
				break;
			case 'deleted':
				return Alert::getSuccess('Cid10 excluído com sucesso!');
				break;
			
		}
	}
	
	
	//Metodo responsávelpor retornar o formulário de Edição 
	public static function getEditCid10($request,$id){
		//obtém o usuário do banco de dados
		$ob = EntityCid10::getCid10ById($id);
		
		//Valida a instancia
		if(!$ob instanceof EntityCid10){
			$request->getRouter()->redirect('/admin/cid10');
		}
		
		//Conteúdo do Formulário
		$content = View::render('admin/modules/cid10/form',[
				'title' => 'Editar Cid10',
				'nome' => $ob->nome,
				'descricao' => $ob->descricao,
				'statusMessage' => self::getStatus($request),
				
				
				
		]);
		
		//Retorna a página completa
		return parent::getPanel('Editar Cid10 > Siscaps', $content,'cid10', self::$hidden);
		
	}
	
	//Metodo responsável por gravar a atualizacao de um usuário
	public static function setEditCid10($request,$id){
		//obtém o usuário do banco de dados
		$ob = EntityCid10::getCid10ById($id);
		
		//Valida a instancia
		if(!$ob instanceof EntityCid10){
			$request->getRouter()->redirect('/admin/cid10');
		}
		
		
		//Post Vars
		$postVars = $request->getPostVars();
		$nome = $postVars['nome'] ?? '';
		$descricao = $postVars['descricao'] ?? '';
		
		//Atualiza a instância
		$ob->nome = $nome;
		$ob->descricao = $descricao;
		$ob->atualizar();
		
		//Redireciona o usuário
		$request->getRouter()->redirect('/admin/cid10/'.$ob->id.'/edit?status=updated');
		
		
	}
	
	
	//Metodo responsávelpor retornar o formulário de Exclusão 
	public static function getDeleteCid10($request,$id){
		//obtém o registro do banco de dados
		$ob = EntityCid10::getCid10ById($id);
		
		//Valida a instancia
		if(!$ob instanceof EntityCid10){
			$request->getRouter()->redirect('/admin/cid10');
		}
		
		
		
		//Conteúdo do Formulário
		$content = View::render('admin/modules/cid10/delete',[
				'nome' => $ob->nome,
				'descricao' => $ob->descricao
				
				
		]);
		
		//Retorna a página completa
		return parent::getPanel('Excluir Cid10 > Siscaps', $content,'cid10' , self::$hidden);
		
	}
	
	//Metodo responsável por Excluir 
	public static function setDeleteCid10($request,$id){
		//obtém o usuário do banco de dados
		$ob = EntityCid10::getCid10ById($id);
		
		//Valida a instancia
		if(!$ob instanceof EntityCid10){
			$request->getRouter()->redirect('/admin/cid10');
		}
		
			
		//Exclui 
		$ob->excluir($id);
		
		//Redireciona o usuário
		$request->getRouter()->redirect('/admin/cid10?status=deleted');
		
		
	}
	
	
}