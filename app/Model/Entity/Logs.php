<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class Logs{
	
	//ID do Log
	public $id;
	
	//data do Log
	public $data;
	
	//ip do Log
	public $ip;
	
	//user do Log
	public $user;
	
	//acao do Log
	public $acao;
	
	//tabela do Log
	public $tabela;
	
	//campos do Log
	public $campos;
	
	//Método responsavel por cadastrar o usuário no Banco de Dados
	public function cadastrar(){
		$this->data = date('Y-m-d H:i:s');
		$this->ip = $_SERVER['REMOTE_ADDR'];
		$this->user = $_SESSION['admin']['usuario']['id'] ?? $_SESSION['visitor']['usuario']['id'];
		//Insere usuário no Banco de Dados
		$this->id=(new Database('logs'))->insert([
				'data'=> $this->data,
				'ip' 	=> $this->ip,
				'user' 		=> $this->user,
				'acao' 	=> $this->acao,
				'tabela' 	=> $this->tabela,
				'campos' 	=> $this->campos,
		]);
		
		//Sucesso
		return true;
	}
	
	
	//Método responsavel por retornar Usuários
	public static function getLogs($where = null, $order = null, $limit = null, $fields = '*') {
		return (new Database('logs'))->select($where,$order,$limit,$fields);
	}
	
}