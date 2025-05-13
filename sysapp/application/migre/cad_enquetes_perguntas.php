<?php
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_cad_enquetes_perguntas.html');

	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	
	$v_cor_fundo1 = "#F2F8FC";
	$v_cor_fundo2 = "#FFFFFF";	
	$cor = 1;
	
	$tpl->newBlock('cadastro');
	$tpl->assign('cor_fundo1', $v_cor_fundo1);
	$tpl->assign('cor_fundo2', $v_cor_fundo2);
	if (intval($_REQUEST['eq']) > 0)	
	{
		$qr_sql = " 
					SELECT cd_enquete, 
					       titulo, 
						   cd_responsavel 
					  FROM projetos.enquetes  
					 WHERE cd_enquete = ".intval($_REQUEST['eq'])."
			      ";
		$ob_resul  = pg_query($db, $qr_sql);
		$reg = pg_fetch_array($ob_resul);
		$tpl->assign('titulo', $reg['titulo']);
		$tpl->assign('eq', intval($_REQUEST['eq']));
		if ($reg['cd_responsavel'] != $_SESSION['Z']) 
		{
			$tpl->assignGlobal('ro_responsavel', 'readonly');
			$tpl->assignGlobal('dis_responsavel', 'disabled');
		}
	}
	else
	{
		echo "<BR><BR><BR><center><h1 style='font-family: calibri, arial; font-size: 26pt; color:red;'>ERRO<BR><BR>Pesquisa não informada</h1></center>";
		exit;
	}
	
	//--- Lista de sites
	if (intval($_REQUEST['c']) > 0)	
	{
		#### UPDATE ####
		$qr_sql = " 
					SELECT texto, r1, r2, r3, r4, r5, r6, r7, r8, r9, r10, r11, r12, 
						   r1_complemento, r2_complemento, r3_complemento, r4_complemento, r5_complemento, r6_complemento, 
						   r7_complemento, r8_complemento, r9_complemento, r10_complemento, r11_complemento, r12_complemento, 
		                   r_diss, r_justificativa, 
						   rotulo1, rotulo2, rotulo3, rotulo4, rotulo5, rotulo6, rotulo7, rotulo8, rotulo9, rotulo10, rotulo11, rotulo12, 
						   legenda1, legenda2, legenda3, legenda4, legenda5, legenda6, legenda7, legenda8, legenda9, legenda10, legenda11, legenda12,
						   rotulo_dissertativa, rotulo_justificativa, cd_agrupamento,
						   r1_complemento_rotulo, r2_complemento_rotulo, r3_complemento_rotulo, r4_complemento_rotulo, r5_complemento_rotulo, r6_complemento_rotulo, r7_complemento_rotulo, r8_complemento_rotulo, r9_complemento_rotulo, r10_complemento_rotulo, r11_complemento_rotulo, r12_complemento_rotulo
					  FROM projetos.enquete_perguntas
					 WHERE cd_enquete  = ".intval($_REQUEST['eq'])."
					   AND cd_pergunta = ".intval($_REQUEST['c'])." 
				  ";
		$ob_resul = pg_query($db, $qr_sql);
		$reg      = pg_fetch_array($ob_resul);
		$v_agrupamento = $reg['cd_agrupamento'];
		$v_site        = $reg['cd_site'];
		$v_responsavel = $reg['cd_responsavel'];
		
		$tpl->assign('codigo', intval($_REQUEST['c']));
		$tpl->assign('questao', $reg['texto']);		
		$tpl->assign(($reg['r_diss'] == 'S' ? 'fl_dissertativa_sim' : 'fl_dissertativa_nao'), 'selected');
		$tpl->assign(($reg['r_justificativa'] == 'S' ? 'fl_justificativa_sim' : 'fl_justificativa_nao' ), 'selected');
		$tpl->assign('rotulo_dissertativa', $reg['rotulo_dissertativa']);
		$tpl->assign('rotulo_justificativa', $reg['rotulo_justificativa']);		
		
		for ($i = 1; $i <= 12; $i++)
		{
			$tpl->newBlock('resposta');
			if ($cor == 1) 
			{
				$tpl->assign('cor_fundo', $v_cor_fundo1);
				$cor = 2;
			}
			else 
			{
				$tpl->assign('cor_fundo', $v_cor_fundo2);
				$cor = 1;
			}			
			
			$tpl->assign('nr_resposta', $i);
			$tpl->assign(($reg['r'.$i] == 'S' ? 'fl_resposta_sim' : 'fl_resposta_nao'), 'selected');
			$tpl->assign('rotulo', $reg['rotulo'.$i]);
			$tpl->assign('legenda', $reg['legenda'.$i]);		
			$tpl->assign(($reg['r'.$i.'_complemento'] == 'S' ? 'fl_complemento_sim' : 'fl_complemento_nao'), 'selected');
			$tpl->assign('complemento_rotulo', $reg['r'.$i.'_complemento_rotulo']);
		}
		

	}
	else 
	{
		#### INSERT ####
		$tpl->assign('fl_dissertativa_nao', 'selected');
		$tpl->assign('fl_justificativa_nao', 'selected');		
		for ($i = 1; $i <= 12; $i++)
		{
			$tpl->newBlock('resposta');
			if ($cor == 1) 
			{
				$tpl->assign('cor_fundo', $v_cor_fundo1);
				$cor = 2;
			}
			else 
			{
				$tpl->assign('cor_fundo', $v_cor_fundo2);
				$cor = 1;
			}				
			$tpl->assign('nr_resposta', $i);
			$tpl->assign('fl_resposta_nao', 'selected');
			$tpl->assign('fl_complemento_nao', 'selected');
		}		

	}
	
	//--- Lista de agrupamentos
	$qr_sql = "
				SELECT cd_agrupamento, 
					   nome 
				  FROM projetos.enquete_agrupamentos 
				 WHERE cd_enquete  = ".intval($_REQUEST['eq'])."
				   AND dt_exclusao IS NULL 
				 ORDER BY nome
		      ";
	$ob_resul = pg_query($db, $qr_sql);
	while ($reg = pg_fetch_array($ob_resul)) 
	{
		$tpl->newBlock('agrupamento');
		$tpl->assign('cd_agrupamento', $reg['cd_agrupamento']);
		$tpl->assign('nome_agrupamento', $reg['nome']);
		$tpl->assign('chk_agrupamento', ($reg['cd_agrupamento'] == $v_agrupamento ? ' selected' : ''));
	}

	pg_close($db);
	$tpl->printToScreen();	
?>