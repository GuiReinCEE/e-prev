<?php
	include_once('inc/sessao.php');
	
	#### REDIRECIONA PARA NOVA TELA DE ATENDIMENTO DAS ATIVIDADES (15/12/2015, 21/12/2015) ####
	echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL='.base_url()."index.php/atividade/atividade_atendimento/index/".intval($_REQUEST["n"]).'">';
	exit;			
	
	
	if ((($_SERVER['SERVER_ADDR'] == '10.63.255.5') or ($_SERVER['SERVER_ADDR'] == '10.63.255.7')) and ($_SERVER['HTTPS'] != 'on'))
	{
		#### REDIRECIONA PARA HTTPS ####
		$ir_para_https = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
		$ir_para_https = str_replace('http://','',$ir_para_https);
		$ir_para_https = str_replace('https://','',$ir_para_https);
		$ir_para_https = 'https://'.$ir_para_https;
		header("location: ".$ir_para_https);
		exit;
	}


	include_once('inc/conexao.php');
	include_once('inc/ePrev.DAL.DBConnection.php');
	include_once('inc/class.TemplatePower.inc.php');
	
	
	#### SETA VARIAVEL $_REQUEST["aa"] ####
	if ((intval($_REQUEST["n"]) > 0) and (trim($_REQUEST["aa"]) == ""))
	{
		$qr_sql = " 
					SELECT area
					  FROM projetos.atividades 
		             WHERE numero = ".intval($_REQUEST["n"])."
		          ";
		$ob_resul = pg_query($qr_sql);
		$ar_reg = pg_fetch_array($ob_resul);
		if($ar_reg["area"] != $_REQUEST["aa"])
		{
			echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL='.base_url()."sysapp/application/migre/cad_atividade_atend.php?n=".intval($_REQUEST["n"])."&aa=".$ar_reg["area"].'">';
			exit;			
		}
	}	
	
	
	#### REDIRECIONA PARA NOVA TELA DE ATENDIMENTO DAS ATIVIDADES DO CENARIO LEGAL - 16/12/2015 ####
	if(intval($_REQUEST["n"]) > 0)
	{
		$qr_sql = " 
					SELECT tipo
					  FROM projetos.atividades 
		             WHERE numero = ".intval($_REQUEST["n"])."
		          ";
		$ob_resul = pg_query($qr_sql);
		$ar_reg = pg_fetch_array($ob_resul);
		if($ar_reg["tipo"] == "L")
		{
			echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL='.base_url()."index.php/atividade/atividade_atendimento/index/".intval($_REQUEST["n"]).'">';
			exit;	
		}		
	}	
	
	
	
	// VARIÁVEIS PRIVADAS DA PÁGINA
	
	// METHODS
    function listar_lista_programas($_db)
    {
        $dal = new DBConnection();
        $dal->loadConnection( $_db );

        $dal->createQuery("

                SELECT codigo, 
                       descricao 
                  FROM listas 
                 WHERE categoria = 'PRFC' 
              ORDER BY descricao

        ");

        $r = $dal->getResultset();
        return $r;
    }
    
	function listar_rateios_previdenciarios( $_db, $_cd_atividade )
	{
		$dal = new DBConnection();
		$dal->loadConnection( $_db );

		$dal->createQuery("

			SELECT nr_natureza, 
                   nr_percentual,
                   cd_listas_programa 
              FROM projetos.atividade_rateio
             WHERE cd_atividade = {cd_atividade}

		");

		$dal->setAttribute("{cd_atividade}",  $_cd_atividade);

		$r = $dal->getResultset();
		return $r;

	}
	// METHODS

   	if ($msg == 'A')
	{
    	echo "<script language='JavaScript'>;alert('Alterações Efetuadas');</script>";
	}
    // -------------------------------------------------------------------------------
	if (trim($aa)=="")
	{
		$aa = $D;
	}
    // -------------------------------------------------------------------------------
	$sql = " 
			SELECT numero,
			       area,
			       sistema,
			       cod_solicitante,
			       tipo_solicitacao,
			       TO_CHAR(dt_cad, 'dd/mm/yyyy') AS dt_cad,
			       tipo AS tipo_ativ,
			       status_atual,
			       cod_atendente,
			       complexidade,
			       descricao,
			       problema,
			       solucao,
			       negocio_fim,
			       prejuizo,
			       legislacao,
			       cliente_externo,
			       concorrencia,
			       ok,
			       complemento,
			       num_dias_adicionados,
			       numero_dias,
			       periodicidade,
			       cod_testador,
			       TO_CHAR(dt_implementacao_norma_legal, 'dd/mm/yyyy') AS dt_implementacao_norma_legal,
			       TO_CHAR(dt_prevista_implementacao_norma_legal, 'dd/mm/yyyy') AS dt_prevista_implementacao_norma_legal,
			       pertinencia,
			       cd_cenario,
				   fl_balanco_gi,
			       TO_CHAR(dt_inicio_prev, 'dd/mm/yyyy') AS dt_inicio_prev,
			       TO_CHAR(dt_fim_prev, 'dd/mm/yyyy') AS dt_fim_prev,
			       TO_CHAR(dt_inicio_real, 'dd/mm/yyyy') AS dt_inicio_real,
			       TO_CHAR(dt_fim_real, 'dd/mm/yyyy') AS dt_fim_real,
			       TO_CHAR(dt_env_teste, 'dd/mm/yyyy hh24:mi') AS dt_env_teste,
			       TO_CHAR(dt_limite_testes, 'dd/mm/yyyy') AS dt_limite_teste,
			       TO_CHAR(dt_limite, 'dd/mm/yyyy') AS dt_limite,
			       fl_teste_relevante
			  FROM projetos.atividades
			 WHERE numero = ".intval($n);

	$rsOs = pg_query($db, $sql);
	$regOs = pg_fetch_array($rsOs);
	$tpl = new TemplatePower('tpl/tpl_frm_atividade_atend.html');
	$tpl->prepare();

	#### BOOTSTRAP ####
	if(preg_match('/(?i)MSIE [1-7].0/',$_SERVER['HTTP_USER_AGENT']))
	{
		$tpl->assignGlobal('style_bootstrap', '<link href="'.base_url().'bootstrap/css/bootstrap-ie.css" rel="stylesheet" media="screen">');
	}
	else
	{
		$tpl->assignGlobal('style_bootstrap', '<link href="'.base_url().'bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">');
	}	
	
	$tpl->assign( "site_url", site_url());
	$tpl->assign( "url_lista", ($TA == 'L' ? "legal" : "minhas") );
    $tpl->assign( "projeto_visibility", ($aa == 'GRI') ? "hidden" :"visible" );
	$tpl->assign( "projeto_visibility", ($aa == 'GA') ? "hidden" :"visible" );
	$tpl->assign( "projeto_visibility", ($aa == 'GJ') ? "hidden" :"visible" );
	$tpl->assign( "projeto_visibility", ($aa == 'SG') ? "hidden" :"visible" );
	

									
    /*************************************************************
     * MOVIMENTO DO COMBO DOS PROJETOS CASO ATENDIMENTO PELA GRI
     */
    $tpl->assign( "cbo_sistema_not_gri_extension_name", ($aa == 'GRI')?"_IGNORE":"" );
    $tpl->assign( "cbo_sistema_gri_extension_name", ($aa == 'GRI')?"":"_IGNORE" );
    
    /**************************************************************/

    // RAGC : Reencaminhada Atividade para Outra Divisão
    $tpl->assign( "object_html_salvar_extend", ( $regOs['status_atual']=="CANC" || $regOs['status_atual']=="CACS" || $regOs['status_atual']=="RAGC" )
									?"style='display:none'"
									:"" );

	$tpl->assign('n', $n);
	$tpl->assign("link_anexo", site_url('atividade/atividade_anexo/index/'.$n.'/'.$aa));
	$tpl->assign("link_acompanhamento", site_url('atividade/atividade_acompanhamento/index/'.$n.'/'.$aa));
	$tpl->assign("link_historico", site_url('atividade/atividade_historico/index/'.$n.'/'.$aa));
	//link
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	$tpl->assign('ta', $TA);
	$tpl->assign('aa', $aa); // Divisão Atendente
	$cbo_sistemas        = $regOs['sistema'];
	$divisao_atendente   = $regOs['area'];
	$cbo_area            = $regOs['area'];
	$cbo_solicitante     = $regOs['cod_solicitante'];
	$cbo_tipo_manutencao = $regOs['tipo_solicitacao'];
	$cbo_status_atual    = $regOs['status_atual'];
	$cbo_analista        = $regOs['cod_atendente'];
	$cbo_testador        = $regOs['cod_testador'];
	$cbo_complexidade    = $regOs['complexidade'];
	$cbo_fl_balanco_gi    = $regOs['fl_balanco_gi'];

	$cbo_tipo_atividade  = $regOs['tipo_ativ'];	
	
	$dt_prevista_implementacao_norma_legal = $regOs['dt_prevista_implementacao_norma_legal']; 
	$dt_implementacao_norma_legal = $regOs['dt_implementacao_norma_legal'];
	$tpl->assign('dt_cad',    $regOs['dt_cad']);
	$tpl->assign('numero_os', $regOs['numero']);
	$tpl->assign('descricao', $regOs['descricao']);
	$tpl->assign('problema', ( $U==$regOs['atendente'] ? $regOs['problema'] : str_replace(chr(13).chr(10), "<br>", $regOs['problema']) ) );
	$tpl->assign('problema',  $regOs['problema']);
	$tpl->assign('negocio_fim',     $regOs['negocio_fim']);
	$tpl->assign('prejuizo',        $regOs['prejuizo']);
	$tpl->assign('legislacao',      $regOs['legislacao']);
	$tpl->assign('cliente_externo', $regOs['cliente_externo']);
	$tpl->assign('concorrencia',    $regOs['concorrencia']);
	$tpl->assign('cod_atendente', 	$regOs['cod_atendente']); 
	$tpl->assign('tipo_ativ', 		$regOs['tipo_ativ']);

	$tpl->assign('num_dias_influenciados', $regOs['num_dias_adicionados']);
	$tpl->assign('cd_cenario', 		$regOs['cd_cenario']);
	
	$tpl->assign('dt_limite', 		$regOs['dt_limite']);
	
	
	if ($regOs['ok'] == '1'){
		$tpl->assign('opt1',   'checked');
	}
	else {
		$tpl->assign('opt2',   'checked');
	}
	$tpl->assign('status_anterior', 	$cbo_status_atual);
	$tpl->assign('complemento', $regOs['complemento']);

    // Atividades do Cenário Legal:
	if ( $regOs['tipo_ativ'] != 'L' ) {		
		$tpl->newBlock('testes');

		if ($aa == 'GI')
		{
			$tpl->assign('teste', 'Teste');
			$tpl->assign('gi_style', 'display:;');	
		}
		else
		{
			$tpl->assign('teste', 'Análise do Solicitante');
			$tpl->assign('gi_style', 'display:none;');	
		}

			//echo "teste";exit;
		if(($aa == 'GF') or ($aa == 'GC') or ($aa == "GA") or ($aa == "GJ") or ($aa == "SG"))
		{
			$tpl->assign('gf_testes_style', 'display:none');
		}


		if( $regOs['fl_teste_relevante']=="S" )
		{
			$tpl->assign('teste_relevante_sim_selected', ' selected ');
		}
		else
		{
			$tpl->assign('teste_relevante_nao_selected', ' selected ');
		}

		$tpl->assign('dt_env_teste',   	$regOs['dt_env_teste']);
		$tpl->assign('dt_limite_teste', $regOs['dt_limite_teste']);
		$tpl->newBlock('encaminhamento');
		
		$tpl->assign( "style_encaminhamento", ($regOs['area'] == 'GRI')
									?"display:none;"
									:"display:;" );
									
		$tpl->assign( "style_encaminhamento_descricao", ($regOs['area'] == 'GRI')
									?"display:none;"
									:"display:;" );									

		$tpl->assign( "style_encaminhamento", ($regOs['area'] == 'GA')
									?"display:none;"
									:"display:;" );
									
		$tpl->assign( "style_encaminhamento", ($regOs['area'] == 'GJ')
									?"display:none;"
									:"display:;" );		
									
		$tpl->assign( "style_encaminhamento", ($regOs['area'] == 'SG')
									?"display:none;"
									:"display:;" );										
									
		$tpl->assign( "style_cronograma", ($regOs['area'] != 'GI')
									?"display:none;"
									:"display:;" );
		
		$tpl->assign('dt_inicio_real', 	$regOs['dt_inicio_real']);
		$tpl->assign('dt_fim_real',    	$regOs['dt_fim_real']);
		$tpl->assign('solucao', ( $U==$regOs['atendente'] ? $regOs['solucao'] : $regOs['solucao'] ) );
		$tpl->assign('dias_previstos', 	$regOs['numero_dias']);
		$tpl->assign('periodicidade', 	$regOs['periodicidade']);
	}
	
	if(trim($cbo_fl_balanco_gi) == 'S')
	{
		$tpl->assign('chkbal_sim',   'selected');
	}
	else
	{
		$tpl->assign('chkbal_nao',   'selected');
	}

	#### INFOS ATIVIDADE LEGAL ####
	if($regOs["tipo_ativ"] == "L")
	{
		$qr_status = "
						SELECT CASE WHEN (a.status_atual = 'CAGC') THEN
																	(
																	SELECT ah.observacoes
																	  FROM projetos.atividade_historico ah
																	 WHERE ah.cd_atividade = a.numero
																	   AND ah.status_atual = 'CAGC'
																	 ORDER BY ah.codigo DESC 
																	 LIMIT 1
																	)
									WHEN (a.status_atual = 'RAGC') THEN
																	(
																	SELECT ah.observacoes
																	  FROM projetos.atividade_historico ah
																	 WHERE ah.cd_atividade = a.numero
																	   AND ah.status_atual = 'RAGC'
																	 ORDER BY ah.codigo DESC 
																	 LIMIT 1
																	)
									WHEN (a.pertinencia = '0') THEN 'Não pertinente'
									WHEN (a.pertinencia = '1') THEN 'Pertinente, mas não altera processo'
									WHEN (a.pertinencia = '2') THEN 'Pertinente e altera processo'
									ELSE 'Não verificado'
							   END AS pertinencia,
							   CASE WHEN (a.status_atual = 'CAGC') THEN 'gray'
									WHEN (a.status_atual = 'RAGC') THEN 'gray'
									WHEN (a.pertinencia = '0')     THEN 'black'
									WHEN (a.pertinencia = '1')     THEN 'green'
									WHEN (a.pertinencia = '2')     THEN 'blue'
									ELSE 'orange'
							   END AS cor,
							   CASE WHEN (a.status_atual = 'CAGC') THEN ''
									WHEN (a.status_atual = 'RAGC') THEN ''
									WHEN (a.pertinencia = '0')     THEN 'label-inverse'
									WHEN (a.pertinencia = '1')     THEN 'label-success'
									WHEN (a.pertinencia = '2')     THEN 'label-info'
									ELSE 'label-important'
							   END AS cor_status,
							   CASE WHEN (a.status_atual = 'CAGC') THEN 'N'
									WHEN (a.status_atual = 'RAGC') THEN 'N'
									WHEN (a.pertinencia = '0')     THEN 'N'
									WHEN (a.pertinencia = '1')     THEN 'N'
									WHEN (a.pertinencia = '2')     THEN 'N'
									ELSE 'S'
							   END AS fl_salvar_legal,							   
							   a.cd_cenario,
							   c.cd_edicao
						  FROM projetos.atividades a 
						  JOIN projetos.usuarios_controledi uc
							ON a.cod_atendente = uc.codigo
						  LEFT JOIN projetos.cenario c
							ON c.cd_cenario = a.cd_cenario
						 WHERE a.numero = ".intval($_REQUEST["n"])."
					 ";
		$ob_status = pg_query($db, $qr_status);
		$ar_status = pg_fetch_array($ob_status);
		$tpl->newBlock('info_atividade_legal');
		$tpl->assign('status_legal', '<span class="label '.$ar_status["cor_status"].'">'.wordwrap($ar_status['pertinencia'], 50, "<BR>", false).'</span>');
		$tpl->assign('link_legal', '<a href="'.base_url().'index.php/ecrm/informativo_cenario_legal/legislacao/'.$ar_status["cd_edicao"].'/'.$ar_status["cd_cenario"].'" target="_blank" style="font-weight:bold;">[Ver o Cenário Legal]</a>');
		
		$tpl->assignGlobal('fl_salvar_legal', ($ar_status["fl_salvar_legal"] == "N" ? "disabled" : ""));
	}	
	
	// SOLICITACAO SUPORTE
	if ($regOs['tipo_ativ'] == 'S')  
	{
		$tpl->newBlock('banco_de_solucao');

		// BUSCA REGISTRO GRAVADO NO BD SOLUCAO
		$qr_categoria = "
							SELECT cd_atividade_solucao,
							       cd_categoria, 
							       ds_assunto
							  FROM projetos.atividade_solucao
                             WHERE cd_atividade = ".intval($n)."
		                ";
		$ob_resul = pg_query($db, $qr_categoria);
		$ar_categoria = pg_fetch_array($ob_resul);
		$cd_solucao_categoria = $ar_categoria['cd_categoria'];
		$tpl->assign('cd_atividade_solucao', $ar_categoria['cd_atividade_solucao']);
		$tpl->assign('ds_solucao_assunto', $ar_categoria['ds_assunto']);

		// LISTA OPCOES PARA GRAVAR NO BD SOLUCAO
		$qr_categoria = "
							SELECT codigo AS cd_solucao_categoria,
							       descricao AS ds_solucao_categoria
							  FROM public.listas
							 WHERE divisao   = 'GI'
							   AND categoria = 'SOLU'		
		                ";
		$ob_resul = pg_query($db, $qr_categoria);
		while ($ar_categoria = pg_fetch_array($ob_resul)) 
		{
			$tpl->newBlock('cd_solucao_categoria');
			$tpl->assign('cd_solucao_categoria', $ar_categoria['cd_solucao_categoria']);
			$tpl->assign('ds_solucao_categoria', $ar_categoria['ds_solucao_categoria']);
			$tpl->assign('fl_solucao_categoria', ($ar_categoria['cd_solucao_categoria'] == $cd_solucao_categoria ? ' selected' : ''));
		}		
	}	

    // Datas previstas de início e fim
	if ($regOs['tipo_ativ'] == 'F')  {
		$tpl->newBlock('datas_cronograma');
		$tpl->assign('dt_inicio_prev', 	$regOs['dt_inicio_prev']);
		$tpl->assign('dt_fim_prev',    	$regOs['dt_fim_prev']);
	}						

    // Número de Dias a Adicionar no cronograma
	if (($regOs['tipo_ativ'] == 'I') and ($regOs['cod_atendente'] == $Z))  {
		$tpl->newBlock('num_dias_adicionar');
		$tpl->assign('num_dias_influenciados', $regOs['num_dias_adicionados']);
	}

    // Datas previstas de início e fim
	if ($regOs['tipo_ativ'] == 'L')  {
		$tpl->newBlock('cenario_legal');
		switch ($regOs['pertinencia']) {
				case '0': 
					$tpl->assign('check_pert0',   'checked'); break;
				case '1': 
					$tpl->assign('check_pert1',   'checked'); break;
				case '2': 
					$tpl->assign('check_pert2',   'checked'); break;
		}
		$sql2 = 		" select 	to_char(dt_implementacao, 'dd/mm/yyyy') as dt_implementacao, ";
		$sql2 = $sql2 . " 			to_char(dt_prevista, 'dd/mm/yyyy') 		as dt_prevista ";
		$sql2 = $sql2 . " from 		projetos.cenario ";
		$sql2 = $sql2 . " where 	cd_cenario = " . $regOs['cd_cenario'];
		$rs2 = pg_query($db, $sql2);
		$reg2 = pg_fetch_array($rs2);		
		$tpl->assign('dt_implementacao', $dt_implementacao_norma_legal);
		$tpl->assign('dt_prevista', $dt_prevista_implementacao_norma_legal);
		$tpl->assign('dt_impl', $reg2['dt_implementacao']);
		$tpl->assign('gerencia_responsavel', $cbo_area);
		$tpl->assign('dt_prev', $reg2['dt_prevista']);
		$tpl->assign('cor_fundo1', $v_cor_fundo1);
		$tpl->assign('cor_fundo2', $v_cor_fundo2);

        // Combo Gerências para encaminhamento de normas legais:
		$sql = "SELECT codigo, nome FROM projetos.divisoes WHERE tipo IN ('ASS', 'DIV') ORDER BY nome";
		$rs = pg_query($db, $sql);
		while ($reg = pg_fetch_array($rs)) {
			$tpl->newBlock('sel_gerencia');
			$tpl->assign('cd_gerencia', $reg['codigo']);
			$tpl->assign('nome_gerencia', $reg['nome']);
			$tpl->assign('chk_gerencia', ($reg['codigo'] == $cbo_area ? ' selected' : ''));
		}
	}		

    // Previsão Orçamentária
	if ( $aa == 'GI' ) {
		$tpl->newBlock('prev_orcamento');
		$tpl->assign('ativ', $n);
		$sql = "SELECT ap.cd_projeto, ap.num_dias, ap.cd_programa, pp.nome, l.descricao ";
		$sql = $sql . " from projetos.ativ_projetos ap, projetos.projetos pp, listas l ";
		$sql = $sql . " where cd_atividade = $n ";
		$sql = $sql . " and pp.codigo = ap.cd_projeto and  l.categoria = 'PRFC' and l.codigo = ap.cd_programa ";
		$rs = pg_query($db, $sql);
		while ($reg = pg_fetch_array($rs)) {
			$tpl->newBlock('ativ_proj');
			$tpl->assign('numero_os', $n);
			$tpl->assign('dias', $reg['num_dias']);
			$tpl->assign('sistema', $reg['nome']);
			$tpl->assign('cd_sistema', $reg['cd_projeto']);
			$tpl->assign('programa', $reg['descricao']);
		}
	}

    // Rateio Previdenciário
	if ( $aa == 'GRI' ) {
        
        //$rstProgramas = listar_lista_programas( $db );

        $tpl->newBlock('atividade_rateio');
		$result = listar_rateios_previdenciarios($db, $n);
		$index = 0;
		$soma_percentuais = 0;
		if($result) {
			while ($reg = pg_fetch_array($result)) {
				
                $tpl->newBlock('atividade_rateio_percentuais');
				
                $tpl->assign('rateio_programa_id', "rateio_programa_".$index."");
                $tpl->assign( $reg["cd_listas_programa"], " SELECTED" );
				$tpl->assign('natureza_id', "rateio_natureza_".$index."");
				$tpl->assign('percentual_id', "rateio_percentual_".$index."");

				$tpl->assign('natureza_value', $reg["nr_natureza"]);
				$tpl->assign('percentual_value', $reg["nr_percentual"]);

				$tpl->assign('linha_percentual_id', "linha_rateio_".$index."");

				$soma_percentuais += $reg["nr_percentual"];

				$index++;
                
			}
			$tpl->newBlock('atividade_rateio_scripts');
			$tpl->assign('atividade_rateio_count', "setRateioCount( " . $index . " )");
			$tpl->assign('atividade_rateio_soma_percentuais', "setSumPercents( " . $soma_percentuais . " );");
		}

		if ($index==0)
		{
			$tpl->newBlock('atividade_rateio_scripts');
			$tpl->assign('atividade_rateio_add_rateio', "addRateio();");
		}
		$tpl->newBlock('campo_projeto_gri');
	}

    // Tarefas desta atividade
	$qt_tarefa_aberta = 0;
	$qt_tarefa_exec   = 0;
	$sql = "SELECT opt_tarefas FROM projetos.usuarios_controledi WHERE codigo = ".intval($_SESSION["Z"]);
	$rs = pg_query($db, $sql);
	$reg = pg_fetch_array($rs);
	if ($reg['opt_tarefas'] == 'S') 
	{
		$tpl->newBlock('sub_atividades');
		$tpl->assign( "style_tarefas", ($regOs['area'] == 'GRI')
									?"display:none;"
									:"display:;" );

    	if ($aa == 'GI')
		{
	 		$tpl->assign('link_tarefas','visibility:hidden;');
		}
		else
		{
			$tpl->assign('link_tarefas_gi','visibility:hidden;');
		}
		$tpl->assign("link_cadastro", site_url('atividade/tarefa/cadastro'));
		
		$tpl->assign('ativ', $n);				// No sql abaixo, buscar a descrição dos status em listas
		$sql = "		
				SELECT t.cd_tarefa AS codigo, 
					   u.guerra AS guerra, 
					   t.programa AS programa, 
					   t.resumo, 
					   t.status_atual AS st_tarefa,
					   h.status_atual AS st_historico,
					   to_char(t.dt_inicio_prog, 'dd/mm/yyyy') AS dt_inicio_prog,
					   CASE WHEN (h.status_atual='AMAN') THEN 'Aguardando Manutenção' 
					  	    WHEN (h.status_atual='EMAN') THEN 'Em Manutenção' 
					  	    WHEN (h.status_atual='AINI') THEN 'Aguardando Início' 
					  	    WHEN (h.status_atual='LIBE') THEN 'Liberada' 
					  	    WHEN (h.status_atual='CONC') THEN 'Concluída'
							WHEN (h.status_atual='CANC') THEN 'Cancelada'
							WHEN (h.status_atual='AGDF') THEN 'Aguardando Definição'
							WHEN (h.status_atual='SUSP' AND (SELECT status_atual FROM projetos.atividades WHERE numero = t.cd_atividade)='ADIR') THEN 'Atividade Aguardando Diretoria'
							WHEN (h.status_atual='SUSP' AND (SELECT status_atual FROM projetos.atividades WHERE numero = t.cd_atividade)='AUSR') THEN 'Atividade Aguardando Usuário'
							WHEN (h.status_atual='SUSP' AND (SELECT status_atual FROM projetos.atividades WHERE numero = t.cd_atividade)='SUSP') THEN 'Atividade Suspensa'
							WHEN (h.status_atual='SUSP') THEN 'Em Manutenção (Pausa)'
					   END as status,
					   t.fl_tarefa_tipo
				  FROM projetos.tarefas t, 
					   projetos.usuarios_controledi u, 
					   projetos.tarefa_historico h 
				 WHERE t.cd_atividade   = ".$n."  
				   AND t.cd_recurso   = u.codigo 
				   AND t.cd_atividade = h.cd_atividade
				   AND t.cd_tarefa    = h.cd_tarefa
				   /* AND t.cd_recurso   = h.cd_recurso  -- RETIRADO PARA RESOLVER PROBLEMA NA TROCA DE RECURSO DA TAREFA QUE FAZIA SUMIR DA LISTA A TAREFA    */
				   AND h.dt_inclusao = (SELECT MAX(dt_inclusao)
	                                      FROM projetos.tarefa_historico
					                     WHERE cd_atividade = h.cd_atividade
		                                   AND cd_tarefa    = h.cd_tarefa)
				   AND t.dt_exclusao IS NULL
				 ORDER BY UPPER(u.guerra),
				          t.dt_inicio_prog
		";
		$rs = pg_query($db, $sql);
		while ($reg = pg_fetch_array($rs)) 
		{
			$tpl->newBlock('tarefas');
			$tpl->assign('ativ', $n);
			$tpl->assign('link_cadastro', site_url('atividade/tarefa/cadastro'));
			$tpl->assign('codtarefa', $reg['codigo']);
			$tpl->assign('dt_inicio_prog', $reg['dt_inicio_prog']);
			$tpl->assign('programador', $reg['guerra']);
			$tpl->assign('descricao', $reg['resumo']);
			$tpl->assign('status', $reg['status']);
			$tpl->assign('tarefa_tipo', strtolower($reg['fl_tarefa_tipo']));

			if(($reg['st_tarefa'] != 'CONC') and ($reg['st_historico'] != 'CONC'))
			{
				$qt_tarefa_aberta++;
			}

			if(($reg['st_tarefa'] == 'EMAN') and ($reg['st_historico'] == 'EMAN'))
			{
				$qt_tarefa_exec++;
			}
		}
	}
	$tpl->newBlock('qt_tarefa');
	$tpl->assign('qt_tarefa_aberta', $qt_tarefa_aberta);
	$tpl->assign('qt_tarefa_exec', $qt_tarefa_exec);

    // Preenchimento de combos ou dos campos obtidos através de combos
	if ( ($Z == $regOs['cod_atendente']) or (is_null($n)) or ($Z==$regOs['cod_solicitante'])  or ($T=='G') )
	{
		if ($Z != $regOs['cod_atendente'])
		{
			$tpl->assign('ro_solic', 'readonly');
		}

        // Combo Sistemas
		if ($divisao_atendente == "GRI" )
		{
            $tpl->newBlock('cbo_sistemas_gri');
		}
        else
        {
            $tpl->newBlock('cbo_sistemas');
        }
		$tpl->assign('codsis', '');			
		$tpl->assign('nomesis', '');
		
		if($regOs["tipo_ativ"] == "L") 
		{
			$sql = "SELECT * FROM projetos.projetos WHERE codigo = 115 ";
			$rs = pg_query($db, $sql);
			
			while ($reg = pg_fetch_array($rs)) 
			{
				$tpl->newBlock('cbo_sistemas');
				$tpl->assign('codsis', $reg['codigo']);
				$tpl->assign('nomesis', $reg['nome']);
				$tpl->assign('chksis', ($reg['codigo'] == (intval($cbo_sistemas) == "" ? 115 : intval($cbo_sistemas)) ? ' selected' : ''));
			}			
		}
		else 
		{
			if ( $divisao_atendente == "GRI" ) 
			{
					$sql = "SELECT * FROM projetos.projetos  ";
			}
			else 
			{
				$sql = "SELECT * FROM projetos.projetos WHERE area='" . $divisao_atendente . "' and dt_exclusao is null AND fl_atividade = 'S' ORDER BY nome";
			}
			
			$rs = pg_query($db, $sql);
			while ($reg = pg_fetch_array($rs)) 
			{
				if ($divisao_atendente == "GRI") 
				{
					$tpl->newBlock('cbo_sistemas_gri');
				}
				else 
				{
					$tpl->newBlock('cbo_sistemas');
				}
				$tpl->assign('codsis', $reg['codigo']);
				$tpl->assign('nomesis', $reg['nome']);
				$tpl->assign('chksis', ($reg['codigo'] == $cbo_sistemas ? ' selected' : ''));
			}
		}
		
        // Combo Divisão
		$sql = "";
		$sql = $sql . " SELECT distinct ls.codigo as codigo, ";
		$sql = $sql . "        ls.descricao as descricao,    ";
		$sql = $sql . "        ls.divisao   as divisao       ";
		$sql = $sql . " FROM listas            ls,           ";
		$sql = $sql . "      projetos.projetos pp            ";
		$sql = $sql . " WHERE ls.categoria  = 'DIVI'         ";
		$sql = $sql . "       and ls.codigo = pp.area        ";
		$rs = pg_query($db, $sql);
		while ($reg = pg_fetch_array($rs)) {
			$tpl->newBlock('cbo_area');
			$tpl->assign('codare', $reg['codigo']);
			$tpl->assign('nomeare', $reg['descricao']);
			if (isset($aa)) {
				$tpl->assign('chkare', ($reg['codigo'] == $aa ? ' selected' : ''));
			}
			else {
				$tpl->assign('chkare', ($reg['codigo'] == $cbo_area ? ' selected' : ''));
			}
		}
        
        // Combo Solicitante
		$sql = "SELECT * FROM projetos.usuarios_controledi order by nome";
		$rs = pg_query($db, $sql);
		while ($reg = pg_fetch_array($rs)) {
			$tpl->newBlock('cbo_solicitante');
			$tpl->assign('codsol', $reg['codigo']);
			$tpl->assign('nomesol', $reg['nome']);
			if (isset($n)) {
				$tpl->assign('chksol', ($reg['codigo'] == $cbo_solicitante ? ' selected' : ''));
			}
			else {
				$tpl->assign('chksol', ($reg['codigo'] == $Z ? ' selected' : ''));
			}
		}

        // Combo Tipo Atividade (Periodicidade)
		$sql = "SELECT * FROM listas WHERE categoria='TPAT' ORDER BY descricao";
		$rs = pg_query($db, $sql);
		while ($reg = pg_fetch_array($rs)) {
			$tpl->newBlock('cbo_tipo_atividade');
			$tpl->assign('codtpativ', $reg['codigo']);
			$tpl->assign('nometpativ', $reg['descricao']);
			$tpl->assign('chktpativ', ($reg['codigo'] == $cbo_tipo_atividade ? ' selected' : ''));
		}

        // Combo Tipo Manutenção
		$sql = "SELECT * FROM listas WHERE categoria='TPMN' and divisao='$aa' ORDER BY descricao";
		$rs = pg_query($db, $sql);
		while ($reg = pg_fetch_array($rs)) {
			$tpl->newBlock('cbo_tipo_manutencao');
			$tpl->assign('codman', $reg['codigo']);
			$tpl->assign('nomeman', $reg['descricao']);
			$tpl->assign('chkman', ($reg['codigo'] == $cbo_tipo_manutencao ? ' selected' : ''));
		}
        
        // Combo status Atual
		if ($regOs['tipo_ativ'] == 'L')  
		{
			$tpl->newBlock('desc_status_atual');
			$sql = "SELECT descricao FROM listas 
			         WHERE categoria='STAT' 
					   AND (CASE WHEN divisao = 'GC' THEN COAlESCE(tipo,'') <> 'N' ELSE TRUE END)  
					   AND codigo = '".$cbo_status_atual."'";
			$rs = pg_query($db, $sql);
			if ($reg = pg_fetch_array($rs)) 
			{
				$tpl->assign('situacao', $reg['descricao']);
			}
		} 
		else 
		{
			$tpl->newBlock('lista_status_atual');

		    /* ($regOs['status_atual'] == 'AICS') // "COCS";"Aguardando início" GRI
			or ($regOs['status_atual'] == 'COCS') // "COCS";"Atividade concluída" GRI
			or ($regOs['status_atual'] == 'CACS') // "CACS";"Cancelada" GRI
			or ($regOs['status_atual'] == 'AOCS') // "AOCS";"Análise do Solicitante" GRI
			or ($regOs['status_atual'] == 'ASCS') // "ASCS";"Aguardando Solicitante" GRI
			or ($regOs['status_atual'] == 'APCS') // "APCS";"Aprovada pelo Solicitante" GRI */
			
			// GRI
			if ( ($regOs['cod_atendente'] == $Z) and ($aa=="GRI") ) 
			{  
			   
			   	// PARA STATUS CONCLUIDO OU CANCELADO EXIBE NO COMBO APENAS O STATUS ATUAL
			   	if ( $cbo_status_atual == 'COCS' || $cbo_status_atual == 'CACS' ) 
			   	{
					$sql = "

						SELECT * 
						  FROM listas 
                         WHERE categoria = 'STAT' 
						   AND (CASE WHEN divisao = 'GC' THEN COAlESCE(tipo,'') <> 'N' ELSE TRUE END) 
                           AND divisao = '" . $aa . "'
                           AND codigo = '" . $cbo_status_atual . "' 
                      ORDER BY descricao
				
					";
				}

				// PARA STATUS APROVADO PELO SOLICITANTE, EXIBE NO COMBO O STATUS ATUAL E 
				// POSSIBLIDADE DE CANCELAMENTO OU CONCLUSÃO PELO ATENDENTE
				else if ( $cbo_status_atual == 'APCS' ) 
			   	{
					$sql = "

						SELECT * 
                          FROM listas 
                         WHERE categoria='STAT'
                           AND (CASE WHEN divisao = 'GC' THEN COAlESCE(tipo,'') <> 'N' ELSE TRUE END) 						 
                           AND divisao = '" . $aa . "' 
                           AND codigo IN ('APCS', 'COCS', 'CACS' ) 
                      ORDER BY descricao

					";
				}

				// PARA OS OUTROS STATUS, EXIBE NO COMBO STATUS COM EXCEÇÃO PARA 
				// CONCLUÍDO, CANCELADO E APROVADO PELO SOLICITANTE
				else
				{
					$sql = "

						SELECT * 
                          FROM listas 
                         WHERE categoria='STAT' 
						   AND (CASE WHEN divisao = 'GC' THEN COAlESCE(tipo,'') <> 'N' ELSE TRUE END) 
                           AND divisao = '" . $aa . "' 
                           AND NOT codigo IN ('CONC', 'COCS', 'APCS' ) 
                      ORDER BY descricao

					";
				}
	   
		    }
			else if ( 
						($regOs['cod_atendente'] == $Z) 
					and ($regOs['status_atual'] != 'CONC') 
					and ($regOs['status_atual'] != 'CANC') 
					and ($regOs['status_atual'] != 'AGDF') 
			   ) 
			{
				if ( $cbo_status_atual == 'CONC' || $cbo_status_atual == 'CACS' ) 
				{
					$sql = "SELECT * FROM listas 
					         WHERE categoria='STAT' 
							   AND (CASE WHEN divisao = 'GC' THEN COAlESCE(tipo,'') <> 'N' ELSE TRUE END)  
							   AND divisao = '" . $aa . "' 
							 ORDER BY descricao -- 1";
				}
				else
				{
					$sql = "

						SELECT * 
                          FROM listas 
                         WHERE categoria='STAT' 
						   AND (CASE WHEN divisao = 'GC' THEN COAlESCE(tipo,'') <> 'N' ELSE TRUE END) 
                           AND divisao = '" . $aa . "' 
                           AND NOT codigo IN ('CONC') 
                      ORDER BY descricao -- 2

					";
				}
			}

			else if ($regOs['status_atual'] == 'AGDF') // SOLICITADO BETE 24/07/2007
			{
				$sql = "SELECT * 
				          FROM listas 
						 WHERE categoria='STAT' 
						   AND (CASE WHEN divisao = 'GC' THEN COAlESCE(tipo,'') <> 'N' ELSE TRUE END) 
						   and divisao = '" . $aa . "' and codigo IN ('CANC','AGDF') ORDER BY descricao -- 3";
			}
			else 
			{
				$sql = "SELECT * 
				          FROM listas 
						 WHERE categoria='STAT'
						   AND (CASE WHEN divisao = 'GC' THEN COAlESCE(tipo,'') <> 'N' ELSE TRUE END) 
						   AND codigo='$cbo_status_atual' ORDER BY descricao -- 4";
			}
			//echo( $sql ); exit();
			$rs = pg_query($db, $sql);
			while ($reg = pg_fetch_array($rs)) 
			{
				$tpl->newBlock('cbo_status_atual');
				$tpl->assign('codstt', $reg['codigo']);
				$tpl->assign('nomestt', $reg['descricao']);
				$tpl->assign('chkstt', ($reg['codigo'] == $cbo_status_atual ? ' selected' : ''));
			}
		}
//------------------------------------------------------------------------------------------ Combo testador
		$sql = "
					SELECT codigo,
	                       nome		
			          FROM projetos.usuarios_controledi  
			   ";
		if(trim($cbo_testador) != "")
		{
			$sql.= "
					  WHERE codigo IN (SELECT codigo
			                             FROM projetos.usuarios_controledi
										WHERE tipo <> 'X')
						 OR codigo IN (".$cbo_testador.")
			       ";
		}
		else
		{
			$sql.= "
					 WHERE tipo <> 'X'
			       ";		
		}
		$sql.= "
					 ORDER BY nome
			   ";		
		$rs = pg_query($db, $sql);
		$tpl->newBlock('cbo_testador');
		$tpl->assign('cod_testador', '');
		$tpl->assign('nome_testador', '');
		while ($reg = pg_fetch_array($rs)) 
		{
			$tpl->newBlock('cbo_testador');
			$tpl->assign('cod_testador', $reg['codigo']);
			$tpl->assign('nome_testador', $reg['nome']);
			$tpl->assign('chk_testador', ($reg['codigo'] == $cbo_testador ? ' selected' : ''));
		}

		#### COMBO COMPLEXIDADE ####
		if(($regOs['cod_atendente'] == $Z) or (is_null($n))) 
		{
			$sql = "
					SELECT * 
					  FROM listas 
					 WHERE categoria = 'CPLX' 
					 ORDER BY codigo
				   ";
		}
		else 
		{
			$sql = "
					SELECT * 
					  FROM listas 
					 WHERE categoria = 'CPLX' 
					   AND codigo    = '".$cbo_complexidade."' 
					 ORDER BY codigo
				   ";
		}
		$rs = pg_query($db, $sql);
		while ($reg = pg_fetch_array($rs)) 
		{
			$tpl->newBlock('cbo_complexidade');
			$tpl->assign('codcplx', $reg['codigo']);
			$tpl->assign('nomecplx', $reg['descricao']);
			$tpl->assign('chkcplx', ($reg['codigo'] == $cbo_complexidade ? ' selected' : ''));
		}
		
		#### COMBO CRONOGRAMA (12/08/2010) ####
		$qr_sql = "
					SELECT ac.cd_atividade_cronograma, 
					       ac.descricao
                      FROM projetos.atividade_cronograma ac
                     WHERE ac.cd_responsavel = ".$regOs['cod_atendente']."
					   AND ac.dt_exclusao    IS NULL
					   AND 0 = (SELECT COUNT(*)
								  FROM projetos.atividade_cronograma_item aci
								 WHERE aci.cd_atividade_cronograma = ac.cd_atividade_cronograma
								   AND aci.cd_atividade  = ".$regOs['numero']."
								   AND aci.dt_exclusao   IS NULL)
					   AND ac.dt_encerra IS NULL
					 ORDER BY ac.descricao
				   ";
		$ob_resul = pg_query($db, $qr_sql);
		while ($ar_reg = pg_fetch_array($ob_resul)) 
		{
			$tpl->newBlock('combo_cronograma_analista');
			$tpl->assign('cd_atividade_cronograma', $ar_reg['cd_atividade_cronograma']);
			$tpl->assign('descricao', $ar_reg['descricao']);
		}

		#### LISTA CRONOGRAMAS QUE COMTEM A OS (12/08/2010) ####
		$qr_sql = "
					SELECT ac.cd_atividade_cronograma, 
					       aci.cd_atividade_cronograma_item, 
					       (ac.descricao || ' - ' || uc.guerra) AS descricao,
						   ac.dt_encerra
                      FROM projetos.atividade_cronograma ac
					  JOIN projetos.atividade_cronograma_item aci
					    ON aci.cd_atividade_cronograma = ac.cd_atividade_cronograma
					  JOIN projetos.usuarios_controledi uc
					    ON uc.codigo = ac.cd_responsavel
                     WHERE aci.cd_atividade  = ".$regOs['numero']."
					   AND ac.dt_exclusao    IS NULL
					   AND aci.dt_exclusao   IS NULL
					 ORDER BY ac.descricao
				   ";
		$ob_resul = pg_query($db, $qr_sql);
		while ($ar_reg = pg_fetch_array($ob_resul)) 
		{
			$tpl->newBlock('combo_cronograma_analista_incluidos');
			$tpl->assign('cd_atividade_cronograma', $ar_reg['cd_atividade_cronograma']);
			$tpl->assign('cd_atividade_cronograma_item', $ar_reg['cd_atividade_cronograma_item']);
			$tpl->assign('descricao', $ar_reg['descricao']);
			
			if(trim($ar_reg['dt_encerra']) != '')
			{
				$tpl->assign('fl_encerra_cronograma', 'display:none;');
			}
			else
			{
				$tpl->assign('fl_encerra_cronograma', '');
			}
		}		
	}
	else {
//===================================================================================================== Sistema
		if ($cbo_sistemas != '') {
			$sql = "SELECT * FROM projetos.projetos WHERE codigo='$cbo_sistemas'";
			$rs = pg_query($db, $sql);
			$reg = pg_fetch_array($rs);
			$tpl->assign('nomesis', $reg['nome']);
		}
//----------------------------------------------------------------------------------------------------- Divisão
		$sql = "SELECT * FROM listas WHERE categoria='DIVI' and codigo='$cbo_area'";
		$rs = pg_query($db, $sql);
		$reg = pg_fetch_array($rs);
		$tpl->assign('nomeare', $reg['descricao']);
//----------------------------------------------------------------------------------------------------- Solicitante
		$sql = "SELECT * FROM projetos.usuarios_controledi WHERE codigo = $cbo_solicitante";
		$rs = pg_query($db, $sql);
		$reg = pg_fetch_array($rs);
		$tpl->assign('nomesol', $reg['nome']);
//---------------------------------------------------------------------------------------------------- Tipo Atividade
		$sql = "SELECT * FROM listas WHERE categoria='TPAT' and codigo='$cbo_tipo_atividade'";
		$rs = pg_query($db, $sql);
		$reg = pg_fetch_array($rs);
		$tpl->assign('tipoativ', $reg['descricao']);
//---------------------------------------------------------------------------------------------------- Tipo Manutenção
		$sql = "SELECT * FROM listas WHERE categoria='TPMN' and codigo='$cbo_tipo_manutencao' AND divisao='GAP' ";
		$rs = pg_query($db, $sql);
		$reg = pg_fetch_array($rs);
		$tpl->assign('nomeman', $reg['descricao']);
//--------------------------------------------------------------------------------------------------- Status Atual
		$sql = "SELECT * 
		          FROM listas 
				 WHERE categoria='STAT' 
				   AND (CASE WHEN divisao = 'GC' THEN COAlESCE(tipo,'') <> 'N' ELSE TRUE END) 
				   and codigo='$cbo_status_atual' and divisao = '" . $aa . "' ";
		$rs = pg_query($db, $sql);
		$reg = pg_fetch_array($rs);
		$tpl->assign('nomestt', $reg['descricao']);
//--------------------------------------------------------------------------------------------------- Atendente da Atividade 
		$sql = "SELECT * FROM projetos.usuarios_controledi WHERE codigo = $cbo_analista";
		$rs = pg_query($db, $sql);
		$reg = pg_fetch_array($rs);
		$tpl->assign('nomeana', $reg['nome']);
//--------------------------------------------------------------------------------------------------- Complexidade
		$sql = "SELECT * FROM listas WHERE categoria='CPLX' and codigo='$cbo_complexidade'";
		$rs = pg_query($db, $sql);
		$reg = pg_fetch_array($rs);
		$tpl->assign('nomecplx', $reg['descricao']);
	}
//------------------------------------------------------------------------------------ Finaliza construção da página

	pg_close($db);
	$tpl->printToscreen();
	require_once('inc/ajaxobject.php');
?>