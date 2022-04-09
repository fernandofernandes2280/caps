<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;
use App\Utils\View;

class Substancia extends Generica {
		
	//Método responsavel por cadastrar um bairro no banco de dados
	public function cadastrar(){
		
		//Insere paciente no banco de dados
		$this->id = (new Database('substancias'))->insert([
				'nome'=>$this->nome,
		]);
		//Sucesso
		return true;
	}
	
	//Método responsavel por retornar um bairro com base no seu Id
	public static function getSubstanciaById($id){
		return self::getSubstancias('id = '.$id)->fetchObject(self::class);
		
	}
	
	//Método responsavel por retornar Pacientes
	public static function getSubstancias($where = null, $order = null, $limit = null, $fields = '*') {
		return (new Database('substancias'))->select($where,$order,$limit,$fields);
	}
	
	//Método responsavel por atualizar os dados no banco
	public function atualizar(){
		return (new Database('substancias'))->update('id = '.$this->id,[
				'nome' 		=> $this->nome,
				
		]);
		
		
	}
	
	//Método responsavel por excluir
	public function excluir(){
		return (new Database('substancias'))->delete('id = '.$this->id);
		
		//Sucesso
		return true;
	}
	
	//Método responsavel por listar os Substancia, selecionando a do paciente
	public static function getSelectSubstancia($id){
		$resultados = '';
		$results =  self::getSubstancias(null,'nome asc',null);
		//verifica se o id não é nulo e obtém o Substancia do banco de dados
		if (!is_null($id)) {
			$selected = '';
			while ($obSubstancia = $results -> fetchObject(self::class)) {
				
				//seleciona o Substancia do paciente
				$obSubstancia->id == $id ? $selected = 'selected' : $selected = '';
				//View de Substancia
				$resultados .= View::render('admin/modules/pacientes/itemSelect',[
						'id' => $obSubstancia ->id,
						'nome' => $obSubstancia->nome,
						'selecionado' => $selected
				]);
			}
			//retorna os Substancia
			return $resultados;
		}else{ //se for nulo, lista todos e seleciona um em branco
			while ($obSubstancia = $results -> fetchObject(self::class)) {
				//seleciona o Substancia do paciente
				$obSubstancia->nome == 'Nenhuma' ? $selected = 'selected' : $selected = '';
				$resultados .= View::render('admin/modules/pacientes/itemSelect',[
						'id' => $obSubstancia ->id,
						'nome' => $obSubstancia->nome,
						'selecionado' => $selected
				]);
			}
			//retorna a listagem
			return $resultados;
		}
	}
	
}