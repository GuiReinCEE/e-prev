<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/ePrev.DAL.DBConnection.php');
	include_once('inc/class.TemplatePower.inc.php');
	
    $tpl = new TemplatePower('tpl/tpl_lst_visitantes.html');
	$tpl->prepare();
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	
	if(trim($_POST['dt_entrada']) == "")
	{
		$tpl->assign('dt_entrada', date('d/m/Y'));
	}
	else
	{
		$tpl->assign('dt_entrada', $_POST['dt_entrada']);
	}

	if(trim($_POST['dt_fim']) == "")
	{
		$tpl->assign('dt_fim', date('d/m/Y'));
	}
	else
	{
		$tpl->assign('dt_fim', $_POST['dt_fim']);
	}
    
    $dal = new DBConnection();
    $dal->loadConnection( $db );
    
    $dal->createQuery("

            SELECT l.codigo, l.descricao
              FROM public.listas l
             WHERE l.categoria = 'TACE'
               AND l.divisao   = 'GAD'
          ORDER BY l.codigo

    ");
    
    $resultset = $dal->getResultset();
    
    $tpl->newBlock('sel_procedencia');
    $tpl->assign( 'sel_procedencia_value', "" );
    $tpl->assign( 'sel_procedencia_text', ":: selecione ::" );
    while ( $option = pg_fetch_array($resultset) )
    {
        $tpl->newBlock( 'sel_procedencia' );
        $tpl->assign( 'sel_procedencia_value', $option["codigo"] );
        $tpl->assign( 'sel_procedencia_text', $option["codigo"] . " - " . $option["descricao"] );
        if ($option["codigo"]==$_POST["sel_procedencia"]) {
            $tpl->assign( 'sel_procedencia_selected', "selected" );
		}
            
    }
    
    $dal->createQuery("

        SELECT DISTINCT UPPER(trim(ds_destino)) AS ds_destino
          FROM projetos.visitantes
      ORDER BY ds_destino

    ");
    
    $resultset = $dal->getResultset();
    
    $tpl->newBlock( 'sel_destino' );
    $tpl->assign( 'sel_destino_value', "" );
    $tpl->assign( 'sel_destino_text', ":: selecione ::" );
    while ( $option = pg_fetch_array($resultset) )
    {
        $tpl->newBlock( 'sel_destino' );
        $tpl->assign( 'sel_destino_value', $option["ds_destino"] );
        $tpl->assign( 'sel_destino_text', $option["ds_destino"] );
        if ($option["ds_destino"]==$_POST["sel_destino"]) {
            $tpl->assign( 'sel_destino_selected', "selected" );
		}
    }
    	
	$_POST['dt_entrada'] = (trim($_POST['dt_entrada']) == "" ? "CURRENT_DATE" : "TO_DATE('".$_POST['dt_entrada']."','DD/MM/YYYY')");	
	$_POST['dt_fim'] = (trim($_POST['dt_fim']) == "" ? "CURRENT_DATE" : "TO_DATE('".$_POST['dt_fim']."','DD/MM/YYYY')");	
	$_POST['ds_ordem']   = (trim($_POST['ds_ordem'])   == "" ? "ds_nome"   : $_POST['ds_ordem']);
	
	$tpl->assign('ds_ordem', $_POST['ds_ordem']);
	
	$qr_select = "
					SELECT cd_visitante,
					       cd_registro_empregado,
					       (CASE WHEN nr_cracha IS NULL 
                                 THEN ' - '
                                 ELSE TO_CHAR(nr_cracha, '9999999999')
                           END) AS ds_cracha,
					       TO_CHAR(dt_entrada,'DD/MM/YYYY HH24:MI:SS') AS dt_entra,
						   cd_tipo_visita || ' - ' || ds_origem AS ds_origem,
						   UPPER(ds_nome) AS ds_nome,
						   UPPER(ds_destino) AS ds_destino,
						   (dt_saida - dt_entrada) AS hr_tempo
					  FROM projetos.visitantes
			         WHERE DATE_TRUNC('day', dt_entrada) BETWEEN ".$_POST['dt_entrada']." AND ".$_POST['dt_fim']." ";
    
    if ($_POST["sel_procedencia"]!="") {
		$qr_select .= " AND cd_tipo_visita = '".$_POST["sel_procedencia"]."' ";
	}
    if ($_POST["sel_destino"]!="") {
        $qr_select .= " AND UPPER(ds_destino) LIKE '%".$_POST["sel_destino"]."%' ";
    }
    
    $qr_select .= " ORDER BY ".$_POST['ds_ordem']."";
	
    //echo( $qr_select );
    
    $ob_result = pg_query($db, $qr_select);	
	$nr_conta  = 0;
	while ($ar_reg = pg_fetch_array($ob_result)) 
	{
		if(($nr_conta % 2) != 0)
		{
			$bg_color = '#F4F4F4';
		}
		else
		{
			$bg_color = '#FFFFFF';		
		}
		$nr_conta++;
		
		$tpl->newBlock('lst_movimento');		
		$tpl->assign('bg_color',              $bg_color);
		$tpl->assign('cd_visitante',          $ar_reg['cd_visitante']);
		$tpl->assign('nr_cracha',             $ar_reg['ds_cracha']);
		$tpl->assign('cd_registro_empregado', $ar_reg['cd_registro_empregado']);
		$tpl->assign('ds_nome',               $ar_reg['ds_nome']);
		$tpl->assign('dt_entra',              $ar_reg['dt_entra']);
		$tpl->assign('hr_tempo',              $ar_reg['hr_tempo']);
		$tpl->assign('ds_origem',             $ar_reg['ds_origem']);
		$tpl->assign('ds_destino',            $ar_reg['ds_destino']);
	}

	
	$tpl->printToScreen();
	pg_close($db);
?>