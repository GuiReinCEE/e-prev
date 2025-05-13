<?


	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_cad_familia.html');
	
	header( 'location:'.base_url().'index.php/cadastro/avaliacao_familia/cadastro/'.$c);
//-----------------------------------------------   
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
//-----------------------------------------------
	if(!gerencia_in(array('GAD')))
	{
   		header('location: acesso_restrito.php?IMG=banner_familias');
	}

	// ABAS - BEGIN
	$abas[] = array('aba_lista', 'Lista', false, 'aba_lista_click(this)');
	$abas[] = array('aba_cadastro', 'Cadastro', true, '');
	$tpl->assignGlobal( 'ABA_START', aba_start( $abas ) );
	$tpl->assignGlobal( 'ABA_END', aba_end( '') );
	$tpl->assignGlobal( 'link_lista', site_url("cadastro/avaliacao_familia") );
	// ABAS - END

	$tpl->newBlock('cadastro');
	if (isset($c))
	{
		$sql =        " select cd_familia, nome_familia, dt_inclusao, dt_alteracao ";
		$sql = $sql . " from projetos.familias_cargos where cd_familia=$c " ;
		$rs = pg_exec($db, $sql);
		$reg=pg_fetch_array($rs);
		$tpl->assign('codigo', $reg['cd_familia']);
		$tpl->assign('familia', $reg['nome_familia']);
	}
//----------------------------------------------- Escolaridades:
	$sql =        " select 	c.cd_escolaridade, c.nome_escolaridade ";
	$sql = $sql . " from   	projetos.escolaridade c ";
	$sql = $sql . " order 	by c.nome_escolaridade ";
	$rs = pg_exec($db, $sql);
	while ($reg=pg_fetch_array($rs)) 
	{
		$tpl->newBlock('escolaridade');
		$tpl->assign('cd_escolaridade', $reg['cd_escolaridade']);
		$tpl->assign('nome_escolaridade', $reg['nome_escolaridade']);
		if (isset($c)) {
			$sql2 =			" select * from   projetos.familias_escolaridades ";
			$sql2 = $sql2 . " where cd_familia = " . $c ;
			$sql2 = $sql2 . " 	and cd_escolaridade = " . $reg['cd_escolaridade'];
			$rs2 = pg_exec($db, $sql2);
			if ($reg2 = pg_fetch_array($rs2)) { 
				$tpl->assign('valor_perc_escolaridade', $reg2['grau_percentual']);
				if ($reg2['nivel'] == 'B') {
					$tpl->assign('chk_basico', 'selected'); }
				elseif ($reg2['nivel'] == 'P') {
					$tpl->assign('chk_pleno', 'selected'); }
				elseif ($reg2['nivel'] == 'E') {
					$tpl->assign('chk_excel', 'selected'); 
				}
			}
		}
	}
//-----------------------------------------------
	pg_close($db);
	$tpl->printToScreen();	
?>