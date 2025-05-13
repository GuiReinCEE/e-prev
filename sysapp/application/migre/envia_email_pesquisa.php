<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');


	envia_email_pesquisa($num_pesquisa, $db);

	pg_close($db);
	header('location: cad_enquetes.php?c='.$num_pesquisa);

	function envia_email_pesquisa($cd_pesquisa, $db) 
	{
		$vbcrlf = chr(10).chr(13);
		$ds_msg = "Prezado(a): {nome}".$vbcrlf;
		$ds_msg.= "Por favor, responda a pesquisa.".$vbcrlf;
		$ds_msg.= "------------------------------------------------------------".$vbcrlf;
		$ds_msg.= "Nome da pesquisa: {titulo}".$vbcrlf;
		$ds_msg.= "Perнodo da pesquisa: de {dt_ini} atй {dt_fim}".$vbcrlf;
		$ds_msg.= "Link para pesquisa: http://www.e-prev.com.br/controle_projetos/resp_enquetes_capa.php?c=".$cd_pesquisa.$vbcrlf ;
		$ds_msg.= "------------------------------------------------------------" . $vbcrlf;
		
		#### DADOS DA PESQUISA ####
		$qr_sql = " 
					SELECT titulo, 
					       TO_CHAR(dt_inicio,'DD/MM/YYYY HH24:MI') AS dt_inicio,
						   TO_CHAR(dt_fim,'DD/MM/YYYY HH24:MI') AS dt_fim
					  FROM projetos.enquetes
				     WHERE cd_enquete = ".$cd_pesquisa;
		$ob_resul = pg_query($db, $qr_sql);
		$ar_reg = pg_fetch_array($ob_resul);
		$ds_assunto = $ar_reg['titulo'];
		$ds_msg = str_replace("{titulo}",$ds_assunto,$ds_msg);
		$ds_msg = str_replace("{texto_abertura}",$ar_reg['texto_abertura'],$ds_msg);
		$ds_msg = str_replace("{dt_ini}",$ar_reg['dt_inicio'],$ds_msg);
		$ds_msg = str_replace("{dt_fim}",$ar_reg['dt_fim'],$ds_msg);
		

		#### BUSCA COLABORADORES FUNDACAO CEEE ####
		$qr_sql = "  
					SELECT usuario, 
					       nome
					  FROM projetos.usuarios_controledi 
					 WHERE tipo IN ('U', 'N', 'E', 'G', 'D')
				  ";
		$ob_resul = pg_query($db, $qr_sql);
		while ($ar_reg = pg_fetch_array($ob_resul)) 
		{
			$qr_email = " INSERT INTO projetos.envia_emails 
							   ( 
								 dt_envio, 
								 de,
								 para, 
								 cc,
								 cco,
								 assunto,
								 texto,
								 cd_evento
							   ) 
						  VALUES
							   ( 
								 CURRENT_TIMESTAMP, 
								 'Pesquisa Fundaзгo CEEE',
								 '".$ar_reg['usuario']."@eletroceee.com.br', 
								 '',
								 '',
								 '".$ds_assunto."', 
								 '".str_replace("'", "`", str_replace("{nome}",$ar_reg['nome'],$ds_msg))."',
								 43
							   );
						";	
			@pg_query($db, $qr_email);
		}
	}
?>