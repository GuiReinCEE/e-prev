<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');

	$tthis->load->model( 'projetos/Tarefas_model' );

	$fl_tarefa_forms    = 'none';
	$fl_tarefa_reports  = 'none';
	$fl_tarefa_arquivos = 'none';
	if($fl_rel=='S')
	{
		header( 'location:'.base_url().'index.php/atividade/tarefa/imprimir/'.$os.'/'.$c);
		$tpl = new TemplatePower('tpl/tpl_frm_tarefa_imprimir.html');
		if($f == 'r')
		{
			$fl_tarefa_reports = "";
		}
		else if($f == 'f')
		{
			$fl_tarefa_forms = "";
		}
		else if($f == 'a')
		{
			$fl_tarefa_arquivos = "";
		}
	}
	else
	{
		if($c == '')
		{
			if($f == 'f')
			{
				$tpl = new TemplatePower('tpl/tpl_frm_nova_tarefa_forms.html');
			}
			elseif($f == 'r')
			{
				$tpl = new TemplatePower('tpl/tpl_frm_nova_tarefa_reports.html');
			}
			elseif($f == 'a')
			{
				$tpl = new TemplatePower('tpl/tpl_frm_nova_tarefa_arquivos.html');
			}
			else
			{				
				$tpl = new TemplatePower('tpl/tpl_frm_nova_tarefa.html');
			}
		}
		else
		{
			if($f == 'r')
			{
				$tpl = new TemplatePower('tpl/tpl_frm_tarefa_reports.html');
			}
			elseif($f == 'f')
			{
				$tpl = new TemplatePower('tpl/tpl_frm_tarefa_forms.html');
			}
			elseif($f == 'a')
			{
				$tpl = new TemplatePower('tpl/tpl_frm_tarefa_arquivos.html');
			}
			else
			{
				$tpl = new TemplatePower('tpl/tpl_frm_tarefa.html');
			}
		}
	}

// ----------------------------------------------------------------------------
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	$tpl->newBlock('cadastro');
   	$tpl->assign('os', $os);
	$tpl->assignGlobal('origem', $os);
	$tpl->assign('fl_tipo_grava', $f);
	$tpl->assignGlobal('aa', $D);
	$tpl->assign( "site_url", site_url());
	$tpl->assign('historico', site_url('atividade/tarefa_historico/index/'.$os.'/'.$c));
	$tpl->assign('anexo', site_url('atividade/tarefa_anexo/index/'.$os.'/'.$c));
	$tpl->assign('execucao', site_url('atividade/tarefa_execucao/index/'.$os.'/'.$c));
	$tpl->assign('checklist', site_url('atividade/tarefa_checklist/index/'.$os.'/'.$c));
	$tpl->assign('url_imprimir', site_url('atividade/tarefa/imprimir/'.$os.'/'.$c));

	if ($D != 'GI')
	{
   		header('location: acesso_restrito.php?IMG=banner_exec_tarefa');
	}

	### USADO NA VERSAO DE IMPRESSÃO ###
	$tpl->assign('fl_tarefa_reports', $fl_tarefa_reports);
	$tpl->assign('fl_tarefa_forms', $fl_tarefa_forms);
	$tpl->assign('fl_tarefa_arquivos', $fl_tarefa_arquivos);
// ----------------------------------------------------------------------------
	if ($c != '')	
	{
		$reg=$tthis->Tarefas_model->carregar( $c, $os );
		
		$tpl->assign('codigo', $c);
		$tpl->assign('cd_tarefa', $reg['cd_tarefa']);
		$tpl->assign('cd_atividade', $os);
		$tpl->assign('dt_inicio', $reg['dt_inicio_prev']);
		$tpl->assign('dt_fim', $reg['dt_fim_prev']);
		$tpl->assign('tp_tarefa', $reg['tipo']);
		$tpl->assign('cd_habil', $reg['cd_habilidade']);

		$tpl->assign('ver_checklist', ( $reg['fl_checklist']=='S' )?'display:;':'display:none;' );

		if($fl_rel== "S")
		{
			$tpl->assign('descricao', nl2br( $reg['descricao']));
		}
		else
		{
			$tpl->assign('descricao', ($reg['descricao']));
		}

		$tpl->assign('resumo', $reg['resumo']);
		$tpl->assign('obs', $reg['observacoes']);

		$tpl->assign('dur_ant', $reg['duracao']);

		if($fl_rel== "S")
		{
			$tpl->assign('casos_testes', nl2br( $reg['casos_testes']));
		}
		else
		{
			$tpl->assign('casos_testes', ( $reg['casos_testes']));
		}	

		if($fl_rel== "S")
		{
			$tpl->assign('tabs_envolv', nl2br( $reg['tabs_envolv']));
		}
		else
		{
			$tpl->assign('tabs_envolv', ( $reg['tabs_envolv']));
		}		

        $tpl->assign('dt_cadastro',  $reg['dt_cadastro']);
        $tpl->assign('hr_inicio_real',  $reg['hr_inicio']);
        $tpl->assign('dt_fim_real',  $reg['dt_fim']);
        $tpl->assign('hr_fim_real',  $reg['hr_fim']);
		$tpl->assign('dt_ok_anal',  $reg['dt_ok_anal']);
		$tpl->assign('dt_fim_prog',  $reg['dt_fim_prog']);
		$tpl->assign('dt_inicio_prog',  $reg['dt_inicio_prog']);
		$tpl->assign('imagem',  $reg['imagem']);
		$tpl->assign('cd_recurso',  $reg['cd_recurso']);
		$tpl->assign('rel_prioridade',  $reg['ds_prioridade']);
		$tpl->assign('rel_orientacao',  $reg['ds_orientacao']);
		$tpl->assign('rel_largura',  $reg['ds_largura']);

		$tpl->assign('nao_ok',  'javascript:showConfirma('.$reg['cd_atividade'].','.$reg['cd_tarefa'].','.$reg['cd_recurso'].')');

		if($reg['status_atual'] !='LIBE')
		{
			$tpl->assign('visualiza_botoes_ok',  'display:none;');
		}

		if(trim($reg['dt_encaminhamento']) != "")
		{
			$tpl->assign('fl_encaminhamento_nao', 'display:none;');
			$tpl->assign('fl_encaminhamento_sim', 'display:block;  font-size: 8pt; font-style:italic; color:green;');
		}		
		else
		{
			$tpl->assign('fl_encaminhamento_nao', 'display:block;');
			$tpl->assign('fl_encaminhamento_sim', 'display:none;');
		}	

		$cd_atividade = $reg['cd_atividade'];
		$v_cd_tarefa = $reg['cd_tarefa'];
		$v_cd_recurso = $reg['cd_recurso'];
		$v_cd_mandante = $reg['cd_mandante'];
		$v_programa = $reg['programa'];
		$cd_tipo_tarefa = $reg['cd_tipo_tarefa'];
		$prioridade = $reg['prioridade'];
		$nr_nivel_prioridade = $reg['nr_nivel_prioridade'];
		$fl_orientacao = $reg['fl_orientacao'];
		$fl_largura = $reg['fl_largura'];
		$fl_checkbox = $reg['fl_checkbox'];
		$fl_checklist = $reg['fl_checklist'];
	}
	
	$prioridade =='S' ? $tpl->assign('chkPrioridadeSim','checked') : $tpl->assign('chkPrioridadeNao','checked');
	$fl_orientacao =='P' ? $tpl->assign('fl_orientacao_paisagem','checked') : $tpl->assign('fl_orientacao_retrato','checked');	
	$fl_largura =='S' ? $tpl->assign('fl_orientacao_sim','checked') : $tpl->assign('fl_orientacao_nao','checked');

	$tpl->assign('nr_nivel_prioridade_'.$nr_nivel_prioridade,'selected');

	if($fl_checklist=='S')
	{
		$tpl->assign('checklistSim','checked');
	}
	else
	{
		$tpl->assign('checklistNao', 'checked');
	}

// ----------------------------------------------------------------------------
// ----------------------------------------------------- se é uma nova Ação, TR vem com 'I'
	if ($op == 'A') {
		$n = 'U';
	}
	else {
		$n = 'I';
	}
	$tpl->assign('cd_tarefa', $c);
	$tpl->assign('insere', $n);
if ($c != "")
{
	############### FORMULARIO REPORTS ###############
	if($f == "r") 
	{
		if($fl_rel== "S")
		{
			$tpl->assign('ds_menu', (str_replace(" ", "&nbsp;",str_replace("\t", "&nbsp;&nbsp;&nbsp;&nbsp;", $reg['ds_menu']))));
		}
		else
		{
			$tpl->assign('ds_menu', $reg['ds_menu']);
		}		

		##### PARAMETROS #####
		$rs = $tthis->Tarefas_model->listar_tarefas_parametros($c,$os);
		
		foreach($rs as $reg)
		{
			$tpl->newBlock('parametros');

            $temp = "<input type='text' name='ar_param_nome[]' value='" . $reg['ds_campo'] . "' />";
			$tpl->assign('ds_campo',  $temp);

            $temp = "<input type='text' name='ar_param_tipo[]' value='" . $reg['ds_tipo'] . "' >";
			$tpl->assign('ds_campo_tipo', $temp );

            $temp = "<input type='text' name='ar_param_ordem[]' value='" . $reg['nr_ordem'] . "' maxlength='2' style='width:60px; text-align:right;' onKeyPress='JavaScript:return formataNumero(event);'>";
			$tpl->assign('nr_campo_ordem', $temp);

			$tpl->assign('cd_atividade',  $os);		
			$tpl->assign('cd_tarefa',  $c);		
			$tpl->assign('fl_tipo_grava',  'r');		
			$tpl->assign('cd_tarefas_parametros',  $reg['cd_tarefas_parametros']);
		}

		##### TABELAS #####
		$rs = $tthis->Tarefas_model->listar_tarefas_tabelas($c,$os);
		foreach( $rs as $reg )
		{
			$tpl->newBlock('detalhe_tabelas_report');

            $temp = "<input type='text' name='ar_db[]' value='" . $reg['ds_banco'] . "' readonly style='color:#999999;' >";
            $tpl->assign('ds_banco',  $temp);
			$temp = "<input type='text' name='ar_tabela[]' value='" . $reg['ds_tabela'] . "' readonly style='color:#999999;' >";
            $tpl->assign('ds_tabela',  $temp);
            $temp = "<input type='text' name='ar_campo[]' value='" . $reg['ds_campo'] . "' />";
			$tpl->assign('ds_campo',  $temp);
            $temp = "<input type='text' name='ar_label[]' value='" . $reg['ds_label'] . "' >";
			$tpl->assign('ds_label',  $temp);

			$tpl->assign('cd_atividade',  $os);
			$tpl->assign('cd_tarefa',  $c);
			$tpl->assign('fl_tipo_grava',  'r');
			$tpl->assign('cd_tarefas_tabelas',  $reg['cd_tarefas_tabelas']);			
		}
		/*
		##### ARQUIVOS #####
		$rs = $tthis->Tarefas_model->listar_anexos_tarefas( $c,$os );
		foreach($rs as $reg)
		{
			$tpl->newBlock('anexo_report');
			$tpl->assign('ds_arquivo',  $reg['ds_arquivo']);
			$tpl->assign('ds_arquivo_tipo',  $reg['ds_arquivo_tipo']);

			$tpl->assign('cd_atividade',  $os);		
			$tpl->assign('cd_tarefa',  $c);		
			$tpl->assign('fl_tipo_grava',  'r');		
			$tpl->assign('cd_anexo',  $reg['cd_anexo']);				
		}
		*/
	}
	################################################
	############### FORMULARIO FORMS ###############
	if($f == "f") 
	{
		if($fl_rel== "S")
		{
			$tpl->assign('ds_menu', (str_replace(" ", "&nbsp;",str_replace("\t", "&nbsp;&nbsp;&nbsp;&nbsp;", $reg['ds_menu']))));
		}
		else
		{
			$tpl->assign('ds_menu', $reg['ds_menu']);
		}
		$tpl->assign('ds_nome_tela',  $reg['ds_nome_tela']);

		##### LOVS #####
		$sql = " 
			SELECT cd_tarefas_lovs,
					    ds_seq,
					    ds_tabela,
					    ds_campo_ori,
					    ds_campo_des
				   FROM projetos.tarefas_lovs
				  WHERE cd_atividade = ".$os."
			AND cd_tarefa = ".$c."
	    ";
		//$rs = pg_query($db, $sql);
		$rs = $tthis->db->query($sql)->result();
		//while($reg=pg_fetch_object($rs))
		foreach($rs as $reg)
		{
			$tpl->newBlock('lovs');
			$tpl->assign('ds_lovs_seq',  $reg->ds_seq);
			$tpl->assign('ds_lovs_tabela',  $reg->ds_tabela);
			$tpl->assign('ds_lovs_campo_ori',  $reg->ds_campo_ori);
			$tpl->assign('ds_lovs_campo_des',  $reg->ds_campo_des);
			
			$tpl->assign('cd_atividade',  $os);
			$tpl->assign('cd_tarefa',  $c);
			$tpl->assign('fl_tipo_grava',  'f');
			$tpl->assign('cd_tarefas_lovs',  $reg->cd_tarefas_lovs);
		}
		
		##### TABELAS #####
		$sql = " SELECT cd_tarefas_tabelas,
						ds_banco, 
						ds_tabela, 
						ds_campo, 
						ds_label,
						fl_tipo,
						fl_visivel,
                        fl_campo AS fl_campo_id,
						(CASE UPPER(fl_campo)
						      WHEN 'T' THEN 'Text Item'
							  WHEN 'L' THEN 'List Item'
							  WHEN 'C' THEN 'Check Box'
							  WHEN 'R' THEN 'Radio Group'
							  WHEN 'P' THEN 'Push Bottom'
							  WHEN 'D' THEN 'Display Item'
						      ELSE UPPER(fl_campo)
						 END) AS fl_campo,
						ds_vl_dominio,
						(CASE UPPER(fl_campo_de)
                              WHEN 'E' THEN 'Enable'
                              WHEN 'D' THEN 'Disable'
						      ELSE UPPER(fl_campo_de)
	                    END) AS	fl_campo_de, fl_campo_de as fl_campo_de_id
				   FROM projetos.tarefas_tabelas
				  WHERE cd_atividade = ".$os."
					AND cd_tarefa    = ".$c."
					AND fl_tipo      = 'T'
			      ORDER BY UPPER(ds_banco),
				           UPPER(ds_tabela),
                           UPPER(ds_campo)						   
			   ";

		//$rs = pg_query($db, $sql);
		$rs = $tthis->db->query($sql)->result();
		//while($reg=pg_fetch_object($rs))
		foreach($rs as $reg)
		{
			$tpl->newBlock('detalhe_tabelas');
            
            $temp = "<input type='text' name='ar_tabela[]' value='" . $reg->ds_banco.".".$reg->ds_tabela . "' readonly style='color:#999999;' >";
            $tpl->assign('ds_tabela',  $temp);
			
            $temp = "<input type='text' name='ar_campo[]' value='" . $reg->ds_campo . "'>";
            $tpl->assign('ds_campo',  $temp);

            $artmp[0] = ($reg->fl_campo_id=="T") ? " selected" : "";
			$artmp[1] = ($reg->fl_campo_id=="L") ? " selected" : "";
			$artmp[2] = ($reg->fl_campo_id=="C") ? " selected" : "";
			$artmp[3] = ($reg->fl_campo_id=="R") ? " selected" : "";
			$artmp[4] = ($reg->fl_campo_id=="P") ? " selected" : "";
			$artmp[5] = ($reg->fl_campo_id=="D") ? " selected" : "";
            $temp = "
                <select name='ar_fl_campo[]'>
                    <option value='T'" . $artmp[0] . ">Text Item</option>
                    <option value='L'" . $artmp[1] . ">List Item</option>
                    <option value='C'" . $artmp[2] . ">Check Box</option>
                    <option value='R'" . $artmp[3] . ">Radio Group</option>
                    <option value='P'" . $artmp[4] . ">Push Bottom</option>
                    <option value='D'" . $artmp[5] . ">Display Item</option>
                </select>
            ";
            $tpl->assign('fl_campo',  $temp);
            
            $temp = "<input type='text' name='ar_vl_dominio[]' value='".$reg->ds_vl_dominio."' >";
			$tpl->assign('ds_vl_dominio',  $temp);
			

            $artmp[0] = ($reg->fl_campo_de_id=="E") ? " selected" : "";
            $artmp[1] = ($reg->fl_campo_de_id=="D") ? " selected" : "";
            $temp = "
                <select name='ar_fl_campo_de[]'>
                    <option value='E'" . $artmp[0] . ">Enable</option>
                    <option value='D'" . $artmp[1] . ">Disable</option>
                </select>
            ";
            $tpl->assign('fl_campo_de',  $temp);
            
            $temp = "<input type='text' name='ar_prompt[]' value='" . $reg->ds_label . "' >";
			$tpl->assign('ds_label',  $temp);
			
            $artmp[0] = ($reg->fl_visivel=="S") ? " selected" : "";
            $artmp[1] = ($reg->fl_visivel=="N") ? " selected" : "";
            $temp = "
                <select name='ar_fl_visivel[]'>
                    <option value='S'" . $artmp[0] . ">Sim</option>
                    <option value='N'" . $artmp[1] . ">Não</option>
                </select>
            ";
            $tpl->assign('fl_visivel',  $temp);

			$tpl->assign('cd_atividade',  $os);		
			$tpl->assign('cd_tarefa',  $c);		
			$tpl->assign('fl_tipo_grava',  'f');		
			$tpl->assign('cd_tarefas_tabelas',  $reg->cd_tarefas_tabelas);			
		}
		
		##### ORDENAÇÃO #####
		$sql = " 
                 SELECT cd_tarefas_tabelas,
						ds_banco, 
						ds_tabela, 
						ds_campo, 
						ds_label,
						fl_tipo,
						nr_ordem,
						(CASE UPPER(fl_campo)
						      WHEN 'T' THEN 'Text Item'
							  WHEN 'L' THEN 'List Item'
							  WHEN 'C' THEN 'Check Box'
							  WHEN 'R' THEN 'Radio Group'
							  WHEN 'P' THEN 'Push Bottom'
							  WHEN 'D' THEN 'Display Item'
						      ELSE UPPER(fl_campo)
						 END) AS fl_campo,
						ds_vl_dominio,
						(CASE UPPER(fl_campo_de)
                              WHEN 'E' THEN 'Enable'
                              WHEN 'D' THEN 'Disable'
						      ELSE UPPER(fl_campo_de)
	                    END) AS	fl_campo_de
				   FROM projetos.tarefas_tabelas
				  WHERE cd_atividade = " . $os . "
					AND cd_tarefa    = " . $c . "
					AND fl_tipo      = 'O'
               ORDER BY nr_ordem
			   ";
		//$rs = pg_query($db, $sql);
		$rs = $tthis->db->query($sql)->result();
		//while( $reg=pg_fetch_object($rs) )
		foreach( $rs as $reg )
		{
			$tpl->newBlock( 'ordem_tabelas' );
			
            $temp = "<input type='text' name='ar_ordem_db[]' value='" . $reg->ds_banco . "' readonly style='color:#999999;' >";
            $tpl->assign( 'ds_ordem_db',     $temp );
            
            $temp = "<input type='text' name='ar_ordem_tabela[]' value='" . $reg->ds_tabela . "' readonly style='color:#999999;' >";
            $tpl->assign( 'ds_ordem_tabela', $temp );
			
            $temp = "<input type='text' name='ar_ordem_campo[]' value='" . $reg->ds_campo . "' />";;
			$tpl->assign( 'ds_ordem_campo',  $temp );
			
            $temp = "
                <input type='text' name='ar_ordem[]' id='ar_ordem_".$reg->cd_tarefas_tabelas."_a' value='" . $reg->nr_ordem . "'>
                <script>
                    MaskInput( document.getElementById( 'ar_ordem_".$reg->cd_tarefas_tabelas."_a' ), '999999' );
                </script>
            ";
            $tpl->assign( 'nr_ordem',  $temp );

			$tpl->assign( 'cd_atividade',  $os );		
			$tpl->assign( 'cd_tarefa',  $c );		
			$tpl->assign( 'fl_tipo_grava',  'f' );		
			$tpl->assign( 'cd_tarefas_tabelas',  $reg->cd_tarefas_tabelas );
		}

		/*
		##### ARQUIVOS #####
		$sql = " SELECT cd_anexo,
		                caminho AS ds_arquivo,
						tipo_anexo AS ds_arquivo_tipo
				   FROM projetos.anexos_tarefas
				  WHERE cd_atividade = ".$os."
					AND cd_tarefa    = ".$c."
			   ";
		//$rs = pg_query($db, $sql);
		$rs = $tthis->db->query($sql)->result();
		//while( $reg=pg_fetch_object($rs) )
		foreach( $rs as $reg )
		{
			$tpl->newBlock( 'anexo_report' );
			$tpl->assign( 'ds_arquivo',  $reg->ds_arquivo );
			$tpl->assign( 'ds_arquivo_tipo',  $reg->ds_arquivo_tipo );
			
			$tpl->assign( 'cd_atividade',  $os );		
			$tpl->assign( 'cd_tarefa',  $c );		
			$tpl->assign( 'fl_tipo_grava',  'f' );		
			$tpl->assign( 'cd_anexo',  $reg->cd_anexo );				
		}
		*/
	}	
    ###################################################

	############### FORMULARIO ARQUIVOS ###############
	if( $f == "a" ) 
	{
		## 18/01/2007 - Cristiano Jacobsen ##
		$tpl->assign( 'ds_processo',    $reg['ds_nome_tela'] );
		$tpl->assign( 'ds_dir',         $reg['ds_nome_tela'] );
		$tpl->assign( 'ds_nome',        $reg['ds_nome_arq'] );
		$tpl->assign( 'ds_delimitador', $reg['ds_delimitador'] );
		if( $fl_rel== "S" )
		{
			$tpl->assign( 'ds_ordem', ($reg['ds_ordem']) );
			
		}
		else
		{
			$tpl->assign( 'ds_ordem', ($reg['ds_ordem']) );
		}			

		##### TIPOS #####
		$sql = " SELECT cd_tarefas_layout,
		                ds_tipo
				   FROM projetos.tarefas_layout
				  WHERE cd_atividade = ".$os."
					AND cd_tarefa    = ".$c."
			   ";
		//$rs = pg_query( $db, $sql );
		$rs = $tthis->db->query($sql)->result();
		//while( $reg=pg_fetch_object($rs) )
		foreach( $rs as $reg )
		{
			$tpl->newBlock('tipo_layout');
			$tpl->assign('cd_tarefas_layout',  $reg->cd_tarefas_layout);
			$tpl->assign('ds_tipo_arq',  $reg->ds_tipo);
			$cd_tarefas_layout = $reg->cd_tarefas_layout;
			$tpl->assign('cd_atividade',  $os);		
			$tpl->assign('cd_tarefa',  $c);		
			$tpl->assign('fl_tipo_grava',  'a');		
			$tpl->assign('cd_tarefas_layout',  $reg->cd_tarefas_layout);

			$qr_select = " SELECT cd_tarefas_layout_campo,
			                      ds_nome,
								  ds_tamanho,
								  ds_caracteristica,
								  ds_formato,
								  ds_definicao
			                 FROM projetos.tarefas_layout_campo
							WHERE cd_atividade      = ".$os."
							  AND cd_tarefa         = ".$c."
							  AND cd_tarefas_layout = ".$reg->cd_tarefas_layout;
			//$ob_data = pg_query($db, $qr_select);
			$ob_data = $tthis->db->query($sql)->result();
			foreach($ob_data as $ob_row )
			{
				$tpl->newBlock('tipo_layout_campo');
				$tpl->assign('ds_tipo_nome',  $ob_row->ds_nome);
				$tpl->assign('ds_tamanho',  $ob_row->ds_tamanho);
				$tpl->assign('ds_caracteristica',  $ob_row->ds_caracteristica);
				$tpl->assign('ds_formato',  $ob_row->ds_formato);
				if($fl_rel== "S")
				{
					$tpl->assign('ds_definicao', ($ob_row->ds_definicao));
					
				}
				else
				{
					$tpl->assign('ds_definicao', $ob_row->ds_definicao);
				}				
				$tpl->assign('cd_atividade',  $os);		
				$tpl->assign('cd_tarefa',  $c);		
				$tpl->assign('fl_tipo_grava',  'a');		
				$tpl->assign('cd_tarefas_layout',  $reg->cd_tarefas_layout);
				$tpl->assign('cd_tarefas_layout_campo',  $ob_row->cd_tarefas_layout_campo);			
			}
		}
		$tpl->newBlock('numero_tipo_js');
		$tpl->assign('max_cd_tarefas_layout',  $cd_tarefas_layout);		
		$tpl->newBlock('numero_tipo');
		$tpl->assign('max_cd_tarefas_layout',  $cd_tarefas_layout);		

		/*
		##### ARQUIVOS #####
		$sql = " SELECT cd_anexo,
		                caminho AS ds_arquivo,
						tipo_anexo AS ds_arquivo_tipo
				   FROM projetos.anexos_tarefas
				  WHERE cd_atividade = ".$os."
					AND cd_tarefa    = ".$c."
			   ";
		//$rs = pg_query($db, $sql);
		$rs = $tthis->db->query($sql)->result();
		//while($reg = pg_fetch_object($rs))
		foreach($rs as $reg)
		{
			$tpl->newBlock('anexo_report');
			$tpl->assign('ds_arquivo',  $reg->ds_arquivo);
			$tpl->assign('ds_arquivo_tipo',  $reg->ds_arquivo_tipo);
			
			$tpl->assign('cd_atividade',  $os);		
			$tpl->assign('cd_tarefa',  $c);		
			$tpl->assign('fl_tipo_grava',  'r');		
			$tpl->assign('cd_anexo',  $reg->cd_anexo);				
		}
		*/
	}	
    ##########################################################

}
// ------------------------------------- Combo tipo da tarefa:
	$sql =        " select cd_tarefa as cd_tarefa, nome_tarefa ";
	$sql = $sql . " from   projetos.cad_tarefas ";
	$sql = $sql . " order by nome_tarefa ";
	//$rs = pg_query($db, $sql);
	$rs = $tthis->db->query($sql)->result_array();
	//while ($reg=pg_fetch_array($rs)) {
	foreach($rs as $reg)
	{
		$tpl->newBlock('tarefa');
		$tpl->assign('cod_cad_tarefa', $reg['cd_tarefa']);
		$tpl->assign('nome_cad_tarefa', $reg['nome_tarefa']);
		if ($reg['cd_tarefa'] == $cd_tipo_tarefa) 
		{ 
			$tpl->assign('sel_tarefa', ' selected'); 
			$tpl->assign('rel_nome_cad_tarefa', $reg['nome_tarefa']);
		}
		else if(($f == "f") and (ereg("forms", trim(strtolower($reg['nome_tarefa']))))) 
		{
			$tpl->assign('sel_tarefa', ' selected'); 
			//$tpl->assign('rel_nome_cad_tarefa', $reg['nome_tarefa']);
		}		
		else if(($f == "r") and (ereg("reports", trim(strtolower($reg['nome_tarefa']))))) 
		{
			$tpl->assign('sel_tarefa', ' selected'); 
			//$tpl->assign('rel_nome_cad_tarefa', $reg['nome_tarefa']);
		}	
	
	}
// ------------------------------------- Combo Analista:
	$sql = " 
				SELECT codigo AS cod_analista, 
				       nome 
	              from projetos.usuarios_controledi 
				 where tipo in('N','G') 
				   AND divisao='$S' 
				 order by nome 
			";	
	$rs = pg_query($db, $sql);
   	$tpl->newBlock('mandante');
	$tpl->assign('cod_analista', '');
    $tpl->assign('nome_analista', 'Selecione');
	while ($reg=pg_fetch_array($rs)) 
	{
		$tpl->newBlock('mandante');
		$tpl->assign('cod_analista', $reg['cod_analista']);
		$tpl->assign('nome_analista', $reg['nome']);
		if ($reg['cod_analista'] == $v_cd_mandante) 
		{ 
			$tpl->assign('sel_analista', ' selected'); 
			$tpl->assign('rel_nome_analista', $reg['nome']);
		}
		else if($v_cd_mandante == "")
		{
			if ($reg['cod_analista'] == $Z) 
			{ 
				$tpl->assign('sel_analista', ' selected'); 
				$tpl->assign('rel_nome_analista', $reg['nome']);
			}
		}
	}
// ------------------------------------- Combo Programadores:
	$sql = " 
				SELECT codigo AS cod_atendente, 
				       nome
				  FROM projetos.usuarios_controledi 
				 WHERE (tipo <> 'X' OR codigo=".intval($v_cd_recurso)." ) 
				   AND divisao='$S' 
				 ORDER BY nome 
		   ";
	$rs = pg_query($db, $sql);
	$tpl->newBlock('atendente');
	$tpl->assign('cod_atendente', '');
	$tpl->assign('nome_atendente', 'Selecione');	
	while ($reg=pg_fetch_array($rs))
	{
		$tpl->newBlock('atendente');
		$tpl->assign('cod_atendente', $reg['cod_atendente']);
		$tpl->assign('nome_atendente', $reg['nome']);
		if ($reg['cod_atendente'] == $v_cd_recurso) 
		{ 
			$tpl->assign('sel_atendente', ' selected'); 
			$tpl->assign('rel_nome_atendente', $reg['nome']); 
		}
	}
// ------------------------------------- Combo Programa:
	$sql =        " select programa ";
	$sql = $sql . " from   projetos.programas ";
	$sql = $sql . " order by programa ";
	$rs = pg_query($db, $sql);
	$tpl->newBlock('programa');
	$tpl->assign('programa', '');
	while ($reg=pg_fetch_array($rs)) 
	{
		$tpl->newBlock('programa');
		$tpl->assign('programa', $reg['programa']);
		if ($reg['programa'] == $v_programa) 
		{ 
			$tpl->assign('sel_programa', ' selected'); 
			$tpl->assign('rel_programa', $reg['programa']); 
		} 
		else if(($f == "f") and (ereg("forms", trim(strtolower($reg['programa']))))) 
		{
			$tpl->assign('sel_programa', ' selected'); 
			$tpl->assign('rel_programa', $reg['programa']); 
		}
		else if(($f == "r") and (ereg("reports", trim(strtolower($reg['programa']))))) 
		{
			$tpl->assign('sel_programa', ' selected'); 
			$tpl->assign('rel_programa', $reg['programa']); 
		}	
	}
// ------------------------------------- Combo Tipo da Tarefa:
	$sql =        " select descricao as tipo_tarefa";
	$sql = $sql . " from   listas ";
	$sql = $sql . " where  categoria = 'TTAR'";
	$sql = $sql . " order by descricao ";
	$rs = pg_query($db, $sql);;
	while ($reg=pg_fetch_array($rs))
	{
		$tpl->newBlock('tipo_tarefa');
		$tpl->assign('cd_tipo_tarefa', $reg['tipo_tarefa']);
		$tpl->assign('nome_tipo_tarefa', $reg['tipo_tarefa']);
		if ($reg['tipo_tarefa'] == $v_tarefa) 
		{ 
			$tpl->assign('sel_tipo_tarefa', ' selected'); 
		
		}
	}
// ------------------------------------- Anexos de tarefas:
	/*
	if ($v_cd_tarefa != '') {
		$sql =        " SELECT cd_anexo, tipo_anexo, caminho ";
		$sql = $sql . " FROM   projetos.anexos_tarefas ";
		$sql = $sql . " WHERE  cd_tarefa = $v_cd_tarefa AND cd_atividade = $cd_atividade ";
		//$rs = pg_query($db, $sql);
		$rs = $tthis->db->query($sql)->result_array();
		//while ($reg=pg_fetch_array($rs)) {
		foreach($rs as $reg)
		{
			$tpl->newBlock('anexo');
			$tpl->assign('tipo_doc', $reg['tipo_anexo']);
			$tpl->assign('nome_doc', $reg['caminho']);
			if ($reg['tipo_tarefa'] == $v_tarefa) { $tpl->assign('sel_tipo_tarefa', ' selected'); }
		}
	}
	*/
//--------------------------------------------------    

	pg_close($db);
	$tpl->printToScreen();
	require_once('inc/ajaxobject.php');
?>
