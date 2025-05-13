<?php
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	
	echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL='.site_url("atividade/acompanhamento/cadastro")."/".intval($_REQUEST['c']).'">';
	exit;

	
	if($_REQUEST['print'] == "S")
	{
		$tpl = new TemplatePower('tpl/tpl_cad_acomp_projetos_imprimir.html');
	}
	else
	{
		$tpl = new TemplatePower('tpl/tpl_cad_acomp_projetos.html');
	}

	
	//-----------------------------------------------   
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
//-----------------------------------------------

	if (($D <> 'GI') and ($Z <> 110)) {
   		header('location: acesso_restrito.php?IMG=banner_acomp_projetos');
	}
//--------------------------------------------------------------	

	if($_REQUEST['print'] == "S")
	{
		#### INF IMPRESSAO ####
		$tpl->newBlock('dt_impressao');
		$tpl->assign('dt_impressao', date("d/m/Y"));
		$tpl->assign('ds_usuario', $N);	

		#### NOME DO PROJETO ####
		$sql = " 
				SELECT p.nome
				  FROM projetos.projetos p,
				       projetos.acompanhamento_projetos ap
				 WHERE ap.cd_acomp   = ".$_REQUEST['c']."
				   AND ap.cd_projeto = p.codigo	
			   ";
		$rs  = pg_query($db, $sql);
		$reg = pg_fetch_array($rs);
		$tpl->newBlock('projeto_impressao');
		$tpl->assign('ds_projeto', $reg['nome']);
	}

		$tpl->newBlock('cadastro');
		if (isset($c))	{
			$sql =        " select cd_projeto, cd_acomp, nome_acomp, to_char(dt_acomp, 'dd/mm/yyyy hh24:mi') as dt_acomp, texto_acomp, ";
			$sql = $sql . " status_ar, status_es, status_au, status_de, status_me, ";
			$sql = $sql . " desc_ar, desc_es, desc_au, desc_de, desc_me, ";
			$sql = $sql . " to_char(dt_encerramento, 'dd/mm/yyyy hh24:mi') as dt_encerramento, to_char(dt_cancelamento, 'dd/mm/yyyy hh24:mi') as dt_cancelamento ";
			$sql = $sql . " from projetos.acompanhamento_projetos where cd_acomp=$c " ;
			$rs = pg_exec($db, $sql);
			$reg=pg_fetch_array($rs);
			$tpl->assign('dt_acomp', $reg['dt_acomp']);
			$tpl->assign('cd_acomp', $reg['cd_acomp']);
			$tpl->assign('cd_projeto', $reg['cd_projeto']);
			$tpl->assign('descricao', $reg['texto_acomp']);
			$tpl->assign('desc_ar', $reg['desc_ar']);
			$tpl->assign('desc_es', $reg['desc_es']);
			$tpl->assign('desc_au', $reg['desc_au']);
			$tpl->assign('desc_de', $reg['desc_de']);
			$tpl->assign('desc_me', $reg['desc_me']);
			
			$fl_encerrado_cancelado = "";
			if ($reg['dt_encerramento'] != '') {
				$tpl->assign('status', 'Projeto encerrado em: '. $reg['dt_encerramento']);
				$tpl->assign('cor_fundo_status', '#dae9f7');
				$fl_encerrado_cancelado = "disabled";
			}
			elseif ($reg['dt_cancelamento'] != '') {
				$tpl->assign('status', 'Projeto cancelado em: '. $reg['dt_cancelamento']);
				$tpl->assign('cor_fundo_status', '#F0E8BA');
				$fl_encerrado_cancelado = "disabled";
			}
			else {
				$tpl->assign('status', 'Projeto em andamento');
				$tpl->assign('cor_fundo_status', '#DCDCDC');
			}
			$tpl->assign('fl_encerrado_cancelado', $fl_encerrado_cancelado);
			$tpl->assign('insere', 'A');
			$tpl->assign('cod_proj', $reg['cd_projeto']);
			
			$v_cd_projeto = $reg['cd_projeto'];
			$v_status_ar = $reg['status_ar'];
			$v_status_es = $reg['status_es'];
			$v_status_au = $reg['status_au'];
			$v_status_de = $reg['status_de'];
			$v_status_me = $reg['status_me'];
		}
// ---------------------------------------------------------- Combo PROJETO
		$sql = "";
		$sql = $sql . " SELECT 	codigo, ";
		$sql = $sql . "        	nome  ";
		$sql = $sql . " FROM 	projetos.projetos ";
		$sql = $sql . " WHERE 	dt_exclusao is null  order by nome ";
		$rs = pg_exec($db, $sql);
		while ($reg = pg_fetch_array($rs)) {
			$tpl->newBlock('projeto');
			$tpl->assign('cd_projeto', $reg['codigo']);
			$tpl->assign('nome_projeto', $reg['nome']);
			if ($reg['codigo'] == $v_cd_projeto) { $tpl->assign('sel_projeto', ' selected'); }
		}

		#### LISTA ANALISTA (CHECKBOX) ####
		$sql = " SELECT codigo, 
		                usuario, 
						nome 
		           FROM projetos.usuarios_controledi
				  WHERE (tipo    = 'N' 
				    AND divisao = '" . $D . "') ";
                    
        if($v_cd_projeto!=""){
            $sql .= "
                OR (
                       SELECT COUNT(*)
                         FROM projetos.analista_projeto
                        WHERE cd_analista = projetos.usuarios_controledi.codigo
                          AND cd_projeto = " . $v_cd_projeto . "
                          AND cd_acomp    = " . $c . "
                    ) > 0
            ";
        }
        
        $sql .= " ORDER BY nome ";
        
		$rs = pg_exec($db, $sql);
		while ($reg = pg_fetch_array($rs)) 
		{
			$tpl->newBlock('analista');
			$tpl->assign('cod_analista', $reg['codigo']);
			$tpl->assign('nome_analista', $reg['nome']);
			if ($v_cd_projeto != '') 
			{
					$sql2 = " SELECT cd_analista 
					            FROM projetos.analista_projeto 
							   WHERE cd_projeto  = ".$v_cd_projeto."
							     AND cd_acomp    = ".$c."
							     AND cd_analista = ".$reg['codigo'];

				$rs2 = pg_exec($db, $sql2);
				if ($reg2 = pg_fetch_array($rs2)) 
				{ 
					$tpl->assign('chk_analista', 'checked'); 
				}
			}
		}

		#### REUNIÕES REALIZADAS ####
		$sql = " 
		         SELECT rp.cd_reuniao, 
		                rp.cd_acomp, 
						rp.descricao, 
						rp.envolvidos, 
						rp.motivo, 
						rp.dt_reuniao,
		                TO_CHAR(rp.dt_reuniao, 'DD/MM/YYYY') AS dt_reuniao_ed,
						rp.ds_arquivo,
						rp.ds_arquivo_fisico,
                        uc.nome
				   FROM projetos.reunioes_projetos rp
                   LEFT JOIN projetos.reunioes_projetos_envolvidos rpe
                     ON rpe.cd_acomp   = rp.cd_acomp
                    AND rpe.cd_reuniao = rp.cd_reuniao
                   LEFT JOIN projetos.usuarios_controledi uc
                     ON rpe.cd_usuario = uc.codigo
				  WHERE rp.dt_exclusao IS NULL
				    AND rp.cd_acomp    = ".$c."
				  ORDER BY rp.dt_reuniao DESC, rp.cd_reuniao 
			   ";
		$rs = pg_query($db, $sql);
		$cd_reuniao_atual = "";
		while ($reg = pg_fetch_array($rs)) 
		{
			if($cd_reuniao_atual != $reg['cd_reuniao'])
			{
				$tpl->newBlock('reuniao');
				$tpl->assign('cd_acomp',          $reg['cd_acomp']);			
				$tpl->assign('cd_reuniao',        $reg['cd_reuniao']);
				$tpl->assign('dt_reuniao',        $reg['dt_reuniao_ed']);
				$tpl->assign('desc_reuniao',      $reg['descricao']);
				$tpl->assign('envolv_reuniao',    $reg['envolvidos']);
				$tpl->assign('motivo_reuniao',    $reg['motivo']);
				
				$tpl->assign('ds_arquivo_fisico',    $reg['ds_arquivo_fisico']);
				$fl_anexo = "display:none;";
				if(trim($reg['ds_arquivo']) != "")
				{
					$fl_anexo = "";
				}
				$tpl->assign('fl_anexo', $fl_anexo);
				
				$cd_reuniao_atual = $reg['cd_reuniao'];
			}
			$tpl->newBlock('reuniao_envolvido');
			$tpl->assign('envolvido', $reg['nome']);			
		}

		#### REGISTRO OPERACIONAL ####
		$sql = " 
				SELECT ap.cd_acompanhamento_registro_operacional,
					   ap.ds_nome,
					   TO_CHAR(ap.dt_cadastro,'DD/MM/YYYY') AS dt_cadastro, 
					   ap.cd_acomp,
					   uc.nome AS ds_nome_user,
					   uc.guerra,
					   TO_CHAR(ap.dt_finalizado,'DD/MM/YYYY') AS dt_finalizado 
				  FROM projetos.acompanhamento_registro_operacional ap,
                       projetos.usuarios_controledi uc				  
				 WHERE ap.cd_acomp    = ".$c."
                   AND ap.cd_usuario  = uc.codigo
				   AND ap.dt_exclusao IS NULL
				 ORDER BY ap.dt_cadastro DESC
			   ";
		$rs = pg_query($db, $sql);
		while ($reg = pg_fetch_array($rs)) 
		{
			$tpl->newBlock('lst_operacional');
			$tpl->assign('cd_acomp',       $reg['cd_acomp']);			
			$tpl->assign('cd_operacional', $reg['cd_acompanhamento_registro_operacional']);
			$tpl->assign('ds_nome',        substr($reg['ds_nome'],0,50));
			$tpl->assign('dt_cadastro',    $reg['dt_cadastro']);
			$tpl->assign('ds_autor',       $reg['guerra']);
			$tpl->assign('dt_finalizado',  $reg['dt_finalizado']);
		}
		
		#### ESCOPOS ####
		$sql = " 
				SELECT ae.cd_acompanhamento_escopos, 
					   TO_CHAR(ae.dt_cadastro,'DD/MM/YYYY') AS dt_cadastro, 
					   ae.cd_acomp,
					   uc.nome AS ds_nome_user,
					   uc.guerra
				  FROM projetos.acompanhamento_escopos ae,
                       projetos.usuarios_controledi uc				  
				 WHERE ae.cd_acomp    = ".$c."
                   AND ae.cd_usuario  = uc.codigo
				   AND ae.dt_exclusao IS NULL
				 ORDER BY ae.dt_cadastro DESC
			   ";
		$rs = pg_query($db, $sql);
		while ($reg = pg_fetch_array($rs)) 
		{
			$tpl->newBlock('escopo');
			$tpl->assign('cd_acomp',     $reg['cd_acomp']);			
			$tpl->assign('cd_escopo',    $reg['cd_acompanhamento_escopos']);
			$tpl->assign('dt_cadastro',  $reg['dt_cadastro']);
			$tpl->assign('ds_nome_user', $reg['ds_nome_user']);
		}		

		#### WBS ####
		$sql = " 
				SELECT aw.cd_acompanhamento_wbs, 
				       aw.cd_acomp,
					   TO_CHAR(aw.dt_cadastro,'DD/MM/YYYY') AS dt_cadastro,
					   aw.ds_arquivo,
					   aw.ds_arquivo_fisico,
					   uc.nome AS ds_nome_user
				  FROM projetos.acompanhamento_wbs aw,
                       projetos.usuarios_controledi uc				  
				 WHERE aw.cd_acomp   = ".$c."
                   AND aw.cd_usuario = uc.codigo
				 ORDER BY aw.dt_cadastro DESC
			   ";
		$rs = pg_query($db, $sql);
		while ($reg = pg_fetch_array($rs)) 
		{
			$tpl->newBlock('escopo_wbs');
			$tpl->assign('cd_acomp',          $reg['cd_acomp']);			
			$tpl->assign('cd_wbs',            $reg['cd_acompanhamento_wbs']);
			$tpl->assign('dt_cadastro',       $reg['dt_cadastro']);
			$tpl->assign('ds_arquivo',        $reg['ds_arquivo']);
			$tpl->assign('ds_arquivo_fisico_link', str_replace($reg['ds_arquivo'],"",$reg['ds_arquivo_fisico']));
			$tpl->assign('ds_arquivo_fisico', $reg['ds_arquivo_fisico']);
			$tpl->assign('ds_nome_user',      $reg['ds_nome_user']);
		}
		
		#### MUDANÇA DE ESCOPO ####
		$sql = " 
				SELECT ae.cd_acompanhamento_mudanca_escopo, 
				       nr_numero,
					   TO_CHAR(ae.dt_cadastro,'DD/MM/YYYY') AS dt_cadastro, 
					   ae.cd_acomp,
					   uc.nome AS ds_nome_solicitante,
					   uc.guerra AS ds_solicitante
				  FROM projetos.acompanhamento_mudanca_escopo ae,
                       projetos.usuarios_controledi uc				  
				 WHERE ae.cd_acomp       = ".$c."
                   AND ae.cd_solicitante = uc.codigo
				   AND ae.dt_exclusao    IS NULL
				 ORDER BY ae.dt_cadastro DESC
			   ";
		$rs = pg_query($db, $sql);
		while ($reg = pg_fetch_array($rs)) 
		{
			$tpl->newBlock('mudanca_escopo');
			$tpl->assign('cd_acomp',            $reg['cd_acomp']);			
			$tpl->assign('cd_mudanca_escopo',   $reg['cd_acompanhamento_mudanca_escopo']);
			$tpl->assign('nr_numero',           $reg['nr_numero']);
			$tpl->assign('dt_cadastro',         $reg['dt_cadastro']);
			$tpl->assign('ds_solicitante',      $reg['ds_solicitante']);
			$tpl->assign('ds_nome_solicitante', $reg['ds_nome_solicitante']);
		}			
		
// ---------------------------------------------------------- Combo PROJETO
		$sql = "";
		$sql = $sql . " SELECT 	codigo, descricao, categoria ";
		$sql = $sql . " FROM  	listas ";
		$sql = $sql . " WHERE 	categoria = 'STPJ' order by categoria, codigo ";
		$rs = pg_query($db, $sql);
		while ($reg = pg_fetch_array($rs)) {
			$tpl->newBlock('status_ar');
			$tpl->assign('cd_status_ar', $reg['codigo']);
			$tpl->assign('nome_status_ar', $reg['descricao']);
			if ($reg['codigo'] == $v_status_ar) { $tpl->assign('sel_status_ar', ' selected'); }
			$tpl->newBlock('status_es');
			$tpl->assign('cd_status_es', $reg['codigo']);
			$tpl->assign('nome_status_es', $reg['descricao']);
			if ($reg['codigo'] == $v_status_es) { $tpl->assign('sel_status_es', ' selected'); }
			$tpl->newBlock('status_au');
			$tpl->assign('cd_status_au', $reg['codigo']);
			$tpl->assign('nome_status_au', $reg['descricao']);
			if ($reg['codigo'] == $v_status_au) { $tpl->assign('sel_status_au', ' selected'); }
			$tpl->newBlock('status_de');
			$tpl->assign('cd_status_de', $reg['codigo']);
			$tpl->assign('nome_status_de', $reg['descricao']);
			if ($reg['codigo'] == $v_status_de) { $tpl->assign('sel_status_de', ' selected'); }
			$tpl->newBlock('status_me');
			$tpl->assign('cd_status_me', $reg['codigo']);
			$tpl->assign('nome_status_me', $reg['descricao']);
			if ($reg['codigo'] == $v_status_me) { $tpl->assign('sel_status_me', ' selected'); }
		}
		
		#### PREVISTO PARA O PRÓXIMO MÊS ####
		$sql = " 
				SELECT cd_previsao, 
				       cd_acomp, 
					   descricao, 
					   mes, 
					   ano, 
					   obs,
					   TO_CHAR(dt_previsao, 'DD/MM/YYYY') AS data_previsao, 
					   dt_previsao
				  FROM projetos.previsoes_projetos 
				 WHERE dt_exclusao IS NULL 
				   AND cd_acomp    = ".$c." 
				 ORDER BY dt_previsao DESC 
			   ";
		$rs = pg_query($db, $sql);
		while ($reg = pg_fetch_array($rs)) 
		{
			$tpl->newBlock('prev');
			$tpl->assign('cd_acomp', $reg['cd_acomp']);			
			$tpl->assign('cd_prev', $reg['cd_previsao']);
			$tpl->assign('dt_previsao', $reg['data_previsao']);
			$tpl->assign('desc_prev', $reg['descricao']);
			$tpl->assign('ano_mes', $reg['mes'] . '/' . $reg['ano']);
			$tpl->assign('obs', $reg['obs']);
		}
	pg_close($db);
	

	
	$tpl->printToScreen();	
?>