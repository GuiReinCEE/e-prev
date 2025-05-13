<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');

	$tpl = new TemplatePower('tpl/tpl_lst_respostas.html');
	$tpl->prepare();
	$tpl->assign('n', $n);

   	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	
	#### BUSCA AGRUPAMENTOS ####
	$qr_sql = "
				SELECT cd_responsavel
				  FROM projetos.enquetes
                 WHERE cd_enquete  = ".$_REQUEST['cd_enquete']."
	          ";
	$ob_resul = pg_query($db, $qr_sql);	
	$ar_reg   = pg_fetch_array($ob_resul);	
	$fl_editar = true;
	if (($_SESSION['CODU'] != $ar_reg['cd_responsavel']) AND ($_SESSION['CODU'] != 170))
	{
   		$fl_editar = false;
	}  	

	$tpl->assign('cd_enquete', $_REQUEST['cd_enquete']);
	$tpl->assign('tp_resposta_'.$_REQUEST['tp_resposta'], "selected");
	
	
	
	#### BUSCA AGRUPAMENTOS ####
	$qr_sql = "
				SELECT cd_enquete,
				       cd_agrupamento, 
				       nome
				  FROM projetos.enquete_agrupamentos 
                 WHERE cd_enquete  = ".$_REQUEST['cd_enquete']."
                   AND dt_exclusao IS NULL 
                 ORDER BY ordem, 
				          nome
	          ";
	$ob_agrupa = pg_query($db, $qr_sql);
	while ($ar_agrupa = pg_fetch_array($ob_agrupa)) 
	{
		#### BUSCA PERGUNTAS ####
		$qr_sql = "
					SELECT p.cd_enquete,
					       p.cd_agrupamento,
						   p.cd_pergunta, 
						   p.texto, 
						   p.r_diss 
					  FROM projetos.enquete_perguntas p
					 WHERE p.cd_enquete     = ".$ar_agrupa['cd_enquete']." 
					   AND p.cd_agrupamento = ".$ar_agrupa['cd_agrupamento']." 
					   AND p.dt_exclusao    IS NULL 
					 ORDER BY cd_pergunta		
		          ";
		$ob_pergunta = pg_query($db, $qr_sql);
		while ($ar_pergunta = pg_fetch_array($ob_pergunta)) 
		{
			$tpl->newBlock('pergunta');
			$tpl->assign('agrupamento', $ar_agrupa['nome']);
			$tpl->assign('pergunta', trim($ar_pergunta['texto']));
			
			
			#### BUSCA RESPOSTAS ####
			$qr_sql = "
						SELECT MD5(CAST(r.cd_enquete AS TEXT) || CAST(r.cd_agrupamento AS TEXT) || CAST(r.questao AS TEXT) || CAST(r.ip AS TEXT)) AS cd_resposta,
						       r.ip, 
				               r.questao, 
				               r.valor, 
				               r.descricao
						  FROM projetos.enquete_resultados r
						 WHERE r.cd_enquete     = ".$ar_pergunta['cd_enquete']." 	
						   AND r.cd_agrupamento = ".$ar_pergunta['cd_agrupamento']." 	
						   AND (r.questao        = 'R_".$ar_pergunta['cd_pergunta']."' OR r.questao = 'Texto')
					  ";
			
			if($_REQUEST['tp_resposta'] == "S")
			{
				$qr_sql.= " AND TRIM(COALESCE(r.descricao,'')) <> ''";
			}

			if($_REQUEST['tp_resposta'] == "N")
			{
				$qr_sql.= " AND TRIM(COALESCE(r.descricao,'')) = ''";
			}
			
			$ob_resposta = pg_query($db, $qr_sql);
			while ($ar_resposta = pg_fetch_array($ob_resposta)) 
			{
				$tpl->newBlock('resposta');
				$tpl->assign('ip', $ar_resposta['ip']);
				$tpl->assign('questao', $ar_resposta['questao']);
				$tpl->assign('valor', $ar_resposta['valor']);
				$tpl->assign('descricao', trim($ar_resposta['descricao']));
				$tpl->assign('cd_resposta', $ar_resposta['cd_resposta']);
				
				if($fl_editar)
				{
					$tpl->newBlock('editar_resposta');
					$tpl->assign('cd_resposta', $ar_resposta['cd_resposta']);
				}
			}			
		}
	}
	
	
	/*
	$sql = " 
			SELECT cd_agrupamento, 
			       ip, 
				   questao, 
				   valor, 
				   descricao, 
				   dt_resposta, 
	               TO_CHAR(dt_resposta, 'DD/MM/YYYY HH:MI') AS dt_resp 
	          FROM projetos.enquete_resultados 
	         WHERE cd_enquete = ".$cd_enquete."
		   ";
	if ($chk_diss == 'S') 
	{
		$sql.= " 
			   AND descricao IS NOT NULL 
			 ORDER BY cd_agrupamento, 
			          questao, 
					  descricao, 
					  dt_resposta DESC 
			   ";
	} 
	else 
	{
		$sql.= " 
		     ORDER BY cd_agrupamento, 
			          questao, 
					  valor DESC, 
					  dt_resposta DESC 
			   ";
	}
//	echo $sql;
	$rs = pg_query($db, $sql);
	$cont = 0;
	$tpl->assign('cd_enquete', $cd_enquete);
	$tpl->assign('chk_diss', '');
	if ($chk_diss == 'S') 
	{
		$tpl->assign('chk_diss_checked', 'checked');		
	} 
	else 
	{
		$tpl->assign('chk_diss_checked', '');		
	}

	while ($reg = pg_fetch_array($rs)) 
	{
		$tpl->newBlock('resposta');
		$v_desc = $reg['descricao'];		
		if ($reg['questao'] == 'Texto') 
		{
			$v_qn = 0;
		} 
		else 
		{
			$v_qn = str_replace("R_", "", $reg['questao']);
		}
		$v_nquestao = $cd_enquete.$reg['cd_agrupamento'].$reg['questao'].$reg['ip'];
		
		if ($reg['cd_agrupamento'] != $agrup_ant) 
		{
			$cont = 0;
			$sql2 = "
						SELECT nome 
						  FROM projetos.enquete_agrupamentos
						 WHERE cd_enquete     = ".$cd_enquete." 
						   AND cd_agrupamento = ".$reg['cd_agrupamento']."
				    ";		
			$rs2 = pg_query($db, $sql2);
			if ($reg2=pg_fetch_array($rs2)) 
			{
				$tpl->assign('cor_fundo', '#BFA260');
				$tpl->assign('desc_agrupamento', $reg2['nome']);
				$tpl->newBlock('resposta');
			}
		}
		$agrup_ant = $reg['cd_agrupamento'];
		
		if ($reg['questao'] != $questao_ant) 
		{
			$cont = 0;	
			$cd_questao = str_replace('R_', '', $reg['questao']);
			
			if(intval($cd_questao) > 0)
			{
				$sql2 = " 
						SELECT texto, 
						       r_diss 
						  FROM projetos.enquete_perguntas 
						 WHERE cd_enquete  = ".$cd_enquete." 
						   AND cd_pergunta = ".$cd_questao."
						";		
				$rs2 = pg_query($db, $sql2);
				if ($reg2=pg_fetch_array($rs2)) 
				{
					$tpl->assign('cor_fundo', '#DCDCCC');
					$tpl->assign('questao', $reg2['texto']);
					$v_r_diss = $reg2['r_diss'];
					$tpl->newBlock('resposta');
				}
			}
		}
		$questao_ant = $reg['questao'];
// ------------------------------------------------
		if ($fundo == '1') 
		{
			$tpl->assign('cor_fundo', $v_cor_fundo1);
			$fundo = 2;
		}
		else 
		{
			$tpl->assign('cor_fundo', $v_cor_fundo2);
			$fundo = 1;
		}
		$cont = $cont + 1;
// ------------------------------------------------
		$tpl->assign('ip', $cont);
		$tpl->assign('nquestao', $v_nquestao);
		if ($v_r_diss == 'S') 
		{
			$tpl->assign("valor", "");
			$tpl->assign('descricao', $v_desc."");
		} 
		else 
		{
			$tpl->assign("valor", $reg['valor']);
			$tpl->assign('descricao', $v_desc."");
		}
	}
// ------------------------------------------------
	pg_close($db);
	*/
	$tpl->printToScreen();	
?>