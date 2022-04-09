<?php

namespace App\Controller\Visitor;

use App\Utils\View;
use App\Utils\Funcoes;
use \App\Model\Entity\Cid10 as Entitycid10;
use \App\Model\Entity\Paciente as EntityPaciente;
use Dompdf\Dompdf;
use App\Model\Entity\Bairro;
use App\Model\Entity\Cidade;

class Lme  extends Page{
	
	//esconde busca rápida de prontuário no navBar
	private static $hidden = '';
	
	//Método responsavel por renderizar o FORM da LME
	public static function getLme($request,$codPronto){
		
		
		//	var_dump($codPronto);exit;
		//obtém o Paciente do banco de dados
		$obPaciente = EntityPaciente::getPacienteByCodPronto($codPronto);
		
		//Valida a instancia
		if(!$obPaciente instanceof EntityPaciente){
			$request->getRouter()->redirect('/admin/pacientes');
		}
		
		//Conteúdo da Home
		$content = View::render('admin/modules/lme/form',[
				'title' => 'Laudo de Solicitação de Medicamento LME',
				'codPronto' => $obPaciente->codPronto,
				'tipo' => $obPaciente->tipo,
				'nome' => $obPaciente->nome,
				'dataNasc' => date('d/m/Y', strtotime($obPaciente->dataNasc)),
				'dataCad' => date('d/m/Y', strtotime($obPaciente->dataCad)),
				'sexo' => $obPaciente->sexo,
				'naturalidade' => $obPaciente->naturalidade,
				'mae' => $obPaciente->mae,
				'cartaoSus' =>Funcoes::mask($obPaciente->cartaoSus,'### ### ### ### ###') ,
				'fone1' =>$obPaciente->fone1,
				'fone2' =>$obPaciente->fone2,
				'cid' =>Entitycid10::getCid10ById($obPaciente->cid1)->nome
		]);
		
		//Retorna a página completa
		return parent::getPanel('Pacientes > Siscaps', $content,'pacientes', self::$hidden);
	//	return $content;
		
	}
	
	//Método responsavel por renderizar o LME
	public static function getLmePrint($request){
		
		//Post vars
		$postVars = $request->getPostVars();
	//	var_dump($postVars);exit;
		
		$codPronto = $postVars['CodPronto'];
		//esconde busca rápida de prontuário no navBar
		$hidden = '';
		
		//	var_dump($codPronto);exit;
		//obtém o Paciente do banco de dados
		$obPaciente = EntityPaciente::getPacienteByCodPronto($codPronto);
		
		//Valida a instancia
		if(!$obPaciente instanceof EntityPaciente){
			$request->getRouter()->redirect('/admin/pacientes');
		}
		
		
		if($postVars['med1'][0] == 'O'){
			$anamnese = 'PACIENTE É PORTADOR DE ESQUIZOFRENIA COM TODOS OS CRITÉRIOS DE SHINADER, ROUBO, INSERÇÃO E IRRADIAÇÃO
						DO PENSAMENTO, DELÍRIOS E ALUCINAÇÕES. FAZ USO DE OLANZAPINA HÁ +/- 2 ANOS.';
			$tratamentoAnterior = 'PACIENTE JÁ FEZ USO DE HALDOL E RISPERIDONA POR 3 ANOS SEM RESPOSTA CLÍNICA E TEVE MUITA
						INTOLERÂNCIA COM SINTOMAS EXTRAPIRAMIDAIS.';
					
		}else if($postVars['med1'][0] == 'R'){
			$anamnese = 'PACIENTE É PORTADOR DE ESQUIZOFRENIA COM TODOS OS CRITÉRIOS DE SHINADER, ROUBO, INSERÇÃO E IRRADIAÇÃO
						DO PENSAMENTO, DELÍRIOS E ALUCINAÇÕES. FAZ USO DE RISPERIDONA HÁ +/- 2 ANOS.';
			$tratamentoAnterior = 'PACIENTE JÁ FEZ USO DE HALDOL E OLANZAPINA POR 3 ANOS SEM RESPOSTA CLÍNICA E TEVE MUITA
						INTOLERÂNCIA COM SINTOMAS EXTRAPIRAMIDAIS.';
		}else{
			$anamnese = '';
			$tratamentoAnterior = '';
		}
		
		
		
		//Renderização do Laudo de solicitação de medicamento
		$content = View::render('admin/modules/lme/lme',[
				
				'nome' => $obPaciente->nome,
				'mae' => $obPaciente->mae,
				'cartaoSus' =>Funcoes::mask($obPaciente->cartaoSus,'### ### ### ### ###') ,
				'fone1' =>$obPaciente->fone1,
				'fone2' =>$obPaciente->fone2,
				'cid' =>Entitycid10::getCid10ById($obPaciente->cid1)->nome,
				'data' => date('d/m/Y'),
				'med1' =>$postVars['med1'],
				'qtd1Med1' =>$postVars['qtd1Med1'] ?? '',
				'qtd2Med1' =>$postVars['qtd2Med1'] ?? '',
				'qtd3Med1' =>$postVars['qtd3Med1'] ?? '',
				'qtd4Med1' =>$postVars['qtd4Med1'] ?? '',
				'qtd5Med1' =>$postVars['qtd5Med1'] ?? '',
				'qtd6Med1' =>$postVars['qtd6Med1'] ?? '',
				'med2' =>$postVars['med2'],
				'qtd1Med2' =>$postVars['qtd1Med2'] ?? '',
				'qtd2Med2' =>$postVars['qtd2Med2'] ?? '',
				'qtd3Med2' =>$postVars['qtd3Med2'] ?? '',
				'qtd4Med2' =>$postVars['qtd4Med2'] ?? '',
				'qtd5Med2' =>$postVars['qtd5Med2'] ?? '',
				'qtd6Med2' =>$postVars['qtd6Med2'] ?? '',
				'med3' =>$postVars['med3'],
				'qtd1Med3' =>$postVars['qtd1Med3'] ?? '',
				'qtd2Med3' =>$postVars['qtd2Med3'] ?? '',
				'qtd3Med3' =>$postVars['qtd3Med3'] ?? '',
				'qtd4Med3' =>$postVars['qtd4Med3'] ?? '',
				'qtd5Med3' =>$postVars['qtd5Med3'] ?? '',
				'qtd6Med3' =>$postVars['qtd6Med3'] ?? '',
				'med4' =>$postVars['med4'],
				'qtd1Med4' =>$postVars['qtd1Med4'] ?? '',
				'qtd2Med4' =>$postVars['qtd2Med4'] ?? '',
				'qtd3Med4' =>$postVars['qtd3Med4'] ?? '',
				'qtd4Med4' =>$postVars['qtd4Med4'] ?? '',
				'qtd5Med4' =>$postVars['qtd5Med4'] ?? '',
				'qtd6Med4' =>$postVars['qtd6Med4'] ?? '',
				'med5' =>$postVars['med5'],
				'qtd1Med5' =>$postVars['qtd1Med5'] ?? '',
				'qtd2Med5' =>$postVars['qtd2Med5'] ?? '',
				'qtd3Med5' =>$postVars['qtd3Med5'] ?? '',
				'qtd4Med5' =>$postVars['qtd4Med5'] ?? '',
				'qtd5Med5' =>$postVars['qtd5Med5'] ?? '',
				'qtd6Med5' =>$postVars['qtd6Med5'] ?? '',
				'med6' =>$postVars['med6'],
				'qtd1Med6' =>$postVars['qtd1Med6'] ?? '',
				'qtd2Med6' =>$postVars['qtd2Med6'] ?? '',
				'qtd3Med6' =>$postVars['qtd3Med6'] ?? '',
				'qtd4Med6' =>$postVars['qtd4Med6'] ?? '',
				'qtd5Med6' =>$postVars['qtd5Med6'] ?? '',
				'qtd6Med6' =>$postVars['qtd6Med6'] ?? '',
				'anamnese' => $anamnese,
				'tratamentoAnterior' => $tratamentoAnterior
				
		]);
		
		
		//Rendereização do Termo de consentimento
		$content .= View::render('admin/modules/lme/termo',[
				
				'nome' => $obPaciente->nome,
				'sexo' => $obPaciente->sexo,
				'idade' =>'',
				'endereco' =>$obPaciente->endereco,
				'bairro' =>Bairro::getBairroById($obPaciente->bairro)->nome,
				'data' => date('d/m/Y'),
				'cidade' =>$obPaciente->cidade,
				'cep' =>$obPaciente->cep,
				'fone' =>$obPaciente->fone1
				
		]); 
		
		//Rendereização da Escala BPRS
		$content .= View::render('admin/modules/lme/escalaBPRS',[
				
				'nome' => $obPaciente->nome,
				'sexo' => $obPaciente->sexo,
				'idade' =>'',
				'endereco' =>$obPaciente->endereco,
				'bairro' =>Bairro::getBairroById($obPaciente->bairro)->nome,
				'data' => date('d/m/Y'),
				'cidade' =>$obPaciente->cidade,
				'cep' =>$obPaciente->cep,
				'fone' =>$obPaciente->fone1
				
		]);
		
		//Retorna a página completa
	//	return parent::getPanel('Pacientes > Siscaps', $content,'pacientes', self::$hidden);
			return $content;
		
	}

	//Método responsavel por gerar o PDF da Capa de Prontuário do Paciente
	public static function getLmePrintPdf($request){

		//Se clicar na pesquisa rápida 
		$postVars = $request->getPostVars();
		$uri = $request->getUri();
		$pront = @$postVars['pront'];
		//retira '/caps' da URI
		$uri = str_replace('/caps','',$uri);
		
		if(isset($pront)){
			$request->getRouter()->redirect('/admin/pacientes/'.$pront.'/lme');
		}
		
		
		
		//se não clicar na pesquisa rápida, continua daqui
		
		//instância a classe
		$dompdf = new Dompdf(["enable_remote" => true]);
		$options = $dompdf->getOptions();
		$options->setDefaultFont('Courier');
		$dompdf->setOptions($options);
		//abre a sessão de cache
		//	ob_start();
		//caminho do arquivo
		//	require '{{URL}}../../resources/view/admin/modules/pacientes/capa.html';
		//recebe o conteudo entre as tags ob_start e ob_get_clean
		//	$pdf = ob_get_clean();
		
		$pdf = self::getLmePrint($request);
		
		//carrega o conteúdo do arquivo .php
		$dompdf->loadHtml($pdf);
		
		
		
		//Configura o tamanho do papel
		$dompdf->setPaper("A4");
		
		$dompdf->render();
		
		$dompdf->stream("Lme-Termo.php", ["Attachment" => false]);
		
	}
}