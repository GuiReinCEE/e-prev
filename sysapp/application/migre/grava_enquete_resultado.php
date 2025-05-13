<?php
//	include_once('inc/sessao_enquetes.php');
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/funcoes.php');
// ------------------------------------------------
	$ip=$_SERVER['REMOTE_ADDR'];	// gravar o ip pelo DAP Atendimento

	$sql = "select controle_respostas, ultimo_respondente,tipo_layout from projetos.enquetes where cd_enquete =  ".$_REQUEST['cd_enquete'];
	$rs = pg_query($db, $sql);
	$reg = pg_fetch_array($rs);

	if ($reg['controle_respostas'] == 'U') 
	{
		$chave = $_SESSION['Z'];
	} 
	elseif ($reg['controle_respostas'] == 'I') 
	{
		$chave = $ip;
	} 
	elseif ($reg['controle_respostas'] == 'P') 
	{
		$chave = $EMP.$RE.$SEQ;
	} 
	elseif ($reg['controle_respostas'] == 'R') 
	{
		session_start("PESQUISA_RE");
		$chave = $_SESSION['ENQ_CD_EMPRESA'].$_SESSION['ENQ_CD_REGISTRO_EMPREGADO'].$_SESSION['ENQ_SEQ_DEPENDENCIA'];
	} 
	elseif ($reg['controle_respostas'] == 'F') 
	{
		$chave = $_SESSION['ENQ_CHAVE'];
	}
	
// ------------------------------------------------------------------------	
	if ($_REQUEST['ultima_tela'] == 'S') 
	{
		if ($reg['tipo_layout'] == '0002')  
		{
			header('location: http://www.fundacaoceee.com.br');
		}
		elseif ($reg['controle_respostas'] == 'F') 
		{
			header('location: resp_enquetes_capa.php?c='.$_REQUEST['cd_enquete']);
		} 
		elseif ($reg['controle_respostas'] == 'P') 
		{
			header('location: http://www.fundacaoceee.com.br');
			//header('location: http://www.e-prev.com.br/controle_projetos/login_eleicoes_fsolidaria.php');
		}
		else 
		{
			header('location: index.php');
		}
	} 
	elseif (($_REQUEST['cd_agrupamento'] == '') and ($reg['controle_respostas'] == 'P')) 
	{
		header('location: login_eleicoes_fsolidaria.php?c='.$_REQUEST['cd_enquete']);
		exit;
	}
// ------------------------------------------------------------------------
	$sql = "delete from projetos.enquete_resultados where cd_enquete = ".$_REQUEST['cd_enquete']." and cd_agrupamento = ".$_REQUEST['cd_agrupamento']." and ip = '".$chave."' ";
	$s = (pg_query($db, $sql));
				
	while(list($key, $value) = each($_POST)) 
	{ 
		$v_str = $key;
		
		if (substr_count($v_str, "R_") > 0) 
		{
			$m = fnc_grava_questao($_REQUEST['cd_enquete'], $_REQUEST['cd_agrupamento'], $chave, $db, $v_str, $value);
		}
	} 

	if ($_REQUEST['resp_texto'] != '') 
	{
		$sql =        " insert into projetos.enquete_resultados ( ";
		$sql = $sql . "        cd_enquete , ";
		$sql = $sql . "        cd_agrupamento , ";
		$sql = $sql . "        ip, ";
		$sql = $sql . "        questao, ";
		$sql = $sql . "        descricao, ";
		$sql = $sql . "        dt_resposta )";
		$sql = $sql . " values ( ";
		$sql = $sql . "        ".$_REQUEST['cd_enquete'].", ";
		$sql = $sql . "        ".$_REQUEST['cd_agrupamento'].", ";
		$sql = $sql . "        '$chave', ";
		$sql = $sql . "        'Texto', ";
		$sql = $sql . "        '".$_REQUEST['resp_texto']."', ";
		$sql = $sql . "			current_timestamp ) ";
//	echo $sql;
		$s = (pg_query($db, $sql));
	}	
	$v_proxima = $_REQUEST['cd_agrupamento'] + 1;
// ------------------------------------------------
//	$sql = "select min(cd_agrupamento) as cd_agrupamento from projetos.enquete_agrupamentos where cd_enquete = ".$cd_enquete." and cd_agrupamento > ".$cd_agrupamento;
	if ($_REQUEST['proxima_ordem'] == '') { 
		$agrup = 0 ; 
		$_REQUEST['proxima_ordem'] = 0;
	} else {
		$sql = "select cd_agrupamento from projetos.enquete_agrupamentos where dt_exclusao is null and cd_enquete = ".$_REQUEST['cd_enquete']." and ordem = ".$_REQUEST['proxima_ordem'];
		$rs = pg_query($db, $sql);
		$reg = pg_fetch_array($rs);
		if ( $reg['cd_agrupamento'] != '') {
			$v_proxima = $reg['cd_agrupamento'];
		}
	}
// ------------------------------------------------
	header('location: resp_enquetes.php?c='.$_REQUEST['cd_enquete'].'&agrup='.$v_proxima.'&proxima_ordem='.$_REQUEST['proxima_ordem']);

//-----------------------------------------------------------------------------------------------
function fnc_grava_questao($cd_enquete, $cd_agrupamento, $ip, $db, $questao, $valor) {
	if ($valor != '') 
	{
		if (substr_count($questao, 'justificativa') != 0) 
		{
			$questao = str_replace('justificativa_', '', $questao);
			$sql =        " update 	projetos.enquete_resultados set descricao = '$valor' ";
			$sql = $sql . " where	cd_enquete = $cd_enquete and cd_agrupamento = $cd_agrupamento and ip = '$ip' and questao = '$questao' ";
		} 
		elseif (substr_count($questao, 'diss') != 0) 
		{
			$questao = str_replace('diss_', '', $questao);
			$sql =        " update 	projetos.enquete_resultados set descricao = '$valor' ";
			$sql = $sql . " where	cd_enquete = $cd_enquete and cd_agrupamento = $cd_agrupamento and ip = '$ip' and questao = '$questao' ";
		} 
		elseif (substr_count($questao, '_complemento_') != 0) 
		{
			$sql = "";
			for ($i = 0; $i <= 12; $i++)
			{
				if(substr_count($questao, '_complemento_'.$i) != 0)
				{
					$questao = str_replace('_complemento_'.$i, '', $questao);
					$sql.= " 
								UPDATE projetos.enquete_resultados 
								   SET complemento    = '$valor' 
								 WHERE cd_enquete     = $cd_enquete 
								   AND cd_agrupamento = $cd_agrupamento 
								   AND ip             = '$ip' 
								   AND questao        = '$questao'
								   AND valor          = $i;
						    ";					
				}
			}
		}		
		else 
		{	
			$sql =        " insert into projetos.enquete_resultados ( ";
			$sql = $sql . "        cd_enquete , ";
			$sql = $sql . "        cd_agrupamento , ";
			$sql = $sql . "        ip, ";
			$sql = $sql . "        questao, ";
			$sql = $sql . "        valor, ";
			$sql = $sql . "        dt_resposta )";
			$sql = $sql . " values ( ";
			$sql = $sql . "        $cd_enquete, ";
			$sql = $sql . "        $cd_agrupamento, ";
			$sql = $sql . "        '$ip', ";
			$sql = $sql . "        '$questao', ";
			$sql = $sql . "        $valor, ";
			$sql = $sql . "			current_timestamp ) ";
		}
		
		$s = (pg_query($db, $sql));
	}
	
	return $ret;
}
?>