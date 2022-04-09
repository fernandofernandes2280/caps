<?php
use \App\Model\Entity\Paciente as EntityPaciente;
use \App\Model\Entity\Procedimento as EntityProcedimento;
use \App\Model\Entity\Cid10 as EntityCid10;
use \App\Model\Entity\Profissional as EntityProfissional;

function mask($val, $mask)
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

$where = 'A.data BETWEEN "2021/07/01" and "2021/07/31" ' ;
$fields = 'A.codPronto as codPronto, A.idProcedimento as procedimento, A.data as data, A.idProfissional as profissional, P.nome as nome, P.cid1 as cid1 ';
$table = 'atendimentos as A inner join pacientes as P on A.codPronto = P.codPronto';
$order = 'A.codPronto, A.data';
//from atendimentos as A
//Obtem os pacientes

$fiels2 = 'DISTINCT A.codPronto';
$dadosPacientes = EntityPaciente::getPacientesRel($where, 'A.codPronto',null,$fiels2,$table);

?>
<html>
	<head>
		<style>
			@page{
				
				margin: 70px 0;
			}
			
			body{
			
				font-family: "Open Sans", sans-serif;
			}
			
			.header{
				position: fixed;
				top: -70px;
				left: 0;
				right: 0;
			}
			
			.footer{
				position: fixed;
				bottom: -27px;
				left: 0;
				
				width: 100%;
				padding: 5px 10px 10px 10px;
				text-align: center;
				background: #555555;
				color: #ffffff;
			}
			.footer .page:after{
				content: counter(page);	
			}
			th{
				text-transform: uppercase; 
			}
			
			*{
				
			}
			
			.titulo{
				background: #000; 
				color: #fff; 
				text-align:center; 
				font-size: 12px; 
				font-weight:bold
			}
			.subtitulo{
				font-size:8px;
			}
			.item{
				font-size:14px;
				font-weight:bold
			}
			th, td {
			  padding: -5px 5px;
			}
			
			 table {border-collapse:collapse; }
			   table td {border:solid 1px #000; word-wrap:break-word;}
			   

			
			@media print {
				.quebra {page-break-after: always;}
			  .print {
			    display: none;
			  }
			}   
			   
			   
			   
			</style>
	</head>
		<div class="header">
			<h2>Centro de Atenção Psicossial</h2>
		</div>
		<div class="footer">
			Gerado dia <?= date(DATE_W3C); ?>, <span class="page">Página </span>
		</div>



<?php 
//var_dump($dadosAtendimentos);exit;


//$nome = $results->codPronto;
$whereA = ' and data BETWEEN "2021/07/01" and "2021/07/31" ' ;
$tableA = 'atendimentos';
$orderA = 'data';

while ($obPaciente = $dadosPacientes -> fetchObject(EntityPaciente::class)) {
	
	
	$prontuario = $obPaciente->codPronto;
	$nome =  EntityPaciente::getPacienteByCodPronto($obPaciente->codPronto)->nome;
	
	$dadosAtendimentos = EntityPaciente::getPacientesRel('codPronto = '.$prontuario.' '.$whereA, $orderA, null,'*',$tableA);
	//var_dump($dadosAtendimentos);exit;
?>

<div>
	<table>
      <tr>
        <td colspan="2" style="width:200px"><img src="https://i2.wp.com/multarte.com.br/wp-content/uploads/2020/10/sus-logo.jpg" height="70" Alt="SUS"></td>
        <td colspan="5" style="width:600px; font-size:20px"><b style="font-size: 30px">RAAS</b> Regsitro das Ações Ambulatoriais de Saúde formulário da Atenção Psicossocial no CAPS </td>
      </tr>
      <tr>
      	<td colspan="7" class="titulo">IDENTIFICAÇÃO DO ESTABELECIMENTO DE SAÚDE</td>
      </tr>
      <tr>
      	<td colspan="6">
      		<span class="subtitulo">NOME DO ESTABELECIMENTO</span><br>
      		<span class="item">CENTRO DE ATENÇÃO PSICOSSOCIAL DE ALCOOL E OUTRAS DROGAS CAPS AD</span>
      	</td>
      	<td>
      		<span class="subtitulo">CNES</span><br>
      		<span class="item">3194248</span>
      	</td>
      </tr>
      <tr>
      	<td colspan="7" class="titulo">IDENTIFICAÇÃO DO USUÁRIO DO SUS</td>
      </tr>
      <tr>
      	<td >
      		<span class="subtitulo">PRONTUÁRIO</span><br>
      		<span class="item"><?=@$prontuario?></span>
      	</td>
      	<td colspan="6">
      		<span class="subtitulo">NOME DO PACIENTE</span><br>
      		<span class="item"><?=@$nome?></span>
      	</td>
      </tr>

       <tr>
      	<td colspan="2">
      		<span class="subtitulo">CARTÃO NACIONAL DE SAÚDE(CNS)</span>
      		<span class="item">XXXXXXXXXXX</span>
      	</td>
      	<td>
      		<span class="subtitulo">SEXO</span><br>
      		<span class="item">xxxx</span>
      	</td>
      	<td colspan="2">
      		<span class="subtitulo">DATA DE NASCIMENTO</span><br>
      		<span class="item">xxxxx</span>
      	</td>
      	<td colspan="2">
      		<span class="subtitulo">NACIONALIDADE</span><br>
      		<span class="item">xxxx</span>
      	</td>
      </tr>
      
       <tr>
      	<td colspan="1">
      		<span class="subtitulo">RAÇA</span>
      	</td>
      	<td colspan="2">
      		<span class="subtitulo">ETNIA</span>
      	</td>
      	<td colspan="4">
      		<span class="subtitulo">NOME DA MÃE</span><br>
      		<span class="item">xxxxx</span>
      	</td>
      </tr>
      
       <tr>
      	<td colspan="4">
      		<span class="subtitulo">NOME DO RESPONSÁVEL</span>
      	</td>
      	<td colspan="2">
      		<span class="subtitulo">MUNICÍPIO DE RESIDÊNCIA</span>
      	</td>
      	<td colspan="1">
      		<span class="subtitulo">UF</span><br>
      		<span class="item">xxxxx</span>
      	</td>
      </tr>

       <tr>
      	<td colspan="1">
      		<span class="subtitulo">CÓD IBGE MUNICÍPIO</span>
      	</td>
      	<td colspan="1">
      		<span class="subtitulo">CEP DE RESIDÊNCIA</span>
      	</td>
      	<td colspan="3">
      		<span class="subtitulo">ENDEREÇO (RUA, NÚMERO)</span>
      	</td>
      	<td colspan="2">
      		<span class="subtitulo">BAIRRO</span><br>
      		<span class="item">xxxxx</span>
      	</td>
      </tr>

       <tr>
      	<td colspan="3">
      		<span class="subtitulo">COMPLEMENTO</span>
      	</td>
      	<td colspan="2">
      		<span class="subtitulo">TELEFONE CELULAR</span>
      	</td>
      	<td colspan="2">
      		<span class="subtitulo">TELEFONE DE CONTATO</span><br>
      		<span class="item">xxxxx</span>
      	</td>
      </tr>      
      
      <tr>
      	<td colspan="7" class="titulo">DADOS DO ATENDIMENTO</td>
      </tr>

       <tr>
      	<td colspan="1">
      		<span class="subtitulo">DATA DE INÍCIO</span>
      	</td>
      	<td colspan="2">
      		<span class="subtitulo">COMPETÊNCIA</span>
      	</td>
      	<td colspan="3">
      		<span class="subtitulo">ORIGEM DO PACIENTE</span><br>
      		<span class="item">xxxxx</span>
      	</td>
      </tr> 

       <tr>
      	<td colspan="1">
      		<span class="subtitulo">CID 10 PRINCIPAL</span>
      	</td>
      	<td colspan="6">
      		<span class="subtitulo">DESCRIÇÃO DO DIAGNÓSTICO PRINCIPAL</span><br>
      		<span class="item">xxxxx</span>
      	</td>
      </tr>                 
      
       <tr>
      	<td colspan="1">
      		<span class="subtitulo">CID 10 SECUNDÁRIO</span>
      	</td>
      	<td colspan="6">
      		<span class="subtitulo">DESCRIÇÃO DO DIAGNÓSTICO COMPLEMENTAR (1º)</span><br>
      		<span class="item">xxxxx</span>
      	</td>
      </tr>                 
      
       <tr>
      	<td colspan="1">
      		<span class="subtitulo">CID 10 SECUNDÁRIO</span>
      	</td>
      	<td colspan="6">
      		<span class="subtitulo">DESCRIÇÃO DO DIAGNÓSTICO COMPLEMENTAR (2º)</span><br>
      	</td>
      </tr> 

       <tr>
      	<td colspan="1">
      		<span class="subtitulo">CID 10 SECUNDÁRIO</span>
      	</td>
      	<td colspan="6">
      		<span class="subtitulo">DESCRIÇÃO DO DIAGNÓSTICO COMPLEMENTAR (3º)</span><br>
      	</td>
      </tr>                 
                      
       <tr>
      	<td colspan="1">
      		<span class="subtitulo">CID 10 CAUSAS ASS</span>
      	</td>
      	<td colspan="6">
      		<span class="subtitulo">DESCRIÇÃO DO DIAGNÓSTICO - CAUSAS ASSOCIADAS</span><br>
      	</td>
      </tr>                 
                      
       <tr>
      	<td colspan="4">
      		<span class="subtitulo">EXISTE COBERTURA DE ESTRATÉGIA SAÚDE DA FAMÍLIA?</span>
      		<span class="item">[ ] SIM [X] NÃO</span>
      	</td>
      	<td colspan="3">
      		<span class="subtitulo">CNES</span><br>
      	</td>
      </tr>   

       <tr>
      	<td colspan="5">
      		<span class="subtitulo">DESTINO DO PACIENTE</span><br>
      		<span class="item"></span>
      	</td>
      	<td colspan="2">
      		<span class="subtitulo">DATA DE CONCLUSÃO</span><br>
      	</td>
      </tr>  



      <tr>
      	<td colspan="7" class="titulo">AÇÕES REALIZADAS</td>
      </tr>
<?php 
while ($obAtendimentos = $dadosAtendimentos -> fetchObject(EntityPaciente::class)) {
//	var_dump($obAtendimentos);exit;
	$procedimento = EntityProcedimento::getProcedimentoById($obAtendimentos->idProcedimento)->codProcedimento;
	$data = date('d/m/Y', strtotime($obAtendimentos->data));
	$obAtendimentos->cid1 != null ? $cid1 = EntityCid10::getCid10ById($obAtendimentos->cid1)->nome : '0';
	$cbo = EntityProfissional::getProfissionalById($obAtendimentos->idProfissional)->cbo;
			
	?>
	 <tr style="text-align:center">
      	<td style="width:120px">
      		<span class="subtitulo">CÓD DA AÇÃO REALIZADA</span><br>
      		<span class="item"><?=@mask($procedimento,'# # # # # # # # # #')?></span>
      	</td>
      	<td style="width:25px">
      		<span class="subtitulo">QTDE</span><br>
      		<span class="item">1</span>
      	</td>
      	<td>
      		<span class="subtitulo">DATA(DD/MM)</span><br>
      		<span class="item"><?=@$data?></span>
      	</td>
      	<td style="width:25px">
      		<span class="subtitulo">CID 10</span><br>
      		<span class="item"><?=@$cid1?></span>
      	</td>
      	<td>
      		<span class="subtitulo">SERVIÇO</span><br>
      		
      	</td>
      	<td>
      		<span class="subtitulo">CLASSIFICAÇÃO</span><br>
      		
      	</td>
      	<td>
      		<span class="subtitulo">CBO EXECUTANTE</span><br>
      		<span class="item"><?=@$cbo?></span>
      	</td>
      </tr>
	
	<?php 
	
}            
      
    ?> 
      
</table>
<div class="quebra"></div>
<?php 
}
	
?>
		</div>
	
	</body>


</html>








