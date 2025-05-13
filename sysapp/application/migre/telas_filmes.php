<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
   
	echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL=video.php?c='.$_REQUEST['c'].'">';
	exit;
   
	require_once('inc/videoPlayer.class.php');
	if (isset($_REQUEST['c']))	
	{
		$qr_sql = " 
					SELECT arquivo, 
					       diretorio 
				      FROM acs.videos 
					 WHERE cd_video = ".$_REQUEST['c'];
		$ob_resul = pg_query($db, $qr_sql);
		$ar_reg   = pg_fetch_array($ob_resul);
		$ob_video_player = new VideoPlayer($ar_reg['diretorio'].$ar_reg['arquivo'], '100%', '100%', 'true');
		
		if (!$ob_video_player) 
		{
			die ($ob_video_player->getLastError());
		}
	  
		if(!$ob_player = $ob_video_player->player()) 
		{
			die ($ob_video_player->getLastError());
		} 
		else 
		{
			echo $ob_player;
		}
	}
	/*
	20/12/2007
	$tpl = new TemplatePower('tpl/tpl_pagina_filmes.html');
   
	$tpl->prepare();
	$tpl->assign('n', $n);
	$tpl->newBlock('cadastro');	
	if (isset($c))	{
		$sql =        " select arquivo, diretorio, titulo, to_char(dt_evento, 'DD/MM/YYYY') as dt_evento, local, to_char(dt_atualizacao, 'DD/MM/YYYY') as dt_atualizacao ";
		$sql = $sql . " from   acs.videos ";
		$sql = $sql . " where  cd_video = $c ";
		$rs = pg_exec($db, $sql);
		$reg=pg_fetch_array($rs);
		$arquivo = $reg['diretorio'] . $reg['arquivo'];		
		$tpl->assign('arquivo', $arquivo);
		$tpl->assign('cd_tela', $reg['cd_tela']);
		$tpl->assign('titulo', $reg['titulo']);
		$tpl->assign('evento', $reg['titulo']);
		$tpl->assign('local', $reg['local']);
		$tpl->assign('data', $reg['dt_evento']);
	}
//	  echo 'ponto 1';
// ----------------------------------------------------------
	pg_close($db);
	$tpl->printToScreen();	
	*/
?>