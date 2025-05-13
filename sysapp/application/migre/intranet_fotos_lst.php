<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	require_once('inc/ajaxobject.php');
	include_once('inc/class.TemplatePower.inc.php');
	require_once('inc/nusoap.php');
   
	$tpl = new TemplatePower('tpl/tpl_intranet_fotos_lst.htm');

	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);	
	
	$qr_sql = "
				SELECT ds_caminho,
				       ds_titulo
				  FROM acs.fotos
				 WHERE cd_fotos = ".$_REQUEST['cd_fotos']."
	          ";
	$ob_resul = pg_query($db, $qr_sql);
	$ar_reg = pg_fetch_array($ob_resul);
	$ar_parametro = array('ds_dir'=>$ar_reg['ds_caminho']);

	if(trim($ar_reg['ds_caminho']) != "")
	{
		$ob_cliente_soap = new nusoap_soapclient('http://10.63.255.16:1111/server.php');
		$resultado = $ob_cliente_soap->call('listaDirCompartilhado',$ar_parametro);
		if ($ob_cliente_soap->fault)
		{
			echo "<PRE>ERRO:<BR>".$ob_cliente_soap->faultstring;
			exit;
		}
		else
		{
			$tpl->assign('ds_titulo', $ar_reg['ds_titulo']);

			$nr_fim = count($resultado);
			$nr_conta = 0;
			$nr_index = 1;
			while($nr_conta < $nr_fim)
			{
				$ds_ext = strtolower(substr($resultado[$nr_conta]['ds_file'], strrpos($resultado[$nr_conta]['ds_file'], '.')+1));
				$ar_tipo = array('gif', 'png', 'jpeg', 'jpg');
				if(in_array($ds_ext, $ar_tipo)) 
				{
					$tpl->newBlock('lst_fotos');
					$tpl->assign('nr_index',  $nr_index);
					$tpl->assign('ds_titulo', $ar_reg['ds_titulo']);
					//$tpl->assign('ds_url_a',"file://".str_replace("\\","/",$resultado[$nr_conta]['ds_dir']."\\".$resultado[$nr_conta]['ds_file']));
					$tpl->assign('ds_url',"file://".$resultado[$nr_conta]['ds_dir']."\\".$resultado[$nr_conta]['ds_file']);
					//$tpl->assign('ds_url',    "exibe_foto.php?ds_arq=".$resultado[$nr_conta]['ds_dir']."\\".$resultado[$nr_conta]['ds_file']."&ds_titulo=".$ar_reg['ds_titulo']);	
					
					$nr_index++;
				}				 
				$nr_conta++;
			}
			$tpl->assignGlobal('nr_index_maximo',  $nr_index);
		}
	}
	$tpl->printToScreen();
?>