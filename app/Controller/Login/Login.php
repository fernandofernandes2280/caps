<?php

namespace App\Controller\Login;

use \App\Utils\View;
use \App\Model\Entity\User;
use \App\Session\Visitor\Login as SessionVisitorLogin;
use \App\Session\Admin\Login as SessionAdminLogin;
use \App\Controller\Admin\Alert;
use \App\Controller\Admin\Page;
use Bissolli\ValidadorCpfCnpj\CPF;
use \App\Controller\Comunication\Email;


class Login extends Page{
	
	public static function geraPalavraAleatoria(){
		$vogais = array('a','e','i','o','u');
		$consoantes = array('b','c','d','f','g','h','nh','lh','ch','j','k','l','m','n','p','qu','r','rr','s','ss','t','v','w','x','y','z',);
		
		$palavra = '';
		$tamanho_palavra = rand(2,5);
		$contar_silabas = 0;
		while($contar_silabas < $tamanho_palavra){
			$vogal = $vogais[rand(0,count($vogais)-1)];
			$consoante = $consoantes[rand(0,count($consoantes)-1)];
			$silaba = $consoante.$vogal;
			$palavra .=$silaba;
			$contar_silabas++;
			unset($vogal,$consoante,$silaba);
		}
		return $palavra;
		
	}
	
	
	
	public static function getRecuperarSenha($request,$errorMessage = null){
		//Status
		$status = !is_null($errorMessage) ? Alert::getError($errorMessage)  : '';
		//COnteúdo da página de login
		$content = View::render('admin/recuperarSenha',[
				'status' => $status
				
		]);
		
		//Retornar a página completa
		return parent::getPage('Login > SisCaps', $content);
		
	}
	
	public static function setRecuperarSenha($request){
		//Post Vars
		$postVars = $request->getPostVars();
		
		//busca usuário pelo CPF sem a maskara
		$obUser = User::getUserByEmail($postVars['email']);
		
		
		if(!$obUser instanceof User){
			return self::getRecuperarSenha($request,'E-mail não cadastrado!');
		}
		
		
		
		$senhaAleatoria = self::geraPalavraAleatoria();
		
		$address = $postVars['email'];
		$subject = 'Senha temporária Siscaps';
		$body = 'Senha aleatória: <b>'.$senhaAleatoria.'</b> <br><br><b>Use está senha para acessar o Siscaps. Depois de entrar, Não esqueça de alterá-la. </b>';
		
		$obMail = new Email;
		$sucesso = $obMail->sendEmail($address, $subject, $body);
		
		
		if($sucesso){
			$statusEmail = Alert::getSuccess('Uma senha aleatória foi enviada para seu e-mail!');
			$obUser->senha = password_hash($senhaAleatoria,PASSWORD_DEFAULT);
			$obUser->atualizar();
			
		}else{
			$statusEmail = Alert::getError($obMail->getError());
		}
		
		//COnteúdo da página de login
		$content = View::render('admin/recuperarSenha',[
				'status' => $statusEmail,
				
		]);
		
		//Retornar a página completa
		return parent::getPage('Login > SisCaps', $content);
		
	}
	
	
	//Método responsável poer retornar a renderizacao da página de login
	public static function getLogin($request,$errorMessage = null){
		
		//Status
		$status = !is_null($errorMessage) ? Alert::getError($errorMessage)  : '';
		
		
		//COnteúdo da página de login
		$content = View::render('admin/login',[
				'status' => $status,
				
		]);
		
		//Retornar a página completa
		return parent::getPage('Login > SisCaps', $content);
		
	}
	
	//Método responsavel por definir o login do usuario
	public static function setLogin($request){
		
		//Post Vars
		$postVars = $request->getPostVars();
	
		$cpf = $postVars['cpf'] ?? '';
		$senha = $postVars['senha'] ?? '';
		
		//instancia classe pra verificar CPF
		$validaCpf = new CPF($cpf);
		
		//verifica se é válido o cpf
		if (!$validaCpf->isValid()){
			
			return self::getLogin($request,'CPF inválido');
		}
		
		
		//busca usuário pelo CPF sem a maskara
		$obUser = User::getUserByCPF($validaCpf->getValue());
		
		
		if(!$obUser instanceof User){
			return self::getLogin($request,'CPF não cadastrado!');
		}
		
		//Verifica a senha do usuário
		if(!password_verify($senha, $obUser->senha)){
			return self::getLogin($request,'Senha inválida');
		}
		
		if($obUser->tipo == 'Admin' || $obUser->tipo == 'Operador' ){
			//Cria a sessão de Login de Admin
			SessionAdminLogin::login($obUser);
			//redireciona o usuario Admin
			$request->getRouter()->redirect('/admin/pacientes');
		}else{
			
			//Cria a sessão de Login de Visitante
			SessionVisitorLogin::login($obUser);
			//redireciona o usuario Visitante
			$request->getRouter()->redirect('/visitor/pacientes');
			
		}
		
		
		
	}
	
	//Método responsavel por deslogar o usuario
	public static function setLogout($request){
		//Destroi a sessões de Login
		SessionVisitorLogin::logout();
		SessionAdminLogin::logout();
		//redireciona o usuario para a tela de login
		$request->getRouter()->redirect('/');
		
		
	}
	
	
	
	
}