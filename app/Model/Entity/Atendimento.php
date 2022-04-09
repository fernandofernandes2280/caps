<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;
use App\Controller\Admin\Logs;

class Atendimento {
	
	
	public $id;
	public $codPronto;
	public $data;
	public $idProfissional;
	public $idProcedimento;
	public $status;
	public $idade;
	
	
		
	//Método responsavel por cadastrar um bairro no banco de dados
	public function cadastrar(){
		
		//Insere paciente no banco de dados
		$this->id = (new Database('atendimentos'))->insert([
				'codPronto'=>$this->codPronto,
				'data'=>date(''.$this->data.' H:i:s'),
				'idProfissional'=>$this->idProfissional,
				'idProcedimento'=>$this->idProcedimento,
				'status'=>$this->status,
				'idade' =>$this->idade
		]);
		
		//Grava o Log do usuário
		Logs::setNewLog('atendimentos',__FUNCTION__ ,'Profissional: '.Profissional::getProfissionalById($this->idProfissional)->nome.' Procedimento: '.Procedimento::getProcedimentoById($this->idProcedimento)->nome.' Status: '.$this->status);
		
		//Sucesso
		return true;
	}
	
	
	//Método responsavel por atualizar os banco de dados com os dados da instancia atual de paciente
	public function atualizar(){
//		$this->data = date('Y-m-d H:i:s');

	
		//Grava o Log do usuário
		Logs::setNewLog('atendimentos',__FUNCTION__ ,'Profissional: '.Profissional::getProfissionalById($this->idProfissional)->nome.' Procedimento: '.Procedimento::getProcedimentoById($this->idProcedimento)->nome.' Status: '.$this->status);
		
		//Atualiza paciente no banco de dados
		return (new Database('atendimentos'))->update('id = '.$this->id,[
				'data'=>$this->data,
				'idProfissional'=>$this->idProfissional,
				'idProcedimento'=>$this->idProcedimento,
				'status'=>$this->status,
				'idade' =>$this->idade
		]);
	
		
	}
	
	
	//Método responsavel por excluir um Atendimento do banco de dados
	public function excluir(){
		
		//Grava o Log do usuário
		Logs::setNewLog('atendimentos',__FUNCTION__ ,'Profissional: '.Profissional::getProfissionalById($this->idProfissional)->nome.' Procedimento: '.Procedimento::getProcedimentoById($this->idProcedimento)->nome.' Status: '.$this->status);
		
			//Exclui o depoimento no Banco de Dados
		return (new Database('atendimentos'))->delete('id = '.$this->id);
		
		
		
		//Sucesso
		return true;
	}
	
	
	//Método responsavel por retornar um bairro com base no seu Id
	public static function getAtendimentoDuplicado($data,$codPronto, $profissional,$procedimento){

		return self::getAtendimentos(' DATE(data) ="'.$data .'" and codPronto = '.$codPronto.' and idProfissional = '.$profissional .' and idProcedimento = '.$procedimento)->fetchObject(self::class);
		
	}
	
	//Método responsavel por retornar um bairro com base no seu Id
	public static function getAtendimentoById($id){
		return self::getAtendimentos('id = '.$id)->fetchObject(self::class);
		
	}
	
	//Método responsavel por retornar um bairro com base no seu Id
	public static function getAtendimentoByCodPronto($codPronto){
		return self::getAtendimentos('codPronto = '.$codPronto)->fetchObject(self::class);
		
	}
	
	//Método responsavel por retornar Pacientes
	public static function getAtendimentos($where = null, $order = null, $limit = null, $fields = '*') {
		return (new Database('atendimentos'))->select($where,$order,$limit,$fields);
	}
	
	
	
}