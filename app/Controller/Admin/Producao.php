<?php

namespace App\Controller\Admin;

use App\Utils\View;
use \App\Model\Entity\Paciente as EntityPaciente;
use App\Utils\Funcoes;
use \App\Model\Entity\Profissional as EntityProfissional;
use \App\Model\Entity\Procedimento as EntityProcedimento;
use \App\Model\Entity\Cid10 as EntityCid10;
use \App\Model\Entity\Substancia as EntitySubstancia;
use \App\Model\Entity\Bairro as EntityBairro;

class Producao{
	
	//Armazena quantidade total de pacientes listados
	private static $qtdTotal ;
	private static $totalGeralBpac = 0;
	//esconde busca rápida de prontuário no navBar
	private static $hidden = '';
	
	
	//Método responsável por retornar os atendimentos do relatório da RAAS
	public static function getProducaoItem($codPronto,$dataInicio,$dataFim,$instrumento){
		
		$resultados = '';
		$resultadosAvulso = '';
		
		//$where = 'codPronto = '.$codPronto.' and data BETWEEN "'.$dataInicio.'" and "'.$dataFim.'" ' ;
		
		//bpac nao usa codpronto, raas utiliza na busca
		if($instrumento == 2 || $instrumento == 3){
			is_null($codPronto) ? $addCodpronto = '' : $addCodpronto = 'A.codPronto = '.$codPronto.' and ';
		}else{
			$addCodpronto = 'A.idProfissional = '.$codPronto.' and ';
		}
		
		
		//	$where = $addCodpronto.' I.id = '.$instrumento.' and A.data BETWEEN "'.$dataInicio.'" and "'.$dataFim.'" ' ;
		$table = 'atendimentos as A inner join pacientes as P on A.codPronto = P.codPronto inner join procedimentos as T on A.idProcedimento = T.id
				 inner join profissionais as F on A.idProfissional = F.id inner join instrumentos as I on T.instrumento = I.id
				 inner join cid10 as C on P.cid1 = C.id	';
		
		//table dos atendimentos avulsos
		$tableAvulso = 'atendimentosAvulsos as A inner join procedimentos as T on A.idProcedimento = T.id inner join profissionais as F on A.idProfissional = F.id inner join instrumentos as I on T.instrumento = I.id';
		
		
		//Condições para BPAI
		if($instrumento == 1){
			$where = $addCodpronto.' A.status = "P" and I.id = '.$instrumento.' and A.data BETWEEN "'.$dataInicio.'" and "'.$dataFim.'" ' ;
			$fields = 'A.idProfissional as idProfissional, T.codProcedimento as codProcedimento, A.data as dataAtendimento, C.nome as cid10, F.cbo as cbo,
				F.cartaoSus as cartaoSus, P.cartaoSus as cartaoSusP, P.nome as nome, P.sexo as sexo, P.dataNasc as dataNasc, P.endereco, P.bairro,P.cidade,P.cep,P.uf, P.fone1, P.fone2	';
			$order = 'A.idProfissional, A.data';
		}
		
		
		//Condições para BPAC
		if($instrumento == 2){
			
			//com soma dos atendimentos por idade (não necessário agora)
			//$where = $addCodpronto.' I.id = '.$instrumento.' and A.data BETWEEN "'.$dataInicio.'" and "'.$dataFim.'" GROUP BY T.codProcedimento, F.cbo, A.idade ' ;
			//$fields = 'T.codProcedimento as codProcedimento, A.idade as idade, F.cbo as cbo, count(*) as totalGrupo';
			
			$where = $addCodpronto.' A.status = "P" and I.id = '.$instrumento.' and A.data BETWEEN "'.$dataInicio.'" and "'.$dataFim.'" GROUP BY T.codProcedimento, F.cbo' ;
			$fields = 'T.codProcedimento as codProcedimento, F.cbo as cbo, count(*) as totalGrupo';
			$order = 'T.codProcedimento';
			
			//Itens dos atendimentos avulsos
			$whereAvulso = ' I.id = '.$instrumento.' and A.data BETWEEN "'.$dataInicio.'" and "'.$dataFim.'" ' ;
			$fieldsAvulso = 'T.codProcedimento as codProcedimento, F.cbo as cbo, A.qtd';
			$orderAvulso = 'T.codProcedimento';
			$dadosAtendimentosAvulso = EntityPaciente::getPacientesRel($whereAvulso,$orderAvulso,null,$fieldsAvulso,$tableAvulso);
		}
		
		//Condições para RAAS
		if($instrumento == 3){
			$where = $addCodpronto.'A.status = "P" and I.id = '.$instrumento.' and A.data BETWEEN "'.$dataInicio.'" and "'.$dataFim.'"  ' ;
			$fields = 'DISTINCT A.codPronto as codPronto, T.codProcedimento as codProcedimento, A.data as dataAtendimento, C.nome as cid10, F.cbo as cbo,
				F.cartaoSus as cartaoSus	';
			$order = 'A.data desc';
		}
		
		
		//	var_dump($where);
		$dadosAtendimentos = EntityPaciente::getPacientesRel($where,$order,null,$fields,$table);
		
		
		
		//	print_r($dadosAtendimentosAvulso);exit;
		
		$cont=0;
		
		
		
		//Instrumento BPAI
		if($instrumento == 1){
			while ($obAtendimento = $dadosAtendimentos -> fetchObject(EntityPaciente::class)) {
				
				$cont++;
				//View de atendimentos RAAS
				$resultados .= View::render('admin/modules/atendimentos/relatorios/itemBpai',[
						
						'cartaoSus' =>Funcoes::mask($obAtendimento->cartaoSusP,'# | # | # | # | # | # | # | # | # | # | # | # | # | # | #'),
						'nome' =>$obAtendimento->nome,
						'sexo' =>$obAtendimento->sexo,
						'dataNasc' =>date('d/m/Y', strtotime($obAtendimento->dataNasc)),
						'procedimento' =>$obAtendimento->codProcedimento,
						'data' =>date('d/m/Y', strtotime($obAtendimento->dataAtendimento)),
						'cid1' =>$obAtendimento->cid10,
						'seq'=>$cont,
						'endereco' => $obAtendimento->endereco.' '.EntityBairro::getBairroById($obAtendimento->bairro)->nome.' - '.$obAtendimento->cidade.'/'.$obAtendimento->uf,
						'fone'=> $obAtendimento->fone1. ' '.$obAtendimento->fone2
						
						
				]);
			}
		}
		
		
		
		//Instrumento BPA C
		if($instrumento == 2){
			while ($obAtendimento = $dadosAtendimentos -> fetchObject(EntityPaciente::class)) {
				$cont++;
				
				self::$totalGeralBpac += $obAtendimento->totalGrupo;
				//View de atendimentos BPA C
				$resultados .= View::render('admin/modules/atendimentos/relatorios/itemBpac',[
						
						'procedimento' =>Funcoes::mask($obAtendimento->codProcedimento,'#  #  #  #  #  #  #  #  #  #'),
						//'idade' =>$obAtendimento->idade,
						'idade' =>'',
						'cbo' => $obAtendimento->cbo,
						'seq' => $cont,
						'qtd' => $obAtendimento->totalGrupo
				]);
			}
			
			while ($obAtendimentoAvulso = $dadosAtendimentosAvulso -> fetchObject(EntityPaciente::class)) {
				$cont++;
				
				self::$totalGeralBpac += $obAtendimentoAvulso->qtd;
				//View de atendimentos BPA C
				$resultadosAvulso .= View::render('admin/modules/atendimentos/relatorios/itemBpac',[
						
						'procedimento' =>Funcoes::mask($obAtendimentoAvulso->codProcedimento,'#  #  #  #  #  #  #  #  #  #'),
						//'idade' =>$obAtendimento->idade,
						'idade' =>'',
						'cbo' => $obAtendimentoAvulso->cbo,
						'seq' => $cont,
						'qtd' => $obAtendimentoAvulso->qtd
				]);
			}
			
			
			
		}
		
		
		
		//Instrumento RAAS
		if($instrumento == 3){
			while ($obAtendimento = $dadosAtendimentos -> fetchObject(EntityPaciente::class)) {
				$cont++;
				//View de atendimentos RAAS
				$resultados .= View::render('admin/modules/atendimentos/relatorios/itemRaas',[
						
						'procedimento' =>Funcoes::mask($obAtendimento->codProcedimento,'#  #  #  #  #  #  #  #  #  #'),
						'data' =>date('d/m/Y', strtotime($obAtendimento->dataAtendimento)),
						'cid1' =>$obAtendimento->cid10,
						'cbo' => $obAtendimento->cbo,
						'cnsProfissional' =>Funcoes::mask($obAtendimento->cartaoSus,'# | # | # | # | # | # | # | # | # | # | # | # | # | # | #'),
						
				]);
			}
		}
		//concatena atendimentos normais com avulsos
		$retorno[0] = $resultados.$resultadosAvulso;
		$retorno[1] = $cont;
		//var_dump($cont);exit;
	//	print_r($retorno);exit;
		return $retorno;
	}
	
	
	
	
	//Metodo responsávelpor retornar o Relatório de RAAS
	public static function getProducao($request){
		
		/*
		 //instância a classe
		 $dompdf = new Dompdf(["enable_remote" => true]);
		 
		 //abre a sessão de cache
		 ob_start();
		 //caminho do arquivo
		 require '{{URL}}../../resources/view/admin/modules/atendimentos/relatorios/raas.php';
		 //recebe o conteudo entre as tags ob_start e ob_get_clean
		 $pdf = ob_get_clean();
		 
		 //carrega o conteúdo do arquivo .php
		 $dompdf->loadHtml($pdf);
		 
		 //Configura o tamanho do papel
		 $dompdf->setPaper("A4");
		 
		 $dompdf->render();
		 
		 $dompdf->stream("file.php", ["Attachment" => false]);
		 */
		
		//Renderiza
		//Recece váriavesi do post
		$postVars = $request->getPostVars();
		$dataInicio = $postVars['dataInicial'];
		$dataFim = $postVars['dataFinal'];
		$instrumento = $postVars['instrumento'];
		
		$resultados = '';
		
		$where = 'I.id = '.$instrumento.' and A.data BETWEEN "'.$dataInicio.'" and "'.$dataFim.'" and A.status = "P"' ;
		$table = 'atendimentos as A inner join pacientes as P on A.codPronto = P.codPronto inner join procedimentos as T on A.idProcedimento = T.id
				 inner join profissionais as F on A.idProfissional = F.id inner join instrumentos as I on T.instrumento = I.id
				 inner join cid10 as C on P.cid1 = C.id	';
		
		
		if($instrumento == 1){
			$fiels = 'DISTINCT A.idProfissional';
			$order = 'A.idProfissional';
		}
		else{
			$fiels = 'DISTINCT A.codPronto';
			$order = 'A.codPronto';
		}
		$dadosPacientes = EntityPaciente::getPacientesRel($where, $order,null,$fiels,$table);
		
		
		//	var_dump($dadosPacientes);exit;
		//total geral de atendimentos do período
		$qtdTotal = EntityPaciente::getPacientesRel($where, 'A.id DESC',null,'COUNT(*) as qtd',$table)->fetchObject()->qtd;
		
		//	var_dump($qtdTotal);exit;
		
		//Instrumento BPAI
		if($instrumento == 1){
			while ($dadosProfissionais = $dadosPacientes -> fetchObject(EntityPaciente::class)) {
				//View de produção RAAS
				$resultados .= View::render('admin/modules/atendimentos/relatorios/bpai',[
						'cnes'=>Funcoes::mask('3194248', '# # # # # # #'),
						'estabelecimento' => 'CENTRO DE ATENÇÃO PSICOSSOCIAL DE ALCOOL E OUTRAS DROGAS CAPS AD',
						'cartaoSus' =>Funcoes::mask(EntityProfissional::getProfissionalById($dadosProfissionais->idProfissional)->cartaoSus,'#  #  #  #  #  #  #  #  #  #  #  #  #  #  #'),
						'cbo' => EntityProfissional::getProfissionalById($dadosProfissionais->idProfissional)->cbo,
						'competencia' => date('m/Y',strtotime($dataInicio)),
						'itens'=>self::getProducaoItem($dadosProfissionais->idProfissional,$dataInicio,$dataFim,$instrumento)[0],
						'total' => self::getProducaoItem($dadosProfissionais->idProfissional,$dataInicio,$dataFim,$instrumento)[1],
				]);
			}
		}
		
		
		//Instrumento BPA C
		if($instrumento == 2){
			
			//View de produção BPAC
			$resultados .= View::render('admin/modules/atendimentos/relatorios/bpac',[
					'cnes'=>Funcoes::mask('3194248', '# # # # # # #'),
					'estabelecimento' => 'CENTRO DE ATENÇÃO PSICOSSOCIAL DE ALCOOL E OUTRAS DROGAS CAPS AD',
					'itens'=>self::getProducaoItem(null,$dataInicio,$dataFim,$instrumento)[0],
					'competencia' => date('m/Y',strtotime($dataInicio)),
					'total' => self::$totalGeralBpac
					
			]);
			
		
			
		}
		
		
		//Instrumento RAAS
		if($instrumento == 3){
			
			while ($obPaciente = $dadosPacientes -> fetchObject(EntityPaciente::class)) {
				$cid1 = $cid2 = $cid1Descricao = $cid2Descricao = '';
				if(!is_null(EntityPaciente::getPacienteByCodPronto($obPaciente->codPronto)->cid1))
					if(EntityPaciente::getPacienteByCodPronto($obPaciente->codPronto)->cid1 == 18){
						$cid1 = '';
				}else{
					$cid1 = EntityCid10::getCid10ById(EntityPaciente::getPacienteByCodPronto($obPaciente->codPronto)->cid1)->nome;
					if(!is_null(EntityPaciente::getPacienteByCodPronto($obPaciente->codPronto)->substanciaPri))
						$cid1Descricao = EntitySubstancia::getSubstanciaById(EntityPaciente::getPacienteByCodPronto($obPaciente->codPronto)->substanciaPri)->nome;
						if(!is_null(EntityPaciente::getPacienteByCodPronto($obPaciente->codPronto)->substanciaSec) && (EntityPaciente::getPacienteByCodPronto($obPaciente->codPronto)->substanciaSec != 11))
							$cid2Descricao = EntitySubstancia::getSubstanciaById(EntityPaciente::getPacienteByCodPronto($obPaciente->codPronto)->substanciaSec)->nome;
				}
				if(!is_null(EntityPaciente::getPacienteByCodPronto($obPaciente->codPronto)->cid2))
					if(EntityPaciente::getPacienteByCodPronto($obPaciente->codPronto)->cid2 == 18){
						$cid2 = '';
				}else {
					$cid2 = EntityCid10::getCid10ById(EntityPaciente::getPacienteByCodPronto($obPaciente->codPronto)->cid2)->nome;
					
				}
				
				//View de produção RAAS
				$resultados .= View::render('admin/modules/atendimentos/relatorios/raas',[
						'cnes'=>Funcoes::mask('3194248', '# # # # # # #'),
						'prontuario' =>str_pad($obPaciente->codPronto,4,"0",STR_PAD_LEFT),
						'nome' => EntityPaciente::getPacienteByCodPronto($obPaciente->codPronto)->nome,
						'itens'=>self::getProducaoItem($obPaciente->codPronto,$dataInicio,$dataFim,$instrumento)[0],
						'cartaoSus' =>Funcoes::mask(EntityPaciente::getPacienteByCodPronto($obPaciente->codPronto)->cartaoSus,'# | # | # | # | # | # | # | # | # | # | # | # | # | # | #'),
						'sexo' => EntityPaciente::getPacienteByCodPronto($obPaciente->codPronto)->sexo,
						'dataNasc' => date('d/m/Y', strtotime(EntityPaciente::getPacienteByCodPronto($obPaciente->codPronto)->dataNasc)),
						'mae' => EntityPaciente::getPacienteByCodPronto($obPaciente->codPronto)->mae,
						'cidade' => EntityPaciente::getPacienteByCodPronto($obPaciente->codPronto)->cidade,
						'uf' => EntityPaciente::getPacienteByCodPronto($obPaciente->codPronto)->uf,
						'cep' => Funcoes::mask(EntityPaciente::getPacienteByCodPronto($obPaciente->codPronto)->cep,'##.###-####'),
						'endereco' => EntityPaciente::getPacienteByCodPronto($obPaciente->codPronto)->endereco,
						'bairro' =>EntityBairro::getBairroById(EntityPaciente::getPacienteByCodPronto($obPaciente->codPronto)->bairro)->nome,
						'fone1' => Funcoes::mask(EntityPaciente::getPacienteByCodPronto($obPaciente->codPronto)->fone1,'#### ####'),
						'fone2' => Funcoes::mask(EntityPaciente::getPacienteByCodPronto($obPaciente->codPronto)->fone2,'#### ####'),
						'dataCad' => date('d/m/Y', strtotime(EntityPaciente::getPacienteByCodPronto($obPaciente->codPronto)->dataCad)),
						'cid1' => $cid1,
						'cid2' => $cid2,
						'cid1Descricao' =>$cid1Descricao,
						'cid2Descricao' =>$cid2Descricao,
						'competencia' => date('m/Y',strtotime($dataInicio)),
						'total' => self::getProducaoItem($obPaciente->codPronto,$dataInicio,$dataFim,$instrumento)[1],
				]);
			}
		}
		
		
		//Retorna Produção
		return $resultados;
		
	}
	
	

	
}