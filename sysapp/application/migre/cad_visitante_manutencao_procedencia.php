<?
    include_once('inc/sessao.php');
    include_once('inc/conexao.php');
    include_once('inc/ePrev.DAL.DBConnection.php');
    include_once('inc/class.TemplatePower.inc.php');

    $dal = new DBConnection();
    $dal->loadConnection($db);

	$tpl = new TemplatePower('tpl/tpl_cad_visitante_manutencao_procedencia.html');
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
    
    $filtro_origem = $_REQUEST['filtro_ds_origem'];
    $filtro_tipo = $_REQUEST['filtro_ds_tipo'];
	$tpl->assign('filtro_ds_origem', $filtro_origem);
	$tpl->assign('filtro_ds_tipo', $filtro_tipo);

    // Destino
    $dal->createQuery("

           SELECT DISTINCT trim(ds_origem) as ds_origem
             FROM projetos.visitantes
            WHERE UPPER(ds_origem) LIKE UPPER('%{ds_origem}%')
              AND (cd_tipo_visita = '{cd_tipo_visita}' OR ''='{cd_tipo_visita}')
         ORDER BY trim(ds_origem)

    ");
    $dal->setAttribute( "{ds_origem}", $filtro_origem );
    $dal->setAttribute( "{cd_tipo_visita}", $filtro_tipo );

    $rs = $dal->getResultset();
    $index = 0;
    while ( $reg = pg_fetch_array($rs) ) 
	{
		$tpl->newBlock( 'origens' );

        $dal->createQuery("

            SELECT cd_visitante
              FROM projetos.visitantes 
             WHERE trim(ds_origem) = '{ds_origem}'

        ");
        $dal->setAttribute( "{ds_origem}", $reg['ds_origem'] );
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
        $tpl->assign( 'ds_origem', $reg['ds_origem'] );
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