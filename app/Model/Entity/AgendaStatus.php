<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;
use App\Controller\Admin\Logs;

class AgendaStatus extends Generica {
	
	//Método responsavel por cadastrar um status de agenda
	public function cadastrar(){
		
		$this->id = (new Database('agendas_status'))->insert([
				'nome'=>$this->nome,
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
		
		return (new Database('agendas_status'))->update('id = '.$this->id,[
				'nome'=>$this->nome,
		]);
	}
	
	
	//Método responsavel por excluir um Atendimento do banco de dados
	public function excluir(){
		
		//Grava o Log do usuário
//		Logs::setNewLog('atendimentos',__FUNCTION__ ,'Profissional: '.Profissional::getProfissionalById($this->idProfissional)->nome.' Procedimento: '.Procedimento::getProcedimentoById($this->idProcedimento)->nome.' Status: '.$this->status);
		
		return (new Database('agendas_status'))->delete('id = '.$this->id);
		//Sucesso
		return true;
	}
	
	//Método responsavel por retornar uma agenda com base no seu Id
	public static function getAgendaStatusById($id){
		return self::getAgendasStatus('id = '.$id)->fetchObject(self::class);
	}
	
	//Método responsavel por retornar Agendas
	public static function getAgendasStatus($where = null, $order = null, $limit = null, $fields = '*') {
		return (new Database('agendas_status'))->select($where,$order,$limit,$fields);
	}
	
	
	
}