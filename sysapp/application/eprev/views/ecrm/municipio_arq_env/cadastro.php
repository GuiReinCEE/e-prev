<?php
	set_title('Família Munícipios - Arquivos Recebidos');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('ds_recusado')); ?>

	function ir_lista()
    {
        location.href = "<?= site_url('ecrm/municipio_arq_env') ?>";
    }

	function rejeitar()
	{
		if($("#ds_recusado").val() != '')
		{
			var confirmacao = 
			 	'Deseja rejeitar a documentação?\n\n'+
	            'Clique [Ok] para Sim\n\n'+
	            'Clique [Cancelar] para Não\n\n';

	        if(confirm(confirmacao))
	        { 
				//$("#form_rejeitar").attr("action", "<?= site_url('ecrm/municipio_arq_env/rejeitar') ?>");
				$("#form_rejeitar").submit();
			}
		}
		else
		{
			alert("Informe o motivo.");
		}
	}

</script>
<style>
    #justificativa_item {
        white-space:normal !important;
    }

    #validado {
        white-space:normal !important;
    }
</style>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cancelar', 'Cadastro', TRUE, 'location.reload();');

	echo aba_start($abas);
		
		echo form_start_box('default_box', 'Documento Encaminhado');
			echo form_default_row('', 'Cód:', '<label class="label label-inverse">'.$row['cd_municipio_arq_env'].'</label>');
			echo form_default_row('', 'Status:', '<label class="'.$row['ds_class_status'].'">'.$row['ds_status'].'</label>');
			echo form_default_row('', 'Empresa:', $row['ds_empresa']);
			echo form_default_row('', 'Dt. Referência:', $row['dt_municipio_arq_env']);
			echo form_default_row('', 'Dt. Encaminhamento:', $row['dt_inclusao']);
			echo form_default_row('', 'Tipo Documento:', $row['ds_municipio_arq_tipo']);
			echo form_default_row('', 'Arquivo:', anchor(base_url().'up/extranet_municipio/'.$row['ds_arquivo'], $row['ds_arquivo_nome'], array('target' => '_blank')));
			if(trim($row['dt_status']) != '')
			{
				echo form_default_row('', 'Dt. Retorno:', $row['dt_status']);
				echo form_default_row('', 'Usuário:', $row['ds_usuario_status']);
			}
		echo form_end_box('default_box');
		echo form_open('ecrm/municipio_arq_env/rejeitar', 'id="form_rejeitar"');
			
			echo form_start_box('default_rejeitar_box', 'Rejeitar');
				echo form_default_hidden('cd_municipio_arq_env', '', $row['cd_municipio_arq_env']);
				echo form_default_textarea('ds_recusado', 'Justificativa: (*)', $row['ds_recusado']);
			echo form_end_box('default_rejeitar_box');
		echo form_close();
		echo form_command_bar_detail_start();
			if(trim($row['dt_status']) == '')
			{
				echo button_save('Rejeitar', 'rejeitar();', 'botao_vermelho');
			}
		echo form_command_bar_detail_end();
	echo aba_end();

	$this->load->view('footer');
?>