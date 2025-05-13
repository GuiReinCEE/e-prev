<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
   
	$tpl = new TemplatePower('tpl/tpl_cad_inscritos.html');

	$tpl->prepare();
	
	// *** abas
	$abas[] = array('aba_identificacao', 'Identificação', true, 'ir_aba_ident()');
	$abas[] = array('aba_contato', 'Contato', false, 'ir_aba_cont()');
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
//   $tpl->newBlock('cadastro');
   // ----------------------------------------------------- se é um novo evento, TR vem com 'I'
	if ($tr == 'U') {
		$n = 'U';
	}
	else {
		$n = 'I';
	}
	if ($emp == '') {
		$emp = 7;
	}
	if ($seq == '') {
		$seq = 0;
	}
	$tpl->assign('insere', $n);
	$sql = " 
			SELECT cd_registro_empregado, 
			       cd_empresa, 
				   cd_sequencia as seq_dependencia,  
				   nome, 
				   cpf, 
				   rg, 
				   crea, 
				   emissor, 
				   to_char(dt_emissao, 'dd/mm/yyyy') as dt_emissao, 
				   to_char(dt_adesao_instituidor, 'dd/mm/yyyy') as dt_adesao_instituidor, 
				   to_char(dt_alteracao, 'dd/mm/yyyy') as dt_alteracao, 
				   cd_instituicao, 
				   cd_agencia, 
				   conta_bco, 
				   sexo,  
				   to_char(dt_nascimento, 'dd/mm/yyyy') as dt_nascimento, 
				   cd_estado_civil, 
				   cd_grau_instrucao, 
				   cd_registro_patroc, 
				   seq_registro_patroc, 
				   categoria,  
				   matricula_titular, 
				   nome_pai, 
				   nome_mae, 
				   ip_inscricao, 
				   usuario_alteracao, 
				   opt_irpf,
				   to_char(dt_documentacao_confirmada, 'DD/MM/YYYY') as dt_documentacao_confirmada
			  FROM expansao.inscritos		
			 WHERE cd_registro_empregado = ".$c." 
			   AND cd_empresa            = ".$emp." 
			   AND cd_sequencia          = ".$seq;
    $rs = pg_exec($db, $sql);
    $reg=pg_fetch_array($rs);
	
	$cd_instituicao = $reg['cd_instituicao'];
	$cd_agencia = $reg['cd_agencia'];
	$cd_grau_instrucao = $reg['cd_grau_instrucao'];
	$cd_estado_civil = $reg['cd_estado_civil'];
	$categoria = $reg['categoria'];
	$tpl->assign('cd_empresa', $reg['cd_empresa']);
	$tpl->assignGlobal('cd_registro_empregado', $reg['cd_registro_empregado']);
	$tpl->assign('seq_dependencia', $reg['seq_dependencia']);
    $tpl->assign('nome', $reg['nome']);
	$tpl->assign('cpf', $reg['cpf']);
	$tpl->assign('rg', $reg['rg']);
	$tpl->assign('emissor', $reg['emissor']);
	$tpl->assign('dt_emissao', $reg['dt_emissao']);
	$tpl->assign('crea', $reg['crea']);
	$tpl->assign('conta', $reg['conta_bco']);
	$tpl->assign('sexo', $reg['sexo']);
	$tpl->assign('dt_nascimento', $reg['dt_nascimento']);
	$tpl->assign('matricula_titular', $reg['matricula_titular']);
	$tpl->assign('nome_pai', $reg['nome_pai']);
	$tpl->assign('nome_mae', $reg['nome_mae']);
	$tpl->assign('ip_inscricao', $reg['ip_inscricao']);
	$tpl->assign('dt_adesao_instituidor', $reg['dt_adesao_instituidor']);
	$tpl->assign('dt_negacao', $reg['dt_alteracao']);
	$tpl->assign('dt_senge', $reg['dt_senge_confirmado']);
	$tpl->assign('matricula', $reg['cd_registro_patroc']);

	$tpl->assign('div_alerta', 'visibility:hidden;');
	if(trim($reg['dt_documentacao_confirmada']) != "")
	{
		$tpl->assign('div_alerta', '');
		$tpl->assign('div_alerta_click', "document.location.href='login_part.php?emp=".$reg['cd_empresa']."&re=".$reg['cd_registro_empregado']."&seq=".$reg['seq_dependencia']."'");
	}
	
	$opt_irpf = $reg['opt_irpf'];
	
	if ($opt_irpf == 2) {
   		$tpl->assign('chk_nao_optou', 'checked');
	}
	elseif ($opt_irpf == 1) {
		$tpl->assign('chk_optou', 'checked');
	}
	if ($reg['cd_registro_patroc'] != '') {
		$tpl->assign('autorizacao', 'btn_ingr_aprovado');
	}
	elseif ($reg['usuario_alteracao'] != '') {
		$tpl->assign('som', 'sons/space.wav');
		$tpl->assign('img_alerta', '<img src="img/blink2.gif" style="background:black;" />');
//		$tpl->assign('img_fundo', 'img/br_bar.gif');
		$tpl->assign('autorizacao', 'btn_nao_autoriz');
	}
	else {
		$tpl->assign('autorizacao', 'btn_sem_manif');
	}
	$tpl->assign('seq_senge', $reg['seq_registro_patroc']);
	$tpl->assign('resp_senge', $reg['usuario_alteracao']);
// --------------------------------------------------------- Combo bancos
	$sql = "";
	$sql = $sql . " select 	cd_instituicao, razao_social_nome";
	$sql = $sql . " from   	instituicao_financeiras ";
	$sql = $sql . " where 	cd_agencia = '0' 
	                  AND status <> 'I'";
	$sql = $sql . " order by razao_social_nome ";
	$rs = pg_exec($db, $sql);

	while ($reg=pg_fetch_array($rs)) {
		$tpl->newBlock('cbo_banco');
		$tpl->assign('cod_banco', $reg['cd_instituicao']);
		$tpl->assign('banco', $reg['razao_social_nome']);
		if ($reg['cd_instituicao'] == $cd_instituicao) { $tpl->assign('sel_banco', ' selected'); }
	}
// ---------------------------------------------------------- Combo agencias
	$sql = "";
	$sql = $sql . " SELECT 	cd_agencia, razao_social_nome 		";
	$sql = $sql . " FROM 	instituicao_financeiras 	";
	if ($cd_instituicao != '') {
		$sql = $sql . " WHERE 	cd_instituicao = " . $cd_instituicao." AND status <> 'I'" ;
	}
	$sql = $sql . " order by razao_social_nome ";
	$rs = pg_exec($db, $sql);
	while ($reg = pg_fetch_array($rs)) {
		$tpl->newBlock('cbo_agencia');
		$tpl->assign('cod_agencia', $reg['cd_agencia']);
		$tpl->assign('agencia', $reg['razao_social_nome']);
		if ($reg['cd_agencia'] == $cd_agencia) { $tpl->assign('sel_agencia', ' selected'); }
	}
//----------------------------------------------------------------------------------------- Combo Estado Civil
		$sql = "";
		$sql = $sql . " select cd_estado_civil, descricao_estado_civil ";
		$sql = $sql . " from   estado_civils ";
		$sql = $sql . " order by descricao_estado_civil ";
		$rs = pg_exec($db, $sql);
		while ($reg = pg_fetch_array($rs)) {
			$tpl->newBlock('cbo_estado_civil');
			$tpl->assign('cod_est_civil', $reg['cd_estado_civil']);
			$tpl->assign('nome_est_civil', $reg['descricao_estado_civil']);
			$tpl->assign('chk_est_civil', ($reg['cd_estado_civil'] == $cd_estado_civil ? ' selected' : ''));
		}
//----------------------------------------------------------------------------------------- Combo Grau de instrução
		$sql = "";
		$sql = $sql . " select cd_grau_de_instrucao, descricao_grau_instrucao ";
		$sql = $sql . " from   grau_instrucaos ";
		$sql = $sql . " order by descricao_grau_instrucao ";
		$rs = pg_exec($db, $sql);
		while ($reg = pg_fetch_array($rs)) {
			$tpl->newBlock('cbo_grau_instrucao');
			$tpl->assign('cod_grau_instrucao', $reg['cd_grau_de_instrucao']);
			$tpl->assign('nome_grau_instrucao', $reg['descricao_grau_instrucao']);
			$tpl->assign('chk_grau_instrucao', ($reg['cd_grau_de_instrucao'] == $cd_grau_instrucao ? ' selected' : ''));
		}
//-------------------------------------------------------
   pg_close($db);
   $tpl->printToScreen();	
?>