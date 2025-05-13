<?
    include_once('inc/sessao.php');
    include_once('inc/conexao.php');
    include_once('inc/ePrev.DAL.PagedResult.php');
    include_once('inc/ePrev.DAL.PagedResult.php');
    include_once('inc/class.TemplatePower.inc.php');

    $dal = new DBConnection();
    $dal->loadConnection($db);
    $paged = new PagedResult( $db );

	$tpl = new TemplatePower('tpl/tpl_cad_visitante_manutencao_nome.html');
	$tpl->assignInclude('mn_sup', 'menu/menu_projetos.htm');

	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once( 'inc/skin.php' );

	$tpl->assign( 'usuario', $N );
	$tpl->assign( 'divsao', $D );
	$tpl->newBlock( 'cadastro' );
	$tpl->assign( 'cor_fundo1', $v_cor_fundo1 );
	$tpl->assign( 'cor_fundo2', $v_cor_fundo2 );

    $filtro = $_REQUEST['filtro_ds_nome'];
    $tpl->assign( 'filtro_ds_nome', $filtro );

    // Paginação
    $paged->setPage( $_REQUEST["pageIndex"] ); 
    $paged->setSize( 150 ); 
    
    if ($_REQUEST["filtro_ds_nome_prioridade"]=="s") {
        $tpl->assign( "filtro_ds_nome_prioridade", " checked " );
		
        $paged->createQueryCount("
    
              SELECT COUNT(*) AS quantos
                FROM projetos.vw_visitantes_agrupados_docs
               WHERE upper(ds_nome) LIKE '%{ds_nome}%'
    
        ");
        $paged->createQueryResultset("
    
              SELECT *
                FROM projetos.vw_visitantes_agrupados_docs
               WHERE upper(ds_nome) LIKE '%{ds_nome}%'
            ORDER BY ds_nome
    
        ");
	
    }
    else {
        $tpl->assign( "filtro_ds_nome_prioridade", " " );
		
        $paged->createQueryCount("
    
              SELECT COUNT(*) AS quantos
                FROM projetos.vw_visitantes_agrupados_nome
               WHERE upper(ds_nome) LIKE '%{ds_nome}%'
    
        ");
        $paged->createQueryResultset("
    
              SELECT *
                FROM projetos.vw_visitantes_agrupados_nome
               WHERE upper(ds_nome) LIKE '%{ds_nome}%'
            ORDER BY ds_nome
    
        ");
	
    }
    
    
    $paged->setAttribute( "{ds_nome}", strtoupper( $filtro ) );
    $rs = $paged->getResultset();

    $tpl->assign( "pageIndex", $paged->getPage() );

    $paginas = "";
    for ($index = $paged->getFirstPage(); $index < $paged->getLastPage(); $index++) {

        if ($index==$paged->getPage()) {
            $paginas .= $separador.'<a href="javascript:changePage('.$index.')"><b>['.($index+1).']</b></a> ';
		}
        else {
            $paginas .= $separador.'<a href="javascript:changePage('.$index.')">'.($index+1).'</a> ';
        }

        $separador = " - ";
	}
    $tpl->assign( "paginacao", $paginas );

    $index = 0;
    while ( $reg = pg_fetch_array($rs) ) 
	{
		$tpl->newBlock( 'nomes' );
        $dal->createQuery("

            SELECT cd_visitante
              FROM projetos.visitantes 
             WHERE trim(ds_nome) = '{ds_nome}'
               AND (nr_rg {nr_rg} )
               AND (nr_cpf {nr_cpf} )
               AND (cd_registro_empregado {cd_registro_empregado} )

        ");
        $dal->setAttribute( "{ds_nome}", trim($reg['ds_nome']) );
        if ($reg['nr_rg']=="") 
        {
            $dal->setAttribute( "{nr_rg}", "is null");
		}
        else
        {
            $dal->setAttribute( "{nr_rg}", "= ".$reg['nr_rg'] );
        }
        if ($reg['nr_cpf']=="") 
        {
            $dal->setAttribute( "{nr_cpf}", "is null" );
        }
        else
        {
            $dal->setAttribute( "{nr_cpf}", "= ".$reg['nr_cpf'] );
        }
        if ($reg['cd_registro_empregado']=="") 
        {
            $dal->setAttribute( "{cd_registro_empregado}", "is null" );
        }
        else
        {
            $dal->setAttribute( "{cd_registro_empregado}", "= ".$reg['cd_registro_empregado'] );
        }
        
        $rsVisitante = $dal->getResultset();
        //echo($dal->getMessage()); exit();
        $cd_visitante = "";
        $virgula = "";
        $count=0;
        while ( $regVisitante = pg_fetch_array($rsVisitante) )
        {
            $cd_visitante .= $virgula . $regVisitante["cd_visitante"];
            $virgula = ",";
            $count++;
        }
        $tpl->assign( 'ordem', ($index+1) + ($paged->getSize()*$paged->getPage()) );
        $tpl->assign( 'index', $index );
        $tpl->assign( 'cd_visitante', $cd_visitante );
        $tpl->assign( 'ds_nome', $reg['ds_nome'] );
        $tpl->assign( 'nr_rg', $reg['nr_rg'] );
        $tpl->assign( 'nr_cpf', $reg['nr_cpf'] );
        $tpl->assign( 'cd_registro_empregado', $reg['cd_registro_empregado'] );
        if ($count==1) {
            $tpl->assign( 'ocorrencias', "(" . $count . " encontrado)" );
		}
        else {
            $tpl->assign( 'ocorrencias', "(" . $count . " encontrados)" );
        }
        $index++;

    }

	pg_close($db);
	$tpl->printToScreen();

?>