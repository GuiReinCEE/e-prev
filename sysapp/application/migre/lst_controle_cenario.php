<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_lst_controle_cenario.html');
	$tpl->prepare();

    header( 'location:'.base_url().'index.php/gestao/controle_cenario');

    //$tpl->assign('n', $n);

	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);

   	if (($D <> 'GC') and ($D <> 'GI')) {
   		header('location: acesso_restrito.php?IMG=banner_cenario');
	}
	$tpl->newBlock('edicao');

    // Verifica dados da edição:
    if(isset($_REQUEST["ano"]))
    {
        $ano = $_REQUEST["ano"];
    }
    else
    {

		$sql = "
				SELECT MAX(EXTRACT('year' FROM dt_legal) ) AS ano_sel
				FROM projetos.cenario 
				WHERE dt_legal IS NOT NULL
		";

		$rs = pg_query($db, $sql);
		if($reg=pg_fetch_array($rs))
		{
			$ano = $reg['ano_sel'];
		}
        // $ano = date('Y');
    }
    if ($ano=="9999")
    {
        $tpl->assign("abaSelecionada", "abaSelecionada");
        $tpl->assign("filtro_mes_style", "display:NONE");
    }

	$tpl->assign('ano_sel', $ano);
	$tpl->assign('cor_fundo', $v_cor_fundo2);
	$sql = "
            SELECT DISTINCT EXTRACT('year' FROM dt_legal) AS ano_sel FROM projetos.cenario 
			 WHERE dt_legal IS NOT NULL 
          ORDER BY extract ('year' FROM dt_legal) DESC
    ";

    $rs = pg_query($db, $sql);
	while ($reg=pg_fetch_array($rs)) {
		$tpl->newBlock('ano_sel');
		if ($reg['ano_sel'] == $ano)
        {
			$tpl->assign('abaSelecionada', "abaSelecionada");
		}
		$tpl->assign('ano', $reg['ano_sel']);
		$tpl->assign('ano_texto', $reg['ano_sel']);
	}
	$tpl->newBlock('ano_sel');
    $tpl->assign('ano_texto', "SEM DATA LEGAL");
    $tpl->assign('ano', "9999");
    if ($ano=="9999")
    {
        $tpl->assign("abaSelecionada", "abaSelecionada");
    }

    $sql = "
            SELECT DISTINCT EXTRACT('month' FROM dt_legal) AS mes_sel
              FROM projetos.cenario
             WHERE dt_legal IS NOT NULL AND EXTRACT('year' FROM dt_legal) = " . $ano . "
          ORDER BY extract ('month' FROM dt_legal) DESC
    ";

    $mes_extenso = array("", "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro");

    // BEGIN : Filtro por Mês
	// echo "<span style='display:none'>$sql</span>";
    $rs = pg_query($db, $sql);
    
    if (isset($_POST["mes_sel"]))
    {
        $mes_selecionado = $_POST["mes_sel"];
	}
    else
    {
        $mes_selecionado = "";
    }
    
    $ultimo_mes_do_ano_filtrado = 0;
    while ( $reg = pg_fetch_array($rs) ) {

        if ($ultimo_mes_do_ano_filtrado=="")
        {
            $ultimo_mes_do_ano_filtrado = $reg['mes_sel']; 
            if ($mes_selecionado == '') 
            { 
                $mes_selecionado = $ultimo_mes_do_ano_filtrado; 
            }
		}

        $tpl->newBlock("mes_sel");
        $tpl->assign("mes", $reg["mes_sel"]);
        $tpl->assign("mes_extenso", $mes_extenso[$reg["mes_sel"]]);
        if ($reg["mes_sel"] == $mes_selecionado) {
            $tpl->assign("mes_selected", " selected ");
        }

    }
    // END : Filtro por Mês

	//$tpl->newBlock('lista');

    // Verifica dados da edição:
	if (isset($_REQUEST["ed"]))
    {
        $ed = $_REQUEST["ed"];
	}
    else
    {
        $ed = "";
    }
    $tpl->assign( 'cd_edicao', $ed );
	$tpl->assign( 'tit_capa', $reg['tit_capa'] );

	$sql = "
            SELECT cd_edicao, cd_cenario,
    			   titulo, pertinencia, cd_usuario,
    			   TO_CHAR(dt_inclusao, 'DD/MM/YYYY') AS data_inc, dt_inclusao,
    			   TO_CHAR(dt_exclusao, 'DD/MM/YYYY') AS data_exc, dt_exclusao,
    			   TO_CHAR(dt_prevista, 'DD/MM/YYYY') AS dt_prev, dt_prevista,
    			   TO_CHAR(dt_legal, 'DD/MM/YYYY') AS dt_leg, dt_legal,
    			   TO_CHAR(dt_implementacao, 'DD/MM/YYYY') AS dt_impl, dt_implementacao
    		  FROM projetos.cenario
    		 WHERE 
               {dt_legal}
               
    	  ORDER BY pertinencia DESC, cd_cenario DESC
    ";
    
    // BEGIN : Data legal será exigida caso não seja marcado checkbox que indica para exibir todos os registros
    $parte_dt_legal = " EXTRACT('year' from dt_legal) = " . $ano . " AND dt_legal IS NOT NULL AND ( 0 = " . $mes_selecionado . " OR EXTRACT( 'month' FROM dt_legal ) = " . $mes_selecionado . " ) ";
    if( $ano=="9999" )
    {
        $parte_dt_legal = " dt_legal IS NULL AND dt_implementacao IS NULL AND EXTRACT('year' from dt_inclusao) >= 2008 "; // pegar apenas do ano de implementação das mudanças pra frente
    }
    $sql = str_replace("{dt_legal}", $parte_dt_legal, $sql);
    // echo( $sql );
    // END : Data legal será exigida caso não seja marcado checkbox que indica para exibir todos os registros


	// echo "<span style='display:none'>$sql</span>";

	$rs = pg_query($db, $sql);
	$cont = 0;
    $lin = 'P';
	while ($reg=pg_fetch_array($rs))
    {
		$tpl->newBlock('projetos');
		$cont = $cont + 1;
		if ($reg['dt_legal'] < $reg['dt_prevista']) {
			$tpl->assign('cor_fundo', '#00FFFF');
		}
		elseif (is_null($reg['dt_prevista']) and $reg['pertinencia'] == 'S') {
			$tpl->assign('cor_fundo', '#00FF00');
		}
		else {
			if ($lin == 'P') {
				$tpl->assign('cor_fundo', $v_cor_fundo1);
				$lin = 'I';
			} else {
				$tpl->assign('cor_fundo', $v_cor_fundo2);
				$lin = 'P';
			}			
		}

		$tpl->assign('cd_edicao',$reg['cd_edicao']);
		$tpl->assign('cd_cenario',$reg['cd_cenario']);
		$tpl->assign('cenario', $reg['titulo']);
		$tpl->assign('dt_cadastro', $reg['data_inc']);
		$tpl->assign('dt_exclusao', $reg['data_exc']);
		$tpl->assign('dt_prevista', $reg['dt_prev']);
		$tpl->assign('dt_legal', $reg['dt_leg']);
		$tpl->assign('dt_implementacao', $reg['dt_impl']);

		switch ($reg['pertinencia']) {
				case '0': 
					$tpl->assign('pertinencia',   'Não pertinente'); break;
				case '1': 
					$tpl->assign('pertinencia',   'Pertinente, mas não altera processo'); break;
				case '2': 
					$tpl->assign('pertinencia',   'Pertinente e altera processo'); break;
		}
		$sql2 = "select guerra, cod_atendente, u.divisao as div from projetos.atividades, projetos.usuarios_controledi u where cd_cenario = " . $reg['cd_cenario'] . " and cod_atendente = codigo";
		$rs2=pg_query($db, $sql2);
		$v_resp = "";
		$div = "";
		while ($reg2=pg_fetch_array($rs2)) {
			$v_resp = $v_resp . $reg2['guerra'] . ", ";
			if (substr_count($div, $reg2['div']) == 0) {
				$div = $div . $reg2['div'] . ", ";
			}
		}
		$tpl->assign('div', $div);
		$tpl->assign('responsavel', $v_resp);
        // Atividades desta norma: - Garcia: 14/03/2007
		$sql2 = "
                    SELECT numero, cod_atendente, u.guerra, a.area, a.pertinencia as pertin, 
					       TO_CHAR(dt_implementacao_norma_legal, 'dd/mm/yyyy') as dt_implementacao_norma_legal, 
					       TO_CHAR(dt_prevista_implementacao_norma_legal, 'dd/mm/yyyy') as dt_prevista_implementacao_norma_legal, 
            			   CASE 
                                WHEN (status_atual = 'CAGC') THEN ( 
                                                                   SELECT observacoes 
                                                                     FROM projetos.atividade_historico 
                                                                    WHERE cd_atividade = a.numero
                                                                      AND status_atual = 'CAGC' 
                                                                 ORDER BY codigo DESC LIMIT(1)
                                                                  )
                                WHEN (status_atual = 'RAGC') THEN ( 
                                                                   SELECT observacoes 
                                                                     FROM projetos.atividade_historico 
                                                                    WHERE cd_atividade = a.numero
                                                                      AND status_atual = 'RAGC' 
                                                                 ORDER BY codigo DESC LIMIT(1)
                                                                  )
                                WHEN (pertinencia = '0') THEN 'Não pertinente'
            			   		WHEN (pertinencia = '1') THEN 'Pertinente, mas não altera processo'
            					WHEN (pertinencia = '2') THEN 'Pertinente e altera processo'
            			  	    ELSE 'Não verificado'
            			   END AS pertinencia
					  FROM projetos.atividades a, projetos.usuarios_controledi u
					 WHERE cd_cenario = " . $reg['cd_cenario'] . " and a.cod_atendente = u.codigo 
					   AND u.tipo not in ('X', 'P')
				  ORDER BY a.numero DESC
        "; // ORDER BY a.numero, a.area, u.guerra, numero

		
        $rs2 = pg_query($db, $sql2);
		while ($reg2=pg_fetch_array($rs2)){
			$tpl->newBlock('atividade');
			if ($reg2['pertin'] == '0') {
				$tpl->assign('texto_pertinencia', 'texto4italico');
			 	$tpl->assign('cor_fundo', $v_cor_fundo1);
			} elseif ($reg2['pertin'] == '1') { 
				$tpl->assign('texto_pertinencia', 'texto4italico');
				$tpl->assign('cor_fundo', '#B5DEC7');
			} elseif ($reg2['pertin'] == '2') { 
				$tpl->assign('texto_pertinencia', 'texto4italico');
				$tpl->assign('cor_fundo', '#F0E0C7');
				if ($reg2['dt_implementacao_norma_legal'] == '') {
					$tpl->assign('img_fundo', 'img/CA.gif');
				}
			} else {
				$tpl->assign('texto_pertinencia', 'texto4vermelhoitalico');
				$tpl->assign('cor_fundo', $v_cor_fundo2);
			}
			$tpl->assign('atividade', $reg2['numero']);
			$tpl->assign('resp', $reg2['guerra']);
			$tpl->assign('gerencia', $reg2['area']);
			$tpl->assign('pert', $reg2['pertinencia']);
			$tpl->assign('dt_imp', $reg2['dt_implementacao_norma_legal']);
			$tpl->assign('dt_prev', $reg2['dt_prevista_implementacao_norma_legal']);
		}
	}
	pg_close($db);
	$tpl->printToScreen();	
?>