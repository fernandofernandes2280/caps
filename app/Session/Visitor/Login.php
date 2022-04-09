<?php

namespace App\Session\Visitor;

class Login{
	
	//Método responsavel por iniciar a sessão
	private static function init(){
		//verifica se a sessao não está ativa
		if(session_status() != PHP_SESSION_ACTIVE ){
			session_start();
		}
	}
	
	//Método responsavel por criar o login do usuário
	public static function login($obUser){
		//Inicia a sessão
		self::init();
		
		//Define a sessao do usuario
		$_SESSION['visitor']['usuario'] = [
				'id' => $obUser->id,
				'nome' => $obUser->nome,
				'email' => $obUser->email,
				'tipo' => $obUser->tipo
		];
		
		//Sucesso
		return true;
	}
	
	//Método responsavel por veririfcar se o ususario está logado
	public static function isLogged() {
		//Inicia a Sessão
		self::init();
		
		//retorna a verificação
		return isset($_SESSION['visitor']['usuario']['id']);
		
		
	}
	
	//Método responsavel por executar logout do usuario
	public static function logout(){
		//Inicia a Sessão
		self::init();
	
		//desloga usuário
		unset($_SESSION['visitor']['usuario']);
		
		//sucesso
		return true;
	}
	
	
}