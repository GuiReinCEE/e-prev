<?php
	set_title('Controle de Documentos GC Investimento');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('ds_justificativa')) ?>

	function ir_lista()
	{
		location.href = "<?= site_url('atividade/protocolo_gc_investimentos') ?>";
	}
	
    function ir_cadastro()
	{
		location.href = "<?= site_url('atividade/protocolo_gc_investimentos/cadastro/'.$row['cd_protocolo_gc_investimentos']) ?>";
	}
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro;');
	$abas[] = array('aba_cadastro', 'Recusar', TRUE, 'location.reload();');
	
	echo aba_start($abas);
		echo form_open('atividade/protocolo_gc_investimentos/salvar_recusa');
			echo form_start_box('default_box', 'Cadastro');
				echo form_default_hidden('cd_protocolo_gc_investimentos', '', $row);	
				echo form_default_row('', 'Documento:', $row['documento']);
				echo form_default_textarea('observacao', 'Observação:', $row, 'style="border: 0px;" readonly');
				echo form_default_row('dt_envio_gc', 'Dt Envio:', $row['dt_envio_gc']);
				echo form_default_row('ds_usuario_envio_gc', 'Envio por:', $row['ds_usuario_envio_gc']);
			echo form_end_box('default_box');
	        echo form_start_box('default_justificativa_box', 'Justificativa');
	            echo form_default_textarea('ds_justificativa', 'Justificativa: (*)') ;
	        echo form_end_box('default_justificativa_box');
			echo form_command_bar_detail_start();

				if((trim($row['dt_encerrar']) == '') AND ((trim($row['dt_envio_gc']) != '') OR (trim($row['dt_recebido']) == '')))
				{
					echo button_save('Recusar');
				}

			echo form_command_bar_detail_end();
		echo form_close();
		echo br();
	echo aba_end();
	$this->load->view('footer_interna');
?>