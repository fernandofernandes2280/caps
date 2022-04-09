<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;
use App\Controller\Admin\Logs;

class AgendaItems {
	public $id;
	public $idAgenda;
	public $idPaciente;
	public $idProcedimento;
	public $idPresenca;
	
	//Método responsavel por cadastrar um status de agenda
	public function cadastrar(){
		
		$this->id = (new Database('agenda_itens'))->insert([
				'idAgenda'=>$this->idAgenda,
				'idPaciente'=>$this->idPaciente,
				'idProcedimento'=>$this->idProcedimento,
				'idPresenca'=>$this->idPresenca
		]);
		
		//Grava o Log do usuário
//		Logs::setNewLog('atendimentos',__FUNCTION__ ,'Profissional: '.Profissional::getProfissionalById($this->idProfissional)->nome.' Procedimento: '.Procedimento::getProcedimentoById($this->idProcedimento)->nome.' Status: '.$this->status);
		
		//Sucesso
		return true;
	}
	
	
	//Método responsavel por atualizar os banco de dados
	public function atualizar(){
	
		//Grava o Log do usuário
//		Logs::setNewLog('atendimentos',__FUNCTION__ ,'Profissional: '.Profissional::getProfissionalById($this->idProfissional)->nome.' Procedimento: '.Procedimento::getProcedimentoById($this->idProcedimento)->nome.' Status: '.$this->status);
		
		return (new Database('agenda_itens'))->update('id = '.$this->id,[
				'idAgenda'=>$this->idAgenda,
				'idPaciente'=>$this->idPaciente,
				'idProcedimento'=>$this->idProcedimento,
				'idPresenca'=>$this->idPresenca
		]);
	}
	
	
	//Método responsavel por excluir um Atendimento do banco de dados
	public function excluir(){
		
		//Grava o Log do usuário
//		Logs::setNewLog('atendimentos',__FUNCTION__ ,'Profissional: '.Profissional::getProfissionalById($this->idProfissional)->nome.' Procedimento: '.Procedimento::getProcedimentoById($this->idProcedimento)->nome.' Status: '.$this->status);
		
		return (new Database('agenda_itens'))->delete('id = '.$this->id);
		//Sucesso
		return true;
	}
	
	//Método responsavel por retornar um item agenda com base no seu Id
	public static function getAgendaItemsById($id){
		return self::getAgendaItems('id = '.$id)->fetchObject(self::class);
	}
	
	//Método responsavel por retornar um item agenda com base no seu id da agenda e Id do paciente 
	public static function getAgendaItemsByIdAgendaPaciente($idAgenda, $idPaciente){
		return self::getAgendaItems('idAgenda = '.$idAgenda.' and idPaciente = '.$idPaciente.'')->fetchObject(self::class);
	}
	
	//Método responsavel por retornar Agendas
	public static function getAgendaItems($where = null, $order = null, $limit = null, $fields = '*') {
		return (new Database('agenda_itens'))->select($where,$order,$limit,$fields);
	}
	
	//Método responsavel por retornar Agendas com clausula GROUP BY HAVING
	public static function getAgendaItems2($where = null, $group = null, $having = null, $order = null, $limit = null, $fields = '*') {
		return (new Database('agenda_itens'))->select2($where, $group, $having, $order,$limit,$fields);
	}
	
}