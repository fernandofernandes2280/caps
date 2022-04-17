<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class Profissional {
	
    //endereço (rua/avenida) do funcionário
    public $endereco;
    
    //bairro do funcionário
    public $bairro;
    
    //cidade do funcionário
    public $cidade;
    
    //cep do funcionário
    public $cep;
    
    //unidade federal do funcionário
    public $uf;
    
    //telefone do funcionário
    public $fone;
    
    //data de nascimento do funcionário
    public $dataNasc;
    
    //data de cadastro do funcionário
    public $dataCad;
    
    //função do funcionário
    public $funcao;
    
    //cpf do do funcionário
    public $cpf;
    
    //cartão do sus do funcionário
    public $cartaoSus;
    
    //cbo do funcionário
    public $cbo;
		
    //status 1-ativo 0-inativo
    public $status;
    
    //e-mail do funcionário
    public $email;
    
    
	//Método responsavel por cadastrar um bairro no banco de dados
	public function cadastrar(){
		
		//Insere paciente no banco de dados
		$this->id = (new Database('profissionais'))->insert([
				'nome'=>$this->nome,
    		    'endereco'=>$this->endereco,
    		    'bairro'=>$this->bairro,
    		    'cidade'=>$this->cidade,
    		    'uf'=>$this->uf,
    		    'cep'=>$this->cep,
    		    'fone'=>$this->fone,
    		    'dataNasc'=>$this->dataNasc,
    		    'status'=>$this->status,
    		    'cartaoSus'=>$this->cartaoSus,
		        'funcao'=>$this->funcao,
		        'cpf'=>$this->cpf,
		        'cbo'=>$this->cbo,
		      'email'=>$this->email
		]);
		//Sucesso
		return true;
	}
	
	//Método responsavel por atualizar os dados no banco
	public function atualizar(){
	    return (new Database('profissionais'))->update('id = '.$this->id,[
	        'nome'=>$this->nome,
	        'endereco'=>$this->endereco,
	        'bairro'=>$this->bairro,
	        'cidade'=>$this->cidade,
	        'uf'=>$this->uf,
	        'cep'=>$this->cep,
	        'fone'=>$this->fone,
	        'dataNasc'=>$this->dataNasc,
	        'status'=>$this->status,
	        'cartaoSus'=>$this->cartaoSus,
	        'funcao'=>$this->funcao,
	        'cpf'=>$this->cpf,
	        'cbo'=>$this->cbo,
	        'email'=>$this->email
	    ]);
	    
	    
	}
	
	
	//Método responsavel por retornar um bairro com base no seu Id
	public static function getProfissionalById($id){
		return self::getProfissionais('id = '.$id)->fetchObject(self::class);
		
	}
	
	//Método responsavel por retornar Pacientes
	public static function getProfissionais($where = null, $order = null, $limit = null, $fields = '*') {
		return (new Database('profissionais'))->select($where,$order,$limit,$fields);
	}
	
	
	//Método responsavel por retornar um usuario com base em seu e-mail
	public static function getUserByCPF($cpf){
	    return self::getProfissionais('cpf = "'.$cpf.'"')->fetchObject(self::class);
	    
	    //Sucesso
	    return true;
	}
	
	
	
}