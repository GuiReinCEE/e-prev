<?php
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   include_once('inc/class.TemplatePower.inc.php');

	header( 'location:'.base_url().'index.php/ecrm/operacional_enquete' );

   $tpl = new TemplatePower('tpl/tpl_lst_enquetes.html');
   $tpl->prepare();
   
   // $tpl->assign('n', $n);
   
   	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
    if(isset($_REQUEST["msg"]))
    {
        $msg = $_REQUEST["msg"];
    }
    else
    {
        $msg = "";
    }
    if ($msg != '')
    {
		$tpl->newBlock('msg');
		$tpl->assign('msg', $msg);
		$msg = '';
	}
	$sql =        " select	e.cd_enquete, trim(e.titulo) as titulo, e.cd_site, u.guerra, e.cd_responsavel, ";
	$sql = $sql . "        	to_char(e.dt_inicio, 'DD/MM/YYYY HH24:MI') as dt_inicio, ";
	$sql = $sql . "        	to_char(e.dt_fim, 'DD/MM/YYYY HH24:MI') as dt_fim ";
	$sql = $sql . " from   	projetos.enquetes e, projetos.usuarios_controledi u ";
	$sql = $sql . " where  	e.cd_responsavel = u.codigo ";
	$sql = $sql . " and  	e.dt_exclusao is null ";
	$sql = $sql . " order 	by cd_enquete DESC";

	$rs=pg_query($db, $sql);
	$tpl->assign('qt_registro',pg_num_rows($rs));
	$cont = 0;
	while ($reg=pg_fetch_array($rs)) {
		$tpl->newBlock('enquete');
		if(isset($_REQUEST["fundo"]))
        {
            $fundo = $_REQUEST["fundo"];
        }
        else
        {
            $fundo = "";
        }
        if ($fundo == '1') {
			$tpl->assign('cor_fundo', $v_cor_fundo1);
			$fundo = 2;
		}
		else {
			$tpl->assign('cor_fundo', $v_cor_fundo2);
			$fundo = 1;
		}
		$tpl->assign('cd_enquete', $reg['cd_enquete']);
		$tpl->assign('titulo', $reg['titulo']);		
		$tpl->assign('dt_inicio', $reg['dt_inicio']);
		$tpl->assign('dt_fim', $reg['dt_fim']);
		$tpl->assign('responsavel', $reg['guerra']);
		if ($reg['cd_responsavel'] == $Z) {
			$tpl->assign('duplicar', '&raquo;&nbsp;Duplicar');
		}			
	}
	pg_close($db);
	$tpl->printToScreen();	
?>