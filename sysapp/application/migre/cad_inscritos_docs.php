<?
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   include_once('inc/class.TemplatePower.inc.php');
   
   $tpl = new TemplatePower('tpl/tpl_cad_inscritos_docs.html');

   $tpl->prepare();

	// *** abas
	$abas[] = array('aba_identificacao', 'Identificaзгo', false, 'ir_aba_ident()');
	$abas[] = array('aba_contato', 'Contato', false, 'ir_aba_cont()');
	$abas[] = array('aba_anexo', 'Anexo', true, 'ir_aba_anx()');
	$abas[] = array('aba_historico', 'Histуrico', false, 'ir_aba_hist()');
	$tpl->assignGlobal( 'ABA_START', aba_start( $abas ) );
	$tpl->assignGlobal( 'ABA_END', aba_end('') );
	$tpl->assignGlobal( 'link_lista', site_url("cadastro/avaliacao_cargo") );
	// *** abas


   $tpl->assign('n', $n);
   $PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
   $tpl->assign('divsao', $D);
   $tpl->newBlock('cadastro');
   // ----------------------------------------------------- se й um novo evento, TR vem com 'I'
		if ($tr == 'U') {
			$n = 'U';
		}
		else {
			$n = 'I';
		}
		$tpl->assign('insere', $n);
	$EMP = 7;
	$PLANO = 7;
	if (isset($c))	{
		$tpl->assignGlobal('cd_registro_empregado', $c);
		$sql =   " ";
		$sql = $sql . " select 	cd_registro_empregado, ";
		$sql = $sql . "			cd_anexo, tipo_anexo, tam_arquivo, ";
		$sql = $sql . "			to_char(dt_upload, 'dd/mm/yyyy') as dt_upload, nome_arquivo, caminho ";
		$sql = $sql . "  from 	expansao.documentos_inscricao		";
		$sql = $sql . "  where 	cd_registro_empregado	= $c and cd_empresa = $EMP";
//		echo $sql;
        $rs = pg_exec($db, $sql);
        while ($reg=pg_fetch_array($rs)) {
			$tpl->newBlock('anexos');
    	    $tpl->assign('nome_arquivo', $reg['nome_arquivo']);
			$tpl->assign('cd_anexo', $reg['cd_anexo']);
			$tpl->assign('tipo_anexo', $reg['tipo_anexo']);
			$tpl->assign('dt_upload', $reg['dt_upload']);
		}
// ------------------------------------------ Pecъlio
		$tpl->assignGlobal('cd_registro_empregado', $c);
		$sql =   " ";
		$sql = $sql . " select 	cd_seq_peculio, ";
		$sql = $sql . "			nome, percentual ";
		$sql = $sql . "  from 	expansao.peculio		";
		$sql = $sql . "  where 	cd_registro_empregado	= $c and cd_empresa = $EMP and cd_plano = $PLANO";
//		echo $sql;
        $rs = pg_exec($db, $sql);
        while ($reg=pg_fetch_array($rs)) {
			$tpl->newBlock('peculio');
    	    $tpl->assign('nome', $reg['nome']);
			$tpl->assign('percentual', $reg['percentual']);
		}
// ------------------------------------------ Pecъlio
		$tpl->assignGlobal('cd_registro_empregado', $c);
		$sql =   " ";
		$sql = $sql . " select 	cd_doc, nro_via, obrigatorio, ";
		$sql = $sql . "		to_char(dt_entrega, 'dd/mm/yyyy') as dt_entrega, to_char(dt_inclusao, 'dd/mm/yyyy') as dt_inclusao ";
		$sql = $sql . "  from 	expansao.registros_documentos		";
		$sql = $sql . "  where 	cd_registro_empregado	= $c and cd_empresa = $EMP ";
//		echo $sql;
        $rs = pg_exec($db, $sql);
        while ($reg=pg_fetch_array($rs)) {
			$tpl->newBlock('docs');
			if ($reg['cd_doc'] == 1){
	    	    $tpl->assign('tipo_doc', 'Carteira de Identidade / CIC');
			}
			elseif ($reg['cd_doc'] == 225) {
	    	    $tpl->assign('tipo_doc', 'Pedido de Inscriзгo');
			}
			$tpl->assign('dt_entrega', $reg['dt_entrega']);
			$tpl->assign('dt_inclusao', $reg['dt_inclusao']);
		}
   }

//-------------------------------------------------------
   pg_close($db);
   $tpl->printToScreen();	
?>