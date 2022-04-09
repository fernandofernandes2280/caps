<?php

namespace App\Controller\Admin;

use \App\Utils\View;
use \App\Model\Entity\Testimony as EntityTestimony;
use \WilliamCosta\DatabaseManager\Pagination;

class Testimony extends Page{
	
	
	//Método responsavel por obter a renderização dos itens de depoimentos para a página
	private static function getTestimonyItems($request, &$obPagination){
		//Depoimentos
		$itens = '';
		
		//Quantidade total de registros
		$quantidadetotal =  EntityTestimony::getTestimonies(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;
		
		//Página atual
		$queryParams = $request->getQueryParams();
		$paginaAtual = $queryParams['page'] ?? 1;
		
		//Instancia de paginacao
		$obPagination = new Pagination($quantidadetotal,$paginaAtual,5);
		
		//Resultados da Página
		$results = EntityTestimony::getTestimonies(null, 'id DESC',$obPagination->getLimit());
		
		//Renderiza o item
		while ($obTestimony = $results->fetchObject(EntityTestimony::class)) {
		
			//View de depoimentos
			$itens.= View::render('admin/modules/testimonies/item',[
					'id' => $obTestimony->id,
					'nome' => $obTestimony->nome,
					'mensagem' => $obTestimony->mensagem,
					'data' => date('d/m/Y H:i:s', strtotime($obTestimony->data))
					
			]);
		}
		
		
		//Retorna os depoimentos
		return $itens;
		
	}
	
	
	//Método responsavel por renderizar a view de Listagem de depoimentos
	public static function getTestimonies($request){
		
		//Conteúdo da Home
		$content = View::render('admin/modules/testimonies/index',[
				'itens' => self::getTestimonyItems($request, $obPagination),
				'pagination' => parent::getPagination($request, $obPagination),
				'status' => self::getStatus($request)
				
		]);
		
		//Retorna a página completa
		return parent::getPanel('Depoimentos > WDEV', $content,'testimonies');
		
	}
	
	//Metodo responsávelpor retornar o formulário de cadastro de um novo
	public static function getNewTestimony($request){
		
		//Conteúdo do Formulário
		$content = View::render('admin/modules/testimonies/form',[
				'title' => 'Cadastrar depoimentos',
				'nome' => '',
				'mensagem' => '',
				'status' => ''
				
				
		]);
		
		//Retorna a página completa
		return parent::getPanel('Cadastrar Depoimentos > WDEV', $content,'testimonies');
		
	}
	
	
	//Metodo responsávelpor por cadastrar um depoimento no banco
	public static function setNewTestimony($request){
		//Post vars
		$postVars = $request->getPostVars();
		
		//Nova instancia de depoimento
		$obTestimony = new EntityTestimony;
		$obTestimony->nome = $postVars['nome'] ?? '';
		$obTestimony->mensagem = $postVars['mensagem'] ?? '';
		$obTestimony->cadastrar();
		
		//Redireciona o usuário
		$request->getRouter()->redirect('/admin/testimonies/'.$obTestimony->id.'/edit?status=created');
		
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
				return Alert::getSuccess('Depoimento criado com sucesso!');
			break;
			case 'updated':
				return Alert::getSuccess('Depoimento atualizado com sucesso!');
				break;
			case 'deleted':
				return Alert::getSuccess('Depoimento excluído com sucesso!');
				break;
		}
	}
	
	
	//Metodo responsávelpor retornar o formulário de Edição de um depoimento
	public static function getEditTestimony($request,$id){
		//obtém o deopimento do banco de dados
		$obTestmony = EntityTestimony::getTestimonyById($id);
		
		//Valida a instancia
		if(!$obTestmony instanceof EntityTestimony){
			$request->getRouter()->redirect('/admin/testimonies');
		}
		
		
		//Conteúdo do Formulário
		$content = View::render('admin/modules/testimonies/form',[
				'title' => 'Editar depoimentos',
				'nome' => $obTestmony->nome,
				'mensagem' => $obTestmony->mensagem,
				'status' => self::getStatus($request)
				
				
		]);
		
		//Retorna a página completa
		return parent::getPanel('Editar Depoimentos > WDEV', $content,'testimonies');
		
	}
	
	//Metodo responsável por gravar a atualizacao de um depoimento
	public static function setEditTestimony($request,$id){
		//obtém o deopimento do banco de dados
		$obTestimony = EntityTestimony::getTestimonyById($id);
		
		//Valida a instancia
		if(!$obTestimony instanceof EntityTestimony){
			$request->getRouter()->redirect('/admin/testimonies');
		}
		
		//Post Vars
		$postVars = $request->getPostVars();
		
		//Atualiza a instância
		$obTestimony->nome = $postVars['nome'] ?? $obTestimony->nome;
		$obTestimony->mensagem = $postVars['mensagem'] ?? $obTestimony->mensagem;
		$obTestimony->atualizar();
		
		//Redireciona o usuário
		$request->getRouter()->redirect('/admin/testimonies/'.$obTestimony->id.'/edit?status=updated');
		
		
	}
	
	
	//Metodo responsávelpor retornar o formulário de Exclusão de um depoimento
	public static function getDeleteTestimony($request,$id){
		//obtém o deopimento do banco de dados
		$obTestmony = EntityTestimony::getTestimonyById($id);
		
		//Valida a instancia
		if(!$obTestmony instanceof EntityTestimony){
			$request->getRouter()->redirect('/admin/testimonies');
		}
		
		
		//Conteúdo do Formulário
		$content = View::render('admin/modules/testimonies/delete',[
				'nome' => $obTestmony->nome,
				'mensagem' => $obTestmony->mensagem
				
				
		]);
		
		//Retorna a página completa
		return parent::getPanel('Excluir Depoimentos > WDEV', $content,'testimonies');
		
	}
	
	//Metodo responsável por Excluir um depoimento
	public static function setDeleteTestimony($request,$id){
		//obtém o deopimento do banco de dados
		$obTestimony = EntityTestimony::getTestimonyById($id);
		
		//Valida a instancia
		if(!$obTestimony instanceof EntityTestimony){
			$request->getRouter()->redirect('/admin/testimonies');
		}
		
		//Exclui o depoimento
		$obTestimony->excluir();
		
		//Redireciona o usuário
		$request->getRouter()->redirect('/admin/testimonies?status=deleted');
		
		
	}
	
	
}