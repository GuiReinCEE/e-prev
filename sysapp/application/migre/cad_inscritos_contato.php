		<?
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   include_once('inc/class.TemplatePower.inc.php');
   
   $tpl = new TemplatePower('tpl/tpl_cad_inscritos_contato.html');

   $tpl->prepare();
	
	// *** abas
	$abas[] = array('aba_identificacao', 'Identificação', false, 'ir_aba_ident()');
	$abas[] = array('aba_contato', 'Contato', true, 'ir_aba_cont()');
	$abas[] = array('aba_anexo', 'Anexo', false, 'ir_aba_anx()');
	$abas[] = array('aba_historico', 'Histórico', false, 'ir_aba_hist()');
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
   // ----------------------------------------------------- se é um novo evento, TR vem com 'I'
		if ($tr == 'U') {
			$n = 'U';
		}
		else {
			$n = 'I';
		}
		$tpl->assign('insere', $n);

   if (isset($c))
   {
        $sql =   " ";
		$sql = $sql . " select 	cd_registro_empregado, ";
		$sql = $sql . "			nome, endereco, bairro, cidade, uf, sigla_pais,  ";
		$sql = $sql . "			cep, complemento_cep, ddd, telefone, ramal, ddd_cel, celular, ";
		$sql = $sql . "			ddd_fax, fax, email ";
		$sql = $sql . "  from 	expansao.inscritos		";
		$sql = $sql . "  where cd_registro_empregado	= $c ";
        $rs = pg_exec($db, $sql);
        $reg=pg_fetch_array($rs);
		$uf = $reg['uf'];
		$cd_cidade = $reg['cidade'];
		$tpl->assignGlobal('cd_registro_empregado', $reg['cd_registro_empregado']);
        $tpl->assign('nome', $reg['nome']);
		$tpl->assign('endereco', $reg['endereco']);
		$tpl->assign('bairro', $reg['bairro']);
		$tpl->assign('sigla_pais', $reg['sigla_pais']);
		$tpl->assign('cep', $reg['cep']);
		$tpl->assign('complemento_cep', $reg['complemento_cep']);
		$tpl->assign('ddd', $reg['ddd']);
		$tpl->assign('telefone', $reg['telefone']);
		$tpl->assign('ramal', $reg['ramal']);
		$tpl->assign('ddd_cel', $reg['ddd_cel']);
		$tpl->assign('celular', $reg['celular']);
		$tpl->assign('ddd_fax', $reg['ddd_fax']);
		$tpl->assign('fax', $reg['fax']);
		$tpl->assign('email', $reg['email']);
		$email = $reg['email'];
// ------------------------------------------------
   }
// --------------------------------------------------------- Combo estados
		$sql = "";
		$sql = $sql . " select sigla, nome";
		$sql = $sql . " from   expansao.estados ";
		$sql = $sql . " order by nome ";
		$rs = pg_exec($db, $sql);
 
		while ($reg=pg_fetch_array($rs)) {
			$tpl->newBlock('cbo_uf');
			$tpl->assign('cod_uf', $reg['sigla']);
			$tpl->assign('uf', $reg['nome']);
			if ($reg['sigla'] == $uf) { $tpl->assign('sel_uf', ' selected'); }
		}
// ---------------------------------------------------------- Combo cidades
		if (isset($uf)) { }
		else { $uf = 'RS'; }
		$sql = "";
		$sql = $sql . " SELECT 	cd_municipio_ibge, 	";
		$sql = $sql . "        	nome_cidade 		";
		$sql = $sql . " FROM 	expansao.cidades 	";
		$sql = $sql . " WHERE 	sigla_uf = '$uf'  order by nome_cidade ";
		$rs = pg_exec($db, $sql);
		while ($reg = pg_fetch_array($rs)) {
			$tpl->newBlock('cbo_cidade');
			$tpl->assign('cod_cidade', $reg['cd_municipio_ibge']);
			$tpl->assign('cidade', $reg['nome_cidade']);
			if ($reg['cd_municipio_ibge'] == $cd_cidade) { $tpl->assign('sel_cidade', ' selected'); }
		}
// ------------------------------------------------
		if ($email != '') {
	        $sql =   " ";
			$sql = $sql . "    SELECT cd_email, to_char(dt_envio, 'dd/mm/yyyy') as dt_envio, de, para, cc, assunto, dt_email_enviado ";
			$sql = $sql . "      FROM projetos.envia_emails ";
			$sql = $sql . "     WHERE para = '" . $email . "' ";
			$sql = $sql . "  ORDER BY cd_email DESC ";
    	    $rs = pg_exec($db, $sql);
			while ($reg=pg_fetch_array($rs)) {				
				$tpl->newBlock('email');
				$tpl->assign('num_email', $reg['cd_email']);
				$tpl->assign('dt_envio', $reg['dt_envio']);
	    	    $tpl->assign('assunto', $reg['assunto']);
				$tpl->assign('para', $reg['para']);
			}
		}

//-------------------------------------------------------
   pg_close($db);
   $tpl->printToScreen();	
?>