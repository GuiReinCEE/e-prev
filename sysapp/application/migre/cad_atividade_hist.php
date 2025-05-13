<?
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   include_once('inc/class.TemplatePower.inc.php');
   
   header( 'location:'.base_url().'index.php/atividade/atividade_historico/index/'.$n.'/'.$aa);

	if( !isset($aa) )
	{
		$aa = $D;
	}

   	if ($n == '-1') 
   	{ 
		echo "<script language='JavaScript'>;alert('Antes de consultar o histórico é necessário salvar a atividade');javascript:go(-1);</script>";		$msg='';
	}
	else 
	{
		$tpl = new TemplatePower('tpl/tpl_frm_atividade_hist.html');
		$tpl->assignInclude('mn_sup', 'menu/menu_projetos.htm');
		$tpl->prepare();
		$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
		include_once('inc/skin.php');
		$tpl->assign('usuario', $N);
		$tpl->assign('divsao', $D);
		$tpl->assign('n', $n);
		$tpl->assign("link_anexo", site_url('atividade/atividade_anexo/index/'.$n.'/'.$aa));
		$tpl->assign('aa', $aa);
		$tpl->assign( "site_url", site_url());

		$tthis->load->model('projetos/Atividade_historico_model');
		$tthis->Atividade_historico_model->lista(intval($n),$col);
		$head = array('Evento','Usuário','Data','Status','Complemento');
		$num=0;
		foreach($col as $item)
		{
			$num++;
		    $body[] = array(
							$num, 
							array($item['responsavel'],"text-align:left;"), 
							$item['data'], 
							'<span style="font-weight:bold; color:'.$item["status_cor"].';">'.$item["status"].'</span>',
							array(nl2br($item['complemento']),"text-align:justify;")
							);
		}
		$tthis->load->helper('grid');
		$grid = new grid();
		$grid->head = $head;
		$grid->body = $body;

		$tpl->assignGlobal('GRID_HISTORICO',$grid->render());

		pg_close($db);
		$tpl->printToscreen();
	}
?>