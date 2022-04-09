<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;
use App\Utils\View;

class Escolaridade extends Generica {
		
	//Método responsavel por cadastrar um bairro no banco de dados
	public function cadastrar(){
		
		//Insere paciente no banco de dados
		$this->id = (new Database('escolaridade'))->insert([
				'nome'=>$this->nome,
		]);
		//Sucesso
		return true;
	}
	
	//Método responsavel por retornar um bairro com base no seu Id
	public static function getEscolaridadeById($id){
		
		return self::getEscolaridades('id = '.$id)->fetchObject(self::class);
		
	}
	
	//Método responsavel por retornar Pacientes
	public static function getEscolaridades($where = null, $order = null, $limit = null, $fields = '*') {
		return (new Database('escolaridade'))->select($where,$order,$limit,$fields);
	}
	
	//Método responsavel por atualizar os dados no banco
	public function atualizar(){
		return (new Database('escolaridade'))->update('id = '.$this->id,[
				'nome' 		=> $this->nome,

		]);
		
		
	}
	
	//Método responsavel por excluir
	public function excluir(){
		return (new Database('escolaridade'))->delete('id = '.$this->id);
		
		//Sucesso
		return true;
	}
	
	//Método responsavel por listar as Escolaridades, selecionando a do paciente
	public static function getSelectEscolaridade($id){
		$resultados = '';
		$results =  self::getEscolaridades(null,'nome asc',null);
		//verifica se o id não é nulo e obtém a Escolaridade do banco de dados
		if (!is_null($id)) {
			$selected = '';
			while ($obEscolaridade = $results -> fetchObject(self::class)) {
				
				//seleciona as Escolaridades do paciente
				$obEscolaridade->id == $id ? $selected = 'selected' : $selected = '';
				//View de as Escolaridades
				$resultados .= View::render('admin/modules/pacientes/itemSelect',[
						'id' => $obEscolaridade ->id,
						'nome' => $obEscolaridade->nome,
						'selecionado' => $selected
				]);
			}
			//retorna os as Escolaridades
			return $resultados;
		}else{ //se as Escolaridades for nulo, lista todos e seleciona um em branco
			while ($obEscolaridade = $results -> fetchObject(self::class)) {
				$obEscolaridade->nome == 'Não Informado' ? $selected = 'selected' : $selected = '';
				$resultados .= View::render('admin/modules/pacientes/itemSelect',[
						'id' => $obEscolaridade ->id,
						'nome' => $obEscolaridade->nome,
						'selecionado' => $selected
				]);
			}
			//retorna os as Escolaridades
			return $resultados;
		}
	}
	
	
}