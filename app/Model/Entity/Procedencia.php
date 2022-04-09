<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;
use App\Utils\View;

class Procedencia {
		
	//Método responsavel por cadastrar um bairro no banco de dados
	public function cadastrar(){
		
		//Insere paciente no banco de dados
		$this->id = (new Database('procedencia'))->insert([
				'nome'=>$this->nome,
		]);
		//Sucesso
		return true;
	}
	
	//Método responsavel por retornar um bairro com base no seu Id
	public static function getProcedenciaById($id){
		return self::getProcedencias('id = '.$id)->fetchObject(self::class);
		
	}
	
	//Método responsavel por retornar Pacientes
	public static function getProcedencias($where = null, $order = null, $limit = null, $fields = '*') {
		return (new Database('procedencia'))->select($where,$order,$limit,$fields);
	}
	
	//Método responsavel por listar os Procedencia, selecionando a do paciente
	public static function getSelectProcedencia($id){
		$resultados = '';
		$results =  self::getProcedencias(null,'nome asc',null);
		//verifica se o id não é nulo e obtém o Procedencia do banco de dados
		if (!is_null($id)) {
			$selected = '';
			while ($obProcedencia = $results -> fetchObject(self::class)) {
				
				//seleciona o Procedencia do paciente
				$obProcedencia->id == $id ? $selected = 'selected' : $selected = '';
				//View de Procedencia
				$resultados .= View::render('admin/modules/pacientes/itemSelect',[
						'id' => $obProcedencia ->id,
						'nome' => $obProcedencia->nome,
						'selecionado' => $selected
				]);
			}
			//retorna os Estados Civis
			return $resultados;
		}else{ //se for nulo, lista todos e seleciona um em branco
			while ($obProcedencia = $results -> fetchObject(self::class)) {
				$obProcedencia->nome == 'Não Informado' ? $selected = 'selected' : $selected = '';
				$resultados .= View::render('admin/modules/pacientes/itemSelect',[
						'id' => $obProcedencia ->id,
						'nome' => $obProcedencia->nome,
						'selecionado' => $selected
				]);
			}
			//retorna a listagem
			return $resultados;
		}
	}
	
}