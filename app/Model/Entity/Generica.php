<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;


//Classe que guarda os campos em comum nas tabelas do banco de dados
class Generica {
	
	//id
	public $id;
	
	//nome 
	public $nome;
	
	
	//Método para gerar qualquer tipo de máscara
	public static function mask($val, $mask)
	{
		$maskared = '';
		$k = 0;
		for ($i = 0; $i <= strlen($mask) - 1; ++$i) {
			if ($mask[$i] == '#') {
				if (isset($val[$k])) {
					$maskared .= $val[$k++];
				}
			} else {
				if (isset($mask[$i])) {
					$maskared .= $mask[$i];
				}
			}
		}
		
		return $maskared;
	}
	
}

