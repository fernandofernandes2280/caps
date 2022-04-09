<?php

namespace App\Controller\Admin;

use \App\Utils\View;
use \App\Model\Entity\Substancia as EntitySubstancia;
use \WilliamCosta\DatabaseManager\Pagination;

class Substancia extends Page{
	
	//esconde busca rápida de prontuário no navBar
	private static $hidden = 'hidden';
	
	//Método responsavel por obter a renderização da listagem dos registros do banco
	private static function getSubstanciasItems($request, &$obPagination){

		$itens = '';
		
		//Quantidade total de registros
		$quantidadetotal =  EntitySubstancia::getSubstancias(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;
		
		//Página atual
		$queryParams = $request->getQueryParams();
		$paginaAtual = $queryParams['page'] ?? 1;
		
		//Instancia de paginacao
		$obPagination = new Pagination($quantidadetotal,$paginaAtual,5);
		
		//Resultados da Página
		$results = EntitySubstancia::getSubstancias(null, 'id',$obPagination->getLimit());
		
		//Renderiza o item
		while ($ob = $results->fetchObject(EntitySubstancia::class)) {
		
			//View de listagem
			$itens.= View::render('admin/modules/substancias/item',[
					'id' => $ob->id,
					'nome' => $ob->nome,
					

			]);
		}
		
		
		//Retorna a listagem
		return $itens;
		
	}
	
	
	//Método responsavel por renderizar a view de Listagem
	public static function getSubstancias($request){
		
		//Conteúdo da Home
		$content = View::render('admin/modules/substancias/index',[
				'itens' => self::getSubstanciasItems($request, $obPagination),
				'pagination' => parent::getPagination($request, $obPagination),
				'status' => self::getStatus($request),
				'title' => 'Substancias',
				'statusMessage' => ''
		]);
		
		//Retorna a página completa
		return parent::getPanel('Substancias > Siscaps', $content,'substancias', self::$hidden);
		
	}
	
	//Metodo responsávelpor retornar o formulário de cadastro 
	public static function getNewSubstancia($request){
		
		//Conteúdo do Formulário
		$content = View::render('admin/modules/substancias/form',[
				'title' => 'Cadastrar Substância',
				'nome' => '',
				'statusMessage' => '',
				
		]);
		
		//Retorna a página completa
		return parent::getPanel('Cadastrar Substância > Siscaps', $content,'substancias', self::$hidden);
		
	}
	
	
	//Metodo responsávelpor por cadastrar no banco
	public static function setNewSubstancia($request){
		//Post vars
		$postVars = $request->getPostVars();
		

		$nome = $postVars['nome'] ?? '';

		
		//Nova instancia
		$ob = new EntitySubstancia();
		$ob->nome = $nome;
		
		$ob->cadastrar();
		
		//Redireciona o usuário
		$request->getRouter()->redirect('/admin/substancias/'.$ob->id.'/edit?status=created');
		
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
				return Alert::getSuccess('Substância criada com sucesso!');
			break;
			case 'updated':
				return Alert::getSuccess('Substância atualizada com sucesso!');
				break;
			case 'deleted':
				return Alert::getSuccess('Substância excluída com sucesso!');
				break;
			
		}
	}
	
	
	//Metodo responsávelpor retornar o formulário de Edição 
	public static function getEditSubstancia($request,$id){
		//obtém o usuário do banco de dados
		$ob = EntitySubstancia::getSubstanciaById($id);
		
		//Valida a instancia
		if(!$ob instanceof EntitySubstancia){
			$request->getRouter()->redirect('/admin/substancias');
		}
		
		//Conteúdo do Formulário
		$content = View::render('admin/modules/substancias/form',[
				'title' => 'Editar Substância',
				'nome' => $ob->nome,
				'statusMessage' => self::getStatus($request),
				
				
		]);
		
		//Retorna a página completa
		return parent::getPanel('Editar Substância > Siscaps', $content,'substancias', self::$hidden);
		
	}
	
	//Metodo responsável por gravar a atualizacao de um usuário
	public static function setEditSubstancia($request,$id){
		//obtém o usuário do banco de dados
		$ob = EntitySubstancia::getSubstanciaById($id);
		
		//Valida a instancia
		if(!$ob instanceof EntitySubstancia){
			$request->getRouter()->redirect('/admin/substancias');
		}
		
		
		//Post Vars
		$postVars = $request->getPostVars();
		$nome = $postVars['nome'] ?? '';
		
		//Atualiza a instância
		$ob->nome = $nome;
		
		$ob->atualizar();
		
		//Redireciona o usuário
		$request->getRouter()->redirect('/admin/substancias/'.$ob->id.'/edit?status=updated');
		
		
	}
	
	
	//Metodo responsávelpor retornar o formulário de Exclusão 
	public static function getDeleteSubstancia($request,$id){
		//obtém o registro do banco de dados
		$ob = EntitySubstancia::getSubstanciaById($id);
		
		//Valida a instancia
		if(!$ob instanceof EntitySubstancia){
			$request->getRouter()->redirect('/admin/substancias');
		}
		
		
		
		//Conteúdo do Formulário
		$content = View::render('admin/modules/substancias/delete',[
				
				'nome' => $ob->nome,
				'title' => 'Excluir Substancia'
				
		]);
		
		//Retorna a página completa
		return parent::getPanel('Excluir Substância > Siscaps', $content,'substancias' , self::$hidden);
		
	}
	
	//Metodo responsável por Excluir 
	public static function setDeleteSubstancia($request,$id){
		//obtém o usuário do banco de dados
		$ob = EntitySubstancia::getSubstanciaById($id);
		
		//Valida a instancia
		if(!$ob instanceof EntitySubstancia){
			$request->getRouter()->redirect('/admin/substancias');
		}
		
			
		
		
		//Exclui
			$ob->excluir($id);
	
		
		
		//Redireciona o usuário
		$request->getRouter()->redirect('/admin/substancias?status=deleted');
		
		
	}
	
	
}