<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class Cidade {
		
	//Método responsavel por cadastrar um bairro no banco de dados
	public function cadastrar(){
		
		//Insere paciente no banco de dados
		$this->id = (new Database('cidades'))->insert([
				'nome'=>$this->nome,
		]);
		//Sucesso
		return true;
	}
	
	//Método responsavel por retornar um bairro com base no seu Id
	public static function getCidadeById($id){
		return self::getCidades('id = '.$id)->fetchObject(self::class);
		
	}
	
	//Método responsavel por retornar Pacientes
	public static function getCidades($where = null, $order = null, $limit = null, $fields = '*') {
		return (new Database('cidades'))->select($where,$order,$limit,$fields);
	}
	
	
	
}