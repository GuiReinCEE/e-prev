<?
    include_once('inc/sessao.php');
    include_once('inc/conexao.php');
    include_once('inc/ePrev.DAL.DBConnection.php');
    include_once('inc/class.TemplatePower.inc.php');

    $dal = new DBConnection();
    $dal->loadConnection($db);

	$tpl = new TemplatePower('tpl/tpl_cad_visitante_manutencao_destino.html');
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

    // Destino
    $dal->createQuery("

          SELECT DISTINCT ds_destino
            FROM projetos.visitantes
           WHERE ds_destino LIKE '%{ds_destino}%'
        ORDER BY ds_destino

    ");
    $dal->setAttribute( "{ds_destino}", "" );

    $rs = $dal->getResultset();
    $index = 0;
    while ( $reg = pg_fetch_array($rs) ) 
	{
		$tpl->newBlock( 'destinos' );

        $dal->createQuery("

            SELECT cd_visitante
              FROM projetos.visitantes 
             WHERE ds_destino = '{ds_destino}'

        ");
        $dal->setAttribute( "{ds_destino}", $reg['ds_destino'] );
        $rsVisitante = $dal->getResultset();
        $cd_visitante = "";
        $virgula = "";
        $count=0;
        while ( $regVisitante = pg_fetch_array($rsVisitante) ) 
        {
            $cd_visitante .= $virgula . $regVisitante["cd_visitante"];
            $virgula = ",";
            $count++;
        }

        $tpl->assign( 'ordem', ($index+1) );
        $tpl->assign( 'index', $index );
        $tpl->assign( 'cd_visitante', $cd_visitante );
        $tpl->assign( 'ds_destino', $reg['ds_destino'] );
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