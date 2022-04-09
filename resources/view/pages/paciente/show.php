<?php

use App\Model\Entity\Paciente;


	$mensagem = '';
	if(isset($_GET['status'])){
		switch ($_GET['status']){
			case 'success' : 
				$mensagem = '<div class="alert alert-success"> Ação executada com sucesso!</div>';
				break;
			case 'error' :
				$mensagem = '<div class="alert alert-danger"> Ação não executada!</div>';
				break;
		}
	}
	
	
	//Busca
	$busca = filter_input(INPUT_GET, 'busca', FILTER_SANITIZE_STRING);
	//Filtro Status
	$filtroStatus = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_STRING);
	//Filtro recebe apenas os valores possíveis(s ou n) caso contrário recebe vazio.
	$filtroStatus = in_array($filtroStatus, ['ATIVO','INATIVO']) ? $filtroStatus : '';
	
	$filtroTipo = filter_input(INPUT_GET, 'tipo', FILTER_SANITIZE_STRING);
	
	//Consições SQL
	$condicoes = [
			
			strlen($busca) ? 'nome LIKE "%'.str_replace(' ', '%', $busca).'%"' : null,
			strlen($filtroStatus) ? 'status = "'.$filtroStatus.'" ' : null,
			strlen($filtroTipo) ? 'tipo = "'.$filtroTipo.'" ' : null
			
	];
	
	//Remove posições vazias
	$condicoes = array_filter($condicoes);
	//cláusula where
	$where = implode(' AND ', $condicoes);
	//Obtem os pacientes
	$pacientes = Paciente::getPacientes($where,'codPronto asc');
	
	$resultados = '';
	foreach ($pacientes as $paciente){
		//muda cor do texto do status para azul(ativo) ou vermelho(inativo)
		$paciente->status == 'ATIVO' ? $cor = 'text-primary' : $cor = 'text-danger';
		
		$resultados .='<tr>
						<td>'.str_pad($paciente->codPronto,4,"0",STR_PAD_LEFT).'</td>
						<td>'.$paciente->nome.'</td>
						<td>'.$paciente->endereco.'</td>
						<td>'.$paciente->tipo.'</td>
						<td class="'.$cor.'">'.$paciente->status.'</td>
						<td>
							<a href="editar.php?id='.$paciente->id.'" class="btn btn-primary btn-sm " >Editar</a>
							<a href="excluir.php?id='.$paciente->id.'" class="btn btn-danger btn-sm " >Excluir</a>
						</td>
					</tr>';
								
	}
	
	
	$resultados = strlen($resultados) ? $resultados : '<tr>
														<td colspan="6" class="text-center">Nenhuma vaga encontrada</td>
														</tr>';
?>

	
<main>
	<?=$mensagem?>
  <section>
    
  </section>
  
  <section>
  	<form method="get">
  		<div class="row my-4">
  			<div class="col-lg-4">
  				<label>Buscar pelo nome do paciente</label>
  				<input type="text" name="busca" class="form-control-sm" value="<?=$busca?>" style="width:100%">
  			</div>
  			
  			<div class="col-lg-2" >
  				<label>Status</label>
  				<select name="status" class="form-control form-control-sm">
  					<option value="">Ativo/Inativo</option>
  					<option value="ATIVO" <?=$filtroStatus == 'ATIVO' ? 'selected' : '' ?> >Ativo</option>
  					<option value="INATIVO" <?=$filtroStatus == 'INATIVO' ? 'selected' : '' ?> >Inativo</option>
  				</select>
  				
  			</div>
  			<div class="col-lg-1" >
  				<label>Tipo</label>
  				<select name="tipo" class="form-control form-control-sm">
  					<option value="">Ad/Tm</option>
  					<option value="AD" <?=$filtroTipo == 'AD' ? 'selected' : '' ?> >Ad</option>
  					<option value="TM" <?=$filtroTipo == 'TM' ? 'selected' : '' ?> >Tm</option>
  				</select>
  				
  			</div>
  			
  			
  			<div class="col-lg-4 d-flex align-items-end">
  			<button type="submit" class="btn btn-primary btn-sm" >Buscar</button>
  			<a href="cadastrar.php" style="margin-left:5px">
		      <button class="btn btn-success btn-sm">Novo</button>
		    </a>
  			</div>
  		</div>
  	</form>
  
  </section>
  
  
  <section>
  	<table class="table  table-sm table-striped table-hover bg-light mt-1">
  		
  		<thead class="bg-success text-light text-center">
  			<tr >
  				<th>Pront.</th>
  				<th>Nome</th>
  				<th>Endereço</th>
  				<th>Tipo</th>
  				<th >Status</th>
  				<th style="width:13%">Ações</th>
  			</tr>
  		</thead>
  		<tbody class="table-success">
  			<?=$resultados;?>
  		</tbody>
  	
  	</table>
  </section>
  
  
</main>
