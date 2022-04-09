<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class Profissional {
	
		
	//Método responsavel por cadastrar um bairro no banco de dados
	public function cadastrar(){
		
		//Insere paciente no banco de dados
		$this->id = (new Database('profissionais'))->insert([
				'nome'=>$this->nome,
		]);
		//Sucesso
		return true;
	}
	
	//Método responsavel por retornar um bairro com base no seu Id
	public static function getProfissionalById($id){
		return self::getProfissionais('id = '.$id)->fetchObject(self::class);
		
	}
	
	//Método responsavel por retornar Pacientes
	public static function getProfissionais($where = null, $order = null, $limit = null, $fields = '*') {
		return (new Database('profissionais'))->select($where,$order,$limit,$fields);
	}
	
	
	
}