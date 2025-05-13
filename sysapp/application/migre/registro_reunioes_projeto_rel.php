<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_registro_reunioes_projeto_rel.html');
	$tpl->prepare();
	
	#### NOME DO PROJETO ####
	$sql = " 
			SELECT p.nome
			  FROM projetos.projetos p,
			       projetos.acompanhamento_projetos ap
			 WHERE ap.cd_acomp   = ".$_REQUEST['cd_acomp']."
			   AND ap.cd_projeto = p.codigo	
		   ";
	$rs  = pg_query($db, $sql);
	$reg = pg_fetch_array($rs);
	$tpl->assign('ds_projeto', $reg['nome']);

	#### INF IMPRESSAO ####
	$tpl->newBlock('dt_impressao');
	$tpl->assign('dt_impressao', date("d/m/Y"));
	$tpl->assign('ds_usuario', $N);	
	
	
	if(trim($_REQUEST['cd_reuniao']) == "")
	{
		#### REUNIÃO INICIAL (ROTEIRO) ####
		$tpl->newBlock('roteiro');
		$sql = " 
				SELECT rpr.nr_ordem,
					   rpr.cd_reunioes_projetos_roteiro,
					   rpr.ds_reunioes_projetos_roteiro
				  FROM projetos.reunioes_projetos_roteiro rpr
				 ORDER BY rpr.nr_ordem		
			   ";
		$rs = pg_query($db, $sql);
		while ($reg = pg_fetch_array($rs)) 
		{
			$tpl->newBlock('roteiro_item');
			$tpl->assign('cd_roteiro', $reg['cd_reunioes_projetos_roteiro']);	
			$tpl->assign('ds_roteiro', $reg['nr_ordem'].") ".$reg['ds_reunioes_projetos_roteiro']);	
			$tpl->assign('ds_resposta', $reg['ds_resposta']);	
		}
	}
	
	#### REUNIÕES REALIZADAS ####
	$filtro_reuniao = "";
	if(trim($_REQUEST['cd_reuniao']) != "")
	{
		$filtro_reuniao = "AND rp.cd_reuniao = ".$_REQUEST['cd_reuniao'];
	}
	
	$sql = " 
			 SELECT rp.cd_reuniao, 
					rp.assunto, 
					rp.ds_arquivo_fisico,
					TO_CHAR(rp.dt_reuniao, 'DD/MM/YYYY') AS dt_reuniao_ed,
					uc.nome
			   FROM projetos.reunioes_projetos rp
			   LEFT JOIN projetos.reunioes_projetos_envolvidos rpe
				 ON rpe.cd_acomp   = rp.cd_acomp
				AND rpe.cd_reuniao = rp.cd_reuniao
			   LEFT JOIN projetos.usuarios_controledi uc
				 ON rpe.cd_usuario = uc.codigo
			  WHERE rp.dt_exclusao IS NULL
				AND rp.cd_acomp    = ".$_REQUEST['cd_acomp']."
				".$filtro_reuniao."
			  ORDER BY rp.dt_reuniao DESC 
		   ";
	$rs = pg_query($db, $sql);
	$cd_reuniao_atual = "";
	$lt_envolvidos = "";
	while ($reg = pg_fetch_array($rs)) 
	{
		if($cd_reuniao_atual != $reg['cd_reuniao'])
		{
			if(trim($lt_envolvidos) != "")
			{
				$tpl->newBlock('reuniao_envolvido');
				$tpl->assign('ds_envolvido', $lt_envolvidos.".");					
				$lt_envolvidos = "";
			}

			$tpl->newBlock('reuniao');
			$tpl->assign('dt_reuniao', $reg['dt_reuniao_ed']);
			$tpl->assign('ds_arquivo_fisico', $reg['ds_arquivo_fisico']);
			$tpl->assign('ds_assunto', nl2br($reg['assunto']));
			$cd_reuniao_atual = $reg['cd_reuniao'];
		}

		if(trim($reg['nome']) != "")
		{
			if(trim($lt_envolvidos) == "")
			{
				$lt_envolvidos = $reg['nome'];
			}
			else
			{
				$lt_envolvidos.= ", ".$reg['nome'];
			}
		}
	}	
	
	if(trim($lt_envolvidos) != "")
	{
		$tpl->newBlock('reuniao_envolvido');
		$tpl->assign('ds_envolvido', $lt_envolvidos,".");					
		$lt_envolvidos = "";
	}	
	$tpl->printToScreen();	
	
?>