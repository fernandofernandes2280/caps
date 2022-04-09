<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;
use App\Utils\View;

class Cid10 extends Generica {
		
	public $descricao;
	
	//Método responsavel por cadastrar no banco de dados
	public function cadastrar(){
		
		//Insere paciente no banco de dados
		$this->id = (new Database('cid10'))->insert([
				'nome'=>$this->nome,
				'descricao'=>$this->descricao
		]);
		//Sucesso
		return true;
	}
	
	//Método responsavel por retornar um registro com base no seu Id
	public static function getCid10ById($id){
		return self::getCid10s('id = '.$id)->fetchObject(self::class);
		
	}
	
	//Método responsavel por retornar registros
	public static function getCid10s($where = null, $order = null, $limit = null, $fields = '*') {
		return (new Database('cid10'))->select($where,$order,$limit,$fields);
	}
	//Método responsavel por atualizar os dados no banco
	public function atualizar(){
		return (new Database('cid10'))->update('id = '.$this->id,[
				'nome' 		=> $this->nome,
				'descricao' 	=> $this->descricao,
		]);
		
		
	}
	
	//Método responsavel por excluir 
	public function excluir(){
		return (new Database('cid10'))->delete('id = '.$this->id);
		
		//Sucesso
		return true;
	}
	
	//Método responsavel por listar os cid10, selecionando a do paciente
	public static function getSelectCid10($id){
		$resultados = '';
		$results =  self::getcid10s(null,'nome asc',null);
		//verifica se o id não é nulo e obtém o cid10 do banco de dados
		if (!is_null($id)) {
			$selected = '';
			while ($obcid10 = $results -> fetchObject(self::class)) {
				
				//seleciona o cid10 do paciente
				$obcid10->id == $id ? $selected = 'selected' : $selected = '';
				//View de cid10
				$resultados .= View::render('admin/modules/pacientes/itemSelect',[
						'id' => $obcid10 ->id,
						'nome' => $obcid10->nome.' - '.$obcid10->descricao,
						'selecionado' => $selected
				]);
			}
			//retorna os cid10
			return $resultados;
		}else{ //se for nulo, lista todos e seleciona um em branco
			while ($obcid10 = $results -> fetchObject(self::class)) {
				$obcid10->nome == 'Não Informado' ? $selected = 'selected' : $selected = '';
				$resultados .= View::render('admin/modules/pacientes/itemSelect',[
						'id' => $obcid10 ->id,
						'nome' => $obcid10->nome.' - '.$obcid10->descricao,
						'selecionado' => $selected
				]);
			}
			//retorna a listagem
			return $resultados;
		}
	}
	
}