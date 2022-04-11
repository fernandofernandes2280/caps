<?php

namespace App\Controller\Pages;

use \App\Utils\View;
use \App\Model\Entity\Organization;
use App\Controller\Admin;
use \App\Model\Entity\Paciente as EntityPaciente;

class Home extends Page{
	
	
	//Método responsável por retornar os atendimentos do relatório da RAAS
	public static function getProducaoPorAtendimento($dataInicio,$dataFim){
		
		$resultado = '';
		
		
		$where = 'status = "P" and data BETWEEN "'.$dataInicio.'" and "'.$dataFim.'" GROUP BY P.nome' ;
		$table = 'atendimentos as A INNER JOIN procedimentos as P ON P.id = A.idProcedimento';
		$fields = 'P.nome as atendimento, COUNT(*) as total';
		$order = ' P.nome';
		
		$dadosAtendimentos = EntityPaciente::getPacientesRel($where,$order,null,$fields,$table);
	
		while ($obAtendimento = $dadosAtendimentos -> fetchObject(EntityPaciente::class)) {
			
			$resultado .=  "['$obAtendimento->atendimento', $obAtendimento->total], "  ;
		}
		
	//	var_dump($resultado);exit;
		
		return $resultado;
		
	}
	
	
	
	
	
	
	
	
	//retorna o conteudo (view) da nossa home
	public static function getHome(){
		$mes_extenso = array(
				'January' => 'Janeiro',
				'February' => 'Fevereiro',
				'March' => 'Marco',
				'April' => 'Abril',
				'May' => 'Maio',
				'June' => 'Junho',
				'July' => 'Julho',
				'August' => 'Agosto',
				'November' => 'Novembro',
				'September' => 'Setembro',
				'October' => 'Outubro',
				'December' => 'Dezembro'
		);

	//var_dump(self::getProducaoPorAtendimento('2021-09-01', '2021-09-30')); exit;
		$mes = $mes_extenso[date('F',strtotime("-1 month"))] ;
		$P_Dia = date('Y-m-01',strtotime("-1 month"));
		$U_Dia = date('Y-m-t',strtotime("-1 month"));
		
		$totalPacientes = EntityPaciente::getPacientes(null, 'id DESC',null,'COUNT(*) as qtd')->fetchObject()->qtd;
		$totalPacientesAd = EntityPaciente::getPacientes('tipo = "Ad" AND status = "Ativo" ', 'id DESC',null,'COUNT(*) as qtd')->fetchObject()->qtd;
		$totalPacientesTm = EntityPaciente::getPacientes('tipo = "Tm" AND status = "Ativo" ', 'id DESC',null,'COUNT(*) as qtd')->fetchObject()->qtd;
		$totalPacientesAtivos = EntityPaciente::getPacientes('status = "Ativo"', 'id DESC',null,'COUNT(*) as qtd')->fetchObject()->qtd;
		$totalPacientesInativos = EntityPaciente::getPacientes('status = "Inativo"', 'id DESC',null,'COUNT(*) as qtd')->fetchObject()->qtd;
		
	//	var_dump($U_Dia);exit;
		$content = View::render('pages/home',[
				'grafico1'=>View::render('pages/graficos/graficos',[
    				'label'=> self::getProducaoPorAtendimento($P_Dia, $U_Dia),
    				'title' => 'Atendimentos do mês de '.$mes .'/'.date('Y'),
				    ]), 
				'totalPacientes' => $totalPacientes,
				'totalAd' => $totalPacientesAd,
				'totalTm' => $totalPacientesTm,
				'totalAtivos' => $totalPacientesAtivos,
				'totalInativos' => $totalPacientesInativos
		]);
		
		return parent::getPage('Siscaps', $content);
		
	}
	
}