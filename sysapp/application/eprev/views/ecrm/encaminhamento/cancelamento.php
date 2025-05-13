<?php
set_title('Encaminhamentos -> Cancelamento');
$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('ds_observacao_cancelamento')) ?>
	
    function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/encaminhamento"); ?>';
	}

	function ir_cadastro()
	{
		location.href='<?php echo site_url("ecrm/encaminhamento/detalhe"); ?>'+'/'+ $("#cd_atendimento").val()+'/'+$("#cd_encaminhamento").val();
	}
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_jogo', 'Cadastro', FALSE, 'ir_cadastro();');
	$abas[] = array('aba_cancelamento', 'Cancelamento', TRUE, 'location.reload();');

	echo aba_start( $abas );
		echo form_start_box( "default_box", "ATENDIMENTO", true, false);
		    echo form_default_hidden('cd_empresa', "cd_empresa: ", $atendimento['cd_empresa']);
		    echo form_default_hidden('cd_registro_empregado', "cd_registro_empregado: ", $atendimento['cd_registro_empregado']);
		    echo form_default_hidden('seq_dependencia', "seq_dependencia: ", $atendimento['seq_dependencia']);
		    echo form_default_text('', "Nrº do Atendimento: ", $atendimento['cd_atendimento'], "style='width:100%;border: 0px;' readonly" );
		    echo form_default_text('ds_tipo', "Tipo: ", $atendimento['tp_atendimento'], "style='width:100%;border: 0px;' readonly" );
		    echo form_default_text('dt_atendimento', "Dt atendimento: ", $atendimento['dt_atendimento'], "style='width:100%;border: 0px;' readonly" );
		    echo form_default_text('ds_emp', "Emp/re/seq: ", $atendimento['cd_empresa']. " / " . $atendimento['cd_registro_empregado'] . " / ". $atendimento['seq_dependencia'], "style='width:100%;border: 0px;' readonly" );
		    echo form_default_text('ds_nome', "Nome participante: ", $atendimento['nome_participante'], "style='width:500px;border: 0px;' readonly" );
		    echo form_default_text('ds_atendente', "Atendente: ", $atendimento['atendente'], "style='width:100%;border: 0px;' readonly" );
		    echo form_default_text('ds_obs', "Observações: ", $atendimento['tp_atendimento'], "style='width:100%;border: 0px;' readonly" );
		    echo form_default_row('', 'Observação: ', $atendimento['obs']);
		echo form_end_box("default_box");
		echo form_start_box( "default_box", "ENCAMINHAMENTO", true, false);
		    echo form_default_text('', "Encaminhamento nº: ", $encaminhamento['cd_encaminhamento'], "style='width:100%;border: 0px;' readonly"  );
			echo form_default_text('ds_atendimento_encaminhamento_tipo', "Tipo Encaminhamento: ", $encaminhamento['ds_atendimento_encaminhamento_tipo'], "style='width:100%;border: 0px;' readonly"  );
		    echo form_default_text('ds_situacao', "Situação: ", $encaminhamento['fl_atendimento'], "style='width:100%;border: 0px;' readonly" );
		    echo form_default_text('ds_solicitado', "Solicitado por: ", $encaminhamento['solicitante'], "style='width:100%;border: 0px;' readonly" );
		    echo form_default_text('dt_solicitacao', "Dt solicitação: ", $encaminhamento['dt_solicitacao'], "style='width:100%;border: 0px;' readonly" );
		    echo form_default_text('ds_encaminhado', "Processado por: ", $encaminhamento['atendente'], "style='width:100%;border: 0px;' readonly" );
		    echo form_default_text('dt_encaminhamento', "Dt encaminhamento: ", $encaminhamento['dt_encaminhamento'], "style='width:100%;border: 0px;' readonly" );
		    echo form_default_row('', 'Observação: ', "<span style='font-size: 160%; color: green; font-weight: bold;'>".$encaminhamento['texto_encaminhamento']."</span>");
		echo form_end_box("default_box");
		echo form_open('ecrm/encaminhamento/cancela_atendimento');
			echo form_start_box( "default_box_cancelamento", "CANCELAMENTO", true, false);
				echo form_default_hidden('cd_atendimento', '', $atendimento['cd_atendimento'] );
				echo form_default_hidden('cd_encaminhamento', '', $encaminhamento['cd_encaminhamento']);
				echo form_default_textarea('ds_observacao_cancelamento', 'Observação: (*)', '', 'style="width: 500px; height: 80px;"');
			echo form_end_box("default_box_cancelamento");
			echo form_command_bar_detail_start();
				echo button_save("Salvar");
			echo form_command_bar_detail_end();
		echo form_close();
	echo aba_end();
?>