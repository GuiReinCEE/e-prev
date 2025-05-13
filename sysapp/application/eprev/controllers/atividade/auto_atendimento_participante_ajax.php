<?php
	$_APP_ID  = "e7a9e3f647dd33941430647118aaf2b7";
	
	include_once('inc/sessao_auto_atendimento.php');
	include_once('inc/conexao.php');
	
	#print_r($POST);
	
	if($_POST)
	{
		if($_POST['ds_funcao'] == "set_grau_de_instrucao")
		{
			set_grau_de_instrucao($_POST['cd_grau_de_instrucao']);
		}	
	
		if($_POST['ds_funcao'] == "getEndereco")
		{
			getEndereco($_POST['nr_cep']);
		}

		if($_POST['ds_funcao'] == "getEnderecoFCPREV")
		{
			getEnderecoFCPREV($_POST['nr_cep'], $_POST['re_cripto']);
		}
		
		if($_POST['ds_funcao'] == "setBannerAA")
		{
			$_SESSION['fl_banner_aa'] = $_POST['fl_banner_aa'];
			echo json_encode(0);
		}		
	}
	else
	{
		echo json_encode(0);
	}

	function getEndereco($nr_cep)
	{
		global $db;
		$qr_sql = "
					SELECT cd_uf,
						   SUBSTR(ds_logradouro,0,40) AS ds_logradouro,
						   SUBSTR(ds_bairro_ini,0,25) AS ds_bairro,
						   SUBSTR(ds_localidade,0,30) AS ds_localidade
					  FROM geografico.cep 
					 WHERE nr_cep = '".str_replace("-","",$nr_cep)."'
				  ";					 
		$ob_result = pg_query($db, $qr_sql);
		$ar_reg = pg_fetch_array($ob_result);
		
		echo json_encode($ar_reg);
	}

	function getEnderecoFCPREV($cep, $re_cripto)
	{
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, APP_SRV.'/srvautoatendimento/index.php/get_endereco');
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "id_app=".$_APP_ID.'&re_cripto='.$re_cripto.'&cep='.$cep);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		
		$server_output = curl_exec($ch);

		echo $server_output;

		curl_close($ch);
	}

	function set_grau_de_instrucao($cd_grau_de_instrucao)
	{
		global $db;
		
		$qr_sql = "
					UPDATE public.participantes
					   SET cd_grau_de_instrucao = ".intval($cd_grau_de_instrucao)."
					 WHERE cd_registro_empregado = ".$_SESSION['RE']."
					   AND seq_dependencia       = ".$_SESSION['SEQ']."
					   AND cd_empresa            = ".$_SESSION['EMP'].";					 
					
					INSERT INTO public.participantes_hist
					SELECT *
					  FROM public.participantes
					 WHERE cd_registro_empregado = ".$_SESSION['RE']."
					   AND seq_dependencia       = ".$_SESSION['SEQ']."
					   AND cd_empresa            = ".$_SESSION['EMP'].";					
		          ";
		pg_query($db, $qr_sql);		  
		
	}

?>