<?
	#### VERIFICA PERMISSAO DO USUARIO #### 
	$qr_sql = " 
				SELECT indic_07 
				  FROM projetos.usuarios_controledi
				 WHERE codigo = ".$_SESSION['Z']."
			  ";
	$ob_resul = pg_query($db, $qr_sql);
	if(pg_num_rows($ob_resul) > 0) 
	{
		$ar_reg = pg_fetch_array($ob_resul);
		if( trim($ar_reg['indic_07']) == '' )
		{
			echo "
					<script>
						alert('Você não possui permissão para acessar.');
						document.location.href = 'workspace.php';
					</script>
			     ";
			exit;
		}
	}
	else
	{
		echo "
				<script>
					alert('Você não possui permissão para acessar.');
					document.location.href = 'workspace.php';
				</script>
			 ";
		exit;
	}
?>