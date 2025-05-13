<?php
	echo  '<CENTER><input id="btProtDigitalizacao" type="button" onclick="novo_protocolo();" value="Novo Protocolo" class="botao" style="display:none; width: 230px;"></CENTER>';
	
	$body = Array();
	$head = array(
		'<input type="checkbox"  id="checkboxCheckAll" onclick="checkAll();" title="Clique para Marcar ou Desmarcar Todos">',
		'RE',
		'Participante',
		'Documento',
		'Usuário',
		'Data',
		'Observação',
		'Folhas',
		'Arquivo',
		'Obs do Recebimento',
		'',
		''
	);

	$qt_recebido = 0;
	$qt_total = 0;
	
	foreach($row['itens'] as $item)
	{
		$grupo_usuarios = Array(); 
		$fl_textarea = false;
		
		$textarea = '<span id="span_obs_recebimento_'.$item['cd_documento_recebido_item'].'">'.form_textarea(array('name' => 'obs_recebimento_'.$item['cd_documento_recebido_item'], 'id' => 'obs_recebimento_'.$item['cd_documento_recebido_item'], 'cols' => 10), $item['ds_observacao_recebimento'], 'onblur="salvar_obs_recebimento('.$item['cd_documento_recebido_item'].')" style="width:200px; height:50px;"').'</span>';
		
		foreach($row['grupo_destino'] as $it)
		{	
			$grupo_usuarios[] = intval($it['cd_usuario']);
		}
		
		$bt_comando_cadastro = "";
		
		if(($row['cd_usuario_cadastro'] == $this->session->userdata('codigo')) and ($item['dt_recebimento'] == '') and ($row['dt_envio'] == ''))
		{
			#### EDITAR ####
			$ar_editar = array("onclick" => 'editar_documento('.$item['cd_documento_recebido_item'].'); return false;');
			$bt_comando_cadastro = anchor("" ,"[editar]",$ar_editar);			
			
			#### EXCLUIR ####
			$ar_excluir = array(
				"onclick" => "excluir_item(".$item['cd_documento_recebido_item']."); return false;",
				"style" => "color: red;"
			);
			
			$bt_comando_cadastro.= nbs().anchor("" ,"[excluir]",$ar_excluir);			
		}	

		$bt_comando_receber = "";
		if(($item['dt_recebimento'] == '') and ($row['dt_envio'] != '') and (($row['cd_usuario_destino'] == usuario_id()) or (in_array(usuario_id(),$grupo_usuarios)))) 
		{
			#### RECEBER ####
			$ar_receber = array(
				"onclick" => "receber_documento(".$item['cd_documento_recebido_item'].", ".intval($item['nr_folha_pdf'])."); return false;",
				"style" => "color: green;"
			);
			$bt_comando_receber = anchor("" ,"[receber]",$ar_receber);			
			
			#### EXCLUIR ####
			$ar_excluir = array(
				"onclick" => "excluir_justificar(".$item['cd_documento_recebido_item']."); return false;",
				"style" => "color: red;"
			);
			$bt_comando_receber.= nbs().anchor("" ,"[excluir]",$ar_excluir);
			$fl_textarea = true;
		}
		else
		{
			if($item['dt_recebimento']!='')
			{
				$bt_comando_receber = 'Recebido em '.$item['dt_recebimento'].' por '.$item['guerra_usuario_recebimento'] . ' da ' . $item['gerencia_usuario_recebimento'];
				$fl_textarea = false;
				$qt_recebido++;
			}
		}
		
		$qt_total++;				
		
		$fl_campo_check = ($item['dt_recebimento'] != "" ? true : false);
		
		$campo_check = array(
			'name'        => 'cd_doc_item_'.$item['cd_documento_recebido_item'],
			'id'          => 'cd_doc_item_'.$item['cd_documento_recebido_item'],
			'value'       => $item['cd_documento_recebido_item'],
			'checked'     => FALSE,
			'style'       => ($fl_campo_check ? "" : "display:none;")
			);			
		
		$id = "informarRE_".$item['cd_documento_recebido_item'];
		
		$body[] = array( 
				'<input type="hidden" id="fl_doc_item_'.$item['cd_documento_recebido_item'].'" name="fl_doc_item_'.$item['cd_documento_recebido_item'].'" value="'.(((trim($item['arquivo']) != "") and (intval($item['cd_registro_empregado']))) ? "S" : "N").'">'
				.
				form_checkbox($campo_check),
				
				(
					(intval($item['cd_registro_empregado']) == 0) ? 
					
					(
						((trim($item['arquivo']) != "") and ($item['dt_recebimento'] == "")) ?
						'<span id="campo_'.$id.'" style="display:none">'			
						.form_default_participante(
										array($id.'_cd_empresa',$id.'_cd_registro_empregado',$id.'_seq_dependencia', $id.'_nome_participante')
										,''
										, false
										, false
										, true
										, 'carregar_dados_participante('.$item['cd_documento_recebido_item'].',emp,re,seq,data);'
										, false
										)
						.'</span>'
						.'<a href="javascript:void(0);" onclick="informarRE(this,'.$item['cd_documento_recebido_item'].');" title="Informar RE">[informar]</a>'
						:
						""
					)
					: 
					$item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia']
				),
				
				
				
				array($item['nome'],'text-align:left;'),
				array($item['nome_documento'],'text-align:left;'),
				array($item['nome_usuario_cadastro'],'text-align:left;'),
				$item['dt_cadastro'],
				array($item['ds_observacao'],'text-align:left;'),
				$item['nr_folha'],
				(trim($item['arquivo']) != "" ? anchor(base_url()."up/documento_recebido/".$item['arquivo'],$item['arquivo_nome'],array('title' => 'Ver','target' => '_blank')) : ""),
				array(($fl_textarea ? $textarea : $item['ds_observacao_recebimento']),'text-align:left;'),
				
                $bt_comando_cadastro,
				
				'<span id="receber_documento_'.$item['cd_documento_recebido_item'].'">'
					.$bt_comando_receber.
				'</span>'
			);
	}

	$ar_oculta = Array();

	if(($ar_protocolo['dt_envio'] == '') and (usuario_id() != intval($ar_protocolo['cd_usuario_cadastro'])))
	{
		$ar_oculta = Array(10);
	}

	
	$this->load->helper('grid');
	$grid = new grid();
	$grid->id_tabela  = 'tabela_documento';
	$grid->head       = $head;
	$grid->body       = $body;
	$grid->col_oculta = $ar_oculta;
	echo $grid->render();

	echo form_hidden('qt_recebido', $qt_recebido);
	echo form_hidden('qt_total', $qt_total);
	
	if(($ar_protocolo['dt_envio'] != '') and (intval($qt_recebido) > 0))
	{
		echo '
			<script>
				$("#btProtDigitalizacao").show();
			</script>
			';
	}

?>