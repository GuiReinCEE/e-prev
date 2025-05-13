<?php
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
   
	header( 'location:'.base_url().'index.php/ecrm/intranet/cadastro/'.$_REQUEST['div'].'/'.$_REQUEST['c'] );
	exit;
   
   // Utilizado para o upload de imagens
	if (!session_is_registered("LocalImgUpload")) 
	{
		session_register("LocalImgUpload");
	}
	$_SESSION['LocalImgUpload'] = "/upload/img/$div/";
	  
	$tpl = new TemplatePower('tpl/tpl_cad_intra_div.html');
   
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	$tpl->assign('div',$div);
	$tpl->newBlock('cadastro');
	
	$cd_item_pai = 0;
	$qt_filho = 0;
	if (intval($_REQUEST['c']) > 0)	
	{
		$tpl->assign('insere', "U"); ### UPDATE
		
		#### BUSCA PAGINA ####
		$qr_sql = " 
					SELECT id.cd_item,
                           COALESCE(id.cd_item_pai,0) AS cd_item_pai,
                           (SELECT COUNT(*)
                              FROM projetos.intra_div id1
                             WHERE id1.cd_item_pai = id.cd_item) AS qt_filho,						   
					       id.div, 
						   id.titulo, 
						   id.conteudo,  
						   TO_CHAR(id.dt_inclusao, 'DD/MM/YYYY HH24:MM') AS dt_inclusao,
						   TO_CHAR(id.dt_exclusao, 'DD/MM/YYYY HH24:MM') AS dt_exclusao,
						   id.imagem,
						   CASE WHEN LOWER(SUBSTRING(id.imagem,STRPOS(id.imagem, '.'), LENGTH(id.imagem))) IN ('.jpg', '.jpeg', '.png', '.gif', '.bmp')
								THEN 'IMG'
								ELSE 'DOC'
						   END AS tp_imagem
					  FROM projetos.intra_div id
					 WHERE id.cd_item     = ".$_REQUEST['c']."
					   AND id.div         = '".$_REQUEST['div']."'
				  ";
		$ob_resul = pg_query($db, $qr_sql);
		$reg   = pg_fetch_array($ob_resul);		
		
		$cd_item_pai = $reg['cd_item_pai'];
		$qt_filho    = $reg['qt_filho'];
		
		$tpl->assign('div',$div);
		$tpl->assign('cd_item', $reg['cd_item']);
		$tpl->assign('titulo', $reg['titulo']);
		$tpl->assign('conteudo',str_replace("'", "\'", str_replace(chr(10),'',str_replace(chr(13),'',$reg[conteudo]))));
		$v_imagem = "";
		if (trim($reg['imagem']) != '') 
		{
			if($reg['tp_imagem'] == "IMG")
			{
				$v_imagem = "<img src='https://www.e-prev.com.br//upload/".$reg['imagem']."'>";
			}
			else
			{
				$v_imagem = "<BR><a href='https://www.e-prev.com.br//upload/".$reg['imagem']."' tagert='_blank'>Ver doumento</a><BR>";
			}
		}	
		$tpl->assign('imagem', $v_imagem);		
		$tpl->assign('dt_inclusao', $reg['dt_inclusao']);
		$tpl->assign('dt_exclusao', $reg['dt_exclusao']);		 

		#### DOCUMENTOS ANEXOS ####
		$qr_sql = " 
					SELECT cd_item, 
					       texto_link, 
						   link, 
						   cd_link,
						   TO_CHAR(dt_inclusao, 'DD/MM/YYYY HH24:MI') AS data_inc,
						   nr_ordem
					  FROM projetos.links_intra_div
					 WHERE cd_item     = ".$_REQUEST['c']." 
					   AND div         = '".$_REQUEST['div']."' 
					   AND dt_exclusao IS NULL
					 ORDER BY nr_ordem DESC, 
					          dt_inclusao DESC
				  ";
		$ob_resul = pg_query($db, $qr_sql);
		$cont = 0;
		while ($ar_reg=pg_fetch_array($ob_resul)) 
		{
			$tpl->newBlock('doc_associado');
			$cont = $cont + 1;
			if (($cont % 2) <> 0) 
			{
				$tpl->assign('cor_fundo', $v_cor_fundo1);
			}
			else 
			{
				$tpl->assign('cor_fundo', $v_cor_fundo2);
			}
			$tpl->assign('nr_ordem', $ar_reg['nr_ordem']);
			$tpl->assign('texto', $ar_reg['texto_link']);
			$tpl->assign('link', 'file:'.$ar_reg['link']);
			$tpl->assign('data', $ar_reg['data_inc']);
			$tpl->assign('cd_item', $ar_reg['cd_item']);
			$tpl->assign('div', $div);
			$tpl->assign('cd_link', $ar_reg['cd_link']);
		}

	}
	else 
	{
		$tpl->assign('insere', "I"); ### INSERT
		
		$qr_sql = " 
					SELECT MAX(cd_item) AS cd_item 
					  FROM projetos.intra_div 
					 WHERE div = '".$_REQUEST['div']."' 
				  ";
		$rs = pg_query($db, $qr_sql);
		$reg=pg_fetch_array($rs);
		$tpl->assign('div',$div);
		$_REQUEST['c'] = ($reg['cd_item'] + 1);
		$tpl->assign('cd_item', $_REQUEST['c']);
	}

	if($qt_filho == 0)
	{
		#### MONTA COMBO SUPERIOR ####
		$qr_sql = " 
					SELECT id.cd_item,
						   id.titulo
					  FROM projetos.intra_div id
					 WHERE id.dt_exclusao             IS NULL 
					   AND id.div                     = '".$_REQUEST['div']."'
					   AND COALESCE(id.cd_item_pai,0) = 0
					   AND id.cd_item                 <> ".$_REQUEST['c']."
					 ORDER BY id.titulo
				  ";
		$ob_resul = pg_query($db, $qr_sql);
		while($ar_reg = pg_fetch_array($ob_resul))
		{
			$tpl->newBlock('subitem');
			$tpl->assign('cd_item_pai', $ar_reg['cd_item']);
			$tpl->assign('ds_item_pai', $ar_reg['titulo']);
			$tpl->assign('fl_item_pai', ($cd_item_pai == $ar_reg['cd_item'] ? "selected" : ""));
		}	   
	}
	
	$tpl->printToScreen();
?>