<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;
use App\Controller\Admin\Logs;

class Agenda {
	
	
	public $id;
	public $data;
	public $idProfissional;
	public $status;
		
	//Método responsavel por cadastrar um bairro no banco de dados
	public function cadastrar(){
		
		//Insere paciente no banco de dados
		$this->id = (new Database('agendas'))->insert([
				'data'=>date(''.$this->data.' H:i:s'),
				'idProfissional'=>$this->idProfissional,
				'status'=>$this->status,
		]);
		
		//Grava o Log do usuário
//		Logs::setNewLog('atendimentos',__FUNCTION__ ,'Profissional: '.Profissional::getProfissionalById($this->idProfissional)->nome.' Procedimento: '.Procedimento::getProcedimentoById($this->idProcedimento)->nome.' Status: '.$this->status);
		
		//Sucesso
		return true;
	}
	
	
	//Método responsavel por atualizar os banco de dados com os dados da instancia atual de paciente
	public function atualizar(){
//		$this->data = date('Y-m-d H:i:s');

	
		//Grava o Log do usuário
//		Logs::setNewLog('atendimentos',__FUNCTION__ ,'Profissional: '.Profissional::getProfissionalById($this->idProfissional)->nome.' Procedimento: '.Procedimento::getProcedimentoById($this->idProcedimento)->nome.' Status: '.$this->status);
		
		//Atualiza paciente no banco de dados
		return (new Database('agendas'))->update('id = '.$this->id,[
				'data'=>$this->data,
				'idProfissional'=>$this->idProfissional,
				'status'=>$this->status,
		]);
	}
	
	
	//Método responsavel por excluir um Atendimento do banco de dados
	public function excluir(){
		
		//Grava o Log do usuário
//		Logs::setNewLog('atendimentos',__FUNCTION__ ,'Profissional: '.Profissional::getProfissionalById($this->idProfissional)->nome.' Procedimento: '.Procedimento::getProcedimentoById($this->idProcedimento)->nome.' Status: '.$this->status);
		
			//Exclui o depoimento no Banco de Dados
		return (new Database('agendas'))->delete('id = '.$this->id);
		//Sucesso
		return true;
	}
	
	
	//Método responsavel por retornar uma agenda já cadastrada
	public static function getAgendaDuplicada($data,$profissional){
		return self::getAgendas(' DATE(data) ="'.$data .'" and idProfissional = '.$profissional )->fetchObject(self::class);
	}
	
	//Método responsavel por retornar uma agenda com base no seu Id
	public static function getAgendaById($id){
		return self::getAgendas('id = '.$id)->fetchObject(self::class);
	}
	
	//Método responsavel por retornar Agendas
	public static function getAgendas($where = null, $order = null, $limit = null, $fields = '*') {
		return (new Database('agendas'))->select($where,$order,$limit,$fields);
	}
	
	
	
}