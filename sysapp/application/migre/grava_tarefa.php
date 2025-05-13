<?php
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/funcoes_edi.php');
	include_once('inc/class.Email.inc.php');

	/*
	echo "<PRE>";
	print_r($_POST);
	echo "</PRE>";
	exit;
	*/
	
	if(intval($executor) == 0)
	{
		$executor = $mandante;
	}
	
	
	$tthis->load->model('projetos/Tarefas_model');

	$uploadDir          = '/u/www/upload/';
	$txt_dt_inicio_prev = ( $dt_inicio  	== '' ? 'Null' : "'".convdata_br_iso($dt_inicio)."'" );
	$txt_dt_fim_prev  	= ( $dt_fim  		== '' ? 'Null' : "'".convdata_br_iso($dt_fim)."'" );
	$txt_dt_deacordo  	= ( $dt_deacordo  	== '' ? 'Null' : "'".convdata_br_iso($dt_deacordo)."'" );
	$txt_dt_hr_inicio  	= ( $dt_hr_inicio  	== '' ? 'Null' : "'".convdata_br_iso($dt_hr_inicio)."'" );
	$txt_dt_hr_fim  	= ( $dt_hr_fim  	== '' ? 'Null' : "'".convdata_br_iso($dt_hr_fim)."'" );
	$txt_dt_inicio  	= ( $dt_inicio_real == '' ? 'Null' : "'".convdata_br_iso($dt_inicio_real)."'" );
	$txt_hr_inicio  	= ( $hr_inicio_real == '' ? 'Null' : "'".$hr_inicio_real."'" );
	$txt_dt_fim  		= ( $dt_fim_real  	== '' ? 'Null' : "'".convdata_br_iso($dt_fim_real)."'" );
	$txt_hr_fim  		= ( $hr_fim_real  	== '' ? 'Null' : "'".$hr_fim_real."'" );
	$ds_nome_tela       = ( trim($ds_nome_tela) == '' ? $ds_processo : $ds_nome_tela);
	$max_cd_tarefas_layout = ( trim($max_cd_tarefas_layout) == '' ? -1 : $max_cd_tarefas_layout);
	$fl_checklist = ( $_POST["chk_checklist"]=="S" ) ? "S" : "N";

	if(isset($_POST['nr_nivel_prioridade']))
	{
		$nr_nivel_prioridade = intval($_POST['nr_nivel_prioridade']);
	}
	else
	{
		$nr_nivel_prioridade = 0;
	}

	switch ($status) 
	{
		case 'FECHADA':
			$txt_status = 'F';
			break;
		case 'EM PAUSA':
			$txt_status = 'P';
			break;
		case 'ABERTA':
			$txt_status = 'A';
			break;
		default: 
			$txt_status = '?';
	}		
	$v_duracao = (convtempo($duracao) + $dur_ant);

	// ------------------------------------------------------------

	if ($cd_tarefa == '') 
	{
		$args['origem']=$origem;
		$args['executor']=$executor;
		$args['programa']=$programa;
		$args['txt_dt_inicio_prev']=$txt_dt_inicio_prev;
		$args['txt_dt_fim_prev']=$txt_dt_fim_prev;
		$args['txt_dt_hr_inicio']=$txt_dt_hr_inicio;
		$args['txt_dt_hr_fim']=$txt_dt_hr_fim;
		$args['v_duracao']=$v_duracao;
		$args['descricao']=$descricao;
		$args['casos_testes']=$casos_testes;
		$args['tabs_envolv']=$tabs_envolv;
		$args['txt_hr_inicio']=$txt_hr_inicio;
		$args['txt_dt_fim']=$txt_dt_fim;
		$args['txt_hr_fim']=$txt_hr_fim;
		$args['mandante']=$mandante;
		$args['cad_tarefa']=$cad_tarefa;
		$args['prioridade']=$prioridade;
		$args['resumo']=$resumo;
		$args['ds_menu']=$ds_menu;
		$args['fl_orientacao']=$fl_orientacao;
		$args['fl_tipo_grava']=strtoupper($_REQUEST['fl_tipo_grava']);
		$args['ds_nome_tela']=$ds_nome_tela;
		$args['ds_dir']=$ds_dir;
		$args['ds_nome']=$ds_nome;
		$args['ds_delimitador']=$ds_delimitador;
		$args['fl_largura']=$fl_largura;
		$args['ds_ordem']=$ds_ordem;
		$args['fl_checklist']=$fl_checklist;
		$args['nr_nivel_prioridade']=$nr_nivel_prioridade;
		
		$saved=$tthis->Tarefas_model->inserir($args);
		/*
		$sql = "
		        INSERT INTO projetos.tarefas 
		                  ( 
						    cd_atividade, 
							cd_recurso,	
							programa, 
							dt_inicio_prev, 
							dt_fim_prev, 
							dt_hr_inicio, 
							dt_hr_fim, 
							duracao, 
							descricao, 
							casos_testes, 
							tabs_envolv, 
							hr_inicio, 
							dt_fim, 
							hr_fim, 
							cd_mandante, 
							cd_tipo_tarefa, 
							status_atual, 
							prioridade, 
							resumo,
							ds_menu,
							fl_orientacao,
                            fl_tarefa_tipo,
                            ds_nome_tela,
							ds_dir,
							ds_nome_arq,
							ds_delimitador,
							fl_largura,
							ds_ordem,
							fl_checklist,
							nr_nivel_prioridade
						  )
    	             VALUES 
					      ( 
				    		" . $origem . ", 
				    		" . $executor . ", 
			    			'" . $programa . "', 	
				    		" . $txt_dt_inicio_prev . ", 	
				    		" . $txt_dt_fim_prev . ",
				    		" . $txt_dt_hr_inicio . ",
				    		" . $txt_dt_hr_fim . ",
				        	" . $v_duracao . ",
				        	'" . $descricao . "',
				        	'" . $casos_testes . "',
				        	'" . $tabs_envolv . "',
				        	" . $txt_hr_inicio . ",
				        	" . $txt_dt_fim . ",
				        	" . $txt_hr_fim . ",
				        	" . $mandante . ",
					    	" . $cad_tarefa . ",
						 	'AMAN',
						 	'" . $prioridade . "',
						 	'" . $resumo . "',
							'" . $ds_menu . "',
							'" . $fl_orientacao . "',
                            '" . strtoupper($_REQUEST['fl_tipo_grava']) . "',
                            '" . $ds_nome_tela . "',
							'" . $ds_dir . "',
							'" . $ds_nome . "',
							'" . $ds_delimitador . "',
							'" . $fl_largura . "',
							'" . $ds_ordem . "',
							'" . $fl_checklist . "',
							" . intval($nr_nivel_prioridade) . "
				    	  )
			   ";*/
	}
	else 
	{
		$args['executor']=$executor;
		$args['programa']=$programa;
		$args['txt_dt_inicio_prev']=$txt_dt_inicio_prev;
		$args['txt_dt_fim_prev']=$txt_dt_fim_prev;
		$args['txt_dt_hr_inicio']=$txt_dt_hr_inicio;
		$args['txt_dt_hr_fim']=$txt_dt_hr_fim;
		$args['v_duracao']=$v_duracao;
		$args['descricao']=$descricao;
		$args['casos_testes']=$casos_testes;
		$args['tabs_envolv']=$tabs_envolv;
		$args['txt_hr_inicio']=$txt_hr_inicio;
		$args['txt_dt_fim']=$txt_dt_fim;
		$args['txt_hr_fim']=$txt_hr_fim;
		$args['cad_tarefa']=$cad_tarefa;
		$args['prioridade']=$prioridade;
		$args['resumo']=$resumo;
		$args['ds_menu']=$ds_menu;
		$args['fl_orientacao']=$fl_orientacao;
		$args['ds_nome_tela']=$ds_nome_tela;
		$args['ds_dir']=$ds_dir;
		$args['ds_nome']=$ds_nome;
		$args['ds_delimitador']=$ds_delimitador;
		$args['fl_largura']=$fl_largura;
		$args['ds_ordem']=$ds_ordem;
		$args['fl_checklist']=$fl_checklist;
		$args['nr_nivel_prioridade']=$nr_nivel_prioridade;
		$args['origem']=$origem;
		$args['cd_tarefa']=$cd_tarefa;

		$saved=$tthis->Tarefas_model->salvar($args);
		//echo 'salvar';
		//exit;

		/*$sql = " 
				UPDATE projetos.tarefas 
				   SET cd_recurso     = ".$executor.",
			           programa       = '".$programa."',
					   dt_inicio_prev = ".$txt_dt_inicio_prev.",
					   dt_fim_prev    = ".$txt_dt_fim_prev.",
					   dt_hr_inicio   = ".$txt_dt_hr_inicio.",
					   dt_hr_fim      = ".$txt_dt_hr_fim.",
					   duracao        = ".$v_duracao.",
					   descricao      = '".$descricao."',
					   casos_testes   = '".$casos_testes."',
					   tabs_envolv    = '".$tabs_envolv."',
					   hr_inicio      = ".$txt_hr_inicio.",
					   dt_fim         = ".$txt_dt_fim.",
					   hr_fim         = ".$txt_hr_fim.",
					   cd_tipo_tarefa = ".$cad_tarefa.", 
					   prioridade     = '".$prioridade."',
					   resumo         = '".$resumo."',
					   ds_menu        = '".$ds_menu."',
					   fl_orientacao  = '".$fl_orientacao."',
					   ds_nome_tela   = '".$ds_nome_tela."',
					   ds_dir         = '".$ds_dir."',
					   ds_nome_arq    = '".$ds_nome."',
					   ds_delimitador = '".$ds_delimitador."',
					   fl_largura     = '".$fl_largura."',
					   ds_ordem       = '".$ds_ordem."',
					   fl_checklist   = '".$fl_checklist."',
					   nr_nivel_prioridade = ".intval($nr_nivel_prioridade)."
				 WHERE cd_atividade   = ".$origem." 
				   AND cd_tarefa      = ".$cd_tarefa;*/
	}

	// if (pg_query($db, $sql))
	if($saved)
	{
		if ($cd_tarefa == '') 
		{
			$sql = "SELECT max(cd_tarefa) as cd_tarefa 
					  FROM projetos.tarefas 
					 WHERE cd_atividade = ".$origem;
			$rs  = pg_query($db, $sql);
			$reg = pg_fetch_array($rs);
			//$rs = $tthis->db->query($sql);
			//$reg = $rs->row_array();
			
			$cd_tarefa = $reg['cd_tarefa'];			
			$tpEmail   = 'I';	
			$descricao = "Criação da Tarefa.";					
			$status    = "AMAN";
			
		}
		else 
		{
			$sql = "SELECT status_atual 				
			          FROM projetos.tarefas
			         WHERE cd_atividade =  ".$origem."
			           AND cd_tarefa	 = ".$cd_tarefa."
			       ";
			$rs  = pg_query($db, $sql);
			$reg = pg_fetch_array($rs);
			//$rs = $tthis->db->query($sql);
			//$reg = $rs->row_array();
			
			$tpEmail = 'A';
			$descricao = "Alteração de Tarefa. ";		
			$status	= $reg['status_atual'];			
		}
		
		if((trim($status) != "AMAN") or ($tpEmail == 'I'))
		{
			$sql  = " INSERT INTO projetos.tarefa_historico 
						                    (
						                      cd_tarefa,
											  cd_atividade,
											  cd_recurso,
											  timestamp_alteracao,
											  descricao,
											  status_atual
											) 
									   VALUES
									        (
											  $cd_tarefa,
											  $origem,
											  $executor,
											  current_timestamp,
											  '$descricao',
											  '$status'
											) ";								
			//$tthis->db->query($sql);
			pg_query($db, $sql);
		}
		
		################ FORMULARIO DE REPORT ####################
		if ($_REQUEST['fl_tipo_grava'] == 'r')
		{
			#################### PARAMETROS #####################
			$nr_conta = 0;
			$nr_fim   = count($ar_param_nome);
			while ($nr_conta < $nr_fim)
			{
    			if (   trim($ar_param_nome[$nr_conta])!="" 
                    || trim($ar_param_tipo[$nr_conta])!="" 
                    || trim($ar_param_ordem[$nr_conta])!=""
                   ) 
                {
                    $count = "0";
                    if ($ar_cd_tarefas_parametros[$nr_conta]!="") {
                        $sql = "
        
                            SELECT COUNT(*) as quantos
                              FROM projetos.tarefas_parametros
                             WHERE cd_atividade = " . $origem . "
                               AND cd_tarefa = " . $cd_tarefa . "
                               AND cd_tarefas_parametros = " . $ar_cd_tarefas_parametros[$nr_conta] . "
        
                        ";
                        
                        //$result = $tthis->db->query($sql);
                        //$row=$result->row_array();
						$rs  = pg_query($db, $sql);
						$row = pg_fetch_array($rs);						
						
                        if($row) 
                        {
                            $count = $row['quantos'];
                        }
                        else
                        {
                            $count = "0";
                        }
                    }

                    
                    if ($count=="0")
                    {
                        $sql = "SELECT max(cd_tarefas_parametros) as cd_tarefas_parametros 
        						  FROM projetos.tarefas_parametros
        						 WHERE cd_atividade = ".$origem."
        						   AND cd_tarefa = ".$cd_tarefa;
        				$rs  = pg_query($db, $sql);
        				$reg = pg_fetch_array($rs);
        				$cd_tarefas_parametros = ($reg['cd_tarefas_parametros'] + 1);
        				if( trim($ar_param_ordem[$nr_conta]) == "" )
        				{
        					$ar_param_ordem[$nr_conta] = 0;
        				}
        				
        				$sql = "INSERT INTO projetos.tarefas_parametros 
        								  (
        									cd_atividade,
        									cd_tarefa,
        									cd_tarefas_parametros,
        								    ds_campo, 
        								    ds_tipo, 
        								    nr_ordem
        								  )  
        							 VALUES 
        								  (
        									".$origem.", 
        									".$cd_tarefa.", 
        									".$cd_tarefas_parametros.", 
        									'".$ar_param_nome[$nr_conta]."', 
        									'".$ar_param_tipo[$nr_conta]."',
        									".$ar_param_ordem[$nr_conta]."
        								   )
        					   ";
    				}
                    else
                    {
                        $sql = "
                                UPDATE projetos.tarefas_parametros 
                                   SET ds_campo = '" . $ar_param_nome[$nr_conta] . "',
                                       ds_tipo = '" . $ar_param_tipo[$nr_conta] . "', 
                                       nr_ordem = " . $ar_param_ordem[$nr_conta] . "
                                 WHERE cd_atividade = " . $origem . "
                                   AND cd_tarefa = " . $cd_tarefa . "
                                   AND cd_tarefas_parametros = " . $ar_cd_tarefas_parametros[$nr_conta] . "
                               ";
                    }
      				pg_query($db, $sql);
				}
				$nr_conta++;
			}

			#################### TABELAS #####################
			$nr_conta = 0;
			$nr_fim   = count($ar_db);
			while ($nr_conta < $nr_fim)
			{
                $count = "0";
                if ($ar_cd_tarefas_tabelas[$nr_conta]!="") {
                    $sql = "
    
                        SELECT COUNT(*) as quantos
                          FROM projetos.tarefas_tabelas
                         WHERE cd_atividade = " . $origem . "
                           AND cd_tarefa = " . $cd_tarefa . "
                           AND cd_tarefas_tabelas = " . $ar_cd_tarefas_tabelas[$nr_conta] . "
    
                    ";
                    $result = pg_query( $db, $sql );
                    if ($row=pg_fetch_array($result)) 
                    {
                        $count = $row[0];
                    }
                    else
                    {
                        $count = "0";
                    }
                }
                
                if ($count=="0") 
                {
                    $sql = "SELECT max(cd_tarefas_tabelas) as cd_tarefas_tabelas 
    						  FROM projetos.tarefas_tabelas 
    						 WHERE cd_atividade = ".$origem."
    						   AND cd_tarefa = ".$cd_tarefa;
    				$rs  = pg_query($db, $sql);
    				$reg = pg_fetch_array($rs);
    				$cd_tarefas_tabelas = ($reg['cd_tarefas_tabelas'] + 1);
    				
    				$sql = "INSERT INTO projetos.tarefas_tabelas 
    								  (
    									cd_atividade,
    									cd_tarefa,
    									cd_tarefas_tabelas,
    								    ds_banco, 
    								    ds_tabela, 
    								    ds_campo, 
    								    ds_label 
    								  )  
    							 VALUES 
    								  (
    									".$origem.", 
    									".$cd_tarefa.", 
    									".$cd_tarefas_tabelas.", 
    									'".$ar_db[$nr_conta]."', 
    									'".$ar_tabela[$nr_conta]."',
    									'".$ar_campo[$nr_conta]."',
    									'".$ar_label[$nr_conta]."'
    								   )
				   ";
				}
                else
                {
                    $sql = "
                            UPDATE projetos.tarefas_tabelas
                               SET ds_banco = '" . $ar_db[$nr_conta] . "', 
                                   ds_tabela = '" . $ar_tabela[$nr_conta] . "', 
                                   ds_campo = '" . $ar_campo[$nr_conta] . "', 
                                   ds_label = '" . $ar_label[$nr_conta] . "'
                             WHERE cd_atividade = " . $origem . "
                               AND cd_tarefa = " . $cd_tarefa . "
                               AND cd_tarefas_tabelas = " . $ar_cd_tarefas_tabelas[$nr_conta] . "
                   ";
                }
				// echo( $sql ); exit();
                pg_query($db, $sql);
				$nr_conta++;
			}				
			
			#################### ARQUIVOS #####################
			$nr_conta = 0;
			$nr_fim   = COUNT($_FILES['ar_arquivo']['name']);
			while ($nr_conta < $nr_fim)
			{
				$uploadFile = $uploadDir . $_FILES['ar_arquivo']['name'][$nr_conta];
				if (move_uploaded_file( $_FILES['ar_arquivo']['tmp_name'][$nr_conta], $uploadFile) )
				{
					$ds_arq_tipo = $_FILES["ar_arquivo"]["type"][$nr_conta];
					
					$sql = "SELECT max(cd_anexo) as cd_anexo 
				              FROM projetos.anexos_tarefas 
						     WHERE cd_atividade = ".$origem."
						       AND cd_tarefa = ".$cd_tarefa;
					$rs  = pg_query($db, $sql);
					$reg = pg_fetch_array($rs);
					$cd_anexo = ($reg['cd_anexo'] + 1);

					$sql = "
                            INSERT INTO projetos.anexos_tarefas 
					                  (
									    cd_atividade,
										cd_tarefa,
										cd_anexo,
										tipo_anexo,
										caminho
									  )  
					             VALUES 
								      (
									    ".$origem.", 
										".$cd_tarefa.", 
										".$cd_anexo.", 
										'".$ds_arq_tipo."', 
										'".$_FILES['ar_arquivo']['name'][$nr_conta]."'
									   )
					";
					pg_query($db, $sql);
				}
				$nr_conta++;
			}
		}		

		################ FORMULARIO DE FORMS ####################
		if ($_REQUEST['fl_tipo_grava'] == 'f')
		{
			#################### LOVS #####################
			$nr_conta = 0;
			$nr_fim   = Count( $ar_lovs_seq );
			while ( $nr_conta < $nr_fim )
			{
				
                if (

                         trim($ar_lovs_seq[$nr_conta]!="") 
                      || trim($ar_lovs_tabela[$nr_conta]) 
                      || trim($ar_lovs_campo_ori[$nr_conta])!="" 
                      || trim($ar_lovs_campo_des[$nr_conta])!="" 

                  ) 
                {

                    $sql = "SELECT max(cd_tarefas_lovs) as cd_tarefas_lovs 
    						  FROM projetos.tarefas_lovs 
    						 WHERE cd_atividade = ".$origem."
    						   AND cd_tarefa = ".$cd_tarefa;
    				$rs  = pg_query($db, $sql);
    				$reg = pg_fetch_array($rs);
    				$cd_tarefas_lovs = ($reg['cd_tarefas_lovs'] + 1);
    				$sql = "INSERT INTO projetos.tarefas_lovs 
    								  (
    									cd_atividade,
    									cd_tarefa,
    									cd_tarefas_lovs,
    									ds_seq,
    									ds_tabela,
    									ds_campo_ori,
    									ds_campo_des
    								  ) 
    							 VALUES 
    								  (
    									" . $origem . ", 
    									" . $cd_tarefa . ", 
    									" . $cd_tarefas_lovs . ", 
    									'" . $ar_lovs_seq[$nr_conta] . "',
    									'" . $ar_lovs_tabela[$nr_conta] . "',
    									'" . $ar_lovs_campo_ori[$nr_conta] . "',
    									'" . $ar_lovs_campo_des[$nr_conta] . "'
    								   )
    					   ";
    				pg_query($db, $sql);
				}
                
				$nr_conta++;
			}

			#################### TABELAS #####################
			$nr_conta = 0;
			$nr_fim   = Count( $ar_campo );
			while ( $nr_conta < $nr_fim )
			{
				$sql = "SELECT max(cd_tarefas_tabelas) as cd_tarefas_tabelas 
						  FROM projetos.tarefas_tabelas 
						 WHERE cd_atividade = ".$origem."
						   AND cd_tarefa = ".$cd_tarefa;
				$rs  = pg_query($db, $sql);
				$reg = pg_fetch_array($rs);
				$cd_tarefas_tabelas = ($reg['cd_tarefas_tabelas'] + 1);
				
                $ar_tmp = explode(".",$ar_tabela[$nr_conta]);
				
                $count = "0";
                if ($ar_cd_tarefas_tabelas[$nr_conta]!="") {
                    $sql = "
    
                        SELECT COUNT(*) as quantos
                          FROM projetos.tarefas_tabelas
                         WHERE cd_atividade = " . $origem . "
                           AND cd_tarefa = " . $cd_tarefa . "
                           AND cd_tarefas_tabelas = " . $ar_cd_tarefas_tabelas[$nr_conta] . "
    
                    ";
                    $result = pg_query( $db, $sql );
                    if ($row=pg_fetch_array($result)) 
                    {
    					$count = $row[0];
    				}
                    else
                    {
    					$count = "0";
                    }
				}

                if ($count=="0")
                {
                    $sql = "
                            INSERT INTO projetos.tarefas_tabelas 
    								  (
    									cd_atividade,
    									cd_tarefa,
    									cd_tarefas_tabelas,
    								    ds_banco, 
    								    ds_tabela, 
    								    ds_campo, 
    								    ds_label,
    									fl_tipo,
    									fl_campo,
    									ds_vl_dominio,
    									fl_campo_de,
    									fl_visivel
    								  )  
    							 VALUES 
    								  (
    									" . $origem . ", 
    									" . $cd_tarefa . ", 
    									" . $cd_tarefas_tabelas . ", 
    									'" . $ar_tmp[0] . "', 
    									'" . $ar_tmp[1] . "." . $ar_tmp[2] . "',
    									'" . $ar_campo[$nr_conta] . "',
    									'" . $ar_prompt[$nr_conta] . "',
    									'T',
    									'" . $ar_fl_campo[$nr_conta] . "',
    									'" . $ar_vl_dominio[$nr_conta] . "',
    									'" . $ar_fl_campo_de[$nr_conta] . "',
    									'" . $ar_fl_visivel[$nr_conta] . "'
    								   )
                    ";
				}
                else
                {
                    $sql = "
                            UPDATE projetos.tarefas_tabelas 
                               SET ds_campo = '" . $ar_campo[$nr_conta] . "', 
                                   ds_label = '" . $ar_prompt[$nr_conta] . "',
                                   fl_tipo = 'T',
                                   fl_campo = '" . $ar_fl_campo[$nr_conta] . "',
                                   ds_vl_dominio = '" . $ar_vl_dominio[$nr_conta] . "',
                                   fl_campo_de = '" . $ar_fl_campo_de[$nr_conta] . "',
                                   fl_visivel = '" . $ar_fl_visivel[$nr_conta] . "'
                             WHERE cd_atividade = " . $origem . "
                               AND cd_tarefa = " . $cd_tarefa . "
                               AND cd_tarefas_tabelas = " . $ar_cd_tarefas_tabelas[$nr_conta] . "  
                    ";
                }

                pg_query( $db, $sql );
				$nr_conta++;
			}				

			#################### ORDENACAO #####################
			$nr_conta = 0;
			$nr_fim   = count( $ar_ordem );
			while ($nr_conta < $nr_fim)
			{
				$sql = "

                        SELECT max(cd_tarefas_tabelas) as cd_tarefas_tabelas 
						  FROM projetos.tarefas_tabelas 
						 WHERE cd_atividade = ".$origem."
						   AND cd_tarefa = " . $cd_tarefa . "

                ";
				$rs  = pg_query( $db, $sql );
				$reg = pg_fetch_array( $rs );
				$cd_tarefas_tabelas = ( $reg['cd_tarefas_tabelas'] + 1 );
                
                $count = "0";
                
                if ($ar_cd_tarefas_tabelas_ordem[$nr_conta]!="") {
                    $sql = "
    
                        SELECT COUNT(*) as quantos
                          FROM projetos.tarefas_tabelas
                         WHERE cd_atividade = " . $origem . "
                           AND cd_tarefa = " . $cd_tarefa . "
                           AND cd_tarefas_tabelas = " . $ar_cd_tarefas_tabelas_ordem[$nr_conta] . "
    
                    ";
                    $result = pg_query( $db, $sql );
                    if ($row=pg_fetch_row($result)) 
                    {
                        $count = $row["quantos"];
                    }
                    else
                    {
                        $count = "0";
                    }
                }

                $nr_ordem = ( $ar_ordem_campo[$nr_conta]=="" ) ? "null":$ar_ordem_campo[$nr_conta];
                if ($count=="0")
                {
    				$sql = "
                            INSERT INTO projetos.tarefas_tabelas 
    								  (
    									cd_atividade,
    									cd_tarefa,
    									cd_tarefas_tabelas,
    								    ds_banco, 
    								    ds_tabela, 
    								    ds_campo,
    									fl_tipo,
    									nr_ordem
    								  )
    							 VALUES 
    								  (
    									" . $origem . ",
    									" . $cd_tarefa . ",
    									" . $cd_tarefas_tabelas . ",
    									'" . $ar_ordem_db[$nr_conta] . "',
    									'" . $ar_ordem_tabela[$nr_conta] . "',
    									'" . $ar_ordem_campo[$nr_conta] . "',
    									'O',
    									" . $ar_ordem[$nr_conta] . "
    								   )
    					   ";
				}
                else
                {
    				$sql = "
                            UPDATE projetos.tarefas_tabelas
                               SET nr_ordem = " . $ar_ordem[$nr_conta] . ",
                                   ds_campo = '" . $ar_ordem_campo[$nr_conta] . "'
                             WHERE cd_atividade = " . $origem . "
                               AND cd_tarefa = " . $cd_tarefa . "
                               AND cd_tarefas_tabelas = " . $ar_cd_tarefas_tabelas_ordem[$nr_conta] . "
                    ";
                }

                pg_query( $db, $sql );
				$nr_conta++;
			}

			#################### ARQUIVOS #####################
			$nr_conta = 0;
			$nr_fim   = Count( $_FILES['ar_arquivo']['name'] );
			while ( $nr_conta < $nr_fim )
			{
				$uploadFile = $uploadDir . $_FILES['ar_arquivo']['name'][$nr_conta];
				if ( move_uploaded_file( $_FILES['ar_arquivo']['tmp_name'][$nr_conta], $uploadFile) )
				{
					$ds_arq_tipo = $_FILES["ar_arquivo"]["type"][$nr_conta];

					$sql = "
                            SELECT max(cd_anexo) as cd_anexo 
				              FROM projetos.anexos_tarefas 
						     WHERE cd_atividade = " . $origem."
						       AND cd_tarefa = " . $cd_tarefa . "
                    ";
					$rs  = pg_query($db, $sql);
					$reg = pg_fetch_array($rs);
					$cd_anexo = ($reg['cd_anexo'] + 1);

					$sql = "
                            INSERT INTO projetos.anexos_tarefas 
					                  (
									    cd_atividade,
										cd_tarefa,
										cd_anexo,
										tipo_anexo,
										caminho
									  )  
					             VALUES 
								      (
									    " . $origem . ", 
										" . $cd_tarefa . ", 
										" . $cd_anexo . ", 
										'" . $ds_arq_tipo . "', 
										'" . $_FILES['ar_arquivo']['name'][$nr_conta] . "'
									   )
						   ";
						   
					//$tthis->db->query( $sql );
					pg_query( $db, $sql );
				}
				$nr_conta++;
			}
		}				

		################ FORMULARIO DE ARQUIVOS ####################
		if ($_REQUEST['fl_tipo_grava'] == 'a')
		{
			#################### LAYOUT #####################
			$nr_conta = 0;
			$nr_fim   = Count( $ar_lay_tipo );

			while ($nr_conta < $nr_fim)
			{
				$sql = "SELECT max(cd_tarefas_layout) as cd_tarefas_layout 
						  FROM projetos.tarefas_layout 
						 WHERE cd_atividade = " . $origem . "
						   AND cd_tarefa = " . $cd_tarefa;
				
				$rs  = pg_query( $db, $sql );
				$reg = pg_fetch_array( $rs );
				
				if ( $max_cd_tarefas_layout < $ar_lay_tipo_table[$nr_conta] )
				{
					$cd_tarefas_layout = ($reg['cd_tarefas_layout'] + 1);
					$sql = "INSERT INTO projetos.tarefas_layout
									  (
										cd_atividade,
										cd_tarefa,
										cd_tarefas_layout,
									    ds_tipo
									  )  
								 VALUES 
									  (
										".$origem.", 
										".$cd_tarefa.", 
										".$cd_tarefas_layout.", 
										'".$ar_lay_tipo[$nr_conta]."'
									   )
						   ";
						   
					//$tthis->db->query( $sql );
					pg_query($db, $sql);
				}
				else
				{
					$cd_tarefas_layout = $ar_lay_tipo_table[$nr_conta];
				}

				$nr_count = 0;
				eval("\$nr_end = count(\$ar_lay_campo_nome_".$ar_lay_tipo_table[$nr_conta].");");
				eval("\$ar_lay_campo_nome = \$ar_lay_campo_nome_".$ar_lay_tipo_table[$nr_conta].";");
				eval("\$ar_lay_campo_tamanho = \$ar_lay_campo_tamanho_".$ar_lay_tipo_table[$nr_conta].";");
				eval("\$ar_lay_campo_caracteristica = \$ar_lay_campo_caracteristica_".$ar_lay_tipo_table[$nr_conta].";");
				eval("\$ar_lay_campo_formato = \$ar_lay_campo_formato_".$ar_lay_tipo_table[$nr_conta].";");
				eval("\$ar_lay_campo_definicao = \$ar_lay_campo_definicao_".$ar_lay_tipo_table[$nr_conta].";");
				
				while ($nr_count < $nr_end)
				{
					$sql = "SELECT max(cd_tarefas_layout_campo) as cd_tarefas_layout_campo 
							  FROM projetos.tarefas_layout_campo 
							 WHERE cd_atividade = ".$origem."
							   AND cd_tarefa = ".$cd_tarefa."
							   AND cd_tarefas_layout = ".$cd_tarefas_layout;
					
					$rs  = pg_query($db, $sql);
					$reg = pg_fetch_array($rs);
					$cd_tarefas_layout_campo = ($reg['cd_tarefas_layout_campo'] + 1);
					
					$sql = "INSERT INTO projetos.tarefas_layout_campo
									  (
										cd_atividade,
										cd_tarefa,
										cd_tarefas_layout,
										cd_tarefas_layout_campo,
									    ds_nome,
									    ds_tamanho,
									    ds_caracteristica,
									    ds_formato,
									    ds_definicao
									  )  
								 VALUES 
									  (
										".$origem.", 
										".$cd_tarefa.", 
										".$cd_tarefas_layout.", 
										".$cd_tarefas_layout_campo.", 
										'".$ar_lay_campo_nome[$nr_count]."',
										'".$ar_lay_campo_tamanho[$nr_count]."',
										'".$ar_lay_campo_caracteristica[$nr_count]."',
										'".$ar_lay_campo_formato[$nr_count]."',
										'".$ar_lay_campo_definicao[$nr_count]."'
									   )
						   ";
						   
					//$tthis->db->query( $sql );
					pg_query($db, $sql);					
					$nr_count++;
				}
				$nr_conta++;
			}
			
			#################### ARQUIVOS #####################
			$nr_conta = 0;
			$nr_fim   = count($_FILES['ar_arquivo']['name']);
			while ($nr_conta < $nr_fim)
			{
				$uploadFile = $uploadDir.$_FILES['ar_arquivo']['name'][$nr_conta];
				if (move_uploaded_file($_FILES['ar_arquivo']['tmp_name'][$nr_conta], $uploadFile))
				{
					$ds_arq_tipo = $_FILES["ar_arquivo"]["type"][$nr_conta];
					
					$sql = "SELECT max(cd_anexo) as cd_anexo 
				              FROM projetos.anexos_tarefas 
						     WHERE cd_atividade = ".$origem."
						       AND cd_tarefa = ".$cd_tarefa;
					$rs  = pg_query($db, $sql);
					$reg = pg_fetch_array($rs);
					$cd_anexo = ($reg['cd_anexo'] + 1);

					$sql = "INSERT INTO projetos.anexos_tarefas 
					                  (
									    cd_atividade,
										cd_tarefa,
										cd_anexo,
										tipo_anexo,
										caminho
									  )  
					             VALUES 
								      (
									    ".$origem.", 
										".$cd_tarefa.", 
										".$cd_anexo.", 
										'".$ds_arq_tipo."', 
										'".$_FILES['ar_arquivo']['name'][$nr_conta]."'
									   )
						   ";
					
					//$tthis->db->query( $sql );
					pg_query($db, $sql);
				}
				$nr_conta++;
			}
		}		

		pg_close($db);

		if ($chk_encaminhar == 'S') 
		{
 
	//echo "teste 1";
	//exit;

 			header('location: encaminha_tarefa.php?a='.$origem.'&t='.$cd_tarefa.'&f='.$_REQUEST['fl_tipo_grava']);
		}
		else
		{

	//echo "teste 2";
	//exit;

 			header('location: frm_tarefa.php?os='.$origem.'&c='.$cd_tarefa.'&f='.$_REQUEST['fl_tipo_grava']);
		}
   }
   else
   {
      pg_close($db);
	  echo "Ocorreu um erro ao tentar incluir esta tarefa";
   }
   function convdata_br_iso($dt)
   {
      // Pressupõe que a data esteja no formato DD/MM/AAAA
      // A melhor forma de gravar datas no PostgreSQL é utilizando 
      // uma string no formato DDDD-MM-AA. Esta função justamente 
      // adequa a data a este formato
      $d = substr($dt, 0, 2);
      $m = substr($dt, 3, 2);
      $a = substr($dt, 6, 4);
      return $a.'-'.$m.'-'.$d;
   }
   function convtempo($hr)
   {
      // Pressupõe que a data esteja no formato HH:MM:SS
      $h = substr($hr, 0, 2);
      $m = substr($hr, 3, 2);
      $s = substr($hr, 6, 2);
      return ($h * 3600) + ($m * 60) + $s;
   }
?>