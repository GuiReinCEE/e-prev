<?php
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	
	header( 'location:'.base_url().'index.php/ecrm/intranet/pagina/'.$_REQUEST['div'] );
	exit;
   
	$tpl = new TemplatePower('tpl/tpl_intranet_div.htm');
   
	$tpl->prepare();
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);	
	$tpl->assign('n', $n);	
	$tpl->assign('div', $_REQUEST['div']);
	
	if (($_REQUEST['c'] == 'p') or (intval($_REQUEST['c']) == 0))
	{
		$qr_sql =  " 
					SELECT cd_item
					  FROM projetos.intra_div 
					 WHERE dt_exclusao             IS NULL   
					   AND div                     = '".$_REQUEST['div']."'
					   AND COALESCE(cd_item_pai,0) = 0
					 ORDER BY titulo
					 LIMIT 1 		
					";
		$ob_resul = pg_query($db, $qr_sql);
		$ar_reg   = pg_fetch_array($ob_resul);
		$_REQUEST['c'] = $ar_reg['cd_item'];
	}
	
	
	if (intval($_REQUEST['c']) > 0)	
	{
		#### BUSCA PAGINA ####
		$qr_sql = " 
					SELECT id.cd_item,
                           COALESCE(id.cd_item_pai,0) AS cd_item_pai,
					       id.div, 
						   id.titulo, 
						   id.conteudo,  
						   TO_CHAR(id.dt_inclusao, 'DD/MM/YYYY HH24:MM') AS dt_inclusao,
						   id.imagem,
						   CASE WHEN LOWER(SUBSTRING(id.imagem,STRPOS(id.imagem, '.'), LENGTH(id.imagem))) IN ('.jpg', '.jpeg', '.png', '.gif', '.bmp')
								THEN 'IMG'
								ELSE 'DOC'
						   END AS tp_imagem
					  FROM projetos.intra_div id
					 WHERE id.cd_item     = ".$_REQUEST['c']."
					   AND id.dt_exclusao IS NULL 
					   AND id.div         = '".$_REQUEST['div']."'
				  ";
		$ob_resul = pg_query($db, $qr_sql);
		$ar_reg   = pg_fetch_array($ob_resul);
		$tpl->newBlock('titulo_pagina');
		$tpl->assign('titulo', $ar_reg['titulo']);
		
		$tpl->newBlock('cadastro');
		$tpl->assign('cd_item', $_REQUEST['c']);		
		$tpl->assign('conteudo',  $ar_reg['conteudo']);
		
				
		if ($ar_reg['imagem'] != '') 
		{
			if($ar_reg['tp_imagem'] == "IMG")
			{
				$v_imagem = "<BR><img src='https://www.e-prev.com.br/upload/".$ar_reg['imagem']."'><BR>";
			}
			else
			{
				$v_imagem = "<BR><a href='https://www.e-prev.com.br/upload/".$ar_reg['imagem']."' tagert='_blank'>Ver documento</a><BR>";
			}
			$tpl->assign('imagem', $v_imagem);
		}
		$tpl->assign('dt_hora_inclusao', $ar_reg['dt_inclusao']);
		
		
		#### MONTA PAGINAS INTERNAS ####
		$qr_sql = " 
					SELECT cd_item, 
						   titulo 
					  FROM projetos.intra_div 
					 WHERE dt_exclusao             IS NULL   
					   AND div                     = '".$_REQUEST['div']."'
					   AND COALESCE(cd_item_pai,0) = ".$_REQUEST['c']."
					 ORDER BY titulo 
				  ";
		$ob_resul = pg_query($db, $qr_sql);
		while ($ar_interna = pg_fetch_array($ob_resul))
		{
			$tpl->newBlock('titulo_pagina_interna');
			$tpl->assign('titulo', $ar_interna['titulo']);
			$tpl->assign('div', $_REQUEST['div']);
			$tpl->assign('cd_item', $ar_interna['cd_item']);
		}		
		
		#### DOCUMENTOS ANEXADOS ####
		$qr_sql = " 
					SELECT lid.cd_item, 
					       lid.texto_link, 
						   lid.link, 
					       TO_CHAR(lid.dt_inclusao, 'DD/MM/YYYY HH24:MI') AS dt_inclusao
					  FROM projetos.links_intra_div lid
					 WHERE lid.dt_exclusao IS NULL   
					   AND lid.div         = '".$_REQUEST['div']."'
					   AND lid.cd_item     = ".$_REQUEST['c']."
					 ORDER BY lid.nr_ordem DESC, 
					          lid.dt_inclusao DESC 
				  ";
		$ob_resul = pg_query($db, $qr_sql);
		
		$tpl->newBlock('documentos');
		
		while ($ar_reg = pg_fetch_array($ob_resul))
		{
			$tpl->newBlock('doc_associado');
			$cont = $cont + 1;
			$background = "background:#F2F8FC";
			if (($cont % 2) <> 0) 
			{
				$tpl->assign('background', $background);
			}
			else 
			{
				$tpl->assign('background', '');
			}
			$tpl->assign('texto', $ar_reg['texto_link']);
			$tpl->assign('link', 'file:'.$ar_reg['link']);
			$tpl->assign('data', $ar_reg['dt_inclusao']);
		}			
	}
	
	#### TITULO DO MENU ####
	$tpl->newBlock('tit_link');
	$tpl->assign('div', $_REQUEST['div']);	
	
	#### MONTA MENU ####
	$qr_sql = " 
				SELECT cd_item, 
					   titulo 
				  FROM projetos.intra_div 
				 WHERE dt_exclusao             IS NULL   
				   AND div                     = '".$_REQUEST['div']."'
				   AND COALESCE(cd_item_pai,0) = 0
				 ORDER BY titulo 
			  ";

	$rs = pg_query($db, $qr_sql);
	while ($ar_reg = pg_fetch_array($rs))
	{
		$tpl->newBlock('item_menu');
		$tpl->assign('titulo', $ar_reg['titulo']);
		$tpl->assign('div', $_REQUEST['div']);
		$tpl->assign('cd_item', $ar_reg['cd_item']);
		
		
		#### MONTA SUBMENU ####
		$qr_sql = " 
					SELECT cd_item, 
						   titulo 
					  FROM projetos.intra_div 
					 WHERE dt_exclusao             IS NULL   
					   AND div                     = '".$_REQUEST['div']."'
					   AND COALESCE(cd_item_pai,0) = ".$ar_reg['cd_item']."
					 ORDER BY titulo 
				  ";

		$ob_resul_submenu = pg_query($db, $qr_sql);
		while ($ar_submenu = pg_fetch_array($ob_resul_submenu))
		{
			$tpl->newBlock('item_submenu');
			$tpl->assign('titulo', $ar_submenu['titulo']);
			$tpl->assign('div', $_REQUEST['div']);
			$tpl->assign('cd_item', $ar_submenu['cd_item']);
		}			
	}

	$tpl->printToScreen();
?>