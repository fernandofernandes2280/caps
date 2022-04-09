<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class Testimony{
	
	//id do depoimento
	public $id;
	
	//nome do usuário que fez o depoimento
	public $nome;
	
	//mensagem do depoimento
	public $mensagem;
	
	//data de publicação do depoimento
	public $data;
	
	//Método responsavel por cadastrar a instancia atual no Banco de Dados
	public function cadastrar(){
		//Define a data
		$this->data=date('Y-m-d H:i:s');
		
		//Insere depoimento no Banco de Dados
		$this->id=(new Database('depoimentos'))->insert([
				'nome' 		=> $this->nome,
				'mensagem' 	=> $this->mensagem,
				'data' 		=> $this->data
		]);
		
		//Sucesso
		return true;
	}
	
	//Método responsavel por atualizar os dados Banco com a instancia atual
	public function atualizar(){
						
		//Atualiza o depoimento no Banco de Dados
		return (new Database('depoimentos'))->update('id = '.$this->id,[
				'nome' 		=> $this->nome,
				'mensagem' 	=> $this->mensagem
				
		]);
		
		//Sucesso
		return true;
	}
	
	//Método responsavel por excluir um depoimento do banco de dadosl
	public function excluir(){
		
		//Exclui o depoimento no Banco de Dados
		return (new Database('depoimentos'))->delete('id = '.$this->id);
		
		//Sucesso
		return true;
	}
	
	
	//Método responsavel por retornar um depoimento com base no seu Id
	public static function getTestimonyById($id){
		return self::getTestimonies('id = '.$id)->fetchObject(self::class);
		
	}
	
	//Método responsavel por retornar Depoimentos
	public static function getTestimonies($where = null, $order = null, $limit = null, $fields = '*') {
		return (new Database('depoimentos'))->select($where,$order,$limit,$fields);		
	}
	
	
	
	
	
}