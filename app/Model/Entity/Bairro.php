<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;
use \App\Utils\View;

class Bairro {
	
		
	//Método responsavel por cadastrar um bairro no banco de dados
	public function cadastrar(){
		
		//Insere paciente no banco de dados
		$this->id = (new Database('bairros'))->insert([
				'nome'=>$this->nome,
		]);
		//Sucesso
		return true;
	}
	
	//Método responsavel por retornar um bairro com base no seu Id
	public static function getBairroById($id){
		return self::getBairros('id = '.$id)->fetchObject(self::class);
		
	}
	
	//Método responsavel por retornar Pacientes
	public static function getBairros($where = null, $order = null, $limit = null, $fields = '*') {
		return (new Database('bairros'))->select($where,$order,$limit,$fields);
	}
	
	//Método responsavel por listar os Bairros no select option, selecionando o do paciente
	public static function getSelectBairros($id){
		$resultados = '';
		$results =  self::getBairros(null,'nome asc',null);
		//verifica se o id não é nulo e obtém o Procedencia do banco de dados
		if (!is_null($id)) {
			$selected = '';
			while ($ob = $results -> fetchObject(self::class)) {
				
				//seleciona o Procedencia do paciente
				$ob->id == $id ? $selected = 'selected' : $selected = '';
				//View de Procedencia
				$resultados .= View::render('admin/modules/pacientes/itemSelect',[
						'id' => $ob ->id,
						'nome' => $ob->nome,
						'selecionado' => $selected
				]);
			}
			//retorna
			return $resultados;
		}else{ //se for nulo, lista todos e seleciona um em branco
			while ($ob = $results -> fetchObject(self::class)) {
				$ob->nome == 'Não Informado' ? $selected = 'selected' : $selected = '';
				$resultados .= View::render('admin/modules/pacientes/itemSelect',[
						'id' => $ob ->id,
						'nome' => $ob->nome,
						'selecionado' => $selected
				]);
			}
			//retorna a listagem
			return $resultados;
		}
	}
	
	
	
}