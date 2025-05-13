<?PHP
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	
	$sql = " 
	        SELECT nome_sem_acento AS nome,
				   UPPER(funcoes.remove_acento(TRIM(empresa))) AS empresa,				   
				   cd_barra AS codigo
	          FROM acs.seminario 
	         WHERE cd_seminario_edicao = ".$_REQUEST['cd_seminario']."
			   AND dt_exclusao IS NULL
			 ORDER BY nome_sem_acento
		   ";
	$ob_resul = pg_query($db, $sql);
	
    header('Pragma: public');
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");                  // Date in the past   
    header('Last-Modified: '.gmdate('D, d M Y H:i:s') . ' GMT');
    header('Cache-Control: no-store, no-cache, must-revalidate');     // HTTP/1.1
    header('Cache-Control: pre-check=0, post-check=0, max-age=0');    // HTTP/1.1
    header ("Pragma: no-cache");
    header("Expires: 0");
    header('Content-Transfer-Encoding: none');
    header('Content-Type: application/vnd.ms-excel;');                 // This should work for IE & Opera
    header("Content-type: application/x-msexcel");                    // This should work for the rest
    header('Content-Disposition: attachment; filename=lista_inscritos.csv');
	
	
	echo "NOME;EMPRESA;CODIGO\n";
	
	while($ar_reg = pg_fetch_array($ob_resul))
	{
		echo $ar_reg['nome'].";".$ar_reg['empresa'].";\"".$ar_reg['codigo']."\"\n";
	}
	
?>



