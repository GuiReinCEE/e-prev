<?php 
set_title('Protocolo Interno - Excluir');
$this->load->view('header'); 
?>
<script>
	<?php echo form_default_js_submit( array( "justificativa") ); ?>
	
	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/cadastro_protocolo_interno"); ?>';
	}

	function ir_relatorio()
	{
		location.href="<?php echo site_url('ecrm/cadastro_protocolo_interno/relatorio'); ?>";
	}

	function ir_resumo()
    {
        location.href="<?php echo site_url('ecrm/cadastro_protocolo_interno/resumo'); ?>";
    }
	
	function ir_cadastro()
    {
        location.href="<?php echo site_url('ecrm/cadastro_protocolo_interno/detalhe/'.$row['cd_documento_recebido']); ?>";
    }
	

</script>
<?php
$abas[] = array('aba_lista', 'Lista', false, 'ir_lista();');
$abas[] = array('aba_relatorio', 'Relatório', false, 'ir_relatorio();');
$abas[] = array('aba_resumo', 'Resumo', false, 'ir_resumo();');
$abas[] = array('aba_detalhe', 'Cadastro', false, 'ir_cadastro();');
$abas[] = array('aba_detalhe', 'Excluir', true, 'location.reload();');

$arr_tipo[] = array('text' => 'Normal', 'value' => '0' );
$arr_tipo[] = array('text' => 'Benefício', 'value' => '1' );
$arr_tipo[] = array('text' => 'Inscrição', 'value' => '2' );

echo aba_start( $abas );
	echo form_hidden('cd_documento_recebido', intval($row['cd_documento_recebido']));
	echo form_hidden('ar_proto_selecionado');
	// *** INÍCIO DO BOX PRINCIPAL
	echo form_start_box( "default_box", "Protocolo Interno" );
		if(intval($row["cd_documento_recebido"])>0)
		{
			echo form_default_text("protocolo", "Número: ", $row["nr_documento_recebido"], 'style="border: 0px; width: 500px; font-weight: bold;" readonly' );	
			echo form_default_text("ds_tipo", "Tipo: ", $row["ds_tipo"], 'style="border: 0px; width: 500px;" readonly' );	
			echo form_default_hidden( "cd_tipo", "", $row["cd_documento_recebido_tipo"] );
			echo form_default_text("dt_cadastro", "Protocolado em: ", $row["dt_cadastro"] . ' por ' . $row["nome_usuario_cadastro"], 'style="border: 0px; width: 500px;" readonly' );
			
			if(($row['dt_devolucao'] != "") and ($row["dt_envio"] == ""))
			{
				echo form_default_text("dt_devolucao", "Devolvido em: ", $row["dt_devolucao"] . ' por ' . $row["devolvido_por"], 'style="color: red; font-weight: bold; border: 0px; width: 500px;" readonly' );
				echo form_default_textarea("devolucao_descricao", "Justificativa da devolução: ", $row["devolucao_descricao"], " style='height: 70px; border: 1px solid gray;' readonly");
			}

			// *** data de envio
			if(($row["dt_envio"] == '') and (usuario_id() == intval($row['cd_usuario_cadastro'])))
			{
				echo form_default_row( "escolher_destino", "", br().comando("enviar_button", "Enviar Documentos (escolher destino)", "escolher_destino(this.form)") );
			}
			else
			{
				if($row['dt_redirecionamento']!='')
				{
					if($row["nome_usuario_destino"]!='')
					{
						echo form_default_text("dt_redirecionamento", "Redirecionado em: ", $row["dt_redirecionamento"] . ' para ' . $row["nome_usuario_destino"], 'style="color: green; font-weight: bold; border: 0px; width: 500px;" readonly' );
					}
					elseif($row["grupo_destino_nome"]!='')
					{
						echo form_default_text("dt_envio", "Encaminhado em: ", $row["dt_envio"] . ' para o grupo ' . $row["grupo_destino_nome"], 'style="color: green; font-weight: bold; border: 0px; width: 500px;" readonly' );
					}
				}
				else
				{
					if($row["nome_usuario_destino"]!='')
					{
						echo form_default_text("dt_envio", "Encaminhado em: ", $row["dt_envio"] . ' para ' . $row["nome_usuario_destino"], 'style="color: green; font-weight: bold; border: 0px; width: 500px;" readonly' );				
					}
					elseif($row["grupo_destino_nome"]!='')
					{
						echo form_default_text("dt_envio", "Encaminhado em: ", $row["dt_envio"] . ' para o grupo ' . $row["grupo_destino_nome"], 'style="color: green; font-weight: bold; border: 0px; width: 500px;" readonly' );
					}
				}

				if($row['dt_ok']!='')
				{
					echo form_default_text("dt_ok", "Encerrado em: ", $row["dt_ok"] . ' por ' . $row["nome_usuario_ok"], 'style="color: blue; font-weight: bold; border: 0px; width: 500px;" readonly' );
				}
				else
				{
					echo form_default_hidden("dt_ok","","");
				}
			}
		}
		else
		{
			echo form_default_dropdown_db("cd_documento_recebido_tipo", "Tipo *", array( "projetos.documento_recebido_tipo", "cd_documento_recebido_tipo", "ds_tipo" ), array( $row["cd_documento_recebido_tipo"] ), "", "", FALSE, ""); 
		}
	echo form_end_box("default_box");
	echo form_start_box( "default_box_documento", "Documento" );
		echo form_default_text("re", "RE: ", $row['cd_empresa'].'/'.$row['cd_registro_empregado'].'/'.$row['seq_dependencia'], 'style="border: 0px; width: 500px;" readonly' );	
		echo form_default_text("participante", "Participante: ", $row["nome"], 'style="border: 0px; width: 500px;" readonly' );	
		echo form_default_text("documento", "Documento: ", $row["nome_documento"], 'style="border: 0px; width: 500px;" readonly' );	
		echo form_default_text("usuário", "Usuário: ", $row["nome_usuario_cadastro_item"], 'style="border: 0px; width: 500px;" readonly' );	
		echo form_default_text("data", "Data: ", $row["dt_cadastro"], 'style="border: 0px; width: 500px;" readonly' );	
		echo form_default_text("observação", "Observação: ", $row["ds_observacao"], 'style="border: 0px; width: 500px;" readonly' );	
		echo form_default_text("folhas", "Folhas: ", $row["nr_folha"], 'style="border: 0px; width: 500px;" readonly' );	
		echo form_default_row('arquivo','Arquivo:', (trim($row['arquivo']) != "" ? anchor(base_url()."up/documento_recebido/".$row['arquivo'],$row['arquivo_nome'],array('title' => 'Ver','target' => '_blank')) : ""));
		echo form_default_text("obs", "Obs do Recebimento: ", $row["ds_observacao_recebimento"], 'style="border: 0px; width: 500px;" readonly' );	
	echo form_end_box("default_box_documento");
	echo form_open('ecrm/cadastro_protocolo_interno/excluir_justificado', 'name="filter_bar_form_cadastro"');
		echo form_start_box( "default_box", "Justificativa" );
			echo form_default_hidden( "cd_documento_recebido_item", "", $row["cd_documento_recebido_item"] );
			echo form_default_textarea('justificativa', 'Justificativa :*');
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();   
				echo button_save("Excluir", null, "botao_vermelho");
		echo form_command_bar_detail_end();
	echo form_close();
echo aba_end();

$this->load->view('footer_interna');
?>