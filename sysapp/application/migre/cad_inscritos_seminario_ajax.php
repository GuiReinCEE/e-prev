<?
	header('Content-Type: text/html; charset=ISO-8859-1'); 
	include_once('inc/conexao.php');
	
	//print_r($_POST);
	
	#### VERIFICA CODIGO DE BARRAS ####
	if(trim($_POST['cd_barra'])!= "")
	{
		$sql = "
					SELECT COUNT(*) AS qt_total
					  FROM acs.seminario  
				     WHERE cd_barra = ".substr($_POST['cd_barra'], 0, 12)."
					   AND dt_exclusao IS NULL
		       ";
		$rs  = pg_query($db, $sql);
		$reg = pg_fetch_array($rs);
		if($reg['qt_total'] != 0)
		{
			echo $reg['qt_total'];
		}
	}
	
	#### LISTA CIDADES ####
	if(trim($_POST['uf'])!= "")
	{
		$sql = "
				SELECT nome_cidade AS cd_cidade,
					   nome_cidade AS ds_cidade
				  FROM expansao.cidades 
				 WHERE sigla_uf = '".trim($_POST['uf'])."' 
				 ORDER BY nome_cidade
				";
		$rs = pg_query($db, $sql);
		echo "
				<select name='cidade' id='cidade' style='width:400px;'>
					<option value=''>Selecione a cidade</option>
			 ";
		while ($cidade_reg = pg_fetch_array($rs)) 
		{
			echo "
					<option value='".$cidade_reg['cd_cidade']."'>".$cidade_reg['ds_cidade']."</option>
				 ";				 
		}		
		echo "
				</select>		
			 ";						
	}

/*	
	else
	{
		echo "
				<select name='cidade' id='cidade' style='width:400px;'>
					<option value=''>Selecione a cidade</option>
				</select>		
			 ";	
	}
*/	
?>