<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class Paciente extends Generica{
	

	//codigo do prontuário do paciente
	public $codPronto;

	//endereço (rua/avenida) do paciente
	public $endereco;
	
	//bairro do paciente
	public $bairro;
	
	//cidade do paciente
	public $cidade;
	
	//cep do paciente
	public $cep;
	
	//unidade federal do paciente
	public $uf;
	
	//telefone 1 do paciente
	public $fone1;
	
	//telefone 2 do paciente
	public $fone2;
	
	//data de nascimento do paciente
	public $dataNasc;
	
	//data de cadastro do paciente
	public $dataCad;
	
	//cidade de nascimento do paciente
	public $naturalidade;
	
	//nome da mãe do paciente
	public $mae;
	
	//escolaridade do paciente
	public $escolaridade;
	
	//sexo do paciente
	public $sexo;
	
	//caminho da foto do paciente
	public $foto;
	
	//estado civil do paciente
	public $estadoCivil;
	
	//observações do paciente
	public $obs;
	
	//status do paciente (ativo/inativo)
	public $status;
	
	//procedência do paciente
	public $procedencia;
	
	//motivo de inatividade do paciente
	public $motivoInativo;
	
	//cartão do sus do paciente
	public $cartaoSus;
	
	//tipo do paciente (AD/TM)
	public $tipo;
	
	//Cid numero 1 do paciente;
	public $cid1;
	
	//Cid numero 2 do paciente;
	public $cid2;
	
	//substancia principal consumida pelo paciente
	public $substanciaPri;
	
	//substancia secundaria consumida pelo paciente
	public $substanciaSec;
	
	//substancia Outra consumida pelo paciente
	public $substanciaOutra;
	
		
	//Método responsavel por cadastrar um paciente no banco de dados
	public function cadastrar(){
		//define a data
	//	$this->dataCad = date('Y-m-d H:i:s');
		//Insere paciente no banco de dados
		$this->id = (new Database('pacientes'))->insert([
				'codPronto' => $this->codPronto,
				'nome' => $this->nome,
				'endereco'=>$this->endereco,
				'bairro'=>$this->bairro,
				'cidade'=>$this->cidade,
				'uf'=>$this->uf,
				'cep'=>$this->cep,
				'fone1'=>$this->fone1,
				'fone2'=>$this->fone2,
				'dataNasc'=>$this->dataNasc,
				'dataCad'=>$this->dataCad,
				'naturalidade'=>$this->naturalidade,
				'mae'=>$this->mae,
				'escolaridade'=>$this->escolaridade,
				'sexo'=>$this->sexo,
				'foto'=>$this->foto,
				'estadoCivil'=>$this->estadoCivil,
				'obs'=>$this->obs,
				'status'=>$this->status,
				'procedencia'=>$this->procedencia,
				'motivoInativo'=>$this->motivoInativo,
				'cartaoSus'=>$this->cartaoSus,
				'tipo'=>$this->tipo,
				'cid1'=>$this->cid1,
				'cid2'=>$this->cid2,
				'substanciaPri'=>$this->substanciaPri,
				'substanciaSec'=>$this->substanciaSec,
				'substanciaOutra'=>$this->substanciaOutra
		]);
		//Sucesso
		return true;
	}
	
	
	//Método responsavel por atualizar os banco de dados com os dados da instancia atual de paciente
	public function atualizar(){
		
		//Atualiza paciente no banco de dados
		return (new Database('pacientes'))->update('id = '.$this->id,[
				'codPronto' => $this->codPronto,
				'nome' => $this->nome,
				'endereco'=>$this->endereco,
				'bairro'=>$this->bairro,
				'cidade'=>$this->cidade,
				'uf'=>$this->uf,
				'cep'=>$this->cep,
				'fone1'=>$this->fone1,
				'fone2'=>$this->fone2,
				'dataNasc'=>$this->dataNasc,
		        'dataCad'=>$this->dataCad,
				'naturalidade'=>$this->naturalidade,
				'mae'=>$this->mae,
				'escolaridade'=>$this->escolaridade,
				'sexo'=>$this->sexo,
				'foto'=>$this->foto,
				'estadoCivil'=>$this->estadoCivil,
				'obs'=>$this->obs,
				'status'=>$this->status,
				'procedencia'=>$this->procedencia,
				'motivoInativo'=>$this->motivoInativo,
				'cartaoSus'=>$this->cartaoSus,
				'tipo'=>$this->tipo,
				'cid1'=>$this->cid1,
				'cid2'=>$this->cid2,
				'substanciaPri'=>$this->substanciaPri,
				'substanciaSec'=>$this->substanciaSec,
				'substanciaOutra'=>$this->substanciaOutra
		]);
	
	}
	//Método responsavel por retornar um depoimento com base no seu Id
	public static function getPacienteById($id){
		return self::getPacientes('id = '.$id)->fetchObject(self::class);
		
	}
	
	//Método responsavel por retornar um depoimento com base no seu Id
	public static function getPacienteByCodPronto($codPronto){
		return self::getPacientes('codPronto = '.$codPronto)->fetchObject(self::class);
		
	}
	
	
	
	//Método responsavel por retornar Pacientes
	public static function getPacientes($where = null, $order = null, $limit = null, $fields = '*') {
		return (new Database('pacientes'))->select($where,$order,$limit,$fields);
	}
	
	//Método responsavel por excluir um depoimento do banco de dadosl
	public function excluir(){
		
		//Exclui o depoimento no Banco de Dados
		return (new Database('pacientes'))->delete('id = '.$this->id);
		
		//Sucesso
		return true;
	}
	
	//Método responsavel por retornar Pacientes para Relatório
	public static function getPacientesRel($where = null, $order = null, $limit = null, $fields = '*',$table) {
		return (new Database($table))->select($where,$order,$limit,$fields);
	}
	
	//Método responsavel por retornar 
	public static function getPacienteDuplicado($data,$nome){
		
		return self::getPacientes('DATE(dataNasc) ="'.$data .'" and nome ="'.$nome.'" ')->fetchObject(self::class);
		
	}
	
	
	
	
	
}