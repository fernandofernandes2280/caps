<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;
use App\Utils\View;

class MotivoInativo {
		
	//Método responsavel por cadastrar um bairro no banco de dados
	public function cadastrar(){
		
		//Insere paciente no banco de dados
		$this->id = (new Database('motivoInativo'))->insert([
				'nome'=>$this->nome,
		]);
		//Sucesso
		return true;
	}
	
	//Método responsavel por retornar um bairro com base no seu Id
	public static function getMotivoInativoById($id){
		return self::getMotivoInativos('id = '.$id)->fetchObject(self::class);
		
	}
	
	//Método responsavel por retornar Pacientes
	public static function getMotivoInativos($where = null, $order = null, $limit = null, $fields = '*') {
		return (new Database('motivoInativo'))->select($where,$order,$limit,$fields);
	}
	
	//Método responsavel por listar os Motivo Inativo, selecionando a do paciente
	public static function getSelectMotivoInativo($id){
		$resultados = '';
		$results =  self::getMotivoInativos(null,'nome asc',null);
		//verifica se o id não é nulo e obtém o Motivo Inativo do banco de dados
		if (!is_null($id)) {
			$selected = '';
			while ($obMotivoInativo = $results -> fetchObject(self::class)) {
				
				//seleciona o Motivo Inativo do paciente
				$obMotivoInativo->id == $id ? $selected = 'selected' : $selected = '';
				//View de Motivo Inativo
				$resultados .= View::render('admin/modules/pacientes/itemSelect',[
						'id' => $obMotivoInativo ->id,
						'nome' => $obMotivoInativo->nome,
						'selecionado' => $selected
				]);
			}
			//retorna os Motivo Inativo
			return $resultados;
		}else{ //se for nulo, lista todos e seleciona um em branco
			while ($obMotivoInativo = $results -> fetchObject(self::class)) {
				$obMotivoInativo->nome == 'Não Informado' ? $selected = 'selected' : $selected = '';
				$resultados .= View::render('admin/modules/pacientes/itemSelect',[
						'id' => $obMotivoInativo ->id,
						'nome' => $obMotivoInativo->nome,
						'selecionado' => $selected
				]);
			}
			//retorna a listagem
			return $resultados;
		}
	}
	
	
}