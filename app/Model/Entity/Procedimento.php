<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class Procedimento {
	
	//código do procedimento no SIaSUS
	public $codProcedimento;
	//Instrumento utilizado pelo procedimento
	public $instrumento;
	
		
	//Método responsavel por cadastrar um bairro no banco de dados
	public function cadastrar(){
		
		//Insere paciente no banco de dados
		$this->id = (new Database('procedimentos'))->insert([
				'codProcedimento'=>$this->codProcedimento,
				'nome'=>$this->nome,
				'instrumento'=>$this->instrumento,
		]);
		//Sucesso
		return true;
	}
	//Método responsavel por atualizar os dados no banco
	public function atualizar(){
		return (new Database('procedimentos'))->update('id = '.$this->id,[
				'codProcedimento'=>$this->codProcedimento,
				'nome'=>$this->nome,
				'instrumento'=>$this->instrumento,
		]);
		
		
	}
	
	//Método responsavel por excluir
	public function excluir(){
		
		
		
		try {
			return (new Database('procedimentos'))->delete('id = '.$this->id);
			
		} catch (\Exception $e) {
			echo "erro";
		}
		//Sucesso
		return true;
	}
	//Método responsavel por retornar um bairro com base no seu Id
	public static function getProcedimentoById($id){
		return self::getprocedimentos('id = '.$id)->fetchObject(self::class);
		
	}
	
	//Método responsavel por retornar Pacientes
	public static function getprocedimentos($where = null, $order = null, $limit = null, $fields = '*') {
		return (new Database('procedimentos'))->select($where,$order,$limit,$fields);
	}
	
	
	
}