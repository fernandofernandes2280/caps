<?php

namespace App\WebService;

class ViaCEP{
	
	//Método responsavel por consultar um CEP no ViaCEP
	public static function consultarCEP($cep){
		
		//Inicia o cURL
		$curl = curl_init();
		
		//Configurações do cURL
		curl_setopt_array($curl, [
				
				CURLOPT_URL => 'https://viacep.com.br/ws/'.$cep.'/json/',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_CUSTOMREQUEST => 'GET'
		]);
		
		//Response
		
		$response = curl_exec($curl);
		
		//Fecha a conexão
		curl_close($curl);
		
		//Converte o Json para Array
		$array = json_decode($response,true);
		
		//Retornar conteúdo em array
	
		return (isset($array['cep']) ? $array : null);
		
			
	}
	
}

####################################################################################
//código para chamar a api de CEP via PHP (nao usei pq estou usando ajax jquery)
//use \App\WebService\ViaCEP;
//$dadosCEP = ViaCEP::consultarCEP("68926068");
//isset($dadosCEP['logradouro']) ? print_r($dadosCEP) : print_r("CEP não existe");
###################################################################################
