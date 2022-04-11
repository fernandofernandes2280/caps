<?php

namespace App\Controller\Admin;

use App\Utils\View;
use \App\Model\Entity\User as EntityUser;

class Page{

	private static $teste = 'Usuários';
	
	
	//Módulos disponíveis no painel
	private static $modules = [
		
		'pacientes' =>[
					'label' => 'Pacientes',
					'link' => URL.'/admin/pacientes',
				'material-icons' => 'family_restroom',
				'modal' => ''
		],
		'atendimentos' =>[
					'label' => 'Atendimentos',
				'link' => URL.'/admin/atendimentos/0001/atendimento',
				'material-icons' => 'assignment',
				'modal' => ''
			],
		'agendas' =>[
				'label' => 'Agendas',
				'link' => URL.'/admin/agendas',
				'material-icons' => 'people',
				'modal' => ''
		],
			'users' =>[
					'label' => 'Usuários',
					'link' => URL.'/admin/users',
					'material-icons' => 'people',
					'modal' => ''
			],
			'logs' =>[
					'label' => 'Logs',
					'link' => URL.'/admin/logs',
					'material-icons' => 'people',
					'modal' => ''
			],
			'relatorios' =>[
					'label' => 'Relatórios',
					'link' => '#',
					'material-icons' => 'people',
					'modal' => 'data-toggle="modal" data-target="#relatorioModal"'
			],
			'trocarSenha' =>[
					'label' => 'Trocar Senha',
					'link' => URL.'/admin/trocarSenha'  ,
					'material-icons' => 'assignment',
					'modal' => ''
			],
			
			
	];
	
	
	//Módulos dropdown Produção
	private static $modulesDropdownProducao = [
			
			'Producao' =>[
					'label' => 'Produção',
					'link' => ''
			],
			
	];
	
	//Módulos de itens do Menu dropdown Produção
	private static $modulesDropdownItemsProducao = [
			
			'Raas' =>[
					'label' => 'Raas',
					'idBotao' => 'btnRaas',
					
			],
			'Bpac' =>[
					'label' => 'Bpac',
					'idBotao' => 'btnBpac',
					
			],
			'Bpai' =>[
					'label' => 'Bpai',
					'idBotao' => 'btnBpai',
					
			]
	];
	//método que renderiza os itens do dropdown Producao
	private static function getDropdownItemsProducao(){
		$linksDropItems='';
		//Itera os módulos
		foreach (self::$modulesDropdownItemsProducao as $hash=>$module){
			$linksDropItems .= View::render('admin/menu/linkDropdown',[
					'label' => $module['label'],
					'idBotao' => $module['idBotao'],
					'material-icons'=>'navigate_next',
					'modal' => 'data-toggle="modal" data-target="#producaoModal"',
					'link' => '#',
			]);
		}
		return  $linksDropItems;
	}
	

	//Módulos dropdown Manutenção
	private static $modulesDropdownManutencao = [
			
			'manutencao' =>[
					'label' => 'Manutenção',
					'link' => ''
			],
			
	];
	//Módulos de itens do Menu dropdown Produção
	private static $modulesDropdownItemsManutencao = [
			
			'cid10' =>[
					'label' => 'Cid10',
					'idBotao' => 'btnCid10',
					'link' => URL.'/admin/cid10',
			],
			'procedimentos' =>[
					'label' => 'Procedimentos',
					'idBotao' => 'btnProcedimentos',
					'link' => URL.'/admin/procedimentos',
			],
			'escolaridade' =>[
					'label' => 'Escolaridades',
					'idBotao' => 'btnEscolaridade',
					'link' => URL.'/admin/escolaridades',
			],
			'substancias' =>[
					'label' => 'Substâncias',
					'idBotao' => 'btnSubstancias',
					'link' => URL.'/admin/substancias',
			],
    	    'profissionais' =>[
    	        'label' => 'Profissionais',
    	        'idBotao' => 'btnProfissionais',
    	        'link' => URL.'/admin/profissionais',
    	    ]
			
	];
	//método que renderiza os itens do dropdown Manutenção
	private static function getDropdownItemsManutencao(){
		$linksDropItems='';
		//Itera os módulos
		foreach (self::$modulesDropdownItemsManutencao as $hash=>$module){
			$linksDropItems .= View::render('admin/menu/linkDropdown',[
					'label' => $module['label'],
					'idBotao' => $module['idBotao'],
					'material-icons'=>'navigate_next',
					'modal' => '',
					'link' => $module['link'],
			]);
		}
		return  $linksDropItems;
	}
	
	//Módulos dropdown
	private static $modulesDropdownRelatorio = [
		
			'Relatorios' =>[
					'label' => 'Relatórios',
					'link' => ''
			]
	];
	//Módulos de itens do Menu dropdown Relatórios
	private static $modulesDropdownItemsRelatorios = [
			
			'Atendimentos' =>[
					'label' => 'Atendimentos',
					'idBotao' => 'btnRelAtend',
					
			]
			
	];
	
	//Módulos de itens do Menu dropdown Relatórios
	private static $modulesDropdownSubItems = [
			
			'Funcionario' =>[
					'label' => 'Por Funcionário',
					'idBotao' => 'btnRelAtendFunc',
			],
			'Procedimento' =>[
					'label' => 'Por Procedimento',
					'idBotao' => 'btnRelAtendProc',
			]
			
			
	];
	//método que renderiza os Sub itens do dropdown Relatório
	private static function getDropdownSubItems(){
		$linksDropItems='';
		//Itera os módulos
		foreach (self::$modulesDropdownSubItems as $hash=>$module){
			$linksDropItems .= View::render('admin/menu/linkDropdown',[
					'label' => $module['label'],
					'idBotao' => $module['idBotao'],
			]);
		}
		return  $linksDropItems;
	}
	
	
	//método que renderiza os itens do dropdown Relatorios
	private static function getDropdownItemsRelatorios(){
		$linksDropItems='';
		//Itera os módulos
		foreach (self::$modulesDropdownItemsRelatorios as $hash=>$module){
			$linksDropItems .= View::render('admin/menu/linkDropdownSub',[
					'label' => $module['label'],
					'idBotao' => $module['idBotao'],
					'itensSub' => self::getDropdownSubItems(),
					'material-icons'=>'arrow_right'
			]);
		}
		return  $linksDropItems;
	}

	

	
	
	

	
	//Método responsável por renderizar a view do menu do painel
	private static function getMenu($currentModule){
		
		//Links do Menu
		$links ='';
		

		//Itera os módulos Menu simples
		foreach (self::$modules as $hash=>$module){
			
			//desabilita módulo usuários e Logs para Operador
			if($module['label'] == 'Usuários' && $_SESSION['admin']['usuario']['tipo'] == 'Operador') $module['modal'] = 'hidden';
			if($module['label'] == 'Logs' && $_SESSION['admin']['usuario']['tipo'] == 'Operador') $module['modal'] = 'hidden';
			
			
			$links .= View::render('admin/menu/link',[
					'label' => $module['label'],
					'link' => $module['link'],
					'current' => $hash == $currentModule ? 'active' : '',
					'material-icons'=> $module['material-icons'],
					'modal'=> $module['modal']
 					
			]);
			
		}

		
		//Menu dropdown Produção
		$linksDropProducao='';
		foreach (self::$modulesDropdownProducao as $hash=>$module){
			$linksDropProducao .= View::render('admin/menu/dropdown',[
					'label' => $module['label'],
					'itensDropDown' => self::getDropdownItemsProducao(),
					'current' => $hash == $currentModule ? 'active' : '',
					'material-icons'=>'description'
			]);
			
		}
		
		//Menu dropdown Manutenção
		$linksDropManutencao = '';
		foreach (self::$modulesDropdownManutencao as $hash=>$module){
			$linksDropManutencao .= View::render('admin/menu/dropdown',[
					'label' => $module['label'],
					'itensDropDown' => self::getDropdownItemsManutencao(),
					'current' => $hash == $currentModule ? 'active' : '',
					'material-icons'=>'description'
			]);
			
		}
		
		//Itera os módulos Menu dropdown Relatórios
		$linksDropRelatorio='';
		foreach (self::$modulesDropdownRelatorio as $hash=>$module){
			$linksDropRelatorio .= View::render('admin/menu/dropdown',[
					'label' => $module['label'],
					'itensDropDown' => self::getDropdownItemsRelatorios(),
					'current' => $hash == $currentModule ? 'active' : '',
					'material-icons'=>'descriptions'
			]);
			
		}
		
		$id = $_SESSION['admin']['usuario']['id'];
		$obUser = EntityUser::getUserById($id);
		is_null($obUser->foto) ? $imagem = '/resources/imagens/profile.png' : $imagem = '/app/File/files/images/'.$obUser->foto; 
		
		//Retorna a renderização do menu
		return View::render('admin/menu/box',[
				'links' => $links,
				'logout'=>'admin',
				'dropdownProducao' => $linksDropProducao,
				'dropdownRelatorio' => $linksDropRelatorio,
				'dropdownManutencao' => $linksDropManutencao,
				'usuarioLogado' => $_SESSION['admin']['usuario']['nome'] ?? '',
				'tipoUsuario' => $_SESSION['admin']['usuario']['tipo'] ?? '',
				'imagem' => $imagem,
				
		]);
		
	}
	
	//Método responsavel por retornar o conteudo (view) da estrutura generica de página do painel
	public static function getPage($title,$content){
		return View::render('admin/page',[
				'title' => $title,
				'content' => $content,
				'relatorio' =>'/admin/relatorios'
		]);
	}
	
	
	//Método resposanvel por renderizar a view do painel com conteúdos dinâmicos
	public static function getPanel($title, $content, $currentModule, $hidden){
		
		//Renderiza a view do painel
		$contentPanel = View::render('admin/panel',[
				'menu' => self::getMenu($currentModule),
				'content' => $content,
				'navBar'=>View::render('admin/navBar',['hidden' => $hidden]),
				'footer'=>View::render('admin/modules/pacientes/footer',[]),


		]);
		
		//Retorna a página renderizada
		return self::getPage($title, $contentPanel,$currentModule);
		
	}
	
	//Método responsavel por retornar com um link da paginação
	private static function getPaginationLink($queryParams,$page,$url,$label=null){
		//Altera a página
		$queryParams['page'] = $page['page'];
		
		//Link
		$link = $url.'?'.http_build_query($queryParams);
		
		//view
		return View::render('pages/pagination/link',[
				'page' => $label ?? $page['page'],
				'link' => $link,
				'active' => $page['current'] ? 'active' : ''
		]);
	}
	
	//Método responsvel por renderizar o layout de paginação
	public static function getPagination($request,$obPagination){
		//Páginas
		$pages = $obPagination->getPages();
		
		//Verifica a quantidade de páginas
		if(count($pages) <=1) return '';
		
		//Links
		$links = '';
		
		//url atual (sem gets)
		$url = $request->getRouter()->getCurrentUrl();
		
		//GET
		$queryParams = $request->getQueryParams();
		
		//Página ataual
		$currentPage = $queryParams['page'] ?? 1;
		
		//Limite de páginas
		$limit = getenv('PAGINATION_LIMIT');
		
		//Meio da paginação
		$middle = ceil($limit/2);
		
		//Início da paginação
		$start = $middle > $currentPage ? 0 : $currentPage - $middle;
		
		//Ajusta o final da paginação
		$limit = $limit + $start;
		
		//Ajusta o início da paginação
		if($limit > count($pages)){
			$diff = $limit - count($pages);
			$start = $start - $diff;
		}
		
		//Link Inicial
		if($start > 0){
			$links .= self::getPaginationLink($queryParams,reset($pages),$url,'<<');
		}
		
		
		//Renderiza os Links
		foreach ($pages as $page){
			//Verifica o start da paginação
			if($page['page'] <= $start) continue;
			
			//Verifica o limite de paginação
			if($page['page'] > $limit){
				$links .= self::getPaginationLink($queryParams,end($pages),$url,'>>');
				
				break;
			}
			
			$links .= self::getPaginationLink($queryParams,$page,$url);
			
		}
		
		
		
		
		//Renderiza box de paginação
		return View::render('pages/pagination/box',[
				'links' => $links
				
		]);
		
		
		
		
	}
	
	
}