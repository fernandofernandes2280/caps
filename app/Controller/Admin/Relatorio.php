<?php

namespace App\Controller\Admin;

use App\Utils\View;
use \App\Model\Entity\Paciente as EntityPaciente;
use \App\Model\Entity\Profissional as EntityProfissional;

class Relatorio  extends Page{
	
	//Armazena quantidade total de pacientes listados
	private static $qtdTotal ;
	private static $totalGeralBpac = 0;
	//esconde busca rápida de prontuário no navBar
	private static $hidden = 'hidden';
	
	
	
	
	//Método responsável por retornar relatorio por atendimento
	public static function getRelPorAtendimento($request,$dataInicio,$dataFim){

		$resultado = '';
		$total = 0;
		$where = 'status = "P" and data BETWEEN "'.$dataInicio.'" and "'.$dataFim.'" GROUP BY P.nome' ;
		$table = 'atendimentos as A INNER JOIN procedimentos as P ON P.id = A.idProcedimento';
		$fields = 'P.nome as atendimento, COUNT(*) as total';
		$order = ' P.nome';
		
		$dadosAtendimentos = EntityPaciente::getPacientesRel($where,$order,null,$fields,$table);
		
		while ($obAtendimento = $dadosAtendimentos -> fetchObject(EntityPaciente::class)) {
			
			$resultado .= '<tr>
								<td>'.$obAtendimento->atendimento.'</td>
								<td>'.$obAtendimento->total.'</td>
						  </tr>'; 
			$total += $obAtendimento->total; 
			
		}
		
		$resultado .= '<tr>
								<td>Total</td>
								<td>'.$total.'</td>
						  </tr>';
	
		return $resultado;
		
	}
	
	//Método responsável por retornar relatorio por atendimento
	public static function getRelPorProfissional($request,$dataInicio,$dataFim){
		
		$resultado = '';
		
		$where = 'A.status = "P" and data BETWEEN "'.$dataInicio.'" and "'.$dataFim.'  " GROUP BY P.id' ;
		$table = 'atendimentos as A INNER JOIN profissionais as P ON P.id = A.idProfissional';
		$fields = 'P.id as idProfissional, COUNT(*) as total';
		$order = ' P.id';
		
		$dados = EntityPaciente::getPacientesRel($where,$order,null,$fields,$table);
	
		while ($ob = $dados -> fetchObject(EntityPaciente::class)) {
			
			$resultado .= 			
			
			'<table class="table table-striped">
			<thead class="bg-dark">
			<tr>
			<th style="color: #fff">'.EntityProfissional::getProfissionalById($ob->idProfissional)->nome.'</th>
			<th style="color: #fff">qtd</th>
			</tr>
			</thead>
			<tbody>
			'.self::getRelPorProfissionalItem($ob->idProfissional,$dataInicio, $dataFim).'
			</tbody>
			</table>';
			
			
		}
		
		return $resultado;
		
	}
	//Método responsável por retornar relatorio por atendimento
	public static function getRelPorProfissionalItem($id,$dataInicio,$dataFim){
		
		$resultado = '';
		$total = 0;
		$where = 'A.idProfissional = '.$id.' and status = "P" and data BETWEEN "'.$dataInicio.'" and "'.$dataFim.' " GROUP BY P.nome' ;
		$table = 'atendimentos as A INNER JOIN procedimentos as P ON P.id = A.idProcedimento';
		$fields = 'P.nome as atendimento, COUNT(*) as total';
		$order = ' P.nome';
		
		$dados = EntityPaciente::getPacientesRel($where,$order,null,$fields,$table);
	
		while ($ob = $dados -> fetchObject(EntityPaciente::class)) {
			
			$resultado .= '<tr>
								<td>'.$ob->atendimento.'</td>
								<td>'.$ob->total.'</td>
						  </tr>';
			$total += $ob->total; 
			
		}
		
		$resultado .= '<tr>
								<td style="color: #fff">Total</td>
								<td style="color: #fff">'.$total.'</td>
						  </tr>';
		
		return $resultado;
		
	}
	
	
	
	
	public static function getRelatorio($request){
		$postVars = $request->getPostVars();
		$dataInicio = $postVars['dataInicial'];
		$dataFim = $postVars['dataFinal'];
		
		$content = View::render('admin/modules/relatorios/index',[
				'title' => 'Relatórios de Atendimentos do Período: '.date('d/m/Y', strtotime($dataInicio)) .' à '.date('d/m/Y', strtotime($dataFim)),
				'procedimentos' => self::getRelPorAtendimento($request,$dataInicio,$dataFim),
				'profissional' => self::getRelPorProfissional($request, $dataInicio, $dataFim)
				
				
		]);
		
		//Retorna a página completa
		return parent::getPanel('Pacientes > Siscaps', $content,'relatorios', self::$hidden);
		
		
		
	}
	
	
	
	
	
	

	
}