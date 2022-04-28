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
	    
        //recebe dados os atendimentos
		$resultado = '';
		//recebe dados do gráfico
		$resultadoGrafico = '';
		
		$total = 0;
		$where = 'status = "P" and data BETWEEN "'.$dataInicio.' 00:00:01" and "'.$dataFim.' 23:59:59" GROUP BY P.nome' ;
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
			
			//dados para geração do gráfico
			$resultadoGrafico .=  "['$obAtendimento->atendimento', $obAtendimento->total], "  ;
			
		}
		
		
	
		
		
		$dados['resultado'] = $resultado;
		$dados['grafico'] = $resultadoGrafico;
		//recebe o total de atendimentos
		self::$qtdTotal = self::$qtdTotal + $total;  
		return $dados;
		
		
		
	}
	
	//Método responsável por retornar relatorio por atendimento Avulso
	public static function getRelPorAtendimentoAvulso($request,$dataInicio,$dataFim){
	    
	    //recebe dados os atendimentos
	    $resultado = '';
	    //recebe dados do gráfico
	    $resultadoGrafico = '';
	    
	    $total = 0;
	    $where = 'A.idProcedimento = P.id and data BETWEEN "'.$dataInicio.' 00:00:01" and "'.$dataFim.' 23:59:59" GROUP BY A.idProcedimento' ;
	    $table = ' atendimentosAvulsos A, procedimentos P';
	    $fields = 'P.nome as atendimento, SUM(A.qtd) as total';
	   
	    
	    $dadosAtendimentosAvulsos = EntityPaciente::getPacientesRel($where,null,null,$fields,$table);
	    
	    while ($obAtendimento = $dadosAtendimentosAvulsos -> fetchObject(EntityPaciente::class)) {
	        
	        $resultado .= '<tr>
								<td>Ação com a comunidade (Palestras, Consultas, Cortes de cabelo, etc..)</td>
								<td>'.$obAtendimento->total.'</td>
						  </tr>';
	        $total += $obAtendimento->total;
	        
	        //dados para geração do gráfico
	        $resultadoGrafico .=  "['$obAtendimento->atendimento', $obAtendimento->total], "  ;
	        
	    }
	    //soma atendimentos + atendimentos avulsos.
	    self::$qtdTotal = self::$qtdTotal + $total;
	    $resultado .= '<tr>
								<td>Total Geral</td>
								<td>'.self::$qtdTotal.'</td>
						  </tr>';
	    
	    
	    
	    $dados['resultado'] = $resultado;
	    $dados['grafico'] = $resultadoGrafico;
	    
	    return $dados;
	    
	    
	    
	}
	
	//Método responsável por retornar relatorio por atendimento
	public static function getRelPorProfissional($request,$dataInicio,$dataFim){
		
		$resultado = '';
		
		$where = 'A.status = "P" and data BETWEEN "'.$dataInicio.' 00:00:01" and "'.$dataFim.' 23:59:59  " GROUP BY P.id' ;
		$table = 'atendimentos as A INNER JOIN profissionais as P ON P.id = A.idProfissional';
		$fields = 'P.id as idProfissional, COUNT(*) as total';
		$order = ' P.id';
		
		$dados = EntityPaciente::getPacientesRel($where,$order,null,$fields,$table);
	
		while ($ob = $dados -> fetchObject(EntityPaciente::class)) {
			
			$resultado .= 			
			
			'<table class="table table-striped">
			<thead class="bg-dark">
			<tr>
			<th style="color: #fff">'.EntityProfissional::getProfissionalById($ob->idProfissional)->nome.' ('.EntityProfissional::getProfissionalById($ob->idProfissional)->funcao.')'. '</th>
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
		$where = 'A.idProfissional = '.$id.' and status = "P" and data BETWEEN "'.$dataInicio.' 00:00:01" and "'.$dataFim.' 23:59:59 " GROUP BY P.nome' ;
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
		//Atendimentos avulsos
		$whereAvulso = 'A.idProfissional = '.$id.' and data BETWEEN "'.$dataInicio.' 00:00:01" and "'.$dataFim.' 23:59:59" ' ;
		$tableAvulso = ' atendimentosAvulsos A INNER JOIN procedimentos P ON A.idProcedimento = P.id';
		$fieldsAvulso = 'P.nome as atendimento, A.qtd as total';
		$dadosAtendimentosAvulsos = EntityPaciente::getPacientesRel($whereAvulso,null,null,$fieldsAvulso,$tableAvulso);
	
		while ($obAvulso = $dadosAtendimentosAvulsos -> fetchObject(EntityPaciente::class)) {
		    
		    $resultado .= '<tr>
								<td>'.$obAvulso->atendimento.'</td>
								<td>'.$obAvulso->total.'</td>
						  </tr>';
		$total += $obAvulso->total;
		    
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
				'procedimentos' => self::getRelPorAtendimento($request,$dataInicio,$dataFim)['resultado'],
		        'procedimentosAvulsos' => self::getRelPorAtendimentoAvulso($request,$dataInicio,$dataFim)['resultado'],
				'profissional' => self::getRelPorProfissional($request, $dataInicio, $dataFim),
		    /*
                'graficoProcedimentos'=>View::render('pages/graficos/graficos',[
                    'label'=> self::getRelPorAtendimento($request,$dataInicio,$dataFim)['grafico'],
                    'title' => '<h5>Gráfico por atendimentos</h5>',
                ]), 
				*/
		    'graficoProcedimentos'=>''
				
		]);
		
		//Retorna a página completa
		return parent::getPanel('Pacientes > Siscaps', $content,'relatorios', self::$hidden);
		
		
		
	}
	
	
	
	
	
	

	
}