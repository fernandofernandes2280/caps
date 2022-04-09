<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class Instrumento extends Generica {
		
	public $descricao;
	
	//Método responsavel por cadastrar no banco de dados
	public function cadastrar(){
		
		//Insere paciente no banco de dados
		$this->id = (new Database('instrumentos'))->insert([
				'nome'=>$this->nome,
				'descricao'=>$this->descricao
		]);
		//Sucesso
		return true;
	}
	
	//Método responsavel por retornar um registro com base no seu Id
	public static function getInstrumentoById($id){
		return self::getInstrumentos('id = '.$id)->fetchObject(self::class);
		
	}
	
	//Método responsavel por retornar registros
	public static function getInstrumentos($where = null, $order = null, $limit = null, $fields = '*') {
		return (new Database('instrumentos'))->select($where,$order,$limit,$fields);
	}
	
	//Método responsavel por atualizar os dados no banco
	public function atualizar(){
		return (new Database('instrumentos'))->update('id = '.$this->id,[
				'nome' 		=> $this->nome,
				'descricao' 	=> $this->descricao,
		]);
		
		
	}
	
	//Método responsavel por excluir 
	public function excluir(){
		return (new Database('instrumentos'))->delete('id = '.$this->id);
		
		//Sucesso
		return true;
	}
	
	
}