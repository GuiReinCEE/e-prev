<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');

	header('location:'.base_url().'index.php/atividade/acompanhamento');

	include_once('inc/ePrev.DAL.DBConnection.php');
	include_once('inc/class.TemplatePower.inc.php');

	$dal = new DBConnection();
	$dal->loadConnection($db);

	$tpl = new TemplatePower('tpl/tpl_lst_acomp_projetos.html');
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);

	$sql = "
			SELECT ap.cd_projeto, 
			       p.nome,
			       ap.cd_acomp,
			       TO_CHAR(dt_acomp, 'DD/MM/YYYY') AS dt_acompanha,
			       TO_CHAR(dt_encerramento, 'DD/MM/YYYY') AS dt_encerra,
			       app.mes, 
			       app.ano
			  FROM projetos.acompanhamento_projetos ap
			  JOIN projetos.projetos p 
			    ON ap.cd_projeto = p.codigo
			  LEFT JOIN (SELECT pp.cd_acomp, 
						        pp.mes, 
						        pp.ano
						   FROM projetos.previsoes_projetos pp
						  WHERE dt_exclusao  IS NULL 
						    AND pp.dt_previsao = (SELECT MAX(pp1.dt_previsao)
						                            FROM projetos.previsoes_projetos pp1
						                           WHERE pp1.dt_exclusao IS NULL
						                             AND pp1.cd_acomp    = pp.cd_acomp)) app
			    ON app.cd_acomp = ap.cd_acomp	
			ORDER BY dt_encerramento DESC, dt_acomp DESC
	";
	$rs = pg_query($sql);
	while ($reg=pg_fetch_array($rs))
	{
		$tpl->newBlock('acomp');
		if ($linha == 'P') 
		{
			$linha = 'I';
			$tpl->assign('cor_fundo', $v_cor_fundo1);
		}
		else 
		{
			$linha = 'P';
			$tpl->assign('cor_fundo', $v_cor_fundo2);
		}		
		$tpl->assign('cd_acomp',        $reg['cd_acomp']);
		$tpl->assign('projeto',         $reg['nome']);
		$tpl->assign('dt_acomp',        $reg['dt_acompanha']);
		$tpl->assign('dt_encerramento', $reg['dt_encerra']);
		$tpl->assign('dt_previsto',     $reg['mes']."/".$reg['ano']);

		// RESPONSÁVEIS
		$responsaveis = "";
		$dal->createQuery("

			    SELECT a.cd_analista, b.guerra
			      FROM projetos.analista_projeto a
			INNER JOIN projetos.usuarios_controledi b
			        ON b.codigo = a.cd_analista 
			       AND a.cd_projeto = ::cd_projeto 
			       AND a.cd_acomp = ::cd_acomp

        ");
		$dal->setAttribute( "::cd_acomp",    $reg['cd_acomp'] );
		$dal->setAttribute( "::cd_projeto",  $reg['cd_projeto'] );

		$result = $dal->getResultset();
		while ( $respons = pg_fetch_array($result) ){
			if( trim($responsaveis)!="" ){
				$responsaveis .= ", ".$respons["guerra"];
			} else {
				$responsaveis .= $respons["guerra"];
			}
		}

		$tpl->assign('responsaveis', $responsaveis);
		// RESPONSÁVEIS
	}

	pg_close($db);
	$tpl->printToScreen();	
?>