<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.Email.inc.php');
	include_once('inc/nextval_sequence.php');
	
	#### STATUS DE AGUARDANDO INICIO ####
	$ar_status_inicio_gerencia["GB"]  = "AISB";
	$ar_status_inicio_gerencia["GF"]  = "AINF";
	$ar_status_inicio_gerencia["GRI"] = "AICS";
	$ar_status_inicio_gerencia["GA"]  = "AIGA";
	$ar_status_inicio_gerencia["GI"]  = "AINI";
	$ar_status_inicio_gerencia["GAP"] = "AIST";
	$ar_status_inicio_gerencia["GC"]  = "GCAI";
	$ar_status_inicio_gerencia["GJ"]  = "AIGJ";
	$ar_status_inicio_gerencia["SG"]  = "SGAI";		
	
	#### STATUS DE AGUARDANDO INICIO ####
	$ar_status_inicio = Array('AINI','AIST', 'AICS', 'AINF', 'GCAI', 'AIGA','AISB','AIGJ','SGAI');	
	
	#### STATUS DE CONCLUIDO ####
	$ar_status_concluido = Array('COSB','COST','CONC','CONF','COGA','COCS','GCCO','COGJ','SGCA','SGCO');


    if ( ($_POST['sel_gerencia'] <> $_POST['gerencia_responsavel']) and ($_POST['cd_cenario']!="") ){ // and ($_POST['TA'] == 'L') )
        cenario_legal_troca_responsavel($db);
    }
    else {
        not_cenario_legal_or_not_troca_responsavel($db);
    }

    function cenario_legal_troca_responsavel($_db)
    {
        $crlf = chr(10);
		$cd_atividade = $_POST['n'];

        // BEGIN : Abrir cenário legal
		$qr_sql = "
					SELECT cd_cenario, 
                           titulo
                      FROM projetos.cenario
                     WHERE cd_cenario = ".$_POST["cd_cenario"]."
                     ORDER BY cd_cenario
				  ";
		$ob_resul = pg_query($_db, $qr_sql);
		$rowCenario = pg_fetch_array($ob_resul);		
        // END : Abrir cenário legal

        // BEGIN: Encerramento de Atividade
        $qr_sql = "
					UPDATE projetos.atividades 
					   SET dt_fim_real  = CURRENT_TIMESTAMP,
                           status_atual = 'RAGC'
                     WHERE numero      = ".$cd_atividade."
					   AND dt_fim_real IS NULL
				  ";
        @pg_query($_db, $qr_sql);

        $responsavel_gc = "amedeiros@eletroceee.com.br";
        $divisao_antiga = $_POST['gerencia_responsavel'];
        $divisao_nova   = $_POST['sel_gerencia'];
        envia_email_troca_divisao_encerramento_atividade($_db, $responsavel_gc, $divisao_antiga, $divisao_nova, $cd_atividade, $_POST["cd_cenario"] );
        // END: Encerramento de Atividade

        // BEGIN: Abertura de novas atividades para os novos responsáveis
        $qr_sql = "
					SELECT codigo, usuario, guerra, nome, divisao
                      FROM projetos.usuarios_controledi 
                     WHERE divisao = '".$_POST['sel_gerencia']."' 
                       AND indic_03 = '*' 
                       AND NOT tipo IN ('X')
                  ";
		$ob_resul = pg_query($_db, $qr_sql);

        $virgula   = "";
        while ($reg = pg_fetch_array($ob_resul)) 
        {
            $novo_responsavel = $reg["usuario"] . "@eletroceee.com.br";
            $nome_novo_responsavel = $reg["nome"];

            // Captura id da atividade
            $cd_atividade_nextval = getNextval("projetos", "atividades", "numero", $_db);

            $qr_sql = "
						INSERT INTO projetos.atividades 
							 (
								numero, 
								tipo,           
								dt_cad,
								descricao,
								area,
								divisao,
								status_atual,
								tipo_solicitacao,
								titulo,
								cod_solicitante,
								cod_atendente,
								cd_cenario
							 )                 
						VALUES 
							 (
								".$cd_atividade_nextval.",
								'L',            
								CURRENT_TIMESTAMP,  
								'Prezado(a): ".$reg["nome"].$crlf.$crlf."Verificar procedência do seguinte conteúdo do Cenário Legal: ".$rowCenario["titulo"]."', 
								'".$divisao_nova."',
								'FC',
								'AIGC',
								'VP',
								'Verificação de Procedência',
								98,
								".$reg["codigo"].",
								".$_POST["cd_cenario"]."
							 )
					  ";
			pg_query($_db, $qr_sql);

            $tpEmail = 'I'; // Inclusão de novo item
            $m = envia_emailx($cd_atividade_nextval, $_db, $tpEmail);

            // BEGIN : Grava Histórico de transferencia de atividade no destino
            $qr_sql = "
						INSERT INTO projetos.atividade_historico 
							 ( 
								cd_atividade, 
								cd_recurso, 
								dt_inicio_prev,
								status_atual,
								observacoes 
							 )
						VALUES 
							 ( 
								".$cd_atividade_nextval.", 
								".$_SESSION['Z'].",
								CURRENT_TIMESTAMP,
								'RAGC',
								'Atividade criada por reencaminhamento, Atividade origem: ".$cd_atividade."'
							 )
                      ";
			pg_query($_db, $qr_sql);
            // END : Grava Histórico de transferencia de atividade no destino
            
            // Armazena códigos das atividades geradas para gravar histórico ao final
            $cd_atividade_nextval_concat.= $virgula.$cd_atividade_nextval;
            $virgula = ", ";
        }

		outros_responsaveis_cancelar_atividade($_db, $_POST['cd_cenario'], $_POST['cod_atendente'], $divisao_antiga, 1);
        // END: Abertura de novas atividades para os novos responsáveis
        
        // BEGIN : Grava Histórico de transferencia de atividade na origem
        $qr_sql = "
					INSERT INTO projetos.atividade_historico 
                         ( 
							cd_atividade, 
							cd_recurso, 
							dt_inicio_prev,
							status_atual,
							observacoes 
                         )
                    VALUES 
                         ( 
							".$cd_atividade.", 
							".$_SESSION['Z'].",
							CURRENT_TIMESTAMP,
							'RAGC',
							'Reencaminhamento para ".$_POST['sel_gerencia'].", Atividade(s): ".$cd_atividade_nextval_concat."'
                         );
						 
					INSERT INTO projetos.atividade_acompanhamento
						 (
							cd_atividade, 
							cd_usuario_inclusao,
							ds_atividade_acompanhamento
						 )
					VALUES 
						 (
							".$cd_atividade.", 
							".$_SESSION['Z'].",
							'Reencaminhamento para ".$_POST['sel_gerencia'].", Atividade(s): ".$cd_atividade_nextval_concat."'
						 );  						 
                  ";
		pg_query($_db, $qr_sql);
        // END : Grava Histórico de transferencia de atividade na origem


		header( "Location: lst_atividades.php?TA=L" );
		exit;
    }

    function not_cenario_legal_or_not_troca_responsavel($db)
	{ 
		global $ar_status_concluido;
        $dt_inicio_real      = ($_POST['txt_dt_inicio_real']  == '' ? 'NULL'       : "TO_DATE('".$_POST['txt_dt_inicio_real']."','DD/MM/YYYY')");
        $dt_env_teste        = ($_POST['txt_dt_env_teste']    == '' ? 'NULL'       : "TO_DATE('".$_POST['txt_dt_env_teste']."','DD/MM/YYYY')");
        $dt_inicio_prev      = ($_POST['txt_dt_inicio_prev']  == '' ? 'NULL'       : "TO_DATE('".$_POST['txt_dt_inicio_prev']."','DD/MM/YYYY')");
        $dt_fim_prev         = ($_POST['txt_dt_fim_prev']     == '' ? 'NULL'       : "TO_DATE('".$_POST['txt_dt_fim_prev']."','DD/MM/YYYY')");
        $dt_fim_real         = ($_POST['txt_dt_fim_real']     == '' ? 'NULL'       : "TO_DATE('".$_POST['txt_dt_fim_real']."','DD/MM/YYYY')");
        $dt_limite_teste     = ($_POST['txt_dt_limite_teste'] == '' ? 'NULL'       : "TO_DATE('".$_POST['txt_dt_limite_teste']."','DD/MM/YYYY')");
        $dt_prevista         = ($_POST['dt_prevista']         == '' ? 'NULL'       : "TO_DATE('".$_POST['dt_prevista']."','DD/MM/YYYY')");
        $dt_implementacao    = ($_POST['dt_implementacao']    == '' ? 'NULL'       : "TO_DATE('".$_POST['dt_implementacao']."','DD/MM/YYYY')");
        $dt_start            = ($_POST['txt_dt_start']        == '' ? '2000-01-01' : "TO_DATE('".$_POST['txt_dt_start']."','DD/MM/YYYY')");
        $txt_solucao         = ($_POST['txt_solucao']         == '' ? 'NULL'       : "'".str_replace("'","´",$_POST['txt_solucao'])."'");
        $nr_periodicidade    = ($_POST['periodicidade']       == '' ? '0'          : $_POST['periodicidade']);
        $nr_dias_previstos   = ($_POST['dias_previstos']      == '' ? '0'          : $_POST['dias_previstos'] );
        $cd_atividade        = $_POST['n'];
        $fl_atividade_tipo   = $_POST['TA'];
        $ar_rateio_percentual= $_POST['ar_rateio_percentual']; 
        $ar_rateio_programa  = $_POST['ar_rateio_programa']; 
        $cbo_area            = $_POST['cbo_area'];
        $fl_teste_relevante = $_POST['fl_teste_relevante'];

        $nr_dias_adicionados = ($_POST['num_dias_crono'] + $_POST['num_dias_influenciados']);

        $txt_problema        = ( $txt_problema        == '' ? 'NULL' : "'".str_replace("'","´", $txt_problema)."'" ); // SEM USO
        $strarea             = str_replace("cad_atividade.php?aa=","",$cbo_area); //SEM USO

        if ( ($_POST['cbo_status_atual'] == 'ETES') and ($dt_env_teste == 'NULL') ) 
        {
            $dt_env_teste = " CURRENT_DATE ";
        }

		#### CANCELADO E CONCLUIDO GRAVA DT_FIM_REAL ####
        if ( (
		   ($_POST['cbo_status_atual'] == 'COGA') 
		or ($_POST['cbo_status_atual'] == 'COGJ') 
		or ($_POST['cbo_status_atual'] == 'GCCO') 
		or ($_POST['cbo_status_atual'] == 'CANF') 
		or ($_POST['cbo_status_atual'] == 'CONF') 
		or ($_POST['cbo_status_atual'] == 'CANC')
		or ($_POST['cbo_status_atual'] == 'AGDF') 
		or ($_POST['cbo_status_atual'] == 'COCS') 
		or ($_POST['cbo_status_atual'] == 'CACS')
		or ($_POST['cbo_status_atual'] == 'COSB')
		or ($_POST['cbo_status_atual'] == 'CASB')
		or ($_POST['cbo_status_atual'] == 'SGCA')
		or ($_POST['cbo_status_atual'] == 'SGCO')
		) 
		and ($dt_fim_real == 'NULL') ) 
        {
            if( ($_POST['cbo_status_atual'] == 'CANC') or ($_POST['cbo_status_atual'] == 'AGDF') )
            {
                $dt_fim_real  = " COALESCE(dt_fim_real,CURRENT_TIMESTAMP) ";
            }
            else
            {
                $dt_fim_real  = " CURRENT_TIMESTAMP ";
            }
        }

        $qt_dias_realizados = '0';
        if (($_POST['cbo_status_atual'] == 'ETES') or ($_POST['cbo_status_atual'] == 'CONC') or ($_POST['cbo_status_atual'] == 'AGDF') or ($_POST['cbo_status_atual'] == 'CANC') or ($_POST['cbo_status_atual'] == 'SUSP')) 
        {
            if ($dt_inicio_real <> 'NULL') 
            {
                if ($dt_env_teste <> 'NULL') 
                {
                    //$qt_dias_realizados = "REPLACE(REPLACE((".$dt_env_teste." - ".$dt_inicio_real."),' days',''),' day','')::bigint";
					$qt_dias_realizados =  "EXTRACT('days' FROM AGE(".$dt_env_teste.",".$dt_inicio_real."))";
                }
                else 
                {
                    if ($dt_fim_real <> 'NULL') 
                    {
                        //$qt_dias_realizados =  "REPLACE(REPLACE((".$dt_fim_real." - ".$dt_inicio_real."),' days',''),' day','')::bigint"; 
						
						$qt_dias_realizados =  "EXTRACT('days' FROM AGE(".$dt_fim_real.",".$dt_inicio_real."))";
                    }
                }
            }
        }
        else 
        {
            $dt_env_teste = 'NULL';
        }

        // RATEIO PREVIDENCIARIO
        deletar_rateios_da_atividade( $db, $cd_atividade );
        for ( $index = 0; $index < sizeof($ar_rateio_percentual); $index++ )
        {
        	if($ar_rateio_percentual[$index]!="")
        	{
	            adicionar_atividade_rateio( $db, $cd_atividade, $ar_rateio_percentual[$index], $ar_rateio_programa[$index] );
        	}
        }
        // RATEIO PREVIDENCIARIO

        pg_query($db,"BEGIN TRANSACTION");
        if($fl_teste_relevante=="") $fl_teste_relevante="N";

        /**
         * combo de projetos(sistemas) tem comportamento diferenciado entre as gerencias
         * abaixo as condições básicas para gravação
         */

        // caso exista um objeto referente projeto/sistema e o mesmo está preenchido
        if(isset($_POST['cbo_sistema'])) if($_POST['cbo_sistema']!="") $cbo_sistema=$_POST['cbo_sistema'];

        // caso exista o objeto referente projeto/sistema e o mesmo não está preenchido
        if(isset($_POST['cbo_sistema'])) if($_POST['cbo_sistema']=="") $cbo_sistema="null";

        // caso não exista o objeto referente ao projeto/sistema
        if(!isset($_POST['cbo_sistema'])) $cbo_sistema="null";

		$sql = "
                UPDATE projetos.atividades 
                   SET  dt_inicio_prev   = ".$dt_inicio_prev.",
                        sistema          = ".$cbo_sistema.",
                        solucao          = ".$txt_solucao.",
                        dt_inicio_real   = ".$dt_inicio_real.",
                        status_atual     = '".$_POST['cbo_status_atual']."',
                        complexidade     = '".$_POST['cbo_complexidade']."',
                        dt_fim_prev      = ".$dt_fim_prev.",
                        dt_fim_real      = ".$dt_fim_real.",
                        dt_env_teste     = ".$dt_env_teste.",
                        fl_teste_relevante = '".$fl_teste_relevante."',
                        dt_limite_testes = ".$dt_limite_teste.",
                        numero_dias      = ".$nr_dias_previstos.",
                        periodicidade    = ".$nr_periodicidade.",
                        dias_realizados  = ".$qt_dias_realizados.",
                        dt_prevista_implementacao_norma_legal = ".$dt_prevista.",
                        dt_implementacao_norma_legal = ".$dt_implementacao.",
                        pertinencia      = '".$_POST['pert']."',
						fl_balanco_gi    = '".$_POST['fl_balanco_gi']."',
                "; 

		if($_POST['cbo_status_atual'] == "ICGA")
		{
			$sql.= "dt_limite = NULL,";
		}
				
        // Tratamento para troca de responsável pelo  Cenário Legal - Garcia - 12/03/2007
        if (($_POST['sel_gerencia'] <> $_POST['gerencia_responsavel']) and ($fl_atividade_tipo == 'L')) 
        {
            $sql2 = "
                      SELECT codigo, usuario, guerra, divisao
                        FROM projetos.usuarios_controledi where divisao = '" . $_POST['sel_gerencia'] . "' 
                         AND indic_03 = '*' 
            ";

            $rs = pg_query($db, $sql2);
            while ($reg = pg_fetch_array($rs)) 
            {
                envia_email_cenario_legal( $cd_atividade, $db, $reg['usuario'] );
            }
            $sql .= " area = '" . $_POST['sel_gerencia'] . "', "; 
        }

        // ------------------------------------------ 
        if ($_POST['cbo_testador'] <> '') 
        {
            $sql.= "    cod_testador     = ".$_POST['cbo_testador'].","; 
        }
        if (($_POST['tipo_ativ'] == 'I') and ($_POST['num_dias_crono'] <> "") and ($_POST['cod_atendente'] == $_SESSION['Z']) )  
        {       
            $sql.= "    num_dias_adicionados = ".$nr_dias_adicionados.",";          
        }                                                                                       
    
        /*$sql.= "        divisao                = '".$_SESSION['S']."'
                  WHERE numero = ".$cd_atividade;*/
    
        $sql.= "        divisao = divisao
                  WHERE numero = ".$cd_atividade;

		
        $fl_tipo = 0;   
        ########################################## ATIVIDADE SUSPENSA ###########################################
        // Suspende as tarefas não concluídas (ok do analista).
        // ------------------------------------------------------ Tratamentos para tarefas da GI (demais divisões possuem status diferentes destes):        
        if($_POST['cbo_status_atual'] == "SUSP")
        {
            $fl_tipo = 1;
            $qr_select = "
                            SELECT cd_tarefa,
                                   cd_recurso
                              FROM projetos.tarefas
                             WHERE cd_atividade = ".$cd_atividade."
                               AND status_atual <> 'CONC';      
                         ";
            $ob_res = pg_query($db, $qr_select);
            while ($ob_reg = pg_fetch_array($ob_res)) 
            {
                $qr_update = "
                              UPDATE projetos.tarefas 
                                 SET status_atual = 'SUSP'
                               WHERE cd_atividade = ".$cd_atividade." 
                                 AND cd_tarefa    = ".$ob_reg['cd_tarefa'].";

                              INSERT INTO projetos.tarefa_historico 
                                   ( 
                                     cd_atividade, 
                                     cd_tarefa, 
                                     cd_recurso, 
                                     timestamp_alteracao, 
                                     descricao, 
                                     status_atual,
									 cd_usuario_inclusao
                                   ) 
                              VALUES
                                   ( 
                                     ".$cd_atividade.", 
                                     ".$ob_reg['cd_tarefa'].", 
                                     ".$ob_reg['cd_recurso'].", 
                                     current_timestamp, 
                                     'Atividade suspensa.', 
                                     'SUSP',
									 ".$_SESSION['Z']."
                                   );
                             ";
                $ob_resul= @pg_query($db,$qr_update);
                if(!$ob_resul)
                {
                    $ds_erro = "ERRO: " . str_replace( "ERROR:", "", pg_last_error($db) );
                    pg_query( $db, "ROLLBACK TRANSACTION" );
                    echo $ds_erro;
                    exit;
                }
            }
        }
        ########################################## ATIVIDADE CANCELADA ##########################################
        // Cancela as tarefas não concluídas (ok do analista).  
        if($_POST['cbo_status_atual'] == "CANC")
        {
            $fl_tipo = 1;
            $qr_select = "
                            SELECT cd_tarefa,
                                   cd_recurso
                              FROM projetos.tarefas
                             WHERE cd_atividade = ".$cd_atividade."
                               AND status_atual <> 'CONC';      
                         ";
            $ob_res = pg_query($db, $qr_select);
            while ($ob_reg = pg_fetch_array($ob_res)) 
            {
                $qr_update = "UPDATE projetos.tarefas 
                                 SET status_atual = 'CANC'
                               WHERE cd_atividade = ".$cd_atividade." 
                                 AND cd_tarefa    = ".$ob_reg['cd_tarefa'].";
                                 
                              INSERT INTO projetos.tarefa_historico 
                                   ( 
                                     cd_atividade, 
                                     cd_tarefa, 
                                     cd_recurso, 
                                     timestamp_alteracao, 
                                     descricao, 
                                     status_atual,
									 cd_usuario_inclusao
                                   ) 
                              VALUES
                                   ( 
                                     ".$cd_atividade.", 
                                     ".$ob_reg['cd_tarefa'].", 
                                     ".$ob_reg['cd_recurso'].", 
                                     current_timestamp, 
                                     'Atividade Cancelada.', 
                                     'CANC',
									 ".$_SESSION['Z']."
                                   );                                
                             ";
                $ob_resul= @pg_query($db,$qr_update);
                if(!$ob_resul)
                {
                    $ds_erro = "ERRO: ".str_replace("ERROR:","",pg_last_error($db));
                    pg_query($db,"ROLLBACK TRANSACTION");
                    echo $ds_erro;
                    exit;
                }           
            }
        }
        ##################################### ATIVIDADE AGUARDANDO DEFINIÇÃO ####################################
        //Coloca o status para Aguardando definição  nas tarefas não concluídas (ok do analista).   
        if($_POST['cbo_status_atual'] == "AGDF")
        {
            $fl_tipo = 1;
            $qr_select = "
                            SELECT cd_tarefa,
                                   cd_recurso
                              FROM projetos.tarefas
                             WHERE cd_atividade = ".$cd_atividade."
                               AND status_atual <> 'CONC';      
                         ";
            $ob_res = pg_query($db, $qr_select);
            while ($ob_reg = pg_fetch_array($ob_res)) 
            {
                $qr_update = "UPDATE projetos.tarefas 
                                 SET status_atual = 'AGDF'
                               WHERE cd_atividade = ".$cd_atividade." 
                                 AND cd_tarefa    = ".$ob_reg['cd_tarefa'].";
                                 
                              INSERT INTO projetos.tarefa_historico 
                                   ( 
                                     cd_atividade, 
                                     cd_tarefa, 
                                     cd_recurso, 
                                     timestamp_alteracao, 
                                     descricao, 
                                     status_atual,
									 cd_usuario_inclusao
                                   ) 
                              VALUES
                                   ( 
                                     ".$cd_atividade.", 
                                     ".$ob_reg['cd_tarefa'].", 
                                     ".$ob_reg['cd_recurso'].", 
                                     current_timestamp, 
                                     'Atividade Aguardando definição.', 
                                     'AGDF',
									 ".$_SESSION['Z']."
                                   );                                
                             ";
                $ob_resul= @pg_query($db,$qr_update);
                if(!$ob_resul)
                {
                    $ds_erro = "ERRO: ".str_replace("ERROR:","",pg_last_error($db));
                    pg_query($db,"ROLLBACK TRANSACTION");
                    echo $ds_erro;
                    exit;
                }           
            }
        }
        ##################################### ATIVIDADE AGUARDANDO DIRETORIA ####################################
        //Coloca o status para PAUSE nas tarefas com status PLAY.   
        if($_POST['cbo_status_atual'] == "ADIR")
        {
            $fl_tipo = 1;
            $qr_select = "
                            SELECT cd_tarefa,
                                   cd_recurso
                              FROM projetos.tarefas
                             WHERE cd_atividade = ".$cd_atividade."
                               AND status_atual IN ('EMAN','SUSP')      
                         ";
            $ob_res = pg_query($db, $qr_select);
            while ($ob_reg = pg_fetch_array($ob_res)) 
            {
                $qr_update = "UPDATE projetos.tarefas 
                                 SET status_atual = 'SUSP'
                               WHERE cd_atividade = ".$cd_atividade." 
                                 AND cd_tarefa    = ".$ob_reg['cd_tarefa'].";
                                 
                              INSERT INTO projetos.tarefa_historico 
                                   ( 
                                     cd_atividade, 
                                     cd_tarefa, 
                                     cd_recurso, 
                                     timestamp_alteracao, 
                                     descricao, 
                                     status_atual,
									 cd_usuario_inclusao
                                   ) 
                              VALUES
                                   ( 
                                     ".$cd_atividade.", 
                                     ".$ob_reg['cd_tarefa'].", 
                                     ".$ob_reg['cd_recurso'].", 
                                     current_timestamp, 
                                     'Atividade Aguardando diretoria.', 
                                     'SUSP',
									 ".$_SESSION['Z']."
                                   );                                
                             ";
                $ob_resul= @pg_query($db,$qr_update);
                if(!$ob_resul)
                {
                    $ds_erro = "ERRO: ".str_replace("ERROR:","",pg_last_error($db));
                    pg_query($db,"ROLLBACK TRANSACTION");
                    echo $ds_erro;
                    exit;
                }           
            }
        }
        
        ##################################### ATIVIDADE AGUARDANDO USUARIO ######################################
        //Coloca o status para PAUSE nas tarefas com status PLAY.   
        if($_POST['cbo_status_atual'] == "AUSR")
        {
            $fl_tipo = 1;
            $qr_select = "
                            SELECT cd_tarefa,
                                   cd_recurso
                              FROM projetos.tarefas
                             WHERE cd_atividade = ".$cd_atividade."
                               AND status_atual IN ('EMAN','SUSP')  
                         ";
            $ob_res = pg_query($db, $qr_select);
            while ($ob_reg = pg_fetch_array($ob_res)) 
            {
                $qr_update = "UPDATE projetos.tarefas 
                                 SET status_atual = 'SUSP'
                               WHERE cd_atividade = ".$cd_atividade." 
                                 AND cd_tarefa    = ".$ob_reg['cd_tarefa'].";
                                 
                              INSERT INTO projetos.tarefa_historico 
                                   ( 
                                     cd_atividade, 
                                     cd_tarefa, 
                                     cd_recurso, 
                                     timestamp_alteracao, 
                                     descricao, 
                                     status_atual,
									 cd_usuario_inclusao
                                   ) 
                              VALUES
                                   ( 
                                     ".$cd_atividade.", 
                                     ".$ob_reg['cd_tarefa'].", 
                                     ".$ob_reg['cd_recurso'].", 
                                     current_timestamp, 
                                     'Atividade Aguardando usuário.', 
                                     'SUSP',
									 ".$_SESSION['Z']."
                                   );                                
                             ";
                $ob_resul= @pg_query($db,$qr_update);
                if(!$ob_resul)
                {
                    $ds_erro = "ERRO: ".str_replace("ERROR:","",pg_last_error($db));
                    pg_query($db,"ROLLBACK TRANSACTION");
                    echo $ds_erro;
                    exit;
                }           
            }
        }
    
        #### BANCO DE SOLUÇÃO ####
        if(trim($_POST['cd_solucao_categoria'])  != "")
        {
            if(trim($_POST['cd_atividade_solucao'])  == "")
            {
                #### INSERE ####
                $qr_exec = "
                                INSERT INTO projetos.atividade_solucao
                                     (
                                       cd_atividade, 
                                       cd_categoria, 
                                       ds_assunto
                                     )
                                VALUES
                                     (
                                       ".$cd_atividade.", 
                                       '".$_POST['cd_solucao_categoria']."', 
                                       '".$_POST['ds_solucao_assunto']."'
                                     );
                             ";
            }
            else
            {
                #### ATUALIZA ####
                $qr_exec = "
                            UPDATE projetos.atividade_solucao
                               SET cd_categoria = '".$_POST['cd_solucao_categoria']."', 
                                   ds_assunto   = '".$_POST['ds_solucao_assunto']."'
                             WHERE cd_atividade_solucao = ".$_POST['cd_atividade_solucao'];
            }
            
            $ob_resul= @pg_query($db,$qr_exec);
            if(!$ob_resul)
            {
                $ds_erro = "ERRO: ".str_replace("ERROR:","",pg_last_error($db));
                pg_query($db,"ROLLBACK TRANSACTION");
                echo $ds_erro;
                exit;
            }   
        }
        
        
		#echo "<PRE>".$sql."</PRE>"; exit;
		
		// ---> ATUALIZA ATIVIDADE <--- //
        $ob_resul= @pg_query($db,$sql);
        if(!$ob_resul)
        {
            $ds_erro = "ERRO: ".str_replace("ERROR:","",pg_last_error($db));
            pg_query($db,"ROLLBACK TRANSACTION");
            echo $ds_erro;
            echo "<!--<PRE>DEBUG\n $sql  \n</PRE>-->";
			exit;
        }   
        else
        {
            // ---> Envia email e grava historico caso o status da Atividade tenha sido alterada <--- //
            if ($_POST['cbo_status_atual'] <> $_POST['status_anterior'])
            {
                $sql = "INSERT INTO projetos.atividade_historico 
                                  ( 
                                    cd_atividade, 
                                    cd_recurso, 
                                    dt_inicio_prev,
                                    status_atual,
                                    observacoes 
                                   )
                              VALUES 
                                   ( 
                                     ".$cd_atividade.", 
                                     ".$_SESSION['Z'].",
                                     current_timestamp,
                                     '".$_POST['cbo_status_atual']."',
                                     'Troca de Status'
                                   )";
    
                $ob_resul= @pg_query($db,$sql);
                if(!$ob_resul)
                {
                    $ds_erro = "ERRO: ".str_replace("ERROR:","",pg_last_error($db));
                    pg_query($db,"ROLLBACK TRANSACTION");
                    echo $ds_erro;
					exit;
                }
                
                envia_email($cd_atividade, $db, 'A');
                fncEnviaEmailTrocaStatusTarefas($cd_atividade, $fl_tipo, $db);
            }
    
            // ---> Soma dias úteis às datas de Início e fim previstas <--- //
            if (($_POST['tipo_ativ'] == 'I') and ($_POST['num_dias_crono'] <> "") and ($_POST['cod_atendente'] == $_SESSION['Z']) )  
            {   
                $sql1 = "SELECT numero, 
                                dt_inicio_prev, 
                                dt_fim_prev 
                           FROM projetos.atividades 
                          WHERE cod_atendente  = ".$_SESSION['Z']."
                            AND dt_inicio_prev > ".$dt_start."
                            AND tipo           = 'F' 
                            AND status_atual   IN ('ADIR', 'AMAN', 'AUSR', 'EANA', 'EMAN', 'AINI')";                
                $rs1  = pg_query($db, $sql1);
                $cont = 0;
                while ($reg1 = pg_fetch_array($rs1)) 
                {                                               
                    $v_num_ativ    = $reg1['numero'];
                    $v_data_inicio = $reg1['dt_inicio_prev'];
                    $v_data_fim    = $reg1['dt_fim_prev'];              
                    $cont = 0;
                    while ($cont < $_POST['num_dias_crono'])
                    {                                               
                        // ---> SOMA DIAS <--- //
                        $cont = $cont + 1;
                        $sql2 = "SELECT fnc_proximo_dia_util('".$v_data_inicio."') AS dt_nova";
                        $rs2  = pg_query($db, $sql2);
                        $reg2 = pg_fetch_array($rs2);
                        $v_data_inicio = $reg2['dt_nova'];
                        if ($v_data_fim <> "") 
                        {
                            $sql2 = "SELECT fnc_proximo_dia_util('".$v_data_fim."') AS dt_nova " ;
                            $rs2  = pg_query($db, $sql2);
                            $reg2 = pg_fetch_array($rs2);
                            $v_data_fim = $reg2['dt_nova'];
                        }
                    }
                    
                    // ---> Grava as novas datas <--- //
                    $sql3 = "UPDATE projetos.atividades 
                                SET dt_inicio_prev = '".$v_data_inicio."'";
                    
                    if ($v_data_fim <> "") 
                    {
                        $sql3.= " , dt_fim_prev = '".$v_data_fim . "'" ;
                    }
                    $sql3.= " WHERE numero = ".$v_num_ativ;
    
                    $ob_resul= @pg_query($db,$sql3);
                    if(!$ob_resul)
                    {
                        $ds_erro = "ERRO: ".str_replace("ERROR:","",pg_last_error($db));
                        pg_query($db,"ROLLBACK TRANSACTION");
                        echo $ds_erro;
						exit;
                    }                                                                                   
                }               
            }

            
            // ---> COMITA DADOS NO BD <--- //
            pg_query($db,"COMMIT TRANSACTION"); 			
			
			// --------------------------------------------------------------------- Cenário Legal
            if ( $fl_atividade_tipo == 'L' ) 
            {
                $sql4a = " 
                        SELECT MAX( dt_prevista_implementacao_norma_legal ) AS dt_prev, MAX( dt_implementacao_norma_legal ) AS dt_imp
                          FROM projetos.atividades 
                         WHERE cd_cenario = " . $_POST['cd_cenario']; // OS 11623

                $rs = pg_query( $db, $sql4a );
                while ( $reg = pg_fetch_array($rs) ) 
                {
                    $dt_prevista = ($reg['dt_prev'] == '' ? 'NULL' : "'".$reg['dt_prev']."'");
                    $dt_implementacao = ($reg['dt_imp'] == '' ? 'NULL' : "'".$reg['dt_imp']."'");
                    $sql4 = "
                             UPDATE projetos.cenario 
                                SET dt_prevista      = " . $dt_prevista . ",
                                    dt_implementacao = " . $dt_implementacao . ",
                                    pertinencia      = '" . $_POST['pert'] . "'
                              WHERE cd_cenario       = " . $_POST['cd_cenario'];
                    $ob_resul= @pg_query($db,$sql4);
                    if(!$ob_resul)
                    {
                        $ds_erro = "ERRO: " . str_replace( "ERROR:", "", pg_last_error($db) );
                        pg_query( $db, "ROLLBACK TRANSACTION" );
                        echo $ds_erro;
						exit;
                    }
                    else
                    {
                        /*
                         * Caso $_POST['sel_gerencia'] seja alterado no formulário para 
                         * Atividade do cenário legal, o fluxo do código dessa página
                         * não chega até essa parte do código, então é garantido que 
                         * $_POST['sel_gerencia'] terá exatamente a Gerência original
                         * para o Update ser executado corretamente.
                         * 
                         * Essa função (outros_responsaveis_cancelar_atividade) deve
                         * encerrar todas atividades de outros responsáveis pela gerência
                         * da Atividade quando o Responsável que está atendendo a atividade
                         * realizar qualquer alteração que de andamento ao processo.
                         * 
                         * by cjunior, created on 11/11/2008
                         */

                        // BEGIN : Encerrar todas as atividades relacionadas ao cenário legal
                        outros_responsaveis_cancelar_atividade(   $db
                                                                , $_POST['cd_cenario']
                                                                , $_POST['cod_atendente']
                                                                , $_POST['sel_gerencia'] 
                                                               );
                        // END : Encerrar todas as atividades relacionadas ao cenário legal
                    }
                }
            }


			
			#### VERICA SE DEVE ABRIR UMA ATIVIDADE #### OS: 33157 - 18/01/2012
			if(in_array($_POST['cbo_status_atual'],$ar_status_concluido))
			{
				gera_atividade(intval($cd_atividade));
			}
			
			

            if ($fl_atividade_tipo == '') 
            {
                $fl_atividade_tipo = 'A'; 
            }

            if ($_POST['cbo_status_atual'] == 'ETES') 
            {
                header('location: lst_atividades.php?TA=' . $fl_atividade_tipo);
            }
            else 
            {
                header('location: cad_atividade_atend.php?n='.$cd_atividade.'&a=a&TA='.$fl_atividade_tipo.'&aa='.$_POST['aa']);     
            }
        }

        pg_close($db);
    }

	function deletar_rateios_da_atividade( $_db, $_cd_atividade )
	{
		$qr_sql = "
					DELETE FROM projetos.atividade_rateio 
	                 WHERE cd_atividade = ".$_cd_atividade."
				  ";
		@pg_query($_db, $qr_sql);		
	}

	function adicionar_atividade_rateio( $_db, $_cd_atividade, $_nr_percentual, $_cd_listas_programa )
	{
		$qr_sql = "
					INSERT INTO projetos.atividade_rateio
					     (
							cd_atividade,
							nr_percentual,
							cd_listas_programa
						 )
					VALUES 
					     (
							".$_cd_atividade.",
							".$_nr_percentual.",
							'".$_cd_listas_programa."'
						 )
				  ";
		@pg_query($_db, $qr_sql);
	}

	// Funções de envio de email
	function envia_email($num_atividade, $db, $tp) 
	{
		global $ar_status_inicio;
		global $ar_status_concluido;

		$sql = " 
                SELECT a.numero, 
				       a.sistema,
					   a.status_atual,
				       ltp.descricao AS tipo, 
					   a.descricao AS descati, 
					   u1.usuario AS solicitante, 
					   u1.nome AS nomesolic, 
					   COALESCE(a.cod_testador, a.cod_solicitante) AS cod_testador,
					   a.tipo as tipo_ativ, 
					   a.problema, 
					   a.solucao, 
					   a.observacoes, 
					   a.area,
					   a.divisao,
					   TO_CHAR(a.dt_limite_testes, 'DD/MM/YYYY') AS dt_limite_testes, 
					   u2.usuario as atendente, 
					   u2.nome as nomeatend, 
					   u1.formato_mensagem as fmens_solic, 
					   u1.e_mail_alternativo as emailalt_solic,
					   u2.formato_mensagem as fmens_atend, 
					   u2.e_mail_alternativo as emailalt_atend,
					   lsa.descricao as situacao
				  FROM projetos.atividades a 
				  JOIN projetos.usuarios_controledi u1 ON u1.codigo     = a.cod_solicitante
				  JOIN projetos.usuarios_controledi u2 ON u2.codigo     = a.cod_atendente
				  JOIN public.listas ltp ON ltp.codigo    = a.tipo AND ltp.categoria = 'TPAT'
				  LEFT JOIN public.listas lsa ON lsa.codigo    = a.status_atual AND lsa.categoria = 'STAT' 
				 WHERE a.numero = ".intval($num_atividade);
		$rs  = pg_query($db, $sql);
		$reg = pg_fetch_array($rs);
		$vbcrlf = chr(10);
		
		if (in_array($reg['status_atual'], $ar_status_concluido)) 
		{
			$v_msg     = "Prezada(o) ".$reg['nomesolic'].$vbcrlf;
			$v_assunto = "(".strtoupper($reg['situacao']).") A seguinte atividade foi concluída: nº ".$num_atividade; 
		}
		else 
		{
			if ($reg['nomeatend'] == $reg['nomesolic']) 
			{		
				$v_msg = "Prezada(o) ".$reg['nomeatend'].$vbcrlf;		
			}													
			else 
			{												
				$v_msg = "Prezadas(os) ".$reg['nomeatend']." e ".$reg['nomesolic'].$vbcrlf;		
			}													
			$v_assunto = "(".strtoupper($reg['situacao']).") Alteração de Situação da Atividade nº ".$num_atividade; 
		}
	
		$v_solicitante = str_replace("Todos","fundacao", $reg['solicitante']);
		$v_atendente   = str_replace("Todos","fundacao", $reg['atendente']);		 
		
		if (in_array($reg['status_atual'], $ar_status_inicio)) 
		{
			$v_para = $v_atendente."@eletroceee.com.br";
		}
		else 
		{
			$v_para = $v_solicitante."@eletroceee.com.br";
		}
		
		if ($reg['cod_testador'] != '') 
		{
			$sql = " 
			        SELECT u2.usuario AS atendente, 
					       u2.nome AS nomeatend,
						   u2.formato_mensagem AS fmens_atend,
						   u2.e_mail_alternativo AS emailalt_atend
					  FROM projetos.usuarios_controledi u2
					 WHERE u2.codigo = ".$reg['cod_testador'];
			$rs2  = pg_query($db, $sql);
			$reg2 = pg_fetch_array($rs2);
			$v_testador      = str_replace("Todos","fundacao", $reg2['atendente']);
			$v_nome_testador = $reg2['nomeatend'];	
			$v_para          = $v_para.';'.$v_testador."@eletroceee.com.br";
		}
		if ($reg['emailalt_solic'] != '')
		{					
			$v_para = $v_para.'; '.$reg['emailalt_solic'];
		}
		if ($reg['emailalt_atend'] != '') 
		{
			$v_para = $v_para.'; '.$reg['emailalt_atend'];	
		}
		
	
		if ($reg['atendente'] <> $reg['solicitante']) 
		{
			if (in_array($reg['status_atual'], $ar_status_inicio)) 
			{
				$v_cc = $v_solicitante."@eletroceee.com.br";
			}
			else 
			{
				$v_cc = $v_atendente."@eletroceee.com.br";
			}
		}
		
		#### 204 - Sistema de Empréstimos - CONTRATO  20/11/2012 ####
		if(($reg['status_atual'] == "ETES") AND (intval($reg['sistema']) == 204))
		{
			#echo  "AQUI";
			/* Quando colocado em teste uma alteração de contrato de empréstimo enviar email para GF e GAPSUPORTE */
			$v_cc.= (trim($v_cc) == "" ? "" : ";")."gapsuporte@eletroceee.com.br;alongaray@eletroceee.com.br;rtortorelli@eletroceee.com.br";
			$v_assunto = "ATENÇÃO: ALTERAÇÃO NO CONTRATO DE EMPRÉSTIMO (".strtoupper($reg['situacao']).") Atividade nº ".$num_atividade; 
		}		
		
		if (in_array($reg['status_atual'], $ar_status_inicio)) 
		{
			$v_msg.= "Foi enviada uma solicitação de ".$reg['tpsolic'].$vbcrlf;
		}
		else 
		{
			$v_msg.= "Alteração de status da atividade.".$vbcrlf;
		}
		// ---> Área da mensagem texto: <--- //
        
		$v_msg.= "Solicitante: " . $reg['nomesolic'] .$vbcrlf;
		$v_msg.= "Atendente: " . $reg['nomeatend'] .$vbcrlf;
		$v_msg.= "Atividade: " . $reg['numero'] .$vbcrlf;
		$v_msg.= "Situação: " . strtoupper($reg['situacao']).$vbcrlf;
		$v_msg.= "-------------------------------------------------------------" . $vbcrlf;
		
		if(($reg['area'] == 'GA') and ($reg['tipo_ativ'] != 'L'))
		{
			$v_msg.= "Descrição: ".$vbcrlf . $reg['descati'].$vbcrlf;
			$v_msg.= "-------------------------------------------------------------" . $vbcrlf;
			$v_msg.= "Link para Atividade: ".$vbcrlf;
			$v_msg.= "http://www.e-prev.com.br/controle_projetos/cad_atividade_solic.php?n=" . $num_atividade . "&aa=".$reg['area']."&TA=A" . $vbcrlf;
			$v_msg.= "-------------------------------------------------------------" . $vbcrlf;		
			$v_msg.= "Descrição da Manutenção: ".$vbcrlf . $reg['solucao'] . $vbcrlf;
			$v_msg.= "-------------------------------------------------------------" . $vbcrlf;
		}
		else
		{
			$v_msg.= "DATA LIMITE PARA TESTES: ". $reg['dt_limite_testes'] . $vbcrlf;
			$v_msg.= "Testador: ". $v_nome_testador . $vbcrlf;
			$v_msg.= "-------------------------------------------------------------" . $vbcrlf;
			$v_msg.= "Descrição: ".$vbcrlf . $reg['descati'].$vbcrlf;
			$v_msg.= "-------------------------------------------------------------" . $vbcrlf;
			$v_msg.= "Link para Atividade: ".$vbcrlf;
			$v_msg.= "http://www.e-prev.com.br/controle_projetos/cad_atividade_solic.php?n=" . $num_atividade . "&aa=".$reg['area']."&TA=A" . $vbcrlf;
			$v_msg.= "-------------------------------------------------------------" . $vbcrlf;		
			$v_msg.= "Justificativa da Manutenção: " . $vbcrlf . $reg['problema'] . $vbcrlf;
			$v_msg.= "-------------------------------------------------------------" . $vbcrlf;
			$v_msg.= "Descrição da Manutenção: ".$vbcrlf . $reg['solucao'] . $vbcrlf;
			$v_msg.= "-------------------------------------------------------------" . $vbcrlf;
			$v_msg.= "Observações: ". $vbcrlf . $reg['observacoes'] . $vbcrlf;
			$v_msg.= "-------------------------------------------------------------" . $vbcrlf;
		}
		$v_msg.= "Esta mensagem foi enviada pelo Controle de Atividades.";
		
		$v_cco = "";
		if ($reg['tipo_ativ'] == 'L') 
		{											
			$v_cco = "amedeiros@eletroceee.com.br";
		}

		$v_de = "Controle de Atividades (Solicitado pela ".$reg['divisao'].")";
		
		// ---> GRAVA EMAIL <--- //	
		$sql = " 
				INSERT INTO projetos.envia_emails 
				     ( 
						dt_envio, 
						de,
						para, 
						cc,	
						cco, 
						assunto,
						texto,
						cd_evento	 
					 ) 
				VALUES
					 ( 
						CURRENT_TIMESTAMP, 
						'".$v_de."',
						'".$v_para."', 
						'".$v_cc."', 
						'".$v_cco."',
						'".str_replace("'", "`", $v_assunto)."', 
						'".str_replace("'", "`", $v_msg)."',
						131
					 )
			   ";
		@pg_query($db, $sql);
		
		#echo "<PRE>".print_r($reg,true)."</PRE>";
		#echo "<PRE>".$sql."</PRE>";exit;
	}
	
    function fncEnviaEmailTrocaStatusTarefas($cd_atividade, $fl_tipo, $db) 
	{
		// ---> ENVIA EMAIL PARA ATENDENTES DAS TAREFAS DA ATIVIDADE <--- //
		//$fl_tipo = 0 -> Status da tarefa alterada pela Atividade (inicia execução da tarefa)
		//$fl_tipo = 1 -> Status da tarefa alterada pela Atividade (para execução da tarefa)
		$vbcrlf = chr(10);
		$sql = "SELECT t.cd_atividade as atividade,
                       t.cd_tarefa as tarefa,		
		               u.guerra as executor, 
					   t.descricao as t_descricao, 
					   t.programa as programa, 
					   u.usuario as usuario,
					  (SELECT l.descricao
					     FROM projetos.atividades a,
						  	  listas l
					    WHERE a.numero       = t.cd_atividade
						  AND a.status_atual = l.codigo
						  AND a.divisao      = l.divisao
						  AND categoria      = 'STAT') as st_atividade,
					   CASE WHEN (h.status_atual='AMAN') THEN 'Aguardando Manutenção' 
					  	    WHEN (h.status_atual='EMAN') THEN 'Em Manutenção' 
					  	    WHEN (h.status_atual='LIBE') THEN 'Liberada' 
							WHEN (h.status_atual='CANC') THEN 'Cancelada'
							WHEN (h.status_atual='AGDF') THEN 'Aguardando Definição'
							WHEN (h.status_atual='SUSP' AND (SELECT status_atual FROM projetos.atividades WHERE numero = t.cd_atividade)='ADIR') THEN 'Atividade Aguardando Diretoria'
							WHEN (h.status_atual='SUSP' AND (SELECT status_atual FROM projetos.atividades WHERE numero = t.cd_atividade)='AUSR') THEN 'Atividade Aguardando Usuário'
							WHEN (h.status_atual='SUSP' AND (SELECT status_atual FROM projetos.atividades WHERE numero = t.cd_atividade)='SUSP') THEN 'Atividade Suspensa'
							WHEN (h.status_atual='SUSP') THEN 'Em Manutenção (Pausa)'
					   END as status,
                       h.descricao AS historico					   
		          FROM projetos.tarefas t, 
				       projetos.usuarios_controledi u,
					   projetos.tarefa_historico h 					   
		         WHERE t.cd_atividade = ".$cd_atividade."  
				   AND t.cd_recurso   = u.codigo 
				   AND t.cd_atividade = h.cd_atividade
				   AND t.cd_tarefa    = h.cd_tarefa
				   AND t.cd_recurso   = h.cd_recurso
				   AND h.timestamp_alteracao = (SELECT MAX(timestamp_alteracao)
	                                              FROM projetos.tarefa_historico
					                             WHERE cd_atividade = h.cd_atividade
		                                           AND cd_tarefa    = h.cd_tarefa
					                               AND cd_recurso   = h.cd_recurso)				   
				   AND t.status_atual <> 'CONC'
				   AND t.dt_encaminhamento IS NOT NULL
				   AND t.dt_exclusao IS NULL
				   ";
				   
		$rs = pg_query($db, $sql);
		while ($reg = pg_fetch_array($rs)) 
		{
			// ---> EMAILS <--- //
			$v_para = $reg['usuario']."@eletroceee.com.br";
			$v_cc   = "";
			$v_cco  = "";
			/*
			if ($_POST['aa']=="GRI") {
				$v_de   = "Controle de Atividades e Tarefas da Gerência de Relações Institucionais";
			} else {
				$v_de   = "Controle de Atividades e Tarefas";
			}
			*/
			
			$v_de   = "Controle de Atividades e Tarefas (".$_POST['aa'].")";
			
			// ---> ASSUNTO <--- //
			$v_assunto = "Alteração de Situação da Atividade/Tarefa - nº ".$reg['atividade']."/" .$reg['tarefa'];	
			// ---> CONTEUDO <--- //
			$v_msg = "Prezado(a) ".$reg['executor'].$vbcrlf.
					 "A tarefa teve seu status alterado pela Atividade:".$vbcrlf.
					 "-------------------------------------------------------------".$vbcrlf.
					 "Tarefa: ".$reg['tarefa'].", Atividade: ".$reg['atividade'].$vbcrlf.
					 "-------------------------------------------------------------".$vbcrlf.
					 "Status atual da Atividade: ".$reg['st_atividade'].
					 "-------------------------------------------------------------".$vbcrlf.
					 "Status atual da Tarefa: ".$reg['status'].
					 "-------------------------------------------------------------".$vbcrlf.
					 "Histórico: ".$reg['historico'].
					 "-------------------------------------------------------------".$vbcrlf.
					 "Descrição: ".$reg['t_descricao'].$vbcrlf.
					 "-------------------------------------------------------------".$vbcrlf;

		    if($fl_tipo == 0)
			{
				$v_msg.= "A presente tarefa deve ser iniciada imediatamente!".$vbcrlf.
				          "-------------------------------------------------------------".$vbcrlf;
			}
			
			if($fl_tipo == 1)
			{
				$v_msg.= "Aguarde alteração de status da Atividade, para dar continuidade a Tarefa.".$vbcrlf.
				          "-------------------------------------------------------------".$vbcrlf;
			}
			// ---> GRAVA EMAIL <--- //	
			$sql = " 
					INSERT INTO projetos.envia_emails 
						 ( 
							dt_envio, 
							de,
							para, 
							cc,	
							cco, 
							assunto,
							texto,
							cd_evento
						 ) 
					VALUES
						 ( 
							CURRENT_TIMESTAMP, 
							'".$v_de."',
							'".$v_para."', 
							'".$v_cc."', 
							'".$v_cco."',
							'".str_replace("'", "`", $v_assunto)."', 
							'".str_replace("'", "`", $v_msg)."',
							131
						 )
				   ";	
			@pg_query($db, $sql);
		}
	}

    // Utilizado pelo Cenário Legal (GC)
	function envia_email_cenario_legal( $num_atividade, $db, $usuario ) 
	{
		$e = new Email();
		$e->IsHTML();
		$sql = " 
                SELECT 	a.numero, 
						a.cd_cenario,
				       	ltp.descricao AS tipo, 
					   	a.descricao AS descati, 
					   	u1.usuario AS solicitante, 
					   	u1.nome AS nomesolic, 
					   	a.cod_testador, 
					   	a.problema, 
					   	a.solucao, 
					   	a.observacoes, 
					   	a.area,
						a.divisao
						c.titulo as titulo_cenario,
						c.conteudo as conteudo_cenario,
						c.referencia as referencia_cenario, 
					   	TO_CHAR(a.dt_limite_testes, 'DD/MM/YYYY') AS dt_limite_testes, 
					   	u2.usuario as atendente, 
						u2.nome as nomeatend, 
						a.status_atual, 
					   	u1.formato_mensagem as fmens_solic, 
						u1.e_mail_alternativo as emailalt_solic,
					   	u2.formato_mensagem as fmens_atend, 
						u2.e_mail_alternativo as emailalt_atend,
					   	lsa.descricao as situacao
				  FROM 	projetos.atividades a,
				       	projetos.usuarios_controledi u1,
					   	projetos.usuarios_controledi u2,
						projetos.cenario c,
					   	public.listas ltp,
					   	public.listas lsa
				 WHERE 	u1.codigo     	= a.cod_solicitante
				   AND 	u2.codigo     	= a.cod_atendente
				   AND 	ltp.codigo    	= a.tipo 
				   and 	a.cd_cenario 	= c.cd_cenario
				   AND 	ltp.categoria 	= 'TPAT'
				   AND 	lsa.codigo    	= a.status_atual 
				   AND 	lsa.categoria 	= 'STAT' 
				   AND 	a.numero      	= ".$num_atividade;
		$rs  = pg_query($db, $sql);
		$reg = pg_fetch_array($rs);
		$vbcrlf = chr(10);
		$v_msg     = "Prezada(o) ".$reg['nomeatend'].$vbcrlf;
		$v_assunto = "A seguinte atividade foi encaminhada para você: " . $num_atividade;

		$v_para = $reg['atendente']."@eletroceee.com.br";

        if ($reg['solicitante']=='amedeiros') {
            $v_cc = $reg['solicitante'] . "@eletroceee.com.br";
		}
        else {
            $v_cc = $reg['solicitante'] . "@eletroceee.com.br;amedeiros@eletroceee.com.br";
        }

		$v_msg.= "Alteração de pessoa para verificação de procedência de item do Cenário Legal.".$vbcrlf;
        $email_alt = "";
		// ---> Área da mensagem texto: <--- //
		$v_msg = $v_msg . "Solicitante: " . $reg['nomesolic'] .$vbcrlf;
		$v_msg = $v_msg . "Atendente: " . $reg['nomeatend'] .$vbcrlf;
		$v_msg = $v_msg . "Atividade:" . $reg['numero'] .$vbcrlf;
		$v_msg = $v_msg . "Situação:" . $reg['situacao'] . $email_alt .$vbcrlf;
		$v_msg = $v_msg . "-------------------------------------------------------------" . $vbcrlf;
		$v_msg = $v_msg . "Descrição: ".$vbcrlf . $reg['descati'].$vbcrlf;
		$v_msg = $v_msg . "-------------------------------------------------------------" . $vbcrlf;
		$v_msg = $v_msg . "Link para Atividade: ".$vbcrlf;
		$v_msg = $v_msg . "http://www.e-prev.com.br/controle_projetos/cad_atividade_solic.php?n=" . $num_atividade . "&aa=".$reg['area']."&TA=A" . $vbcrlf;
		$v_msg = $v_msg . "-------------------------------------------------------------" . $vbcrlf;
		$v_msg = $v_msg . "Justificativa da Manutenção: " . $vbcrlf . $reg['problema'] . $vbcrlf;
		$v_msg = $v_msg . "-------------------------------------------------------------" . $vbcrlf;
		$v_msg = $v_msg . "Descrição da Manutenção: ".$vbcrlf . $reg['solucao'] . $vbcrlf;
		$v_msg = $v_msg . "-------------------------------------------------------------" . $vbcrlf;
		$v_msg = $v_msg . "Observações: ". $vbcrlf . $reg['observacoes'] . $vbcrlf;
		$v_msg = $v_msg . "-------------------------------------------------------------" . $vbcrlf;
		$v_msg = $v_msg . "Esta mensagem foi enviada pelo Controle de Atividades.";
		$v_cco = "";
	   // -------------------------------------------------------------	  
		/*
		if ($_POST['aa']=="GRI") {
			$v_de   = "Controle de Atividades da Gerência de Relações Institucionais";
		} else {
			$v_de = "Controle de Atividades";
		}
		*/
		$v_de = "Controle de Atividades (Solicitado pela ".$reg['divisao'].")";
		// ---> GRAVA EMAIL <--- //	
		$sql = " 
				INSERT INTO projetos.envia_emails 
					 ( 
						dt_envio, 
						de,
						para, 
						cc,	
						cco, 
						assunto,
						texto,
						cd_evento
					 ) 
				VALUES
					 ( 
						CURRENT_TIMESTAMP, 
						'".$v_de."',
						'".$v_para."', 
						'".$v_cc."', 
						'".$v_cco."',
						'".str_replace("'", "`", $v_assunto)."', 
						'".str_replace("'", "`", $v_msg)."',
						131
					 )
			   ";	
		//echo( $sql . "<br><br>" );
        @pg_query($db, $sql);
	}
    
    function envia_email_troca_divisao_encerramento_atividade( $_db, $responsavel_gc, $divisao_antiga, $divisao_nova, $cd_atividade, $cd_cenario )
    {
        $qr_sql = "
					INSERT INTO projetos.envia_emails 
						 ( 
							dt_envio, 
							de,
							para, 
							cc,    
							cco, 
							assunto,
							texto,
							cd_evento
						 )
					VALUES
						 ( 
							CURRENT_TIMESTAMP, 
							'Fundacao CEEE',
							'".$responsavel_gc."',
							'',
							'',
							'Reencaminhamento de divisão de atividade do Cenário Legal',
							'A atividade ".intval($cd_atividade)." referente Cenário Legal ".$cd_cenario." foi reencaminhada da ".$divisao_antiga." para ".$divisao_nova.".',
							131
						 )
                   ";
		@pg_query($_db, $qr_sql);
    }
    
    
    function envia_emailx($num_atividade, $db, $tp) 
    {
        $v_cco = "";
        $vbcrlf = chr(10);
        
        $sqlx = "
                SELECT a.numero,
                       a.descricao as descati, 
                       u1.usuario as solicitante, 
                       u1.nome as nomesolic, 
                       u2.usuario as atendente, u2.nome as nomeatend, a.status_atual,
                       u1.formato_mensagem as fmens_solic, u1.e_mail_alternativo as emailalt_solic,
                       u2.formato_mensagem as fmens_atend, u2.e_mail_alternativo as emailalt_atend
                  FROM projetos.atividades a,
                       projetos.usuarios_controledi u1,
                       projetos.usuarios_controledi u2
                 WHERE u1.codigo = a.cod_solicitante
                   AND u2.codigo = a.cod_atendente
                   AND a.numero = ". $num_atividade ."
                   ";
        
        //echo( "<br><br><b>envia_emailx : </b>" . $sqlx . "<br><br>" );
        $rsx = pg_query($db, $sqlx);
        
        $regx = pg_fetch_array($rsx);
        $msgx = "Prezada(o) ".$regx['nomeatend'] . $vbcrlf;
        $v_assunto = "Nova atividade solicitada - nº $num_atividade" . $vbcrlf;
        $v_para = $regx['atendente']."@eletroceee.com.br";
        if ($regx['emailalt_atend'] != '' ) {                   
            $v_para = $v_para . "; " . $regx['emailalt_atend']; // retirar após teste
        }

        $v_cc = ""; 
        $v_de = "Cenário Legal";
        $v_msgx = $v_msgx . "Foi enviada uma solicitação de Verificação de Procedência (Cenário Legal): " . $vbcrlf;
        $v_msgx = $v_msgx . "Atendente: " . $regx['nomeatend'] . $vbcrlf;
        $v_msgx = $v_msgx . "Atividade: " . $regx['numero'] . $vbcrlf;
        $v_msgx = $v_msgx . "-------------------------------------------------------------" . $vbcrlf;
        $v_msgx = $v_msgx . "Descrição: " . $vbcrlf;
        $v_msgx = $v_msgx . $regx['descati'] . $vbcrlf;
        $v_msgx = $v_msgx . "-------------------------------------------------------------" . $vbcrlf;
        $v_msgx = $v_msgx . "Esta mensagem foi enviada pelo Controle de Atividades.";

        // Novo envio de emails:
        $date = date( "d/m/Y" );
        
        $sql = " 
				INSERT INTO projetos.envia_emails_seleciona 
				     ( 
						dt_envio, 
						de, 
						para, 
						cc,  
						cco, 
						assunto, 
						texto,
						cd_evento
                     ) 
                VALUES 
					 ( 
						CURRENT_TIMESTAMP, 
						'" . $v_de . "', 
						'" . $v_para . "', 
						'" . $v_cc . "', 
						'" . $v_cco . "', 
						'" . $v_assunto . "', 
						'" . $v_msgx . "',
						131
                     )
			   ";   

        @pg_query($db, $sql);

        return true;

    }
    
	/**
     * Cancelar atividades abertas para outros reponsáveis do cenário legal
     */
	function outros_responsaveis_cancelar_atividade($_db, $_cd_cenario, $_cd_responsavel, $_divisao, $mensagem_padrao = 0)
    {
		#### TRANSFERIDO PARA TRIGGER atividade_legal_cancelar NA TABELA projetos.atividades (26/12/2013) ####
		/*
		if(intval($mensagem_padrao) == 0)
		{
			$mensagem = 'Atividade cancelada automaticamente pela resposta de outra atividade da mesma gerência';
		}
		else
		{
			$mensagem = 'Atividade cancelada automaticamente pelo reencaminhamento de outra atividade da mesma gerência';
		}
	
		$qr_sql = "
					UPDATE projetos.atividades 
					   SET dt_fim_real  = CURRENT_TIMESTAMP,
						   status_atual = 'CAGC'
					 WHERE cd_cenario    = ".intval($_cd_cenario)."
					   AND area          = '".trim($_divisao)."'
					   AND cod_atendente <> ".intval($_cd_responsavel)."
					   AND dt_fim_real IS NULL
		          ";
		@pg_query($_db,$qr_sql);

		$qr_sql = "
					SELECT cod_atendente, 
					       numero
                      FROM projetos.atividades
                     WHERE cd_cenario    = ".intval($_cd_cenario)."
                       AND area          = '".trim($_divisao)."'
                       AND cod_atendente <> ".intval($_cd_responsavel)."	
		          ";
		$ob_resul = pg_query($_db,$qr_sql);
		$qr_hist = "";
        while($row = pg_fetch_array($ob_resul))
        {
            $qr_hist.= "
                        INSERT INTO projetos.atividade_historico 
                                  ( 
                                    cd_atividade, 
                                    cd_recurso, 
                                    dt_inicio_prev,
                                    status_atual,
                                    observacoes 
                                   )
                              VALUES 
                                   ( 
                                     ".intval($row["numero"]).", 
                                     ".intval($_SESSION['Z']).",
                                     CURRENT_TIMESTAMP,
                                     'CAGC',
                                     '".trim($mensagem)."'
                                   )
                       ";
        }
        
        if(trim($qr_hist) != "")
		{
			@pg_query($_db,$qr_hist);
		}
		*/
    }
	
	########################
	function gera_atividade($cd_atividade)
	{
		global $db;	
		global $ar_status_inicio_gerencia;
		
		$qr_sql = "
					SELECT COALESCE(a.fl_abrir_encerrar,'N') AS fl_abrir_encerrar,
					       (SELECT uc.divisao FROM projetos.usuarios_controledi uc WHERE codigo = a.cd_usuario_abrir_ao_encerrar) AS cd_gerencia
					  FROM projetos.atividades a
					 WHERE a.numero = ".intval($cd_atividade)."
				  ";

		$ob_resul = pg_query($db, $qr_sql);
		$ar_reg = pg_fetch_array($ob_resul);
		if($ar_reg['fl_abrir_encerrar'] == "S")
		{
			#### GERA NOVA ATIVIADE QUANDO CONCLUI ####
			$qr_sql = "
						INSERT INTO projetos.atividades 
							 ( 
								tipo,                
								dt_cad,                  
								descricao,               
								area,  
                                divisao,							
								cod_solicitante,         
								cod_atendente,           
								status_atual,            
								tipo_solicitacao,        
								dt_limite,               
								titulo,                  
								cd_empresa,              
								cd_registro_empregado,   
								cd_sequencia,            
								cd_atendimento,          
								forma,                   
								tp_envio,                
								solicitante,             
								cd_plano,                
								sistema,
								cd_atividade_origem
							 )                        
						SELECT CASE WHEN (SELECT CASE WHEN TRIM(indic_02) = 'A' THEN 'S'
										  WHEN TRIM(indic_02) = 'S' THEN 'S'
										  ELSE NULL
										 END
										FROM projetos.usuarios_controledi 
										WHERE codigo = 203) = 'S' 
									THEN 'S'
								ELSE 'N'
							   END AS tipo,                
							   CURRENT_TIMESTAMP AS dt_cad,                  
							   a.descricao_abrir_ao_encerrar AS descricao,               
							   (SELECT uc.divisao FROM projetos.usuarios_controledi uc WHERE codigo = a.cd_usuario_abrir_ao_encerrar) AS area,                    
							   (SELECT uc.divisao FROM projetos.usuarios_controledi uc WHERE codigo = a.cod_solicitante) AS area,                    
							   a.cod_solicitante,         
							   a.cd_usuario_abrir_ao_encerrar AS cod_atendente,           
							   '".$ar_status_inicio_gerencia[$ar_reg['cd_gerencia']]."' AS status_atual,            
							   'GP2V' AS tipo_solicitacao,        
							   (a.dt_limite + ('1 day'::interval))::DATE AS dt_limite,               
							   'Atividade aberta pelo encerramento da Atividade número ' || a.numero::TEXT AS titulo,                  
							   a.cd_empresa,              
							   a.cd_registro_empregado,   
							   a.cd_sequencia,            
							   a.cd_atendimento,          
							   a.forma,                   
							   a.tp_envio,                
							   a.solicitante,             
							   a.cd_plano,                
							   a.sistema,
							   a.numero
						  FROM projetos.atividades a
						 WHERE a.numero = ".intval($cd_atividade)."
						RETURNING numero;						
                      ";	
			#echo "<PRE>".$qr_sql."</PRE>"; #exit;
					  
			#### ABRE TRANSACAO COM O BD #####
			pg_query($db,"BEGIN TRANSACTION");			
			$ob_resul = @pg_query($db,$qr_sql);
			if(!$ob_resul)
			{
				$ds_erro = str_replace("ERROR:","",pg_last_error($db));
				#### DESFAZ A TRANSACAO COM BD ####
				pg_query($db,"ROLLBACK TRANSACTION");
				pg_close($db);
				echo '<h1 style="color: red; font-weight:bold; font-family: calibri, arial; font-size: 16pt;">ERRO</h1>'; 
				echo '<h1 style="color: red; font-weight:bold; font-family: calibri, arial; font-size: 12pt;">'.$ds_erro.'</h1>'; 
				echo '<BR><BR>'; 
				echo '<PRE>'.$qr_sql.'</PRE>'; 
				exit; 
			}
			else
			{
				$ar_reg = pg_fetch_array($ob_resul);
				$cd_atividade_new = intval($ar_reg['numero']);
				#print_r($ar_reg);#exit;
				
				#### COMITA DADOS NO BD ####
				pg_query($db,"COMMIT TRANSACTION"); 
				
				
				envia_email_nova($cd_atividade_new);
			}
		}
	}	

	function envia_email_nova($cd_atividade)
	{
		global $db;
		$crlf = chr(10);
	
		$qr_sql = "  
					SELECT a.numero, 
					       a.area,
						   a.divisao, 
						   a.complemento, 	
						   a.titulo,
						   a.descricao, 
						   a.status_atual, 
						   a.problema, 
						   a.cd_atendimento,
						   a.tp_envio, 
						   a.forma, 						   
						   TO_CHAR(a.dt_limite, 'DD/MM/YYYY') AS dt_limite,
						   us.nome AS solicitante_nome,
						   us.usuario || '@eletroceee.com.br' AS solicitante_email,
						   ua.nome AS atendente_nome,
						   ua.usuario || '@eletroceee.com.br' AS atendente_email,						   
					       UPPER(st.descricao) AS ds_status_atual,
						   (CASE WHEN (SELECT COUNT(*) FROM projetos.atividade_anexo at WHERE at.cd_atividade = a.numero) > 0 THEN 'S' ELSE 'N' END) AS fl_anexo,
						   pl.descricao AS plano_nome,
						   p.cd_empresa,
						   p.cd_registro_empregado,
						   p.seq_dependencia,
						   p.nome,
						   funcoes.format_cpf(TO_CHAR(p.cpf_mf,'FM00000000000')) AS cpf,
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
						   p.email_profissional,
						   ts.descricao AS tipo_solicitante,
						   fs.descricao AS forma_solicitacao
					  FROM projetos.atividades a 
					  JOIN projetos.usuarios_controledi us
					    ON us.codigo = a.cod_solicitante
					  JOIN projetos.usuarios_controledi ua
					    ON ua.codigo = a.cod_atendente	
					  JOIN public.listas st
					    ON st.codigo    = a.status_atual 
					   AND st.categoria = 'STAT'
					  LEFT JOIN public.participantes p
					    ON p.cd_empresa            = a.cd_empresa           
					   AND p.cd_registro_empregado = a.cd_registro_empregado
					   AND p.seq_dependencia       = a.cd_sequencia 	
    		          LEFT JOIN public.planos pl
    		            ON pl.cd_plano = a.cd_plano		
                      LEFT JOIN public.listas ts
    		            ON ts.codigo    = a.solicitante
					   AND ts.categoria = 'SDAP'
    		          LEFT JOIN public.listas fs
    		            ON fs.codigo    = a.forma
					   AND fs.categoria = 'FDAP'					   
					 WHERE a.numero = ".intval($cd_atividade)."
			      ";

		$ob_resul = pg_query($db, $qr_sql);
	    $ar_reg = pg_fetch_array($ob_resul);

		#echo "<PRE>".$qr_sql."</PRE>"; exit;

		#### CABECALHO ####
		$de   = "Controle de Atividades (Solicitado pela ".$ar_reg['divisao'].")";
		$para = $ar_reg['atendente_email'];
		$cc   = $ar_reg['solicitante_email'];
		$cco  = "";
		$assunto = "(NOVA) Atividade nº".$ar_reg["numero"];
	
		#### MENSAGEM ####
		$msg.= "-------------------------------------------------------------".$crlf;	
		$msg.= "ATIVIDADE".$crlf;    
		$msg.= "-------------------------------------------------------------".$crlf;			
		$msg.= "Número: ".$ar_reg['numero'].$crlf;
		$msg.= "Solicitante: ".$ar_reg['solicitante_nome'].$crlf;
		$msg.= "Atendente: ".$ar_reg['atendente_nome'].$crlf;
		$msg.= "Status: ".$ar_reg['ds_status_atual'].$crlf;
		$msg.= "Data Limite: ".$ar_reg['dt_limite'].$crlf;    			
		$msg.= ($ar_reg['fl_anexo'] == "S" ? "ESTA ATIVIDADE POSSUI ANEXO(S)".$crlf : "");    			
		$msg.= "-------------------------------------------------------------".$crlf;
		$msg.= "Título: ".$ar_reg['titulo'].$crlf.$crlf; 
		$msg.= "Descrição: ".$crlf.$ar_reg['descricao'].$crlf;    
		$msg.= "-------------------------------------------------------------".$crlf;
		$msg.= ((($ar_reg['area'] != 'GA') or ($ar_reg['tipo_ativ'] == 'L')) ? "Justificativa:".$crlf.$ar_reg['problema'].$crlf."-------------------------------------------------------------".$crlf : "");
		$msg.= "Link: ".base_url()."sysapp/application/migre/cad_atividade_solic.php?n=".$ar_reg['numero'].$crlf; 
		$msg.= "-------------------------------------------------------------".$crlf.$crlf;			
		
		if	(
				($ar_reg["area"] == "GB") or
				($ar_reg["area"] == "GF") or
				($ar_reg["area"] == "GJ") or
				($ar_reg["area"] == "GAP") or 
				($ar_reg["divisao"] == "GAP") or 
				(intval($ar_reg['cd_registro_empregado']) > 0)
			)
		{
			$msg.= "-------------------------------------------------------------".$crlf;
			$msg.= "PARTICIPANTE".$crlf;
			$msg.= "-------------------------------------------------------------".$crlf;
			$msg.= (intval($ar_reg['cd_atendimento']) > 0 ? "Protocolo de atendimento: ".intval($ar_reg['cd_atendimento']).$crlf : ""); 
			$msg.= "Emp/Re/Seq: ".$ar_reg['cd_empresa'].'/'.$ar_reg['cd_registro_empregado'].'/'.$ar_reg['seq_dependencia'].$crlf;
			$msg.= (trim($ar_reg['plano_nome']) != "" ? "Plano: ".$ar_reg['plano_nome'].$crlf : "");
			
			if(trim($ar_reg['nome']) != "")
			{
				$msg.=  "Nome: ".$ar_reg['nome'].$crlf.
				        "CPF: ".$ar_reg['cpf'].$crlf.
						"Endereço: ".str_replace("'","",$ar_reg['endereco']).", ".str_replace("'","",$ar_reg['nr_endereco'])."/".str_replace("'","",$ar_reg['complemento_endereco'])." - ".str_replace("'","",$ar_reg['bairro'])." - ".$ar_reg['cep']." - ".$ar_reg['cidade']." - ".$ar_reg['uf'].$crlf.
						"Telefone 1: ".$ar_reg['ddd']." - ".$ar_reg['telefone'].$crlf.
						"Telefone 2: ".$ar_reg['ddd']." - ".$ar_reg['telefone'].$crlf.
						"Email: ".$ar_reg['email']. " / " .$ar_reg['email_profissional'].$crlf;
			}
			$msg.= (trim($ar_reg['tipo_solicitante']) != "" ? "Solicitante: ".$ar_reg['tipo_solicitante'].$crlf : "");
			$msg.= (trim($ar_reg['forma_solicitacao']) != "" ? "Forma de solicitação: ".$ar_reg['forma_solicitacao'].$crlf : "");
			$msg.= (intval($ar_reg['tp_envio']) == 1 ? "Forma de Envio: Correio".$crlf : "");
			$msg.= (intval($ar_reg['tp_envio']) == 2 ? "Forma de Envio: Central de Atendimento".$crlf : "");
			$msg.= (intval($ar_reg['tp_envio']) == 3 ? "Forma de Envio: Email".$crlf : "");
			$msg.= "-------------------------------------------------------------".$crlf.$crlf;
		}

		#### GRAVA EMAIL ####
		$qr_sql = " 
					INSERT INTO projetos.envia_emails 
						 ( 
							dt_envio, 
							de, 
							para, 
							cc,	
							cco, 
							assunto, 
							texto,
							cd_evento
						 )
					VALUES 
						 ( 
							CURRENT_TIMESTAMP,
							'".$de."', 
							'".$para."', 
							'".$cc."',
							'".$cco."',
							'".$assunto."',
							'".$msg."',
							131
						 )
				   ";	
						 
		#### ABRE TRANSACAO COM O BD #####
		pg_query($db,"BEGIN TRANSACTION");			
		$ob_resul = @pg_query($db,$qr_sql);
		if(!$ob_resul)
		{
			$ds_erro = str_replace("ERROR:","",pg_last_error($db));
			#### DESFAZ A TRANSACAO COM BD ####
			pg_query($db,"ROLLBACK TRANSACTION");
			pg_close($db);
			echo '<h1 style="color: red; font-weight:bold; font-family: calibri, arial; font-size: 16pt;">ERRO</h1>'; 
			echo '<h1 style="color: red; font-weight:bold; font-family: calibri, arial; font-size: 12pt;">'.$ds_erro.'</h1>'; 
			echo '<BR><BR>'; 
			echo '<PRE>'.$qr_sql.'</PRE>'; 
			exit; 
		}
		else
		{
			#### COMITA DADOS NO BD ####
			pg_query($db,"COMMIT TRANSACTION"); 
		}	
	}	
?>