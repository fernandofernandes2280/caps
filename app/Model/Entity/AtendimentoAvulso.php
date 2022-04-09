<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;
use App\Controller\Admin\Logs;

class AtendimentoAvulso {
	
	
	public $id;
	public $data;
	public $idProfissional;
	public $idProcedimento;
	public $qtd;
		
	//Método responsavel por cadastrar um bairro no banco de dados
	public function cadastrar(){
		
		//Insere paciente no banco de dados
		$this->id = (new Database('atendimentosAvulsos'))->insert([
				'data'=>date(''.$this->data.' H:i:s'),
				'idProfissional'=>$this->idProfissional,
				'idProcedimento'=>$this->idProcedimento,
				'qtd'=>$this->qtd,
				
		]);
		
		//Grava o Log do usuário
		Logs::setNewLog('atendimentosAvulsos',__FUNCTION__ ,'Profissional: '.Profissional::getProfissionalById($this->idProfissional)->nome.' Procedimento: '.Procedimento::getProcedimentoById($this->idProcedimento)->nome.' Qtd: '.$this->qtd);
		
		
		//Sucesso
		return true;
	}
	
	
	//Método responsavel por atualizar os banco de dados com os dados da instancia atual de paciente
	public function atualizar(){
//		$this->data = date('Y-m-d H:i:s');

		//Grava o Log do usuário
		Logs::setNewLog('atendimentosAvulsos',__FUNCTION__ ,'Profissional: '.Profissional::getProfissionalById($this->idProfissional)->nome.' Procedimento: '.Procedimento::getProcedimentoById($this->idProcedimento)->nome.' Qtd: '.$this->qtd);
		
		//Atualiza paciente no banco de dados
		return (new Database('atendimentosAvulsos'))->update('id = '.$this->id,[
				'data'=>$this->data,
				'idProfissional'=>$this->idProfissional,
				'idProcedimento'=>$this->idProcedimento,
				'qtd'=>$this->qtd,
		]);
		
		
	}
	
	
	//Método responsavel por excluir um Atendimento Avulso do banco de dados
	public function excluir($id){
		
	
		//Grava o Log do usuário
		Logs::setNewLog('atendimentosAvulsos',__FUNCTION__ ,'Profissional: '.Profissional::getProfissionalById($this->idProfissional)->nome.' Procedimento: '.Procedimento::getProcedimentoById($this->idProcedimento)->nome.' Qtd: '.$this->qtd);
		
		//Exclui o atendimento avulso do Banco de Dados
		return (new Database('atendimentosAvulsos'))->delete('id = '.$this->id);
		//Sucesso
		return true;
	}
	
	
	//Método responsavel por retornar um bairro com base no seu Id
	public static function getAtendimentoAvulsoById($id){
		return self::getAtendimentosAvulsos('id = '.$id)->fetchObject(self::class);
		
	}
	
	//Método responsavel por retornar Pacientes
	public static function getAtendimentosAvulsos($where = null, $order = null, $limit = null, $fields = '*') {
		return (new Database('atendimentosAvulsos'))->select($where,$order,$limit,$fields);
	}
	
	
	
}