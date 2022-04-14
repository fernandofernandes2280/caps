<?php

namespace App\Controller\Admin;

use \App\Utils\View;
use \App\Model\Entity\Logs as EntityLogs;
use \WilliamCosta\DatabaseManager\Pagination;
use Bissolli\ValidadorCpfCnpj\CPF;
use \App\Utils\Funcoes;
use App\Model\Entity\User;

class Logs extends Page{
	
	//esconde busca rápida de prontuário no navBar
	private static $hidden = 'hidden';
	
	//Método responsavel por obter a renderização dos itens de usuários para a página
	private static function getLogsItems($request, &$obPagination){
		//Usuários
		$itens = '';
		
		//Quantidade total de registros
		$quantidadetotal =  EntityLogs::getLogs(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;
		
		//Página atual
		$queryParams = $request->getQueryParams();
		$paginaAtual = $queryParams['page'] ?? 1;
		
		//Instancia de paginacao
		$obPagination = new Pagination($quantidadetotal,$paginaAtual,5);
		
		//Resultados da Página
		$results = EntityLogs::getLogs(null, 'data DESC',$obPagination->getLimit());
		
		//Renderiza o item
		while ($obLog = $results->fetchObject(EntityLogs::class)) {
		
			//View de depoimentos
			$itens.= View::render('admin/modules/logs/item',[
					'id' => $obLog->id,
					'data' => date('d/m/Y H:i:s',strtotime($obLog->data)),
					'ip' => $obLog->ip,
					'user' =>User::getUserById($obLog->user)->nome.' ('.Funcoes::mask(User::getUserById($obLog->user)->cpf, '###.###.###-##')  .')',
					'tabela' => $obLog->tabela,
					'acao' => $obLog->acao,
					'campos' => $obLog->campos
			]);
		}
		
		
		//Retorna os depoimentos
		return $itens;
		
	}
	
	
	//Método responsavel por renderizar a view de Listagem de USuários
	public static function getLogs($request){
		
		//Conteúdo da Home
		$content = View::render('admin/modules/logs/index',[
				'itens' => self::getLogsItems($request, $obPagination),
				'pagination' => parent::getPagination($request, $obPagination),
				'title' => 'Logs'

				
		]);
		
		//Retorna a página completa
		return parent::getPanel('Usuários > SISCAPS', $content,'logs', self::$hidden);
		
	}
	
	
	//Metodo responsávelpor por cadastrar um Log no banco
	public static function setNewLog($tabela,$acao, $campos){
		
		//Nova instancia de Logs
		$obLogs = new EntityLogs();
		$obLogs->acao = $acao;//$request->getUri();
		$obLogs->tabela = $tabela;
		$obLogs->campos = $campos;
		$obLogs->cadastrar();
		
	}
	
}