<?php

namespace App\Controller\Admin;

use \App\Utils\View;
use \App\Model\Entity\Procedimento as EntityProcedimento;
use \App\Model\Entity\Instrumento as EntityInstrumento;
use \WilliamCosta\DatabaseManager\Pagination;

class Procedimento extends Page{
	
	//esconde busca rápida de prontuário no navBar
	private static $hidden = 'hidden';
	
	//Método responsavel por obter a renderização da listagem dos registros do banco
	private static function getProcedimentosItems($request, &$obPagination){

		$itens = '';
		
		//Quantidade total de registros
		$quantidadetotal =  EntityProcedimento::getprocedimentos(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;
		
		//Página atual
		$queryParams = $request->getQueryParams();
		$paginaAtual = $queryParams['page'] ?? 1;
		
		//Instancia de paginacao
		$obPagination = new Pagination($quantidadetotal,$paginaAtual,5);
		
		//Resultados da Página
		$results = EntityProcedimento::getprocedimentos(null, 'id',$obPagination->getLimit());
		
		//Renderiza o item
		while ($ob = $results->fetchObject(EntityProcedimento::class)) {
		
			//View de listagem
			$itens.= View::render('admin/modules/procedimentos/item',[
					'id' => $ob->id,
					'codProcedimento' => $ob->codProcedimento,
					'nome' => $ob->nome,
					'instrumento' => EntityInstrumento::getInstrumentoById($ob->instrumento)->nome

			]);
		}
		
		
		//Retorna a listagem
		return $itens;
		
	}
	
	
	//Método responsavel por renderizar a view de Listagem
	public static function getProcedimentos($request){
		
		//Conteúdo da Home
		$content = View::render('admin/modules/procedimentos/index',[
				'itens' => self::getProcedimentosItems($request, $obPagination),
				'pagination' => parent::getPagination($request, $obPagination),
				'status' => self::getStatus($request),
				'title' => 'Procedimentos',
				'statusMessage' => ''
		]);
		
		//Retorna a página completa
		return parent::getPanel('Procedimentos > Siscaps', $content,'procedimentos', self::$hidden);
		
	}
	
	//Metodo responsávelpor retornar o formulário de cadastro 
	public static function getNewProcedimento($request){
		
		//Conteúdo do Formulário
		$content = View::render('admin/modules/procedimentos/form',[
				'title' => 'Cadastrar Procedimento',
				'nome' => '',
				'codProcedimento' => '',
				'statusMessage' => '',
				'optionInstrumento' => self::getInstrumentos(null),

				
				
		]);
		
		//Retorna a página completa
		return parent::getPanel('Cadastrar Procedimento > Siscaps', $content,'procedimentos', self::$hidden);
		
	}
	
	//Método responsavel por listar as Instrumentos, selecionando o corrente
	public static function getInstrumentos($id){
		$resultados = '';
		$results =  EntityInstrumento::getInstrumentos(null,'nome asc',null);
		//verifica se o id não é nulo e obtém a registro do banco de dados
		if (!is_null($id)) {
			$selected = '';
			while ($ob = $results -> fetchObject(EntityInstrumento::class)) {
				
				//seleciona o Instrumento atual
				$ob->id == $id ? $selected = 'selected' : $selected = '';
				//View de os Instrumentos
				$resultados .= View::render('admin/modules/procedimentos/itemSelect',[
						'id' => $ob ->id,
						'nome' => $ob->nome,
						'selecionado' => $selected
				]);
			}
			//retorna os as Escolaridades
			return $resultados;
		}else{ //se as Escolaridades for nulo, lista todos e seleciona um em branco
			while ($ob = $results -> fetchObject(EntityInstrumento::class)) {
				
				$resultados .= View::render('admin/modules/procedimentos/itemSelect',[
						'id' => $ob ->id,
						'nome' => $ob->nome,
						'selecionado' => ''
				]);
			}
			//retorna os as Escolaridades
			return $resultados;
		}
	}
	
	
	//Metodo responsávelpor por cadastrar no banco
	public static function setNewProcedimento($request){
		//Post vars
		$postVars = $request->getPostVars();
		
		$codigo = $postVars['codProcedimento'] ?? '';
		$nome = $postVars['nome'] ?? '';
		$instrumento = $postVars['instrumento'] ?? '';
		
		//Nova instancia
		$ob = new EntityProcedimento();
		$ob->codProcedimento = $codigo;
		$ob->nome = $nome;
		$ob->instrumento = $instrumento;
		
		$ob->cadastrar();
		
		//Redireciona o usuário
		$request->getRouter()->redirect('/admin/procedimentos/'.$ob->id.'/edit?status=created');
		
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
				return Alert::getSuccess('Procedimento criado com sucesso!');
			break;
			case 'updated':
				return Alert::getSuccess('Procedimento atualizado com sucesso!');
				break;
			case 'deleted':
				return Alert::getSuccess('Procedimento excluído com sucesso!');
				break;
			
		}
	}
	
	
	//Metodo responsávelpor retornar o formulário de Edição 
	public static function getEditProcedimento($request,$id){
		//obtém o usuário do banco de dados
		$ob = EntityProcedimento::getProcedimentoById($id);
		
		//Valida a instancia
		if(!$ob instanceof EntityProcedimento){
			$request->getRouter()->redirect('/admin/procedimentos');
		}
		
		//Conteúdo do Formulário
		$content = View::render('admin/modules/procedimentos/form',[
				'title' => 'Editar Procedimento',
				'nome' => $ob->nome,
				'codProcedimento' => $ob->codProcedimento,
				'optionInstrumento' => self::getInstrumentos($ob->instrumento),
				'statusMessage' => self::getStatus($request),
				
				
		]);
		
		//Retorna a página completa
		return parent::getPanel('Editar Cid10 > Siscaps', $content,'cid10', self::$hidden);
		
	}
	
	//Metodo responsável por gravar a atualizacao de um usuário
	public static function setEditProcedimento($request,$id){
		//obtém o usuário do banco de dados
		$ob = EntityProcedimento::getProcedimentoById($id);
		
		//Valida a instancia
		if(!$ob instanceof EntityProcedimento){
			$request->getRouter()->redirect('/admin/procedimentos');
		}
		
		
		//Post Vars
		$postVars = $request->getPostVars();
		$codigo = $postVars['codProcedimento'] ?? '';
		$nome = $postVars['nome'] ?? '';
		$instrumento = $postVars['instrumento'] ?? '';
		
		//Atualiza a instância
		$ob->codProcedimento = $codigo;
		$ob->nome = $nome;
		$ob->instrumento = $instrumento;
		
		$ob->atualizar();
		
		//Redireciona o usuário
		$request->getRouter()->redirect('/admin/procedimentos/'.$ob->id.'/edit?status=updated');
		
		
	}
	
	
	//Metodo responsávelpor retornar o formulário de Exclusão 
	public static function getDeleteProcedimento($request,$id){
		//obtém o registro do banco de dados
		$ob = EntityProcedimento::getProcedimentoById($id);
		
		//Valida a instancia
		if(!$ob instanceof EntityProcedimento){
			$request->getRouter()->redirect('/admin/procedimentos');
		}
		
		
		
		//Conteúdo do Formulário
		$content = View::render('admin/modules/procedimentos/delete',[
				'codProcedimento' => $ob->codProcedimento,
				'nome' => $ob->nome,
				'instrumento' => EntityInstrumento::getInstrumentoById($ob->instrumento)->nome,
				
				
		]);
		
		//Retorna a página completa
		return parent::getPanel('Excluir Procedimento > Siscaps', $content,'procedimentos' , self::$hidden);
		
	}
	
	//Metodo responsável por Excluir 
	public static function setDeleteProcedimento($request,$id){
		//obtém o usuário do banco de dados
		$ob = EntityProcedimento::getProcedimentoById($id);
		
		//Valida a instancia
		if(!$ob instanceof EntityProcedimento){
			$request->getRouter()->redirect('/admin/procedimentos');
		}
		
			
		
		
		//Exclui
			$ob->excluir($id);
	
		
		
		//Redireciona o usuário
		$request->getRouter()->redirect('/admin/procedimentos?status=deleted');
		
		
	}
	
	
}