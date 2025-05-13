<?php
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
//	require_once('inc/ajaxobject.php');
	include_once('inc/class.TemplatePower.inc.php');

//http://www.e-prev.com.br/cieprev/sysapp/application/migre/pos_venda_participante_resp.php?EMP_GA=9&RE_GA=7536&SEQ_GA=0&CD_ATENDIMENTO_GA=903976
	
	header( 'location:'.base_url().'index.php/ecrm/posvenda/posvenda_participante/'.trim($_REQUEST['EMP_GA']).'/'.trim($_REQUEST['RE_GA']).'/'.trim($_REQUEST['SEQ_GA']).'/'.$_REQUEST['CD_ATENDIMENTO_GA']);
	
	$tpl = new TemplatePower('tpl/tpl_pos_venda_participante_resp.html');
	$tpl->prepare();
	
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	
	$tpl->assign('n', $n);
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	
	$fl_ja_fez_pos_venda = false;
	
	#### PERMISSOES ####
	$fl_editar = false;
	if(($_SESSION['D'] == "GAP") or ($_SESSION['D'] == "GI"))
	{
		$fl_editar = true;
	}
	
	$tpl->assign('url_salvar', site_url("ecrm/posvenda/envia_email_salvar"));
	
	if((trim($_REQUEST['EMP_GA']) == "") or (trim($_REQUEST['RE_GA']) == "") or (trim($_REQUEST['SEQ_GA']) == ""))
	{
		echo "ERRO!<br><br>PARTICIPANTE NÃO ENCONTRADO";
		exit;
	}
	else
	{
		#### BUSCA PARTICIPANTE ####
		$qr_sql = "
					SELECT p.nome,
					       (SELECT TO_CHAR(MAX(pvp.dt_final),'DD/MM/YYYY HH24:MI')
						      FROM projetos.pos_venda_participante pvp
							 WHERE pvp.cd_empresa            = p.cd_empresa 
							   AND pvp.cd_registro_empregado = p.cd_registro_empregado 
							   AND pvp.seq_dependencia       = p.seq_dependencia
							   AND pvp.dt_exclusao 			 IS NULL
							   AND pvp.dt_final              IS NOT NULL) AS dt_ultimo
					  FROM public.participantes p
					 WHERE p.cd_empresa            = ".$_REQUEST['EMP_GA']."
					   AND p.cd_registro_empregado = ".$_REQUEST['RE_GA']."
					   AND p.seq_dependencia       = ".$_REQUEST['SEQ_GA']."
		          ";
		$ob_resul = pg_query($db, $qr_sql);	
		$ar_reg   = pg_fetch_array($ob_resul);
		if(pg_num_rows($ob_resul) == 0)
		{
			echo "ERRO!<br><br>PARTICIPANTE NÃO ENCONTRADO";
			exit;
		}		
		
		$tpl->assign('nome',                  $ar_reg['nome']);
		
		if(trim($ar_reg['dt_ultimo']) != "")
		{
			$tpl->assign('dt_ultimo', 'Realizou Pós-Venda em: '.$ar_reg['dt_ultimo']);
			$tpl->assign('cor_aviso', 'color:red; font-weight: bold;');
			$fl_ja_fez_pos_venda = true;
		}
		else
		{
			$tpl->assign('dt_ultimo', 'Nunca realizou Pós-Venda');
			$tpl->assign('cor_aviso', '');
		}
		
		$tpl->assign('cd_empresa',            $_REQUEST['EMP_GA']);
		$tpl->assign('cd_registro_empregado', $_REQUEST['RE_GA']);
		$tpl->assign('seq_dependencia',       $_REQUEST['SEQ_GA']);
		$tpl->assign('cd_atendimento',        $_REQUEST['CD_ATENDIMENTO_GA']);

		
		#### VERIFICA POS VENDA ABERTO ####
		$qr_sql = "
					SELECT cd_pos_venda,
					       cd_pos_venda_participante
					  FROM projetos.pos_venda_participante
					 WHERE cd_empresa            = ".$_REQUEST['EMP_GA']."
					   AND cd_registro_empregado = ".$_REQUEST['RE_GA']."
					   AND seq_dependencia       = ".$_REQUEST['SEQ_GA']."
					   AND dt_final              IS NULL
		          ";
		$ob_resul = pg_query($db, $qr_sql);	
		if(pg_num_rows($ob_resul) == 1)
		{
			$ar_reg = pg_fetch_array($ob_resul);
			$tpl->assign('fl_iniciar', 'display:none;');
			$tpl->assign('fl_salvar' , '');
			$tpl->assign('cd_pos_venda_participante', $ar_reg['cd_pos_venda_participante']);
			
			#### CONTINUA POS VENDA ABERTO - LISTA PERGUNTAS ####
			$qr_sql = "
						SELECT cd_pos_venda_pergunta, 
						       ds_pergunta, 
							   CASE WHEN fl_multipla_resposta = 'S'
							        THEN 'checkbox'
									ELSE 'radio'
							   END AS tp_resposta
						  FROM projetos.pos_venda_pergunta
						 WHERE cd_pos_venda = ".$ar_reg['cd_pos_venda']."
						   AND dt_exclusao  IS NULL
						 ORDER BY nr_ordem ASC
			          ";
			$ob_resul = pg_query($db, $qr_sql);	
			$nr_conta = 1;
			while($ar_reg_perg = pg_fetch_array($ob_resul))
			{
				$tpl->newBlock('pergunta');
				$tpl->assign('nr_conta',    $nr_conta);				
				$tpl->assign('cd_pos_venda_pergunta', $ar_reg_perg['cd_pos_venda_pergunta']);				
				$tpl->assign('ds_pergunta', $ar_reg_perg['ds_pergunta']);				
				$nr_conta++;
				
				#### LISTA RESPOSTAS ####
				$qr_sql = "
							SELECT pvr.cd_pos_venda_resposta,
							       pvr.cd_resposta, 
							       COALESCE(pvr.ds_resposta,cd_resposta) AS ds_resposta,
							       pvr.fl_complemento, 
							       pvr.fl_complemento_obrigatorio,
                                   CASE WHEN pvpr.cd_pos_venda_resposta IS NOT NULL
								        THEN 'S'
										ELSE 'N'
								   END AS fl_respondido,
								   pvpr.complemento
							  FROM projetos.pos_venda_resposta pvr
							  LEFT JOIN projetos.pos_venda_participante_resposta pvpr
							    ON pvpr.cd_pos_venda_resposta = pvr.cd_pos_venda_resposta
							   AND pvpr.cd_pos_venda_participante = ".$ar_reg['cd_pos_venda_participante']."
							 WHERE pvr.cd_pos_venda_pergunta      = ".$ar_reg_perg['cd_pos_venda_pergunta']."
							   AND pvr.dt_exclusao  IS NULL
							 ORDER BY  pvr.nr_ordem ASC
				          ";
				$ob_resul_resp = pg_query($db, $qr_sql);	
				while($ar_reg_resp = pg_fetch_array($ob_resul_resp))
				{
					$tpl->newBlock('resposta');
					$tpl->assign('tp_resposta', $ar_reg_perg['tp_resposta']);
					$tpl->assign('cd_pos_venda_pergunta', $ar_reg_perg['cd_pos_venda_pergunta']);				
					
					
					$tpl->assign('fl_respondido', ($ar_reg_resp['fl_respondido'] == 'S' ? 'checked' : ''));				
					$tpl->assign('cd_pos_venda_resposta', $ar_reg_resp['cd_pos_venda_resposta']);				
					$tpl->assign('cd_resposta', $ar_reg_resp['cd_resposta']);				
					$tpl->assign('ds_resposta', $ar_reg_resp['ds_resposta']);
					
					$tpl->assign('fl_complemento', $ar_reg_resp['fl_complemento']);
					$tpl->assign('fl_complemento_obrigatorio', $ar_reg_resp['fl_complemento_obrigatorio']);
					$tpl->assign('complemento', $ar_reg_resp['complemento']);
					
					
					if(($ar_reg_resp['fl_complemento'] == "S") and ($ar_reg_resp['fl_respondido'] == "S"))
					{
						#### COMPLEMENTO E RESPONDIDO ####
						$tpl->assign('complemento_display', '');

					}
					else
					{
						$tpl->assign('complemento_display', 'display:none;');
					}
					if(($ar_reg_resp['fl_complemento'] == "S") and ($ar_reg_resp['fl_respondido'] == "S") and ($ar_reg_resp['fl_complemento_obrigatorio'] == "S"))
					{
						#### COMPLEMENTO OBRIGATORIO E RESPONDIDO ####
						$tpl->assign('complemento_obrigatorio_display', '');
					}	
					else
					{
						$tpl->assign('complemento_obrigatorio_display', 'display:none;');
					}					
				}
			}
		}
		else if(pg_num_rows($ob_resul) > 1)
		{
			echo "ERRO!<br><br>EXISTE MAIS UM POS VENDA ABERTO";
			exit;
		}
		else if ($_POST['fl_iniciar'] == "S")
		{
			$tpl->assign('fl_iniciar', 'display:none;');
			$tpl->assign('fl_salvar' , 'display:none;');			
			#### INICIA POS VENDA ####
			$qr_sql = "
						INSERT INTO projetos.pos_venda_participante
						     (
							   cd_pos_venda, 
							   cd_empresa, 
							   cd_registro_empregado, 
							   seq_dependencia, 
							   dt_inicio, 
							   cd_usuario_inicio,
							   cd_atendimento
							 )
                        VALUES 
						     (
							   (SELECT pv.cd_pos_venda 
							      FROM projetos.pos_venda pv
							     WHERE pv.dt_inclusao = (SELECT MAX(pv1.dt_inclusao) FROM projetos.pos_venda pv1 WHERE pv1.cd_empresa = pv.cd_empresa) --PEGA ULTIMO FORMULARIO
								   AND pv.cd_empresa = COALESCE((SELECT (p.cd_empresa + 1000) AS cd_empresa
							   									FROM public.patrocinadoras pa
							   									JOIN public.participantes p
							   									  ON p.cd_empresa = pa.cd_empresa
							   									JOIN public.protocolos_participantes pp
							   									  ON pp.cd_empresa            = p.cd_empresa
							   									 AND pp.cd_registro_empregado = p.cd_registro_empregado
							   									 AND pp.seq_dependencia       = p.seq_dependencia 
							   									JOIN public.titulares t
							   									  ON t.cd_empresa            = p.cd_empresa
							   									 AND t.cd_registro_empregado = p.cd_registro_empregado
							   									 AND t.seq_dependencia       = p.seq_dependencia
							   								   WHERE pa.tipo_cliente         = 'I'
							   									 AND t.dt_ingresso_eletro    IS NULL
							   									 AND t.dt_cancela_inscricao  IS NULL
							   									 AND pp.dt_confirma          IS NOT NULL
							   									 AND p.cd_empresa            = ".$_POST['EMP_GA']."
							   									 AND p.cd_registro_empregado = ".$_POST['RE_GA']."
							   									 AND p.seq_dependencia       = ".$_POST['SEQ_GA']."),".$_POST['EMP_GA'].")), -- VERIFICA QUAL POS VENDA BUSCAR (100X) POS VENDA NAO PAGOU							   
							   ".$_POST['EMP_GA'].",
							   ".$_POST['RE_GA'].",
							   ".$_POST['SEQ_GA'].",
							   CURRENT_TIMESTAMP,
							   ".$_SESSION['Z'].",
							   ".(trim($_POST['cd_atendimento']) == "" ? 'NULL' : $_POST['cd_atendimento'])."
							 );
			          ";
			#### ---> ABRE TRANSACAO COM O BD <--- ####
			pg_query($db,"BEGIN TRANSACTION");	
			$ob_resul= @pg_query($db,$qr_sql);
			if(!$ob_resul)
			{
				$ds_erro = "ERRO: ".str_replace("ERROR:","",pg_last_error($db));
				#### ---> DESFAZ A TRANSACAO COM BD <--- ####
				pg_query($db,"ROLLBACK TRANSACTION");
				echo $ds_erro;
			}
			else
			{
				#### ---> COMITA DADOS NO BD <--- ####
				pg_query($db,"COMMIT TRANSACTION"); 
				echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL=pos_venda_participante_resp.php?EMP_GA='.$_POST['EMP_GA'].'&RE_GA='.$_POST['RE_GA'].'&SEQ_GA='.$_POST['SEQ_GA'].'">';
			}						  
		}
		else
		{
			#### MOSTRA BOTOES PARA INICIAR POS VENDA ####
			if(!$fl_ja_fez_pos_venda)
			{
				$tpl->assign('fl_iniciar', '');
			}
			else
			{
				$tpl->assign('fl_iniciar', 'display:none;');
			}			
			
			$tpl->assign('fl_salvar' , 'display:none;');			
			
		}
	}

	$tpl->printToScreen();
	pg_close($db);
?>