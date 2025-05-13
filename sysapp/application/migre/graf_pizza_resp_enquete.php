<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/jpgraph.php');
	include_once('inc/jpgraph_pie.php');
	include_once("inc/jpgraph_pie3d.php");
	include_once('inc/class.TemplatePower.inc.php');

    // -------------------------------------------------------------------------------
    $sql = "
        SELECT texto 
          FROM projetos.enquete_perguntas 
         WHERE cd_enquete = " . $cd_enquete . " 
           AND cd_pergunta = " . $cd_questao . "
    ";
	$rs = pg_query($db, $sql);
    while ($reg = pg_fetch_array($rs)) {
		$questao = $reg['texto'];
	}
    // -------------------------------------------------------------------------------
    $sql = " 
                SELECT DISTINCT COUNT(*) as soma, valor
                  FROM projetos.enquete_resultados
                 WHERE cd_enquete = " . $cd_enquete . "
        		   AND questao = 'R_" . $cd_questao . "'
            {ANDWHERE}
        	  GROUP BY valor
              ORDER BY soma DESC
    ";
    $where = "";
    if ($_SESSION["filtro_data_inicio"]!="")
    {
        $where = "
                   AND DATE_TRUNC('day', dt_resposta) BETWEEN TO_DATE('" . $_SESSION["filtro_data_inicio"] . "', 'DD/MM/YYYY')
                                                          AND TO_DATE('" . $_SESSION["filtro_data_fim"] . "', 'DD/MM/YYYY')
        ";
    }
    $sql = str_replace( "{ANDWHERE}", $where, $sql );
    $rs = pg_query( $db, $sql );
	while ($reg = pg_fetch_array($rs)) {
		$valores_cr[] = $reg['soma'];
		if ($reg['valor'] == 0) {
			$titulos_cr[] = $reg['valor']. ' (outros)';		
		} else {
			$sqls =  " select coalesce(legenda".number_format($reg['valor']) .", rotulo".number_format($reg['valor']) .") from projetos.enquete_perguntas where cd_enquete = $cd_enquete and cd_pergunta = ".$cd_questao; 
			$rs2 = pg_query($db, $sqls);
			$regs = pg_fetch_array($rs2);
			if ($regs[0] == '') {
				$titulos_cr[] = $reg['valor'];
			} else {
				$titulos_cr[] = $regs[0];
			}
		}
	}
	pg_close($db);
	$graph = new PieGraph(550, 270, "JPG");
	if ($dest != 'img') {
		$graph->title->Set($questao);
		$graph->title->SetFont(FF_VERDANA, FS_NORMAL, 8);
	}
	$graph->img->SetAntiAliasing();
	$tam = 0.5;
	$pz_c = new PiePlot3D($valores_cr);
	$pz_c->SetCenter(0.35, 0.5);
	$pz_c->SetSize($tam);
	$pz_c->SetAngle(50);
	$pz_c->ExplodeSlice (0);
//	$pz_c->SetTheme("water");
	$pz_c->SetTheme("earth");
	$pz_c->SetLegends($titulos_cr);    	
	$graph->Add($pz_c);
// -------------------------------------------------------------------------------
	$graph->SetBackgroundImage('img/img_fundo2.gif', BGIMG_FILLFRAME);
	$graph->SetMarginColor('lightblue@0.3');
	if ($dest == 'img') {
		$graph-> Stroke( "/u/www/upload/g_".$cd_questao.".png");
	} else {
		$graph->Stroke();
	}
?>