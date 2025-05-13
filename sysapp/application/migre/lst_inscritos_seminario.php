<?php
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_lst_inscritos_seminario.html');
	$tpl->prepare();
// -------------------------------------------------------------------   
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	$tpl->assign('cac',$cac);
	$tpl->assign('scep',$cep);
// -------------------------------------------------------------------	
	$tpl->assign('publico', 'Inscritos até o momento');
	
	#### REDIRECIONAMENTO PARA MIGRAÇÃO ####
	header("Location: ".site_url("ecrm/seminario_economico"));
	EXIT;
	
	
	#### COMBO EMAIL ####
	if(trim($_REQUEST['fl_seminario_email']) != "")
	{
		if($_REQUEST['fl_seminario_email'] == 'S')
		{
			$tpl->assign('fl_seminario_email_sim', 'selected');
		}
		
		if($_REQUEST['fl_seminario_email'] == 'N')
		{
			$tpl->assign('fl_seminario_email_nao', 'selected');
		}		
	}
			
	#### COMBO PRESENTE ####
	if(trim($_REQUEST['fl_seminario_presente']) != "")
	{
		if($_REQUEST['fl_seminario_presente'] == 'S')
		{
			$tpl->assign('fl_seminario_presente_sim', 'selected');
		}
		
		if($_REQUEST['fl_seminario_presente'] == 'N')
		{
			$tpl->assign('fl_seminario_presente_nao', 'selected');
		}		
	}	
	
	if(trim($_REQUEST['cd_seminario']) == "")
	{
		$sql = "
				SELECT MAX(cd_seminario_edicao) AS cd_seminario_edicao
				  FROM acs.seminario_edicao
			   ";
		$rs = pg_query($db, $sql);
		$reg = pg_fetch_array($rs);
		$_REQUEST['cd_seminario'] = $reg['cd_seminario_edicao'];		
	}
	
	#### COMBO SEMINARIO ####
	$sql = "SELECT cd_seminario_edicao,
				   ds_seminario_edicao 
	          FROM acs.seminario_edicao";
	$rs = pg_query($db, $sql);
		$tpl->newBlock('cd_seminario');
		$tpl->assign('cd_seminario', -1);
		$tpl->assign('ds_seminario', "Selecione");	
	while ($reg = pg_fetch_array($rs)) 
	{
		$tpl->newBlock('cd_seminario');
		$tpl->assign('cd_seminario', $reg['cd_seminario_edicao']);
		$tpl->assign('ds_seminario', $reg['ds_seminario_edicao']);
		$tpl->assign('fl_seminario', ($reg['cd_seminario_edicao'] == $_REQUEST['cd_seminario']? ' selected' : ''));
	}	
	
	
// -------------------------------------------------------------------
	$sql = " 
			SELECT s.codigo, 
			       MD5(s.codigo::TEXT) AS cd_inscricao,
			       funcoes.remove_acento(s.nome) AS nome, 
			       s.cargo, 
			       funcoes.remove_acento(s.empresa) AS empresa, 
			       s.dt_inclusao AS data_cadastro, 
			       TO_CHAR(s.dt_inclusao,'DD/MM/YYYY HH24:MI') AS dt_cadastro, 
			       s.fl_presente,
				   s.email,
				   se.dt_ano AS nr_ano
			 FROM acs.seminario s
			 JOIN acs.seminario_edicao se
			   ON se.cd_seminario_edicao = s.cd_seminario_edicao
			WHERE s.dt_exclusao IS NULL
		   ";
	
	if($_REQUEST['cd_seminario'] > 0)
	{
		$sql.= " AND s.cd_seminario_edicao = ".$_REQUEST['cd_seminario'];
	}
	
	if(trim($_REQUEST['fl_seminario_email']) != "")
	{
		if($_REQUEST['fl_seminario_email'] == 'S')
		{
			$sql.= " AND s.email LIKE '%@%'";
		}
		
		if($_REQUEST['fl_seminario_email'] == 'N')
		{
			$sql.= " AND COALESCE(s.email,'') NOT LIKE '%@%'";
		}		
	}

	if(trim($_REQUEST['fl_seminario_presente']) != "")
	{
		$sql.= " AND s.fl_presente = '".$_REQUEST['fl_seminario_presente']."'";
	}	

	$sql . " ORDER BY s.nome ";
	$rs = pg_query($db, $sql);
	$nr_conta = 0;
	$qt_presente = 0;
	$qt_presente_email = 0;
	while ($reg = pg_fetch_array($rs)) 
	{
		$tpl->newBlock('convidado');
		$tpl->assign('chkconf', $chkconf);
		$tpl->assign('cac',$cac);
		$tpl->assign('scep',$cep);
		$tpl->assign('cd_codigo', $reg['codigo']);
		$tpl->assign('cd_inscricao', $reg['cd_inscricao']);
		$tpl->assign('data_cadastro', $reg['dt_cadastro'].' '.$reg['hora_cadastro']);
		$tpl->assign('nome', ucwords(strtolower($reg['nome'])));
		$tpl->assign('empresa', ucwords(strtolower($reg['empresa'])));
		$tpl->assign('cargo_funcao', $reg['cargo']);
		$tpl->assign('dt_presente', $reg['fl_presente']);
		$tpl->assign('nr_ano', $reg['nr_ano']);
		
		$fl_certificado       = "display:none;";
		$fl_certificado_email = "display:none;";
		if(trim($reg['fl_presente']) == "S")
		{
			$fl_certificado = "";
			$qt_presente++;
			if(trim($reg['email']) != "")
			{
				$fl_certificado_email = "";
				$qt_presente_email++;
			}
		}
		
		$tpl->assign('fl_certificado', $fl_certificado);
		$tpl->assign('fl_certificado_email', $fl_certificado_email);
		
		$nr_conta++;
	}
//---------------------------------------------------------------------------------
	$tpl->newBlock('total');
	$tpl->assign('total', $nr_conta);
	$tpl->assign('desc_total', 'Inscritos');
	$tpl->newBlock('total');
	$tpl->assign('total', $qt_presente);
	$tpl->assign('desc_total', 'Presentes');	
	$tpl->newBlock('total');
	$tpl->assign('total', $qt_presente_email);
	$tpl->assign('desc_total', 'Presentes com email');	
	
	//$tpl->assign('desc_total', 'Inscritos e '.$confirmados.' confirmados.');
	
//---------------------------------------------------------------------------------
	$tpl->printToScreen();
	pg_close($db);      
?>
