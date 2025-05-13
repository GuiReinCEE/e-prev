<?php
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

	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	
	//header( 'location:'.base_url().'index.php/ecrm/atendimento_protocolo');
	
	$gerencia_tela_nova = array('GI', 'GB', 'GAP', 'GA', 'GF', 'GC', 'GRI');
	
	if((in_array(trim($_REQUEST["aa"]), $gerencia_tela_nova)) OR trim($_REQUEST["TA"]) == 'L')
	{
		$url = base_url().'index.php/atividade/atividade_solicitacao/index/'.trim($_REQUEST["aa"]).'/'.intval($_REQUEST["n"]);
				
		$url .= '/'.trim($_REQUEST["EMP_GA"]);
	
		$url .= '/'.trim($_REQUEST["RE_GA"]);
	
		$url .= '/'.trim($_REQUEST["SEQ_GA"]);
	
		$url .= '/'.trim($_REQUEST["CD_ATENDIMENTO_GA"]);	
	
		header( 'location:'.$url);
	}
	
	#### STATUS DE CONCLUIDO ####
	$ar_status_concluido = Array('COSB','COST','CONC','CONF','COGA','COCS','GCCO','COGJ','SGCO');	
	
	
	#### SETA VARIAVEL $_REQUEST["TA"] ####
	if(intval($_REQUEST["n"]) > 0)
	{
		$qr_sql = " 
					SELECT CASE WHEN tipo = 'I' 
					            THEN 'A'
								ELSE tipo
						   END AS tipo
					  FROM projetos.atividades 
		             WHERE numero = ".intval($_REQUEST["n"])."
		          ";
		$ob_resul = pg_query($qr_sql);
		$ar_reg = pg_fetch_array($ob_resul);
		if($ar_reg["tipo"] != $_REQUEST["TA"])
		{
			echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL='.base_url()."sysapp/application/migre/cad_atividade_solic.php?n=".intval($_REQUEST["n"])."&aa=".$_REQUEST["aa"]."&TA=".$ar_reg["tipo"].'">';
			exit;		
		}
	}
	
	#### SETA VARIAVEL $_REQUEST["aa"] ####
	if(intval($_REQUEST["n"]) > 0)
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
			echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL='.base_url()."sysapp/application/migre/cad_atividade_solic.php?n=".intval($_REQUEST["n"])."&aa=".$ar_reg["area"]."&TA=".$_REQUEST["TA"].'">';
			exit;			
		}
	}	

	#### VERIFICA SE GERENCIA TRABALHA COM ATIVIDADES ####
	if(($_REQUEST["TA"] != "L") and (intval($_REQUEST["n"]) == 0))
	{
		$tthis->load->model("projetos/Divisoes");
		$fl_atividade = $tthis->Divisoes->permite_nova_atividade($_REQUEST['aa']);

		if($fl_atividade==false)	
		{
			echo '
					<script>
						alert("A '.$_REQUEST['aa'].' não permite a abertura de Atividade.");
					</script>
					<META HTTP-EQUIV="Refresh" CONTENT="0;URL='.site_url("atividade/minhas").'">
				 ';
			exit;
		}
	}

	$fl_ga_informacoes_complementares = false;

	// Carregar input com o próximo dia útil para validar o prazo esperado da GRI
	$sql = "SELECT to_char( funcoes.dia_util( 'DEPOIS', CURRENT_DATE, 1 ), 'DD/MM/YYYY' ) AS proximo_dia_util;";
	$result = pg_query($db, $sql);
	$sql = "";
	if( $row = pg_fetch_array($result) )
	{
		$proximo_dia_util = $row['proximo_dia_util'];
	}

	if (trim($aa) == "")
	{
		$aa = $_SESSION['D'];
	}
	
    // ----------------------------------------------------------------------------
	if(intval($_REQUEST["n"]) == 0)
	{
		#### INCLUSÃO ####
		$cbo_area = $D;
		$cbo_analista = $U;
		$cbo_solicitante = $U;
		$cbo_status_atual = 'AINI'; 
		$cbo_tipo_atividade = 'I';

		// QueryString $tm: 'S' quando atividade de suporte
		if (strtoupper($tm) == 'S')
		{
			$tpl = new TemplatePower('tpl/tpl_frm_nova_atividade_solic_suporte.html');
		}
		else
		{
			$tpl = new TemplatePower('tpl/tpl_frm_nova_atividade_solic.html');
		}
		
		$tpl->prepare();
		$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
		include_once('inc/skin.php');

		$tpl->assign('usuario', $N);
		$tpl->assign('divsao', $D);
		$tpl->assign('aa', $aa);
		$tpl->newBlock('ativ_nova');
		$tpl->assign('ta', $TA);
		$tpl->assign('negocio_fim', '0');
		$tpl->assign('prejuizo', '0');
		$tpl->assign('legislacao', '0');
		$tpl->assign('cliente_externo', '0');
		$tpl->assign('concorrencia', '0');
		$tpl->assign('usuario', $N);
		$tpl->assign('divsao', $D);
		
		$tpl->newBlock('hiddens');
		$tpl->assign('aa', $aa);
		$tpl->assign('status_atual', 'Nova Atividade');
		$tpl->assign("proximo_dia_util", $proximo_dia_util);
	}
	else 
	{
		#### ALTERAÇÃO ####
		$sql = " 
				SELECT numero,  
					   area,                                          
					   divisao,                                       
					   sistema,                                       
					   cod_solicitante,                               
					   cod_testador,                                  
					   cd_recorrente,                                 
					   tipo_solicitacao,                              
					   to_char(dt_cad, 'DD/MM/YYYY HH:MI') as dt_cad,  
					   tipo as tipo_ativ,                              
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
					   titulo,		 									
					   cd_empresa,		 								
					   cd_registro_empregado,		 					
					   cd_sequencia AS seq_dependencia,		 							
					   cd_atendimento,		 							
					   to_char(dt_inicio_prev, 'dd/mm/yyyy') 	as dt_inicio_prev, 	
					   to_char(dt_fim_prev, 'dd/mm/yyyy')    	as dt_fim_prev,    	
					   to_char(dt_inicio_real, 'dd/mm/yyyy') 	as dt_inicio_real, 	
					   to_char(dt_fim_real, 'dd/mm/yyyy')    	as dt_fim_real,    	
					   to_char(dt_env_teste, 'dd/mm/yyyy')   	as dt_env_teste,	
					   to_char(dt_limite, 'dd/mm/yyyy')   		as dt_limite,   	
					   to_char(dt_limite, 'yyyy/mm/dd')   		as dt_limite_ymd,  	
					   to_char(dt_retorno, 'dd/mm/yyyy')  		as dt_retorno,   	
					   to_char(dt_limite_testes, 'dd/mm/yyyy')  		as dt_limite_testes,   	
					   opt_grafica,		
					   opt_eletronica,	
					   opt_evento,		
					   opt_anuncio,		
					   opt_folder,		
					   opt_mala,		
					   opt_cartaz,		
					   opt_cartilha,	
					   opt_site,		
					   opt_outro,		
					   cores,			
					   formato,			
					   gramatura,		
					   quantia,			
					   custo,			
					   cc,				
					   pacs,			
					   patracs,			
					   nacs,			
					   cacs,			
					   lacs,			
					   dacs,			
					   forma,			
					   tp_envio,		
					   solicitante,		
		               cd_plano, 
                       cd_atividade_origem, 
					   fl_abrir_encerrar, 
					   cd_usuario_abrir_ao_encerrar, 
                       descricao_abrir_ao_encerrar			
		          FROM projetos.atividades   		
		         WHERE numero = ".intval($n)."  
		       ";

		$rsOs = pg_query($db, $sql);
		$regOs = pg_fetch_array($rsOs);

		if(!isset($_REQUEST['aa']))
		{
			if(isset($regOs['area']))
			{
				$aa=$regOs['area'];
			}
		}

		// ---------------------------------------------------------------------------------------------------

		if($_REQUEST['imp'] == "S")
		{
			$tpl = new TemplatePower('tpl/tpl_frm_atividade_solic_imprime.html');
		}
		else
		{
			$tpl = new TemplatePower('tpl/tpl_frm_atividade_solic.html');
		}
		
		$tpl->prepare();
		$fl_readonly = false;
		
		#### BOOTSTRAP ####
		if(preg_match('/(?i)MSIE [1-7].0/',$_SERVER['HTTP_USER_AGENT']))
		{
			$tpl->assignGlobal('style_bootstrap', '<link href="'.base_url().'bootstrap/css/bootstrap-ie.css" rel="stylesheet" media="screen">');
		}
		else
		{
			$tpl->assignGlobal('style_bootstrap', '<link href="'.base_url().'bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">');
		}
			
		
		
	
		#### SE FOR LEGAL NÃO PERMITE SALVAR ####
		if(strtoupper($regOs['tipo_ativ']) == 'L')
		{
			$tpl->assignGlobal('fl_conclusao_botao', "disabled");
			$fl_readonly = true;
		}

		if(in_array($regOs['status_atual'],$ar_status_concluido))
		{
			$tpl->assignGlobal('fl_conclusao_botao', "disabled");
			$fl_readonly = true;		
		}
		
		$tpl->assign("proximo_dia_util", $proximo_dia_util);

        // Não permite alterações a não ser Aguardando Início:
		$sql = "
				SELECT descricao,
                       CASE WHEN valor = 1 THEN 'blue'
							WHEN valor = 2 THEN 'brown'
							WHEN valor = 3 THEN 'red'
							WHEN valor = 4 THEN '#FF6A00'
							WHEN valor = 5 THEN '#4169E1'
							ELSE 'green'
					   END AS status_cor
				  FROM listas 
				 WHERE categoria = 'STAT' 
				   AND codigo    = '".$regOs['status_atual']."' 
				   AND divisao   = '".$aa."' ";
		$rs = pg_query($db, $sql);
		$reg = pg_fetch_array($rs);
		$tpl->assign('status_cor', $reg['status_cor']);
		$tpl->assign('status_atual', $reg['descricao']);
		
		#### VERIFICA O STATUS DA GA PARA COMPLEMENTAR INFORMAÇÕES ####
		if(($regOs['area'] == 'GA') and ($regOs['status_atual'] == 'ICGA') )
		{
			$fl_ga_informacoes_complementares = true;
		}		
		
        // Não permite alterações a não ser Aguardando Início:
		if (
                   ($regOs['status_atual'] == 'EMAN') 
                or ($regOs['status_atual'] == 'EANA') 
                or ($regOs['status_atual'] == 'CONC') 
                or ($regOs['status_atual'] == 'EMST') 
                or ($regOs['status_atual'] == 'COST')
            )
        {
			$tpl->assign('ro_solic', 'readonly');
			$tpl->assign('dis_solic', 'disabled'); 

            $fl_readonly = true;
		}

        // Não permite alterações a não ser do solicitante ou atendente:
		if ( ($regOs['cod_atendente'] == $Z) or (is_null($n)) or ($regOs['cod_solicitante']==$Z) ) {}
		else
		{
			$tpl->assign( 'ro_solic' , 'readonly' );
			$tpl->assign( 'dis_solic' , 'disabled' );
			$fl_readonly = true;
		}

        // ---------------------------------------------------------------------------------
		$tpl->assign('n', $n);
		$tpl->assign("link_anexo", site_url('atividade/atividade_anexo/index/'.$n.'/'.$aa));
		$tpl->assign("link_acompanhamento", site_url('atividade/atividade_acompanhamento/index/'.$n.'/'.$aa));
		$tpl->assign("link_hsitorico", site_url('atividade/atividade_historico/index/'.$n.'/'.$aa));
		$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
		include_once('inc/skin.php');

		$tpl->assign('usuario', $N);
		$tpl->assign('divsao', $D);
		$tpl->assign('ta', $TA);
		$tpl->assign('aa', $aa);
		$tpl->assign( "site_url", site_url());
		$tpl->assign( "url_lista", ($TA == 'L' ? "legal" : "minhas") );

		$cbo_sistemas        	= $regOs['sistema'];
		$cbo_divisao_atendente	= $regOs['area'];
		$cbo_area            	= $regOs['area'];
		$divisao_solicitante   	= $regOs['divisao'];
		$cbo_solicitante     	= $regOs['cod_solicitante'];
		$cbo_tipo_manutencao 	= $regOs['tipo_solicitacao'];
		$cbo_status_atual    	= $regOs['status_atual'];
		$cbo_analista        	= $regOs['cod_atendente'];
		$cbo_complexidade    	= $regOs['complexidade'];
		$cbo_tipo_atividade  	= $regOs['tipo_ativ'];
		$patrocinadora 			= $regOs['cd_empresa'];
		$registro_empregado 	= $regOs['cd_registro_empregado'];
		$sequencia				= $regOs['seq_dependencia'];
		$cd_atendimento_ga		= $regOs['cd_atendimento'];
		$dt_retorno				= $regOs['dt_retorno'];
		$v_fdap					= $regOs['forma'];
		$v_fedap				= $regOs['tp_envio'];
		$v_sdap					= $regOs['solicitante'];
		$v_plano				= $regOs['cd_plano'];

        $tpl->assign("dt_cad", $regOs["dt_cad"]);
		$tpl->assign("numero_os", $regOs['numero']);
		$tpl->assign("titulo", $regOs['titulo']);

		// -------------------------------------------------------------------------------------------	  
	}
	$tpl->assign('status_anterior', 	$cbo_status_atual);

	#### VALIDA TRANSFERENCIA DE DIVISÃO ####
	// ATIVIDADE DIFERENTES DE LEGAL //
	if( ($TA != 'L') and (trim($n)!= "") )
	{
		$qr_select = "
						SELECT CASE WHEN CURRENT_DATE >= (CAST(DATE_TRUNC('month', dt_cad) + '1 month' AS date) - 1)
									THEN 'N'
									ELSE 'S'
							   END AS fl_transferir
						  FROM projetos.atividades
						 WHERE numero = " . intval($n);

		$ob_resul = pg_query($db, $qr_select);
		$ob_reg = pg_fetch_object($ob_resul);
		if($ob_reg->fl_transferir == 'N')
		{
			$tpl->assign('fl_transferir', 'disabled');
		}
	}

	if ($Z != $regOs['cod_solicitante']) 
	{
		$tpl->assign('dis_cbo_solic', 'disabled');
	}

//============================================================= Preenchimento de combos ou dos campos obtidos através de combos

	#### EXIBE OPÇÕES PARCIPANTE ####
	if((
			#### ALTERACAO ####
			($regOs["area"] == "GB") or
			($regOs["area"] == "GF") or
			($regOs["area"] == "GJ") or
			($regOs["area"] == "GAP") or 
			($regOs["divisao"] == "GAP") or 
			($regOs["area"] == "GP") or 
			($regOs["divisao"] == "GP") or 			
			(intval($regOs['cd_registro_empregado']) > 0)
	  )
	  or
	  (
			#### INCLUSAO ####
			($_REQUEST["aa"] == "GB") or
			($_REQUEST["aa"] == "GF") or
			($_REQUEST["aa"] == "GJ") or
			($_REQUEST["aa"] == "GAP") or	  
			($_REQUEST["aa"] == "GP") or
			($_SESSION['D'] == "GB") or
			($_SESSION['D'] == "GF") or
			($_SESSION['D'] == "GJ") or
			($_SESSION['D'] == "GAP") or	  
			($_SESSION['D'] == "GP")	  
	  ))
	{
		#### GAP ATENDIMENTO ####
		if(trim($_REQUEST['EMP_GA']) != "")
		{
			$patrocinadora = $_REQUEST['EMP_GA'];
		}
		if(trim($_REQUEST['RE_GA']) != "")
		{
			$registro_empregado = $_REQUEST['RE_GA'];
		}
		if(trim($_REQUEST['SEQ_GA']) != "")
		{
			$sequencia = $_REQUEST['SEQ_GA'];
		}
		if(trim($_REQUEST['CD_ATENDIMENTO_GA']) != "")
		{
			$cd_atendimento_ga = $_REQUEST['CD_ATENDIMENTO_GA'];
		}
		if(trim($_REQUEST['FORMA_GA']) != "")
		{
			switch ($_REQUEST['FORMA_GA']) 
			{
				case 'E': $v_fdap = "FAP2"; 
				          break;
				case 'T': $v_fdap = "FAP3"; 
				          break;
				case 'P': $v_fdap = "FAP4"; 
				          break;
				case 'C': $v_fdap = "FAP5"; 
				          break;
				default : $v_fdap = $_REQUEST['FORMA_GA']; 
			}
		}

		$tpl->newBlock('re_d');

		if( $v_fedap=='1' ){ $tpl->assign('fe_correio', ' selected '); }
		if( $v_fedap=='2' ){ $tpl->assign('fe_central', ' selected '); }
		if( $v_fedap=='3' ){ $tpl->assign('fe_email', ' selected '); }

		$tpl->assign('cd_registro_empregado', $registro_empregado);
		$tpl->assign('sequencia', $sequencia);
		$tpl->assign('cd_atendimento', $cd_atendimento_ga);
		$tpl->assign('dt_retorno', $dt_retorno);
		
		$sql = "SELECT cd_empresa, sigla AS nome_reduz FROM patrocinadoras order by nome_reduz";
		$rs = pg_query($db, $sql);
		$tpl->newBlock('cbo_patrocinadora');
		$tpl->assign('cd_patr', '');
		$tpl->assign('nome_patr', '');	
		$tpl->assign('chk_patr', ('' == trim($patrocinadora) ? ' selected' : ''));		
		while ($reg = pg_fetch_array($rs)) 
		{
			$tpl->newBlock('cbo_patrocinadora');

			$tpl->assign('cd_patr', $reg['cd_empresa']);
			$tpl->assign('nome_patr', $reg['nome_reduz']);
			$tpl->assign('chk_patr', ($reg['cd_empresa'] == $patrocinadora ? ' selected' : ''));
		}

		//---- Lista Público Plano
		$sql = "SELECT * FROM planos WHERE cd_plano <> 0 ORDER BY descricao";
		$rs = pg_query($db, $sql);
		$tpl->newBlock('cbo_plan');
		$tpl->assign('cd_plan', '');
		$tpl->assign('nome_plan', '');
		$tpl->assign('chk_plan', ('0' == trim($v_plano) ? ' selected' : ''));
		while ($reg = pg_fetch_array($rs)) 
		{
			$tpl->newBlock('cbo_plan');
			$tpl->assign('cd_plan', $reg['cd_plano']);
			$tpl->assign('nome_plan', $reg['descricao']);
			$tpl->assign('chk_plan', ($reg['cd_plano'] == $v_plano ? ' selected' : ''));
		}

		$sql = "SELECT * FROM listas WHERE categoria='FDAP' ORDER BY descricao";
		$rs = pg_query($db, $sql);

		$tpl->newBlock('cbo_fdap');
		$tpl->assign('cd_fdap', '');
		$tpl->assign('nome_fdap', '');
		$tpl->assign('chk_fdap', ('' == trim($v_fdap) ? ' selected' : '')); // forma
		while ($reg = pg_fetch_array($rs)) 
		{
			$tpl->newBlock('cbo_fdap');
			$tpl->assign('cd_fdap', $reg['codigo']);
			$tpl->assign('nome_fdap', $reg['descricao']);
			$tpl->assign('chk_fdap', ($reg['codigo'] == $v_fdap ? ' selected' : '')); // forma
		}
		$sql = "SELECT * FROM listas WHERE categoria='SDAP' ORDER BY descricao";
		$rs = pg_query($db, $sql);
		$tpl->newBlock('cbo_sdap');
		$tpl->assign('cd_sdap', $reg['codigo']);
		$tpl->assign('nome_sdap', $reg['descricao']);
		$tpl->assign('chk_sdap', ($reg['codigo'] == $v_sdap ? ' selected' : '')); // solicitante		
		while ($reg = pg_fetch_array($rs)) 
		{
			$tpl->newBlock('cbo_sdap');
			$tpl->assign('cd_sdap', $reg['codigo']);
			$tpl->assign('nome_sdap', $reg['descricao']);
			$tpl->assign('chk_sdap', ($reg['codigo'] == $v_sdap ? ' selected' : '')); // solicitante
		}
		
		$ar_part_reg = "";
		if(intval($registro_empregado) > 0)
		{
			$qr_sql = "
						SELECT p.nome,
						       p.endereco,
						       p.nr_endereco,
						       p.complemento_endereco,
						       p.bairro,
						       p.cidade,
						       p.unidade_federativa AS uf,
						       TO_CHAR(p.cep, 'FM00000') || '-' || TO_CHAR(p.complemento_cep,'FM000') AS cep,
							   p.ddd,
							   p.telefone,
						       p.ddd_celular,
						       p.celular,							   
							   p.email,
							   p.email_profissional
						  FROM participantes p
						 WHERE p.cd_empresa            = ".intval($patrocinadora)."
						   AND p.cd_registro_empregado = ".intval($registro_empregado)."
						   AND p.seq_dependencia       = ".intval($sequencia)."
					  ";
			$part_resul = pg_query($db, $qr_sql);
			$ar_part_reg = pg_fetch_array($part_resul);
			$tpl->newBlock('dados_participante');
			$tpl->assign('nome_participante', $ar_part_reg['nome']);
			$tpl->assign('endereco_participante', $ar_part_reg['endereco'].", ".$ar_part_reg['nr_endereco']."/".$ar_part_reg['complemento_endereco']." - ".$ar_part_reg['bairro']." - ".$ar_part_reg['cep']." - ".$ar_part_reg['cidade']." - ".$ar_reg['uf']);
			$tpl->assign('telefone_participante1', $ar_part_reg['ddd']." - ".$ar_part_reg['telefone']);
			$tpl->assign('telefone_participante2', $ar_part_reg['ddd_celular']." - ".$ar_part_reg['celular']);
			$tpl->assign('email_participante', $ar_part_reg['email']." / ".$ar_part_reg['email_profissional']);				
		}
	
	}
	// -@-------------------------------------------------------------------------------------------
	if ($aa == 'GRI')
	{
		$tpl->newBlock('acs');

        $datahora = explode( " ", $regOs["dt_cad"] );
        $data = explode( "/", $datahora[0] );
        $mesano = $data[1].$data[2];

        $tpl->assign("gi_mesmo_mes_extension", ($mesano==date("mY"))
                                                ?""
                                                :"disabled" );

		$tpl->assign('descricao', $regOs['descricao']);
		
		if ($regOs['opt_grafica'] == 'S') { $tpl->assign('chk_grafica', 'checked'); }
		if ($regOs['opt_eletronica'] == 'S') { $tpl->assign('chk_eletronica', 'checked'); }
		if ($regOs['opt_evento'] == 'S') { $tpl->assign('chk_evento', 'checked'); }
		if ($regOs['opt_anuncio'] == 'S') { $tpl->assign('chk_anuncio', 'checked'); }
		if ($regOs['opt_folder'] == 'S') { $tpl->assign('chk_folder', 'checked'); }
		if ($regOs['opt_mala'] == 'S') { $tpl->assign('chk_mala', 'checked'); }
		if ($regOs['opt_cartaz'] == 'S') { $tpl->assign('chk_cartaz', 'checked'); }
		if ($regOs['opt_cartilha'] == 'S') { $tpl->assign('chk_cartilha', 'checked'); }
		if ($regOs['opt_site'] == 'S') { $tpl->assign('chk_site', 'checked'); }
		if ($regOs['opt_outro'] == 'S') { $tpl->assign('chk_outro', 'checked'); }
		$tpl->assign('cores', $regOs['cores']);
		$tpl->assign('formato', $regOs['formato']);
		$tpl->assign('gramatura', $regOs['gramatura']);
		$tpl->assign('quantia', $regOs['quantia']);
		$tpl->assign('custo', $regOs['custo']);
		$tpl->assign('cc', $regOs['cc']);
		$v_pacs = $regOs['pacs'];
		$v_patracs = $regOs['patracs'];
		$v_nacs = $regOs['nacs'];
		$v_cacs = $regOs['cacs'];
		$v_lacs = $regOs['lacs'];
		$v_dacs = $regOs[' dacs'];
		
		//echo $regOs['cod_solicitante'];
		$sql = "SELECT cd_empresa, sigla AS nome_reduz FROM patrocinadoras order by nome_reduz";
		$rs = pg_query($db, $sql);
		while ($reg = pg_fetch_array($rs))
		{
			$tpl->newBlock('cbo_patroc');
			$tpl->assign('cd_patr', $reg['cd_empresa']);
			$tpl->assign('nome_patr', $reg['nome_reduz']);
			$tpl->assign('chk_patr', ($reg['cd_empresa'] == $v_patracs ? ' selected' : ''));
		}
		// ------------------------------------------------------------------------------------------- Lista Público Participante
		$sql = "SELECT * FROM listas WHERE categoria='PACS' ORDER BY descricao";
		$rs = pg_query($db, $sql);
		while ($reg = pg_fetch_array($rs)) {
			$tpl->newBlock('cbo_pacs');
			$tpl->assign('cd_pacs', $reg['codigo']);
			$tpl->assign('nome_pacs', $reg['descricao']);
			$tpl->assign('chk_pacs', ($reg['codigo'] == $v_pacs ? ' selected' : ''));
		}
		// ------------------------------------------------------------------------------------------- Lista Público Comunidade
		$sql = "SELECT * FROM listas WHERE categoria='CACS' ORDER BY descricao";
		$rs = pg_query($db, $sql);
		while ($reg = pg_fetch_array($rs)) {
			$tpl->newBlock('cbo_cacs');
			$tpl->assign('cd_cacs', $reg['codigo']);
			$tpl->assign('nome_cacs', $reg['descricao']);
			$tpl->assign('chk_cacs', ($reg['codigo'] == $v_cacs ? ' selected' : ''));
		}
		// ------------------------------------------------------------------------------------------- Lista Público Plano
		$sql = "SELECT * FROM planos ORDER BY descricao";
		$rs = pg_query($db, $sql);
		while ($reg = pg_fetch_array($rs)) {
			$tpl->newBlock('cbo_nacs');
			$tpl->assign('cd_nacs', $reg['cd_plano']);
			$tpl->assign('nome_nacs', $reg['descricao']);
			$tpl->assign('chk_nacs', ($reg['cd_plano'] == $v_nacs ? ' selected' : ''));
		}
		// ------------------------------------------------------------------------------------------- Lista Público Localização
		$sql = "SELECT * FROM listas WHERE categoria='LACS' ORDER BY descricao";
		$rs = pg_query($db, $sql);
		while ($reg = pg_fetch_array($rs)) {
			$tpl->newBlock('cbo_lacs');
			$tpl->assign('cd_lacs', $reg['codigo']);
			$tpl->assign('nome_lacs', $reg['descricao']);
			$tpl->assign('chk_lacs', ($reg['codigo'] == $v_lacs ? ' selected' : ''));
		}

        // Lista Público Distribuição
		$sql = "SELECT * FROM listas WHERE categoria='DACS' ORDER BY descricao";
		$rs = pg_query($db, $sql);
		while ($reg = pg_fetch_array($rs)) {
			$tpl->newBlock('cbo_dacs');
			$tpl->assign('cd_dacs', $reg['codigo']);
			$tpl->assign('nome_dacs', $reg['descricao']);
			$tpl->assign('chk_dacs', ($reg['codigo'] == $v_dacs ? ' selected' : ''));
		}

		
		if(!isset($_REQUEST['aa']))
		{
			if(isset($regOs['area']))
			{
				$divisao_do_analista = $regOs['area'];
			}
		}
		else
		{
			$divisao_do_analista = $_REQUEST['aa'];
		}		
        // Lista Analista
		$sql = "
            SELECT * 
              FROM projetos.usuarios_controledi
             WHERE tipo in ('N','G','U') 
              AND (divisao = '" . $divisao_do_analista . "' OR divisao_ant = '" . $divisao_do_analista . "' OR '' = '".$divisao_do_analista."')
          ORDER BY nome
        ";
		$rs = pg_query($db, $sql);
		while ($reg = pg_fetch_array($rs))
        {
			$tpl->newBlock('cbo_analista_acs');
			$tpl->assign('codana', $reg['codigo']);
			$tpl->assign('nomeana', $reg['nome']);
			$tpl->assign('chkana', ($reg['codigo'] == $cbo_analista ? ' selected' : ''));
		}

        // -------------------------------------------------
		$tpl->newBlock('GRI_prazo_esperado');

		if( $regOs['dt_limite']=="" && $regOs['cod_solicitante']==$Z )
		{
			$tpl->assign('mensagem_prazo_gri_data_nao_preenchida', '');
			$tpl->assign('mensagem_prazo_gri_data_preenchida', 'none');
		}
		
		if( $regOs['dt_limite']=="" && $regOs['cod_atendente']==$Z )
		{
			$tpl->assign('mensagem_prazo_gri_data_nao_preenchida', 'none');
			$tpl->assign('mensagem_prazo_gri_data_preenchida', '');
		}
		
		if( $regOs['dt_limite']!="" )
		{
			$tpl->assign('mensagem_prazo_gri_data_nao_preenchida', 'none');
			$tpl->assign('mensagem_prazo_gri_data_preenchida', '');
		}
		
		$tpl->assign('dt_limite', $regOs['dt_limite']);
        if($regOs['cod_atendente']!=$Z)
        //if($regOs['cod_solicitante']!=$Z)
        {
            $tpl->assign('readonly_dt_limite_gri', 'readonly');
        }
		elseif( $regOs['dt_limite']!="" )
		//elseif( date('Y/m/d')>$regOs['dt_limite_ymd'] )
		{
			$tpl->assign('readonly_dt_limite_gri', 'readonly');
		}

        // -------------------------------------------------
		$tpl->assign('descr_solic', 'Observações');
	}
	else
		{
		$tpl->newBlock('nao_acs');
        $datahora = explode( " ", $regOs["dt_cad"] );
        $data = explode( "/", $datahora[0] );
        $mesano = $data[1].$data[2];
        $tpl->assign("gi_mesmo_mes_extension", ($mesano==date("mY"))
                                                ?""
                                                :"disabled" );
		$tpl->assign('descricao', $regOs['descricao']);
		
		$tpl->assign('dt_limite', $regOs['dt_limite']);
		$tpl->assign('solucao', ( $U==$regOs['atendente'] ? $regOs['solucao'] : str_replace(chr(13).chr(10), "<br>", $regOs['solucao']) ) );
		
	
		if(($aa != "GF") and ($aa != "GB") and ($aa != "GC") and ($aa != "GA") and ($aa != "GJ"))
		{
			#### CRIA CAMPO JUSTIFICATIVA ####
			$tpl->newBlock('campo_justificativa');
			$tpl->assign('problema', ( $U==$regOs['atendente'] ? $regOs['problema'] : str_replace(chr(13).chr(10), "<br>", $regOs['problema']) ) );
		}
	}
//-@-------------------------------------------------------------------------------------------

	//if ($Z != $regOs['cod_solicitante']) 
	if ( $fl_readonly )
	{
		$tpl->assign('ro_solic', 'readonly');
		$tpl->assign('dis_solic', 'disabled');
	}
// Lista Divisão Atendente da Atividade
//	if ($TA == 'L') {
		$sql = "
            SELECT distinct codigo, nome AS descricao 
              FROM projetos.divisoes 
             WHERE tipo IN ('ASS', 'DIV')
			   AND fl_atividade = 'S'
        ";
//	}
//	else {
//		$sql = "";
//		$sql = $sql . " SELECT 	distinct ls.codigo as codigo, 	";
//		$sql = $sql . "        	ls.descricao as descricao,   	";
//		$sql = $sql . "        	ls.divisao   as divisao      	";
//		$sql = $sql . " FROM 	listas 		ls,           		";
//		$sql = $sql . "			listas		ls1,				";
//		$sql = $sql . "      	projetos.projetos pp        	";
//		$sql = $sql . " WHERE 	ls.categoria  	= 'DIVI'     	";
//		$sql = $sql . "		and	ls1.categoria 	= 'TPAT'		";
//		$sql = $sql . "     and ls.codigo 		= pp.area      	";
//		$sql = $sql . "     and ls1.divisao		= pp.area      	";
//		$sql = $sql . "		and ls.codigo 		= ls1.divisao  	";
//	}
	$rs = pg_query($db, $sql);
	while ($reg = pg_fetch_array($rs)) 
	{
		$tpl->newBlock('cbo_area');
		$tpl->assign('codare', $reg['codigo']);
		$tpl->assign('nomeare', $reg['descricao']);
		if (is_null($n)) 
		{
			$tpl->assign('chkare', ($reg['codigo'] == $aa ? ' selected' : ''));
		}
		else 
		{
			//if ($cbo_divisao_solicitante == $aa) 
			//{
				$tpl->assign('chkare', ($reg['codigo'] == $cbo_divisao_atendente ? ' selected' : ''));
			/*}
			else 
			{
				$tpl->assign('chkare', ($reg['codigo'] == $aa ? ' selected' : ''));
			}*/
		}
	}
//--------------------------------------------------------------------------------------------- Lista Solicitante
	$sql = " SELECT codigo,
					nome
			   FROM projetos.usuarios_controledi 
			  WHERE divisao <> 'SNG'
			     AND tipo <> 'X'
			   ORDER BY nome";

	$tpl->newBlock('cbo_solicitante');
	$tpl->assign('codsol', '/');
	$tpl->assign('nomesol', '');
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
	
	#### INFOS ATIVIDADE LEGAL ####
	if($TA == 'L')
	{
        if(intval($_REQUEST["n"]) > 0)
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
		}
	}
	
//--------------------------------------------------------------------------------------------- Lista Tipo Atividade (Periodicidade)
	if ($TA == 'L') 
	{
		$sql = "SELECT * FROM listas WHERE categoria='TPAT' and divisao = '*' ORDER BY descricao";
	}
	else 
	{
		
		if (isset($aa)) 
		{
			$fl_suporte = " AND codigo <> 'S' ";
			if(trim($tm) == "s")
			{
				$fl_suporte = " AND codigo = 'S' ";
			}
			
			if($cbo_tipo_atividade == "S")
			{
				$fl_suporte = "";
			}

			$sql = "SELECT * 
			          FROM listas 
					 WHERE categoria = 'TPAT' 
					   AND divisao   = '".$aa."'
					   ".$fl_suporte."
					 ORDER BY descricao";
		}
		else 
		{
			$sql = "SELECT * FROM listas WHERE categoria='TPAT' ORDER BY descricao";
		}
		
		if($aa == "GA")
		{
			$tpl->assignGlobal('fl_exibe_cbo_periodicidade', 'style="display:none;"');
		}
	}
	$rs = pg_query($db, $sql);
	while ($reg = pg_fetch_array($rs)) 
	{
		$tpl->newBlock('cbo_tipo_atividade');
		$tpl->assign('codtpativ', $reg['codigo']);
		$tpl->assign('nometpativ', $reg['descricao']);
		$tpl->assign('chktpativ', ($reg['codigo'] == $cbo_tipo_atividade ? ' selected' : ''));
	}
//------------------------------------------------------------------------------------------- Lista Tipo Manutenção
	if ($TA == 'L') 
	{
		$sql = "SELECT * FROM listas WHERE categoria='TPMN' and divisao = '*' ORDER BY descricao";
	}
	else 
	{
		if (isset($aa)) 
		{
			$sql = "SELECT * FROM listas WHERE categoria='TPMN' and divisao = '" . $aa . "' AND dt_exclusao IS null ORDER BY descricao";
		}
		else 
		{
			$sql = "SELECT * FROM listas WHERE categoria='TPMN' AND dt_exclusao IS null ORDER BY descricao";
		}
	}
	$rs = pg_query($db, $sql);
	while ($reg = pg_fetch_array($rs)) 
	{
		$tpl->newBlock('cbo_tipo_manutencao');
		$tpl->assign('codman', $reg['codigo']);
		$tpl->assign('nomeman', $reg['descricao']);
		$tpl->assign('chktpativ', ($reg['codigo'] == $cbo_tipo_manutencao ? ' selected' : ''));
	}
//------------------------------------------------------------------------------------------ Lista Analista
	if ($tm == 's') 
	{
		$sql = "
				SELECT * 
				  FROM projetos.usuarios_controledi 
				 WHERE indic_02 = 'S' 
				   AND tipo <> 'X' 
				 ORDER BY nome
			   ";
	}
	else 
	{
		if(!isset($_REQUEST['aa']))
		{
			if(isset($regOs['area']))
			{
				$divisao_do_analista = $regOs['area'];
			}
		}
		else
		{
			$divisao_do_analista = $_REQUEST['aa'];
		}
		$sql = "
				SELECT * 
				  FROM projetos.usuarios_controledi 
				 WHERE tipo in('N','G','U') 
				   AND (divisao = '" . $divisao_do_analista . "' OR divisao_ant = '" . $divisao_do_analista . "' OR '' = '".$divisao_do_analista."')
				 ORDER BY nome
			   ";
			   
		#echo $sql; exit;
	}
	$rs = pg_query($db, $sql);
	$tpl->newBlock('cbo_analista');
	$tpl->assign('codana', '');
	$tpl->assign('nomeana', '');
	//echo 'cbo_analista1';
	while ($reg = pg_fetch_array($rs)) 
	{
		$tpl->newBlock('cbo_analista');
		$tpl->assign('codana', $reg['codigo']);
		$tpl->assign('nomeana', $reg['nome']);
		$tpl->assign('chkana', ($reg['codigo'] == $cbo_analista ? ' selected' : ''));
	}
//----------------------------------------------------------------------------------------- Lista Complexidade
	if ( ($regOs['cod_atendente'] == $Z) or (is_null($n)) ) {
		$sql = "SELECT * FROM listas WHERE categoria='CPLX' ORDER BY codigo";
	}
	else {
		$sql = "SELECT * FROM listas WHERE categoria='CPLX' AND codigo='$cbo_complexidade' ORDER BY codigo";
	}
	$rs = pg_query($db, $sql);
	while ($reg = pg_fetch_array($rs)) {
		$tpl->newBlock('cbo_complexidade');
		$tpl->assign('codcplx', $reg['codigo']);
		$tpl->assign('nomecplx', $reg['descricao']);
		$tpl->assign('chkcplx', ($reg['codigo'] == $cbo_complexidade ? ' selected' : ''));
	}

	
	#### MONTA COM DE RECORRENCIA ####
	if($_REQUEST['aa'] == 'GI')
	{
		$tpl->newBlock('cbo_recorrente');
		$tpl->assign('cd_recorrente', 'N');
		$tpl->assign('ds_recorrente', 'Não');	
		$tpl->newBlock('cbo_recorrente');
		$tpl->assign('cd_recorrente', 'S');
		$tpl->assign('ds_recorrente', 'Sim');	
		
		$tpl->assign('fl_seleciona', ($regOs['cd_recorrente'] == 'S' ? ' selected' : ''));
	}
	else
	{
		$tpl->assignGlobal('fl_recorrente_exibe', 'none;');
	}
	
	#### PERMITE CONCLUIR ATIVIDADE ####
	if (
	       (
	             ($regOs['status_atual'] == 'ETES')
	          or ($regOs['status_atual'] == 'AOCS')
	       )
	   and
	       (
	             ($regOs['cod_solicitante'] == $_SESSION['Z']) 
	          or ($regOs['cod_testador']    == $_SESSION['Z'])
	       )
	   )
	{
		$tpl->newBlock('conclusao');
		$tpl->assign('complemento', '');
		$tpl->assign('numero_os', $regOs['numero']);
		$tpl->assignGlobal('fl_conclusao_botao', 'style="display:none;"');
	}
//------------------------------------------------------------------------------------ Finaliza construção da página
	
	
	#### PERIMITE DUPLICAR OS ####
	if (
	         ($regOs['status_atual'] == 'AGDF') 
	      or ($regOs['status_atual'] == 'CANC')
	      or ($regOs['status_atual'] == 'CACS') 
	   )
	{
		$tpl->newBlock('dup_ativ');
		$tpl->assign('numero_os', $regOs['numero']);
	}

	#### EXIBE BOTAO PARA CONFIRMAR INFOS COMPLEMENTARES ATIVIDADE GA #####
	if($fl_ga_informacoes_complementares)
	{
		$tpl->assignGlobal('fl_conclusao_botao', "style='display:none'");
		$tpl->newBlock('GA_complementa_informacoes');
	}

	
	#### VERIFICA SE POSSUI ANEXOS ####
	$qr_sql = "
				SELECT COUNT(*) AS fl_anexo
				  FROM projetos.atividade_anexo 
				 WHERE cd_atividade = ".intval($n)."	
	          ";
	$ob_resul = pg_query($db, $qr_sql);
	$ar_reg = pg_fetch_array($ob_resul);	
	if($ar_reg['fl_anexo'] > 0)
	{
		$tpl->newBlock('fl_possui_anexo');
	}
	
	
	#### ABRIR ATIVIDADE APOS CONCLUIDO #### OS: 33157 - 18/01/2012
	if((($_SESSION['D'] == "GAP") or ($regOs['divisao'] == "GAP") or ($_SESSION['D'] == "GP") or ($regOs['divisao'] == "GP")) and (($aa == "GB") or ($aa == "GP")))
	{
		$tpl->newBlock('ABRIR_AO_ENCERRAR');

		$tpl->assign('ck_usuario_abrir_ao_encerrar_S',($regOs['fl_abrir_encerrar'] == "S" ? " selected " : ""));
		$tpl->assign('ck_usuario_abrir_ao_encerrar_N',($regOs['fl_abrir_encerrar'] == "N" ? " selected " : ""));
		
		$tpl->assign('descricao_abrir_ao_encerrar', $regOs['descricao_abrir_ao_encerrar']);
		
		$cd_usuario_abrir_ao_encerrar = (((intval($regOs['cd_usuario_abrir_ao_encerrar']) == 0) and (($_SESSION['D'] == "GAP" ) or ($_SESSION['D'] == "GP" ))) ? 203 : intval($regOs['cd_usuario_abrir_ao_encerrar']));
		
		$qr_sql = "
					SELECT codigo,
					       nome
					  FROM projetos.usuarios_controledi 
					 WHERE (tipo in('N','G','U') OR codigo = ".$cd_usuario_abrir_ao_encerrar.")
					   AND divisao = '".(trim($regOs['divisao']) == "" ? $_SESSION['D'] : trim($regOs['divisao']))."'
					 ORDER BY nome
		          ";
		$ob_resul = pg_query($db, $qr_sql);
		while ($ar_reg = pg_fetch_array($ob_resul)) 
		{
			$tpl->newBlock('CB_USUARIO_ABRIR_AO_ENCERRAR');
			$tpl->assign('cd_usuario_abrir_ao_encerrar', $ar_reg['codigo']);
			$tpl->assign('ds_usuario_abrir_ao_encerrar', $ar_reg['nome']);
			$tpl->assign('ck_usuario_abrir_ao_encerrar', ($ar_reg['codigo'] == $cd_usuario_abrir_ao_encerrar ? " selected " : ""));
		}		
	}
	
	$tpl->newBlock('atendimento_os');
	
	$tpl->assign('descricao_manutencao', $regOs['solucao']);
	$tpl->assign('dt_limite_teste', $regOs['dt_limite_testes']);
	$tpl->assign('dt_envio_teste', $regOs['dt_env_teste']);
	$tpl->assign('dt_inicio_real', $regOs['dt_inicio_real']);
	$tpl->assign('dt_fim_real', $regOs['dt_fim_real']);
	
	
	pg_close($db);
   $tpl->printToscreen();
?>