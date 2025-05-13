<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');

	// $TA='A' : Minhas Atividades
	$TA = '';
	if( isset($_REQUEST['TA']) ){ $TA=$_REQUEST['TA']; }

	if( $TA=='E' )
	{
		header( 'location:'.base_url().'index.php/atividade/encaminhada' );
	}
	elseif( $TA=='R' )
	{
		header( 'location:'.base_url().'index.php/atividade/participante' );
	}
	elseif( $TA=='L' )
	{
		header( 'location:'.base_url().'index.php/atividade/legal' );
	}
	else
	{
		header( 'location:'.base_url().'index.php/atividade/minhas' );
	}

	exit;
	
	include_once('inc/class.TemplatePower.inc.php');

	if( isset($_REQUEST['TA']) )
	{
		if($_REQUEST['TA']=='L')
		{
			header( 'location:'.base_url().'index.php/atividade/legal' );
		}
	}

	#### GAP ATENDIMENTO ####
	if(trim($_REQUEST['EMP_GA']) != "")
	{
		$_REQUEST['filtro_emp'] = $_REQUEST['EMP_GA'];
	}
	if(trim($_REQUEST['RE_GA']) != "")
	{
		$_REQUEST['filtro_re'] = $_REQUEST['RE_GA'];
	}	
	if(trim($_REQUEST['SEQ_GA']) != "")
	{
		$_REQUEST['filtro_seq'] = $_REQUEST['SEQ_GA'];
	}

	function formataDataBD($dt_data)
	{
		return ( trim($dt_data) == '' ? 'NULL' : "TO_DATE('".$dt_data."', 'DD/MM/YYYY')" );
	}

	function limpaFiltroSessao()
	{
		$ar_keys  = array_keys($_SESSION);
		$nr_fim   = count($ar_keys);
		$nr_conta = 0;
		while($nr_conta < $nr_fim)
		{
			if(ereg("_lst_atividade",$ar_keys[$nr_conta])) 
			{
				unset($_SESSION[$ar_keys[$nr_conta]]);
			}
			$nr_conta++;
		}

		$ar_keys  = array_keys($_REQUEST);
		$nr_fim   = count($ar_keys);
		$nr_conta = 0;
		while($nr_conta < $nr_fim)
		{
			unset($_REQUEST[$ar_keys[$nr_conta]]);
			$nr_conta++;
		}
	}

	function setFiltroSessao( $ds_campo, $fl_padrao, $vl_padrao )
	{
		//echo "<BR>".$ds_campo." ";
		#### COLOCA CAMPOS DO FILTRO NA SESSAO ####
		if(trim($_REQUEST[$ds_campo]) <> "")
		{
			//echo " - A ";
			if(!array_key_exists($ds_campo, $_REQUEST))
			{
				//echo " - A1 ";
				$_REQUEST[$ds_campo] = $vl_padrao;
			}
			else
			{
				//echo " - A2 ";
				$_SESSION[$ds_campo.'_lst_atividade'] = $_REQUEST[$ds_campo];
			}
		}
		else if(count($_POST) > 1)
		{
			//echo " - B ";
			if((!array_key_exists($ds_campo.'_lst_atividade', $_SESSION)) and ($fl_padrao))
			{
				//echo " - B1 ";
				$_REQUEST[$ds_campo] = $vl_padrao;
				$_SESSION[$ds_campo.'_lst_atividade'] = $vl_padrao;
			}
			else
			{
				//echo " - B3 ";
				$_SESSION[$ds_campo.'_lst_atividade'] = $_REQUEST[$ds_campo];
			}
		}
		else 
		{
			//echo " - C ";
			if((!array_key_exists($ds_campo.'_lst_atividade', $_SESSION)) and ($fl_padrao))
			{
				$_REQUEST[$ds_campo] = $vl_padrao;
				$_SESSION[$ds_campo.'_lst_atividade'] = $vl_padrao;
			}
			else
			{
				$_REQUEST[$ds_campo] = $_SESSION[$ds_campo.'_lst_atividade'];
			}
		}
	}

	#### LIMPA FILTROS DA SESSÃO ####
	if($_REQUEST['fl_filtro_padrao'] == "S")
	{
		limpaFiltroSessao();
	}
	
	#### FIXA FILTROS DE ACORDO COM A SESSÃO ####
	#### AGUARDANDO ####
	setFiltroSessao('chkAG',true,'S');	
	
	#### EM ANDAMENTO ####
	setFiltroSessao('chkAN',true,'S');
	
	#### EM TESTES ####
	setFiltroSessao('chkTE',true,'S');
	
	#### ENCERRADOS ####
	setFiltroSessao('chkEN',false,'');
	
	#### AGUARDANDO DEFINIÇÃO ####
	setFiltroSessao('chkAD',false,'');

	#### MINHAS SOLICITAÇÕES ####
	setFiltroSessao('chkMS',true,'S');
	
	#### SOLICITACOES RECEBIDAS ####
	setFiltroSessao('chkSR',true,'S');
	
	#### IMEDIATA ####
	setFiltroSessao('chkIme',true,'S');
	
	#### FUTURA ####
	setFiltroSessao('chkFut',true,'S');

	#### ROTINA ####
	setFiltroSessao('chkRot',true,'S');

	#### AGENDA ####
	setFiltroSessao('chkAge',true,'S');

	#### PERIODO DATA SOLICITAÇÃO ####
	setFiltroSessao('dt_inicial',false,'');
	setFiltroSessao('dt_final',false,'');

	#### PERIODO DATA ENVIO PARA TESTE ####
	setFiltroSessao('dt_envio_teste_ini',false,'');
	setFiltroSessao('dt_envio_teste_fim',false,'');	

	#### PERIODO DATA CONCLUSÃO ####
	setFiltroSessao('dt_concluido_ini',false,'');
	setFiltroSessao('dt_concluido_fim',false,'');	

	#### DIVISAO SOLICITANTE ####
	setFiltroSessao('cbo_area',false,'');

	#### PROJETO ####
	setFiltroSessao('cbo_projeto',false,'');

	#### SOLICITANTE ####
	setFiltroSessao('cbo_solicitante',false,'');	

	#### ATENDENTE ####
	setFiltroSessao('an',false,'');		

	#### PALAVRA CHAVE ####
	setFiltroSessao('palavra_chave',false,'');	

	#### EMPRESA/RE/SEQ ####
	setFiltroSessao('filtro_emp',false,'');		
	setFiltroSessao('filtro_re',false,'');
	setFiltroSessao('filtro_seq',false,'');

	#### ORDENAÇÃO ####
	setFiltroSessao('o',true,'NU');	

// ---------------------------------------------------------   
	/*
	if ($_REQUEST['dest'] == 'I') 
	{
		$tpl = new TemplatePower('tpl/tpl_lst_atividades_imprimir.html');
	}
	else 
	{
		$tpl = new TemplatePower('tpl/tpl_lst_atividades.html');
	}
	*/
	$tpl = new TemplatePower('tpl/tpl_lst_atividades.html');
// ---------------------------------------------------------   
	$tpl->prepare();
// --------------------------------------------------------- inicialização do skin das telas:
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
// ---------------------------------------------------------
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	$tpl->assign('dt_inicial', $_REQUEST['dt_inicial']);
	$tpl->assign('dt_final', $_REQUEST['dt_final']);
	$tpl->assign('dt_envio_teste_ini', $_REQUEST['dt_envio_teste_ini']);
	$tpl->assign('dt_envio_teste_fim', $_REQUEST['dt_envio_teste_fim']);
	$tpl->assign('dt_concluido_ini', $_REQUEST['dt_concluido_ini']);
	$tpl->assign('dt_concluido_fim', $_REQUEST['dt_concluido_fim']);
	$tpl->assign('aa', $D);
	$tpl->assignGlobal('ta', $TA);

	// Passa por aqui se for versão para impressão
	if ($_REQUEST['dest'] == 'I')
	{  
		switch($_REQUEST['o'])
		{
			case 'DS': $flt = 'Data Cadastro'; break;
			case 'DT': $flt = 'Data Teste'; break; // $order = " ORDER BY data_br DESC, a.numero DESC, us.divisao, a.descricao ";
			case 'DV': $flt = 'Divisão'; break; 
			case 'DE': $flt = 'Descrição'; break;
			case 'ST': $flt = 'Status'; break;
			case 'PR': $flt = 'Projeto'; break;
			case 'NU': $flt = 'Número'; break;
			case 'DL': $flt = 'Data Limite'; break;
			case 'DC': $flt = 'Data de Conclusão'; break;
		}
		$tpl->assign('filtro', $flt);
		$tpl->assign('ordem', '' );
	}

	if ($TA != '') 
	{		
		switch ($TA)
		{
			case 'A':$tpl->assign('banner', '<img src="img/' . $skin . '/banners/banner_minhas_atividades.jpg"'); break;
			case 'L':$tpl->assign('banner', '<img src="img/' . $skin . '/banners/banner_atividades_legais.jpg"'); break;
			case 'R':$tpl->assign('banner', '<img src="img/' . $skin . '/banners/banner_atividades_re.jpg"'); break;
			case 'E':$tpl->assign('banner', '<img src="img/' . $skin . '/banners/banner_encaminhamentos.jpg"'); break;
		}
	}
	else 
	{
		$tpl->assign('banner', '<img src="img/' . $skin . '/banners/banner_minhas_atividades.jpg"');
	}
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
// --------------------------------------------------------- Se NÃO for versão para impressão, coloca o link no final da tela
	if ($t <> 'prn') 
	{
		// $tpl->newBlock('link');
		$tpl->assign('l', $L);
		$tpl->assign('o', $_REQUEST['o']);
		$tpl->assign('r', $R);
//--------------------------------------
		$tpl->assign('chkIme', $_REQUEST['chkIme']);
		$tpl->assign('chkFut', $_REQUEST['chkFut']);
		$tpl->assign('chkAge', $_REQUEST['chkAge']);
		$tpl->assign('chkRot', $_REQUEST['chkRot']);
//--------------------------------------
		$tpl->assign('chkEC', $CHKEC);
		$tpl->assign('chkAG', $_REQUEST['chkAG']);
		$tpl->assign('chkAN', $_REQUEST['chkAN']);
		$tpl->assign('chkEA', $CHKEA);
		$tpl->assign('chkEN', $_REQUEST['chkEN']);
		$tpl->assign('chkTE', $_REQUEST['chkTE']);
		$tpl->assign('chkMS', $_REQUEST['chkMS']);
		$tpl->assign('chkSR', $_REQUEST['chkSR']);
		$tpl->assign('chkAD', $_REQUEST['chkAD']);

		$tpl->assign('ds', $_REQUEST['cbo_area']);
		$tpl->assign('an', $_REQUEST['an']);
		$tpl->assign('dt_inicial', $_REQUEST['dt_inicial']);
		$tpl->assign('dt_final', $_REQUEST['dt_final']); 
		$tpl->assign('dt_envio_teste_ini', $_REQUEST['dt_envio_teste_ini']);
		$tpl->assign('dt_envio_teste_fim', $_REQUEST['dt_envio_teste_fim']);
		$tpl->assign('dt_concluido_ini', $_REQUEST['dt_concluido_ini']);
		$tpl->assign('dt_concluido_fim', $_REQUEST['dt_concluido_fim']);	
	}	
	// --------------------------------------------------------- Filtro
	// INÍCIO FILTRO ATIVIDADES DI:
	// ---------------------------------------------------------
	if ($TA == 'L') 
	{
		$tpl->newBlock('filtro_atividades_legais');
		$tpl->assign('o', $_REQUEST['o']);
        $tpl->assign('dt_inicial', $_REQUEST['dt_inicial']);
        $tpl->assign('dt_final', $_REQUEST['dt_final']);
		$tpl->assign('dt_envio_teste_ini', $_REQUEST['dt_envio_teste_ini']);
		$tpl->assign('dt_envio_teste_fim', $_REQUEST['dt_envio_teste_fim']);
		$tpl->assign('dt_concluido_ini', $_REQUEST['dt_concluido_ini']);
		$tpl->assign('dt_concluido_fim', $_REQUEST['dt_concluido_fim']);
		
		$tpl->assign('aa', $D);
		if (($opt_nao_verif != 'S') and ($opt_nao_pert != 'S') and ($opt_sem_refl != 'S') and ($opt_com_refl != 'S'))
		{
			$opt_nao_verif = 'S';
		}
		$tpl->assign('ta', $TA);
		if ($opt_nao_verif == 'S') {   // sem verificação de pertinencia
			$tpl->assign('chk_nao_verif', ' checked');
			$tpl->assign('opt_nao_verif', 'S');
		}
		if ($opt_nao_pert == 'S') {
			$tpl->assign('chk_nao_pert', ' checked');
			$tpl->assign('opt_nao_pert', 'S');
		}
		if ($opt_sem_refl == 'S') {
			$tpl->assign('chk_sem_refl', ' checked');
			$tpl->assign('opt_sem_refl', 'S');
		}
		if ($opt_com_refl == 'S') {
			$tpl->assign('chk_com_refl', ' checked');
			$tpl->assign('opt_com_refl', 'S');
		}
	}

	elseif ($TA == 'E') 
	{
		$tpl->newBlock('filtro_encaminhamentos_dap');
        $tpl->assign('dt_inicial', $_REQUEST['dt_inicial']);
        $tpl->assign('dt_final', $_REQUEST['dt_final']);  
		$tpl->assign('dt_envio_teste_ini', $_REQUEST['dt_envio_teste_ini']);
		$tpl->assign('dt_envio_teste_fim', $_REQUEST['dt_envio_teste_fim']);	
		$tpl->assign('dt_concluido_ini', $_REQUEST['dt_concluido_ini']);
		$tpl->assign('dt_concluido_fim', $_REQUEST['dt_concluido_fim']);	
		
		$tpl->assign('aa', $D);
        $tpl->assign('l', $L);
        $tpl->assign('o', $_REQUEST['o']);
        $tpl->assign('r', $R); 
        $tpl->assign('chkIme', $_REQUEST['chkIme']);
        $tpl->assign('chkFut', $_REQUEST['chkFut']);
        $tpl->assign('chkAge', $_REQUEST['chkAge']);
        $tpl->assign('chkRot', $_REQUEST['chkRot']);
        $tpl->assign('chkEC', $CHKEC);
        $tpl->assign('chkAG', $_REQUEST['chkAG']);
        $tpl->assign('chkAN', $_REQUEST['chkAN']);
        $tpl->assign('chkEA', $CHKEA);
        $tpl->assign('chkEN', $_REQUEST['chkEN']);
        $tpl->assign('chkTE', $_REQUEST['chkTE']);
        $tpl->assign('chkMS', $_REQUEST['chkMS']);
        $tpl->assign('chkSR', $_REQUEST['chkSR']);
		$tpl->assign('chkAD', $_REQUEST['chkAD']);
        $tpl->assign('ds', $_REQUEST['cbo_area']);
        $tpl->assign('an', $_REQUEST['an']);
        $tpl->assign('dt_inicial', $_REQUEST['dt_inicial']);
        $tpl->assign('dt_final', $_REQUEST['dt_final']);   
		$tpl->assign('dt_envio_teste_ini', $_REQUEST['dt_envio_teste_ini']);
		$tpl->assign('dt_envio_teste_fim', $_REQUEST['dt_envio_teste_fim']);		
		$tpl->assign('dt_concluido_ini', $_REQUEST['dt_concluido_ini']);
		$tpl->assign('dt_concluido_fim', $_REQUEST['dt_concluido_fim']);	

		$tpl->newBlock('filtro_dap');
		$tpl->assign('aa', $D);
		$tpl->assign('ta', $TA);
		//$ds = $_REQUEST['cbo_area'];
		//$an = $cbo_analista;
		$tpl->assign('ds', $_REQUEST['cbo_area']);  // ds = divisão solicitante
		$tpl->assign('an', $_REQUEST['an']);  // an = atendente
		$tpl->assign('ta', $TA);
	   $tpl->assign('l', $L);
	   $tpl->assign('o', $_REQUEST['o']);
	   $tpl->assign('r', $R);
// --------------------------------------------------------- CheckBox Status aguardando, em andamento, encerrados e em testes
		if ($_REQUEST['chkAG'] == 'S') { // Aguardando
			$tpl->assign('chkchkag', ' checked');
			$tpl->assign('chkAG', 'S');
		}
		if ($_REQUEST['chkAN'] == 'S') { // Em Andamento
			$tpl->assign('chkchkan', ' checked');
			$tpl->assign('chkAN', 'S');
		}
		if ($_REQUEST['chkEN'] == 'S') { // Encerrados
			$tpl->assign('chkchken', ' checked');
			$tpl->assign('chkEN', 'S');
		}
		if ($_REQUEST['chkTE'] == 'S') { // Em Testes
			$tpl->assign('chkchkte', ' checked');
			$tpl->assign('chkTE', 'S');
		}
		
		if ($_REQUEST['chkAD'] == 'S') { // Aguardando definição
			$tpl->assign('chkchkad', ' checked');
			$tpl->assign('chkAD', 'S');
		}		
		
// --------------------------------------------------------- Tipo de execução
		if ($_REQUEST['chkIme'] == 'S') {
			$tpl->assign('chkchkime', 'Checked');
			$tpl->assign('chkIme', 'S');
		}
		if ($_REQUEST['chkFut'] == 'S') {
			$tpl->assign('chkchkfut', ' checked');
			$tpl->assign('chkFut', 'S');
		}
		if ($_REQUEST['chkAge'] == 'S') {
			$tpl->assign('chkchkage', ' checked');
			$tpl->assign('chkAge', 'S');
		}
		if ($_REQUEST['chkRot'] == 'S') {
			$tpl->assign('chkchkrot', ' checked');
			$tpl->assign('chkRot', 'S');
		}
// ----------------------------------------------------------------------------------------------- feitas e recebidas
		if ($_REQUEST['chkMS'] == 'S') {
			$tpl->assign('chkchkms', ' checked');
			$tpl->assign('chkMS', 'S');
		}
		if ($_REQUEST['chkSR'] == 'S') {
			$tpl->assign('chkchksr', ' checked');
			$tpl->assign('chkSR', 'S');
		}
		
		
//----------------------------------------------------------------------------------------------- Filtro Divisão Solicitante:
		$sql = "";
		$sql = $sql . " SELECT 	distinct codigo, ";
		$sql = $sql . "        	nome as descricao    ";
		$sql = $sql . " FROM 	projetos.divisoes           ";
		$rs = pg_query($db, $sql);
		$tpl->newBlock('cbo_area');
		$tpl->assign('aa', $D);
		$tpl->assign('codare', '');
		$tpl->assign('nomeare', 'Todas');
		while ($reg = pg_fetch_array($rs)) 
		{
			$tpl->newBlock('cbo_area');
			$tpl->assign('codare', $reg['codigo']);
			$tpl->assign('nomeare', $reg['descricao']);
			$tpl->assign('chkare', ($reg['codigo'] == $_REQUEST['cbo_area'] ? ' selected' : ''));
		}
				

//------------------------------------------------------------------------------------------ Combo Analista
		$sql = "SELECT * FROM projetos.usuarios_controledi WHERE tipo <> 'X' AND divisao in ";
		$sql = $sql . " (select distinct area from projetos.atividades where divisao = 'GAP')  ";
		$sql = $sql . " and codigo in          ";
		$sql = $sql . " (select distinct cod_atendente from projetos.atividades where divisao = 'GAP') ";
		$sql = $sql . "  ORDER BY nome";
		$rs = pg_query($db, $sql);
		$tpl->newBlock('cbo_atendente');
		$tpl->assign('codana', '');
		$tpl->assign('nomeana', 'Todos');
		while ($reg = pg_fetch_array($rs)) 
		{
			$tpl->newBlock('cbo_atendente');
			$tpl->assign('codana', $reg['codigo']);
			$tpl->assign('nomeana', $reg['nome']);
			$tpl->assign('chkana', ($reg['codigo'] == $_REQUEST['an'] ? ' selected' : ''));
		}
	}

	else {
		$tpl->newBlock('filtro_atividades_controle_projetos');
		$tpl->assign('aa', $D);
        // <julio - 27/06/2005>
		// Dados para versão de impressão (link)		
        $tpl->assign('dt_inicial', $_REQUEST['dt_inicial']);
        $tpl->assign('dt_final', $_REQUEST['dt_final']);   
		$tpl->assign('dt_envio_teste_ini', $_REQUEST['dt_envio_teste_ini']);
		$tpl->assign('dt_envio_teste_fim', $_REQUEST['dt_envio_teste_fim']);
		$tpl->assign('dt_concluido_ini', $_REQUEST['dt_concluido_ini']);
		$tpl->assign('dt_concluido_fim', $_REQUEST['dt_concluido_fim']);	
		
        $tpl->assign('l', $L);
        $tpl->assign('o', $_REQUEST['o']);
        $tpl->assign('r', $R); 
        $tpl->assign('chkIme', $_REQUEST['chkIme']);
        $tpl->assign('chkFut', $_REQUEST['chkFut']);
        $tpl->assign('chkAge', $_REQUEST['chkAge']);
        $tpl->assign('chkRot', $_REQUEST['chkRot']);
        $tpl->assign('chkEC', $CHKEC);
        $tpl->assign('chkAG', $_REQUEST['chkAG']);
        $tpl->assign('chkAN', $_REQUEST['chkAN']);
        $tpl->assign('chkEA', $CHKEA);
        $tpl->assign('chkEN', $_REQUEST['chkEN']);
        $tpl->assign('chkTE', $_REQUEST['chkTE']);
        $tpl->assign('chkMS', $_REQUEST['chkMS']);
        $tpl->assign('chkSR', $_REQUEST['chkSR']);
		$tpl->assign('chkAD', $_REQUEST['chkAD']);
        $tpl->assign('ds', $_REQUEST['cbo_area']);
        $tpl->assign('an', $_REQUEST['an']);
        $tpl->assign('dt_inicial', $_REQUEST['dt_inicial']);
        $tpl->assign('dt_final', $_REQUEST['dt_final']);   
		$tpl->assign('dt_envio_teste_ini', $_REQUEST['dt_envio_teste_ini']);
		$tpl->assign('dt_envio_teste_fim', $_REQUEST['dt_envio_teste_fim']);	
		$tpl->assign('dt_concluido_ini', $_REQUEST['dt_concluido_ini']);
		$tpl->assign('dt_concluido_fim', $_REQUEST['dt_concluido_fim']);	
		
		$tpl->newBlock('filtro');
		$tpl->assign('aa', $D);
		$tpl->assign('ta', $TA);
        $tpl->assign('dt_inicial', $_REQUEST['dt_inicial']);
        $tpl->assign('dt_final', $_REQUEST['dt_final']);  
		$tpl->assign('dt_envio_teste_ini', $_REQUEST['dt_envio_teste_ini']);
		$tpl->assign('dt_envio_teste_fim', $_REQUEST['dt_envio_teste_fim']);	
		$tpl->assign('dt_concluido_ini', $_REQUEST['dt_concluido_ini']);
		$tpl->assign('dt_concluido_fim', $_REQUEST['dt_concluido_fim']);			
		//$ds = $_REQUEST['cbo_area'];
		//$an = $cbo_analista;
		$tpl->assign('ds', $_REQUEST['cbo_area']);  // ds = divisão solicitante
		$tpl->assign('an', $_REQUEST['an']);  // an = atendente
		$tpl->assign('ta', $TA);
	    $tpl->assign('l', $L);
	    $tpl->assign('o', $_REQUEST['o']);
	    $tpl->assign('r', $R);
// --------------------------------------------------------- CheckBox Status aguardando, em andamento, encerrados e em testes
		if ($_REQUEST['chkAG'] == 'S') { // Aguardando
			$tpl->assign('chkchkag', ' checked');
			$tpl->assign('chkAG', 'S');
		}
		if ($_REQUEST['chkAN'] == 'S') { // Em Andamento
			$tpl->assign('chkchkan', ' checked');
			$tpl->assign('chkAN', 'S');
		}
		if ($_REQUEST['chkEN'] == 'S') { // Encerrados
			$tpl->assign('chkchken', ' checked');
			$tpl->assign('chkEN', 'S');
		}
		if ($_REQUEST['chkTE'] == 'S') { // Em Testes
			$tpl->assign('chkchkte', ' checked');
			$tpl->assign('chkTE', 'S');
		}
		
		if ($_REQUEST['chkAD'] == 'S') {
			$tpl->assign('chkchkad', ' checked');
			$tpl->assign('chkAD', 'S');
		}		
// --------------------------------------------------------- Tipo de execução
		
		if ($_REQUEST['chkIme'] == 'S') {
			$tpl->assign('chkchkime', 'Checked');
			$tpl->assign('chkIme', 'S');
		}
		if ($_REQUEST['chkFut'] == 'S') {
			$tpl->assign('chkchkfut', ' checked');
			$tpl->assign('chkFut', 'S');
		}
		if ($_REQUEST['chkAge'] == 'S') {
			$tpl->assign('chkchkage', ' checked');
			$tpl->assign('chkAge', 'S');
		}
		if ($_REQUEST['chkRot'] == 'S') {
			$tpl->assign('chkchkrot', ' checked');
			$tpl->assign('chkRot', 'S');
		}
// ----------------------------------------------------------------------------------------------- feitas e recebidas
		if (($_REQUEST['chkMS'] != 'S') and ($_REQUEST['chkSR'] != 'S')) {
			$_REQUEST['chkMS'] = 'S';
			$_REQUEST['chkSR'] = 'S';
		}
		if ($_REQUEST['chkMS'] == 'S') {
			$tpl->assign('chkchkms', ' checked');
			$tpl->assign('chkMS', 'S');
		}
		if ($_REQUEST['chkSR'] == 'S') {
			$tpl->assign('chkchksr', ' checked');
			$tpl->assign('chkSR', 'S');
		}
		
//---------------------------- Filtro Divisão Solicitante:
		$sql = "";
		$sql = $sql . " SELECT 	distinct codigo, ";
		$sql = $sql . "        	nome as descricao    ";
		$sql = $sql . " FROM 	projetos.divisoes           ";
		$rs = pg_query($db, $sql);
		$tpl->newBlock('cbo_area');
		$tpl->assign('aa', $D);
		$tpl->assign('codare', '');
		$tpl->assign('nomeare', 'Todas');
		while ($reg = pg_fetch_array($rs)) {
			$tpl->newBlock('cbo_area');
			$tpl->assign('codare', $reg['codigo']);
			$tpl->assign('nomeare', $reg['descricao']);
			$tpl->assign('chkare', ($reg['codigo'] == $_REQUEST['cbo_area'] ? ' selected' : ''));
		}
	################################# COMBO PROJETO ###########################################
		$sql = "SELECT codigo,
                       nome		
		          FROM projetos.projetos
				 WHERE codigo IN(SELECT DISTINCT(a.sistema)
                                   FROM projetos.atividades a,
                                        listas l1,
                                        listas l2 
                                  WHERE l1.codigo    = a.status_atual 
                                    AND l1.categoria = 'STAT' 
                                    AND l2.categoria = 'TPAT' 
                                    AND l2.codigo    = a.tipo)
				 ORDER BY nome";
			 
		$rs = pg_query($db, $sql);
		$tpl->newBlock('cbo_projeto');
		$tpl->assign('cod_proj', '');
		$tpl->assign('nome_proj', 'Todos');
		
		while ($reg = pg_fetch_array($rs)) 
		{
			$tpl->newBlock('cbo_projeto');
			$tpl->assign('cod_proj', $reg['codigo']);
			$tpl->assign('nome_proj', $reg['nome']);
			$tpl->assign('chk_proj', ($reg['codigo'] == $_REQUEST['cbo_projeto'] ? ' selected' : ''));
		}
	################################# COMBO SOLICITANTE #######################################
		$sql = "SELECT codigo,
                       nome		
		          FROM projetos.usuarios_controledi 
				 WHERE tipo IN ('D','G','N','U')
				 ORDER BY nome";
			 
		$rs = pg_query($db, $sql);
		$tpl->newBlock('cbo_solicitante');
		$tpl->assign('cod_soli', '');
		$tpl->assign('nome_soli', 'Todos');
		
		while ($reg = pg_fetch_array($rs)) 
		{
			$tpl->newBlock('cbo_solicitante');
			$tpl->assign('cod_soli', $reg['codigo']);
			$tpl->assign('nome_soli', $reg['nome']);
			$tpl->assign('chk_soli', ($reg['codigo'] == $_REQUEST['cbo_solicitante'] ? ' selected' : ''));
		}	
	################################# COMBO ANALISTA ##########################################
		$sql = "
			SELECT codigo, nome
			FROM projetos.usuarios_controledi 
			WHERE divisao IN (SELECT distinct ls.codigo as codigo  
			FROM listas ls, projetos.projetos pp            
			WHERE ls.categoria  = 'DIVI' AND ls.codigo = pp.area) AND codigo IN
			(
				SELECT DISTINCT(a.cod_atendente)
				FROM projetos.atividades a, projetos.usuarios_controledi u, listas l1, listas l2
				WHERE l1.codigo = a.status_atual AND l1.categoria = 'STAT' AND l2.categoria = 'TPAT' AND l2.codigo = a.tipo AND u.codigo = a.cod_atendente
			)
			ORDER BY nome
		";
		/*
			PEGAR SOMENTE ANALISTAS E SUPORTE
			AND (tipo IN ('N','G') OR indic_02 = 'S')
		*/
		$rs = pg_query($db, $sql);
		$tpl->newBlock('cbo_analista');
		$tpl->assign('codana', '');
		$tpl->assign('nomeana', 'Todos');

		while ($reg = pg_fetch_array($rs)) 
		{
			$tpl->newBlock('cbo_analista');
			$tpl->assign('codana', $reg['codigo']);
			$tpl->assign('nomeana', $reg['nome']);
			$tpl->assign('chkana', ($reg['codigo'] == $_REQUEST['an'] ? ' selected' : ''));		
		}
	################################# INPUT PALAVRA CHAVE #####################################		
		$tpl->newBlock('palavra_chave');
		$tpl->assign('palavra_chave', '');
		if(trim($_REQUEST['palavra_chave']) != "")
		{
			$tpl->assign('palavra_chave', $_REQUEST['palavra_chave']);
		}
	################################# INPUT EMPRESA/RE/SEQ #####################################		
		$tpl->newBlock('BCK_participante');
		$tpl->assign('filtro_emp', '');
		$tpl->assign('filtro_re', '');
		$tpl->assign('filtro_seq', '');
		if(trim($_REQUEST['filtro_emp']) != "")
		{
			$tpl->assign('filtro_emp', $_REQUEST['filtro_emp']);
		}		
		if(trim($_REQUEST['filtro_re']) != "")
		{
			$tpl->assign('filtro_re', $_REQUEST['filtro_re']);
		}
		if(trim($_REQUEST['filtro_seq']) != "")
		{
			$tpl->assign('filtro_seq', $_REQUEST['filtro_seq']);
		}		
	}
// ----------------------------------------------------------------------------------------------
// Fim FILTRO ATIVIDADES DI
//----------------------------------------------------------------------------------------------- Usuario
	$tpl->newBlock('cols_tab');
	if ($TA == 'E') {
		$tpl->assign('frm_filtro', 'frame_filtro_dap');
	}
	else {
//		echo 'teste';
		$tpl->assign('frm_filtro', 'frame_filtro');
	}
	$tpl->assign('aa', $D);
	$tpl->assign('l', $L);
	$tpl->assign('o', $_REQUEST['o']);
	$tpl->assign('r', $R);
	$tpl->assign('divsao', $D);
	$tpl->assign('chkIme', $_REQUEST['chkIme']);
	$tpl->assign('chkFut', $_REQUEST['chkFut']);
	$tpl->assign('chkAge', $_REQUEST['chkAge']);
	$tpl->assign('chkRot', $_REQUEST['chkRot']);
	$tpl->assign('chkAG', $_REQUEST['chkAG']);
	$tpl->assign('chkAN', $_REQUEST['chkAN']);
	$tpl->assign('chkEN', $_REQUEST['chkEN']);
	$tpl->assign('chkTE', $_REQUEST['chkTE']);
	$tpl->assign('chkMS', $_REQUEST['chkMS']);
	$tpl->assign('chkSR', $_REQUEST['chkSR']);
	$tpl->assign('chkSR', $_REQUEST['chkAD']);

	$tpl->assign('ds', $_REQUEST['cbo_area']);
	$tpl->assign('an', $_REQUEST['an']);
	$tpl->assign('dt_inicial', $_REQUEST['dt_inicial']);
	$tpl->assign('dt_final', $_REQUEST['dt_final']);   
	$tpl->assign('dt_envio_teste_ini', $_REQUEST['dt_envio_teste_ini']);
	$tpl->assign('dt_envio_teste_fim', $_REQUEST['dt_envio_teste_fim']);	
	$tpl->assign('dt_concluido_ini', $_REQUEST['dt_concluido_ini']);
	$tpl->assign('dt_concluido_fim', $_REQUEST['dt_concluido_fim']);		
	$tpl->assign('ta', $TA);	

	$sql = " 
	        SELECT distinct a.numero, 
	               TO_CHAR(now(), 'DD/MM/YYYY - HH24:MI') AS agora,
			       TO_CHAR(a.dt_cad, 'dd/mm/yy') AS dt_cad,
				   dt_cad	AS data_cadastro,
				   TO_CHAR(a.dt_inicio_prev, 'dd/mm/yy') AS data_br,
				   a.dt_inicio_prev,
				   TO_CHAR(a.dt_limite, 'dd/mm/yy') AS data_limite,
				   a.dt_limite,
				   TO_CHAR(a.dt_limite_testes, 'dd/mm/yy') AS data_limite_teste,
				   a.dt_limite_testes,
				   TO_CHAR(a.dt_fim_real, 'dd/mm/yy') AS data_conclusao,
				   l2.descricao AS tipo,
				   a.descricao,
				   a.area,
				   a.status_atual,
				   l.descricao as status,
				   a.sistema as sistema,
				   CASE WHEN ((current_date > a.dt_inicio_prev) AND (a.dt_inicio_real is NULL)) 
				        THEN 'S'
				  	    ELSE 'N'
				   END AS atrasado,
				   a.cod_solicitante,
				   a.cod_atendente,
				   ua.guerra as nomeatend,
				   us.guerra as nomesolic, ";
	
	if ($TA == 'R') 
	{
		$sql.= "   a.cd_empresa,
		           a.cd_registro_empregado,
				   a.cd_sequencia, ";
	}
	
	$sql.= "	   us.divisao as div_solic
	          FROM projetos.atividades a,
			       listas l,
			       listas l2, ";

	if ($T == 'E') 
	{
		$sql.= "   projetos.tarefas t, ";
	}
	
	$sql.= "       projetos.usuarios_controledi ua,
	               projetos.usuarios_controledi us
		     WHERE l.codigo     = a.status_atual
			   AND l.categoria  = 'STAT'
			   AND l2.categoria = 'TPAT'
			   AND l2.codigo    = a.tipo
			   AND ua.codigo    = a.cod_atendente
			   AND us.codigo    = a.cod_solicitante ";
	
	#### TIPO DE ATIVIDADE ####
	switch ($TA) 
	{
		case 'A': $sql.= "AND  a.tipo                  <> 'L' ";
		          break;
		case 'L': $sql.= "AND  a.tipo                  = 'L' ";
		          break;
		case 'E': $sql.= "AND  a.forma                 IN (SELECT codigo FROM listas WHERE categoria = 'FDAP') ";
		          break;
		case 'R': $sql.= "AND  a.cd_registro_empregado > 0 ";
		          break;
	}

	#### ATIVIDADE LEGAIS ####
	if ($TA == 'L') 
	{
		$sql.= " AND ( a.pertinencia = '' ";
		$sql.= ($opt_nao_verif == 'S' ? ' OR a.pertinencia IS NULL ' : '');
		$sql.= ($opt_nao_pert  == 'S' ? ' OR a.pertinencia = 0::char '     : '');
		$sql.= ($opt_sem_refl  == 'S' ? ' OR a.pertinencia = 1::char '     : '');
		$sql.= ($opt_com_refl  == 'S' ? ' OR a.pertinencia = 2::char '     : '');
		$sql.= " ) ";
	}		
	
	############################### FILTRA DATA DE SOLICITAÇÃO ###############################
	if (formataDataBD($_REQUEST['dt_inicial']) <> 'NULL') 
	{
		$sql.= " AND (DATE_TRUNC('day', dt_cad) >= ".formataDataBD($_REQUEST['dt_inicial']).") ";
	}
	
	if (formataDataBD($_REQUEST['dt_final']) <> 'NULL') 
	{
		$sql.= " AND (DATE_TRUNC('day', dt_cad) <= ".formataDataBD($_REQUEST['dt_final']).") ";
	}

	############################### FILTRA DATA ENVIO PARA TESTE ###############################
	if (formataDataBD($_REQUEST['dt_envio_teste_ini']) <> 'NULL') 
	{
		$sql.= " AND (DATE_TRUNC('day', dt_env_teste) >= ".formataDataBD($_REQUEST['dt_envio_teste_ini']).") ";
	}
	
	if (formataDataBD($_REQUEST['dt_envio_teste_fim']) <> 'NULL') 
	{
		$sql.= " AND (DATE_TRUNC('day', dt_env_teste) <= ".formataDataBD($_REQUEST['dt_envio_teste_fim']).") ";
	}

	############################### FILTRA DATA CONCLUSÃO ###############################
	if (formataDataBD($_REQUEST['dt_concluido_ini']) <> 'NULL') 
	{
		$sql.= " AND (DATE_TRUNC('day', dt_fim_real) >= ".formataDataBD($_REQUEST['dt_concluido_ini']).") ";
	}
	
	if (formataDataBD($_REQUEST['dt_concluido_fim']) <> 'NULL') 
	{
		$sql.= " AND (DATE_TRUNC('day', dt_fim_real) <= ".formataDataBD($_REQUEST['dt_concluido_fim']).") ";
	}
	
	############################### FILTRA PELO PROJETO ###################################
	if(trim($_REQUEST['cbo_projeto']) != "")
	{
		$sql.= " AND a.sistema = ".$_REQUEST['cbo_projeto'];
	}
	
	############################### FILTRA PELO SOLICITANTE ###################################
	if(trim($_REQUEST['cbo_solicitante']) != "")
	{
		$sql.= " AND a.cod_solicitante = ".$_REQUEST['cbo_solicitante'];
	}
	
	############################### FILTRA PELO ATENDENTE #####################################
	if(trim($_REQUEST['an']) != "")
	{
		$sql.= " AND a.cod_atendente = ".$_REQUEST['an'];
	}
	
	############################### FILTRA PELA PALAVRA CHAVE #################################
	if(trim($_REQUEST['palavra_chave']) != "")
	{
		$sql.= " AND UPPER(a.descricao) LIKE UPPER('%".$_REQUEST['palavra_chave']."%')";
	}
	
	############################### FILTRA PELA EMPRESA/RE/SEQ #################################
	if(trim($_REQUEST['filtro_emp']) != "")
	{
		$sql.= " AND a.cd_empresa = ".$_REQUEST['filtro_emp'];
	}	
	if(trim($_REQUEST['filtro_re']) != "")
	{
		$sql.= " AND a.cd_registro_empregado = ".$_REQUEST['filtro_re'];
	}
	if(trim($_REQUEST['filtro_seq']) != "")
	{
		$sql.= " AND a.cd_sequencia = ".$_REQUEST['filtro_seq'];
	}	

    #### DIVISAO SOLICITANTE ####
	if ($_REQUEST['cbo_area'] != '') 
	{
		$sql.= " AND us.divisao = '".$_REQUEST['cbo_area']."' ";
	}

	#### FILTROS ####
	$filtro = '';
	if ($TA != 'L')
	{
		#### TIPO DE USUARIO #####
		switch ($T) 
		{
			#### DIRETORIA/PRESIDÊNCIA ####
			case 'D': $filtro.= " AND a.area IN (SELECT codigo FROM projetos.divisoes) ";
			          break;
			#### GERENTE ####
			case 'G': $filtro.= " AND ((a.area = '".$S."') OR (cod_solicitante = ".$_SESSION['Z']." OR a.cod_atendente = ".$_SESSION['Z'].")) ";
			          break;  
		}
	}

	### FEITAS E RECEBIDAS  
	if ( ($_REQUEST['chkMS'] == 'S') and ($_REQUEST['chkSR'] == 'S') ) 
	{

		#### ARRUMAR PARA PARAMETRO ####
		if($TA == "R")
		{
			$T = "";
		}
		else if(($_SESSION['Z'] == 75) or ($_REQUEST['RE_GA']))
		{
			$T = "G";
		}

		switch($T) 
		{
			case 'D': $filtro.= " AND ((cod_solicitante = cod_solicitante) OR (a.cod_atendente = a.cod_atendente)) ";
			          break;
			case 'A': $filtro.= " AND (cod_testador = ".$_SESSION['Z']." OR cod_solicitante = ".$_SESSION['Z']." OR a.cod_atendente = ".$_SESSION['Z'].") ";
			          break;
			case 'P': $filtro.= " AND (cod_testador = ".$_SESSION['Z']." OR cod_solicitante = ".$_SESSION['Z']." OR a.cod_atendente = ".$_SESSION['Z'].") ";
			          break;
			case 'N': $filtro.= " AND (cod_testador = ".$_SESSION['Z']." OR cod_solicitante = ".$_SESSION['Z']." OR a.cod_atendente = ".$_SESSION['Z'].") ";
			          break;
			case 'U': $filtro.= " AND (cod_testador = ".$_SESSION['Z']." OR cod_solicitante = ".$_SESSION['Z']." OR a.cod_atendente = ".$_SESSION['Z'].") ";
			          break;
			case 'G': $filtro.= " AND (
										(
											cod_solicitante IN (
																SELECT uc.codigo
																FROM projetos.usuarios_controledi uc
																WHERE uc.divisao = '".$D."'
														       )
										)
										OR 
										(
											a.cod_atendente IN (
																SELECT uc.codigo  
																FROM projetos.usuarios_controledi uc
																WHERE uc.divisao = '".$D."'
																)
										)
									) 
									";
					  break;
			case 'E': $filtro.= " AND (cod_solicitante = ".$_SESSION['Z']." OR a.cod_atendente = ".$_SESSION['Z']." OR (t.cd_recurso = ".$_SESSION['Z']." AND a.numero = t.cd_atividade)) ";
			          break;
		}
	}
	else 
	{
		if ($_REQUEST['chkMS'] == 'S') 
		{
			$filtro.= " AND us.codigo = ".$_SESSION['Z'];
		}
		else 
		{
			if ($_REQUEST['chkSR'] == 'S') 
			{
				switch ($T) 
				{
					case 'U': $filtro = $filtro . "AND (cod_testador = ".$_SESSION['Z']." OR  a.cod_atendente = ".$_SESSION['Z'].")";
					          break;
					case 'P': $filtro = $filtro . "AND (cod_testador = ".$_SESSION['Z']." OR  a.cod_atendente = ".$_SESSION['Z'].")";
					          break;
					case 'N': $filtro = $filtro . "AND (cod_testador = ".$_SESSION['Z']." OR  a.cod_atendente = ".$_SESSION['Z'].")";
					          break;
					case 'A': $filtro = $filtro . "AND (cod_testador = ".$_SESSION['Z']." OR  a.cod_atendente = ".$_SESSION['Z'].")";
					          break;
					case 'E': $filtro = $filtro . "(cod_testador = ".$_SESSION['Z']." OR  a.cod_atendente = ".$_SESSION['Z'].") 
					                                OR (t.cd_recurso   = ".$_SESSION['Z']." and a.numero = t.cd_atividade) ";
					          break;
				}
			}
			else 
			{
				if ($TA == 'L') 
				{
					switch($T) 
					{
						case 'D': $filtro.= " AND ((cod_solicitante = cod_solicitante) OR (a.cod_atendente = a.cod_atendente)) ";
						          break;
						case 'A': $filtro.= " AND (cod_solicitante = ".$_SESSION['Z']." OR a.cod_atendente = ".$_SESSION['Z'].") ";
						          break;
						case 'P': $filtro.= " AND (cod_solicitante = ".$_SESSION['Z']." OR a.cod_atendente = ".$_SESSION['Z'].") ";
						          break;
						case 'N': $filtro.= " AND (cod_solicitante = ".$_SESSION['Z']." OR a.cod_atendente = ".$_SESSION['Z'].") ";
						          break;
						case 'U': $filtro.= " AND (cod_solicitante = ".$_SESSION['Z']." OR a.cod_atendente = ".$_SESSION['Z'].") ";
						          break;
						case 'E': $filtro.= " AND (cod_solicitante = ".$_SESSION['Z']." OR a.cod_atendente = ".$_SESSION['Z']." OR (t.cd_recurso = ".$_SESSION['Z']." and a.numero = t.cd_atividade)) ";
						          break;
						case 'G': $filtro.= " AND ((cod_solicitante in (select uc.codigo 
						                                                  from projetos.usuarios_controledi uc 
																		 where uc.divisao = '$D')) 
																		    or (a.cod_atendente in (select uc.codigo  
																			                          from projetos.usuarios_controledi uc
																									 where uc.divisao = '$D'))) ";
								  break;
					}
				}
				else 
				{
					$filtro.= " AND 1 = 2 "; // Se não são solicitações desse usuário, não mostra nada
				}
			}
		}
	}

	$sql.= $filtro;

	if ($TA != 'L')
	{
		#### AGUARDANDO ####
		// AICS: "Aguardando Inicio" pela GRI
		$stat_atu = ($_REQUEST['chkAG'] == "S" ? " 'AINI','ADIR','AMAN','AUSR','AIST', 'AICS', 'AINF', 'GCAI', 'AIGA', 'ICGA', 'AIGJ' " : ""); // Aguardando início
		
		#### EM ANDAMENTO ####
		if ($_REQUEST['chkAN'] == 'S')  
		{ 
			// EECS: "Em Desenvolvimento" pela GRI
			
			if ($stat_atu <> '') 
			{ 
				$stat_atu .= ','; 
			}
			$stat_atu .= " 'EANA','EMAN','EMST', 'EECS', 'EAGJ' "; 
		}
		
		#### ENCERRADOS ####
		if ($_REQUEST['chkEN'] == 'S')  
		{ 
			// CACS: "Cancelada" pela GRI
			// COCS: "Atividade Concluída" pela GRI
			if ($stat_atu <> '') 
			{ 
				$stat_atu .= ','; 
			}
			$stat_atu .= " 'COSB','CANC','CONC','LIBE','SUSP','AGDF','CAST','COST', 'CACS', 'COCS', 'CONF', 'CANF', 'GCCA', 'GCCO', 'COGA', 'COGJ' "; 
		}
		
		#### EM TESTES ####
		if ($_REQUEST['chkTE'] == 'S') 
		{ 
			// AOCS: "Análise do Solicitante" pela GRI
			if ($stat_atu <> '') 
			{ 
				$stat_atu .= ','; 
			}
	        $stat_atu .= " 'ETES', 'AOCS' ";
	    }
		
		#### AGUARDANDO DEFINICAO ####
		if ($_REQUEST['chkAD'] == 'S') 
		{ 
			// ASCS: "Aguardando Solicitante" pela GRI
			if ($stat_atu <> '') 
			{ 
				$stat_atu .= ','; 
			}
	        $stat_atu .= " 'AGDF', 'ASCS' ";
	    }	
		
		$sql.= ($stat_atu != '' ? ' AND a.status_atual in ('.$stat_atu.') ' : '');
	}
	else
	{
		if ($_REQUEST['chkIme'] <> 'S') { $sql.= "   AND a.tipo <> 'I' "; } // Execução Imediata
		if ($_REQUEST['chkFut'] <> 'S') { $sql.= "   AND a.tipo <> 'F' "; } // Execução Futura
		if ($_REQUEST['chkAge'] <> 'S') { $sql.= "   AND a.tipo <> 'A' "; } // Agenda
		if ($_REQUEST['chkRot'] <> 'S') { $sql.= "   AND a.tipo <> 'R' "; } // Rotina
	}

	#### AGRUPAMENTO E ORDEM ####
	switch($_REQUEST['o']) 
	{
		## DATA ##
		case 'DS': 	$order = " ORDER BY data_cadastro DESC, dt_cad DESC, a.numero DESC, us.divisao, a.descricao "; 
				 	$group = " GROUP BY a.dt_cad, a.numero, a.divisao, a.descricao, a.sistema, a.dt_inicio_prev,  ";
					break;
		## DIVISAO ##
		case 'DV': 	$order = " ORDER BY us.divisao, a.numero DESC,data_br, a.descricao"; 
	  				$group = " GROUP BY us.divisao, a.numero, a.dt_inicio_prev, a.descricao, a.dt_cad, a.sistema,  ";
					break;
		## DESCRIÇÃO ##
		case 'DE': 	$order = " ORDER BY a.descricao, a.numero DESC, data_br, us.divisao"; 
	  				$group = " GROUP BY a.descricao, a.numero, a.dt_inicio_prev, a.divisao, a.numero, a.dt_cad, a.sistema,  ";
					break;
		## STATUS ##
		case 'ST': 	$order = " ORDER BY status, a.numero DESC, data_br, a.descricao"; 
	  				$group = " GROUP BY a.status_atual, a.numero, a.dt_inicio_prev, a.divisao, a.descricao, a.dt_cad, a.sistema,  ";
					break;
		## PROJETO ##
		case 'PR': 	$order = " ORDER BY sistema, a.numero DESC, data_br, a.descricao"; 
	  				$group = " GROUP BY a.sistema, a.numero, a.dt_inicio_prev, a.descricao, a.divisao, a.dt_cad,  ";
					break;
		## ATIV ##
		case 'NU': 	$order = " ORDER BY a.numero DESC, sistema, data_br, a.descricao"; 
	  				$group = " GROUP BY a.numero, a.sistema, a.dt_inicio_prev, a.descricao, a.divisao, a.dt_cad, ";
					break;
		## DT LIMITE ##
		case 'DL': 	$order = " ORDER BY a.dt_limite DESC, a.numero DESC, sistema, data_br, a.descricao"; 
	  				$group = " GROUP BY a.dt_limite, a.numero, a.sistema, a.dt_inicio_prev, a.divisao, a.descricao, a.dt_cad, ";
					break;

		## DT TESTE ##
		case 'DT': 	$order = " ORDER BY a.dt_limite_testes DESC, a.numero DESC, sistema, data_br, a.descricao"; 
	  				$group = " GROUP BY a.dt_limite_testes, a.numero, a.sistema, a.dt_inicio_prev, a.divisao, a.descricao, a.dt_cad, ";
					break;

		## DT CONCLUSAO ##
		case 'DC': $order = " ORDER BY data_conclusao DESC, a.numero DESC, sistema, data_br, a.descricao"; 
				   $group = " GROUP BY a.dt_fim_real, a.numero, a.sistema, a.dt_inicio_prev, a.descricao, a.divisao, a.dt_cad, ";
				   break;
	}

	// ---------

	if($TA == 'R')
	{
		$group.= "	a.cd_empresa, a.cd_registro_empregado, a.cd_sequencia, ";
	}
	$group.= " a.dt_limite_testes, a.dt_limite, a.dt_fim_real, a.tipo, a.area, l.descricao, a.status_atual, a.dt_inicio_real, a.cod_solicitante, a.cod_atendente , ua.guerra, us.guerra, us.divisao, l2.descricao ";

	$sql.= $group . $order;

	// echo "<PRE>\n$sql\n</PRE>"; //exit();

	$linha = 'P';
	$primeiro = true;

	$rs = pg_query($db, $sql);
	while ($reg = pg_fetch_array($rs)) 
	{
		if ($primeiro) 
		{
			$primeiro = false;
			if ($_REQUEST['dest'] == 'I') 
			{
				$tpl->newBlock('blk_cabecalho');
				$tpl->assign('aa', $D);
				$tpl->assign('data', $reg['agora']);
				$tpl->assign('usuario', $N);
			}
		}
	   
		$tpl->newBlock('registro');
		if ($D == 'GI') 
		{
			$tpl->assign('link_tarefa','visibility:hidden;');
		}
		else
		{
			$tpl->assign('link_tarefa_gi','visibility:hidden;');
     	}
		
		$tpl->assign('ta', $TA);
		if ($linha == 'P') 
		{
			$linha = 'I';
			$tpl->assign('cor_linha', $v_cor_fundo1);
			$tpl->assign('bg_color', "#F4F4F4");
		}
		else 
		{
			$linha = 'P';
			$tpl->assign('cor_linha', $v_cor_fundo2);
			$tpl->assign('bg_color', "#FFFFFF");
		}	
		
		if ($TA == 'R')
		{
			$v_id = $reg['cd_empresa'] . '/' . $reg['cd_registro_empregado'] . '/' . $reg['cd_sequencia'] . ' - ';
			$tpl->assign('RE', $v_id);
		}
		
		$tpl->assign('div_destino', $reg['area']);	// area = destino da atividade.
		$tpl->assign('numero', $reg['numero']);
		$tpl->assign('period', $reg['tipo']);
		$tpl->assign('data', $reg['data_br']);
		$tpl->assign('data_solic', $reg['dt_cad']);
		$tpl->assign('dt_limite', $reg['data_limite']);
		$tpl->assign('dt_limite_teste', $reg['data_limite_teste']);
		$tpl->assign('dt_conclusao', $reg['data_conclusao']);
		//$tpl->assign('descricao', str_replace(chr(13).chr(10), '<br>', $reg['descricao']));
		$tpl->assign('descricao', $reg['descricao']);

		$tpl->assign('divisao', $reg['div_solic']);
		$tpl->assign('status', $reg['status']);

		$qt_total_solicitacoes++;

		if ( $reg['atrasado'] == 'S') 
		{ 
			$tpl->assign('icone_atraso', "<img src='img/img_atrasado.gif'>"); 
		}

		if ($reg['cod_solicitante'] == $CODU) 
		{ 
			$tpl->assign('cor_celula1', "#B8DEC7"); 
		}	

		if (($reg['cod_solicitante'] == $CODU) and ($reg['cod_atendente'] == $CODU)) 
		{ 
			$tpl->assign('cor_celula1', "#DBC699"); 
		}

		$tpl->assign('solic_atend', $reg['nomesolic'].'<BR><I>'.$reg['nomeatend'].'</I>');
		if ($_REQUEST['dest'] == 'I') 
		{
           $tpl->assign('solicitante', $reg['nomesolic']);
		}
//--------------------------------------------------------------------------------------------------
		if ( $reg['sistema'] == '') 
		{
			$tpl->assign('sistema', '');
		}
		else 
		{
			$sql2 =        " select nome ";						
			$sql2 = $sql2 . " from   projetos.projetos p ";
			$sql2 = $sql2 . " where  codigo = " . $reg['sistema'];
			$rs2 = pg_query($db, $sql2);
			$reg2 = pg_fetch_array($rs2);
			$tpl->assign('sistema', $reg2['nome']);
		}
// ----------------------------------------------------------------------- tarefas:
		$sql2 = " SELECT cd_tarefa, 
		                 descricao,
						 fl_tarefa_tipo
					FROM projetos.tarefas 
				   WHERE cd_atividade = ".$reg['numero']."
				     AND dt_exclusao IS NULL
					 ORDER BY cd_tarefa
				   ";
		$rs2 = pg_query($db, $sql2);
		while ($reg2 = pg_fetch_array($rs2)) 
		{
			$tpl->newBlock('trf');
			$tpl->assign('numero', $reg['numero']);
			$tpl->assign('cd_tarefa', $reg2['cd_tarefa']);
			$tpl->assign('trf', str_replace("'","",str_replace('"','',$reg2['descricao'])));
			$tpl->assign('fl_tipo_grava', strtolower($reg2['fl_tarefa_tipo']));
		}		
//--------------------------------------------------------------------------------------------------
	}
	
	$tpl->newBlock('tot');
	$tpl->assign('qt_total_solicitacoes',    $qt_total_solicitacoes);
	
	$tpl->newBlock('tot_fim');
	$tpl->assign('qt_total_solicitacoes',    $qt_total_solicitacoes);
	
	$tpl->printToScreen();
	pg_close($db);

?>