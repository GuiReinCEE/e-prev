<?php
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_cad_inscritos_seminario.html');
//-----------------------------------------------   
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
//-----------------------------------------------
	$tpl->newBlock('cadastro');
	$tpl->assign('cor_fundo1', $v_cor_fundo1);
	$tpl->assign('cor_fundo2', $v_cor_fundo2);
	
	
	#### REDIRECIONAMENTO PARA MIGRAวรO ####
	header("Location: ".site_url("ecrm/seminario_economico/detalhe/".intval($_REQUEST['c'])));
	EXIT;
	
	
	$fl_barra = false;
	if (trim($_REQUEST['c']) != "")	
	{
		$fl_barra = true;
		$sql = "
				SELECT s.codigo, 
				       s.nome, 
					   s.cargo, 
					   s.empresa, 
					   s.endereco, 
					   s.cidade, 
					   s.uf, 
					   s.cep, 
					   s.telefone, 
					   s.telefone_ramal, 
					   s.fax, 
					   s.fax_ramal, 
					   s.telefone_ddd, 
					   s.fax_ddd, 
					   TO_CHAR(s.dt_inclusao,'DD/MM/YYYY HH24:MI') AS data_cadastro, 
					   s.email, 
					   CASE WHEN s.autoriza_mailing 
					        THEN 'S'
							ELSE 'N'
					   END AS autoriza_mailing, 
		               s.celular_ddd, 
					   s.celular, 
					   s.numero, 
					   s.complemento, 
					   s.seq_dependencia AS sequencia, 
					   s.cd_empresa AS patrocinadora, 
					   s.cd_registro_empregado AS re, 
					   s.cd_seminario_edicao AS cd_seminario ,
					   s.cd_barra,
					   s.fl_presente
		          FROM acs.seminario s
				 WHERE s.codigo = ".$_REQUEST['c']."
				   AND s.dt_exclusao IS NULL
				 ";
		
		$rs = pg_query($db, $sql);
		$reg = pg_fetch_array($rs);
		$tpl->assign('codigo', $reg['codigo']);
		$tpl->assign('nome', $reg['nome']);
		$tpl->assign('cargo', $reg['cargo']);
		$tpl->assign('empresa', $reg['empresa']);
		$tpl->assign('endereco', $reg['endereco']);
		$tpl->assign('cidade', $reg['cidade']);
		$tpl->assign('uf', $reg['uf']);
		$tpl->assign('cep', $reg['cep']);
		$tpl->assign('telefone', $reg['telefone']);
		$tpl->assign('telefone_ramal', $reg['telefone_ramal']);
		$tpl->assign('fax', $reg['fax']);
		$tpl->assign('fax_ramal', $reg['fax_ramal']);
		$tpl->assign('telefone_ddd', $reg['telefone_ddd']);
		$tpl->assign('fax_ddd', $reg['fax_ddd']);
		$tpl->assign('data_cadastro', $reg['data_cadastro']);
		$tpl->assign('hora_cadastro', $reg['hora_cadastro']);
		$tpl->assign('email', $reg['email']);
		if ($reg['autoriza_mailing'] == 'S') 
		{
			$tpl->assign('autoriza_mailing', 'checked');
		}
		$tpl->assign('celular_ddd', $reg['celular_ddd']);
		$tpl->assign('celular', $reg['celular']);
		$tpl->assign('numero', $reg['numero']);
		$tpl->assign('complemento', $reg['complemento']);
		
		$tpl->assign('patrocinadora', $reg['patrocinadora']);
		$tpl->assign('re', $reg['re']);
		$tpl->assign('seq', $reg['sequencia']);
		
		$tpl->assign('cd_barra', $reg['cd_barra']);
		$tpl->assign('fl_presente', $reg['fl_presente']);

	}
	else
	{
		$sql = "
				SELECT MAX(cd_barra) AS cd_barra
				  FROM acs.seminario_presente sp
				 WHERE 0 = (SELECT COUNT(*)
				              FROM acs.seminario s
				             WHERE s.cd_barra = sp.cd_barra
							   AND s.dt_exclusao IS NULL)
			   ";
		$rs = pg_query($db, $sql);	
		$ar_codigo = pg_fetch_array($rs);
		$tpl->assign('cd_barra', $ar_codigo['cd_barra']);
		
		$reg['cd_seminario'] = 2;
	}

	if($fl_barra)
	{
		$tpl->assign('fl_barra', 'display:none;');
	}

	#### LISTA SEMINARIO ####
	$sql = "
			SELECT cd_seminario_edicao,
				   ds_seminario_edicao 
	          FROM acs.seminario_edicao
		   ";
	$rs = pg_query($db, $sql);
	while ($seminario_reg = pg_fetch_array($rs)) 
	{
		$tpl->newBlock('cbo_seminario');
		$tpl->assign('cd_seminario', $seminario_reg['cd_seminario_edicao']);
		$tpl->assign('ds_seminario', $seminario_reg['ds_seminario_edicao']);
		$tpl->assign('fl_seminario', ($seminario_reg['cd_seminario_edicao'] == $reg['cd_seminario']? ' selected' : ''));
	}	
		
	#### LISTA ESTADOS ####
	$sql = "
			SELECT sigla_uf AS cd_uf,
				   sigla_uf AS ds_uf
			  FROM expansao.cidades 
			 GROUP BY sigla_uf 
			 ORDER BY sigla_uf
		   ";
	$rs = pg_query($db, $sql);
	while ($uf_reg = pg_fetch_array($rs)) 
	{
		$tpl->newBlock('cbo_uf');
		$tpl->assign('cd_uf', $uf_reg['cd_uf']);
		$tpl->assign('ds_uf', $uf_reg['ds_uf']);
		$tpl->assign('fl_uf', (trim($uf_reg['cd_uf']) == trim($reg['uf']) ? ' selected' : ''));
	}		
	
	if(trim($reg['uf']) != "")
	{
		#### LISTA CIDADES ####
		$sql = "
				SELECT nome_cidade AS cd_cidade,
					   nome_cidade AS ds_cidade
				  FROM expansao.cidades 
				 WHERE sigla_uf = '".trim($reg['uf'])."' 
				 ORDER BY nome_cidade
				";
		$rs = pg_query($db, $sql);
		while ($cidade_reg = pg_fetch_array($rs)) 
		{
			$tpl->newBlock('cbo_cidade');
			$tpl->assign('cd_cidade', $cidade_reg['cd_cidade']);
			$tpl->assign('ds_cidade', $cidade_reg['ds_cidade']);
			$tpl->assign('fl_cidade', (trim($cidade_reg['cd_cidade']) == trim($reg['cidade']) ? ' selected' : ''));
		}		
	}
	
	#### LISTA PATROCINADORAS ####
	$sql = "
			SELECT codigo AS cd_empresa,
				   descricao AS ds_empresa
			  FROM listas 
			 WHERE categoria = 'PATR' 
			 ORDER BY codigo
		   ";
	$rs = pg_query($db, $sql);
	while ($empresa_reg = pg_fetch_array($rs)) 
	{
		$tpl->newBlock('cbo_patrocinadora');
		$tpl->assign('cd_empresa', $empresa_reg['cd_empresa']);
		$tpl->assign('ds_empresa', $empresa_reg['ds_empresa']);
		$tpl->assign('fl_empresa', (trim($empresa_reg['cd_empresa']) == trim($reg['patrocinadora']) ? ' selected' : ''));
	}			
	

	pg_close($db);
	$tpl->printToScreen();	
	
	require_once('inc/ajaxobject.php');
?>