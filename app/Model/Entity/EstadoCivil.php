<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;
use App\Utils\View;

class EstadoCivil {
		
	//Método responsavel por cadastrar um bairro no banco de dados
	public function cadastrar(){
		
		//Insere paciente no banco de dados
		$this->id = (new Database('estadoCivil'))->insert([
				'nome'=>$this->nome,
		]);
		//Sucesso
		return true;
	}
	
	//Método responsavel por retornar um bairro com base no seu Id
	public static function getEstadoCivilById($id){
		return self::getEstadoCivils('id = '.$id)->fetchObject(self::class);
		
	}
	
	//Método responsavel por retornar Pacientes
	public static function getEstadoCivils($where = null, $order = null, $limit = null, $fields = '*') {
		return (new Database('estadoCivil'))->select($where,$order,$limit,$fields);
	}
	
	
	//Método responsavel por listar os Estados Civis, selecionando a do paciente
	public static function getSelectEstadoCivil($id){
		$resultados = '';
		$results =  self::getEstadoCivils(null,'nome asc',null);
		//verifica se o id não é nulo e obtém o Estado Civil do banco de dados
		if (!is_null($id)) {
			$selected = '';
			while ($obEstadoCivil = $results -> fetchObject(self::class)) {
				
				//seleciona o Estado Civil do paciente
				$obEstadoCivil->id == $id ? $selected = 'selected' : $selected = '';
				//View de Estados Civil
				$resultados .= View::render('admin/modules/pacientes/itemSelect',[
						'id' => $obEstadoCivil ->id,
						'nome' => $obEstadoCivil->nome,
						'selecionado' => $selected
				]);
			}
			//retorna os Estados Civis
			return $resultados;
		}else{ //se for nulo, lista todos e seleciona um em branco
			while ($obEstadoCivil = $results -> fetchObject(self::class)) {
				$obEstadoCivil->nome == 'Não Informado' ? $selected = 'selected' : $selected = '';
				$resultados .= View::render('admin/modules/pacientes/itemSelect',[
						'id' => $obEstadoCivil ->id,
						'nome' => $obEstadoCivil->nome,
						'selecionado' => $selected
				]);
			}
			//retorna a listagem
			return $resultados;
		}
	}
	
}