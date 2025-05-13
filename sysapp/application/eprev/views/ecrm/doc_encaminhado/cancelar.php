<?php
	set_title('Documentos Encaminhados');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('ds_justificativa', 'fl_enviar_email')); ?>

	function ir_lista()
    {
        location.href = "<?= site_url('ecrm/doc_encaminhado') ?>";
    }

    function ir_cadastro()
    {
        location.href = "<?= site_url('ecrm/doc_encaminhado/cadastro/'.intval($row['cd_doc_encaminhado'])) ?>";
    }

	function enviar()
	{
		if($("#fl_enviar_email").val() == 'S')
		{
			var confirmacao = 
			 	'Deseja enviar E-mail?\n\n'+
	            'Clique [Ok] para Sim\n\n'+
	            'Clique [Cancelar] para Não\n\n';
        }
        else
        {
        	var confirmacao = 
			 	'Deseja SEM enviar E-mail?\n\n'+
	            'Clique [Ok] para Sim\n\n'+
	            'Clique [Cancelar] para Não\n\n';
        }

        if(confirm(confirmacao))
        { 
			location.href = "<?= site_url('ecrm/doc_encaminhado/enviar/'.intval($row['cd_doc_encaminhado'])) ?>/"+$("#fl_enviar_email").val();
		}
	}
</script>

<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
	$abas[] = array('aba_cancelar', 'Cancelamento', TRUE, 'location.reload();');

	$enviar_email = array(
		array('value' => 'S', 'text' => 'Sim'),
		array('value' => 'N', 'text' => 'Não')
	);

	echo aba_start($abas);
		echo form_open('ecrm/doc_encaminhado/cancelar');
			echo form_start_box('default_box', 'Documento Encaminhado');
				echo form_default_row('', 'Cód:', '<label class="label label-inverse">'.$row['cd_doc_encaminhado'].'</label>');
				echo form_default_row('', 'Status:', '<label class="'.$row['ds_class_status'].'">'.$row['ds_status'].'</label>');
				echo form_default_row('', 'RE:' ,$row['cd_empresa'].'/'.$row['cd_registro_empregado'].'/'.$row['seq_dependencia']);
				echo form_default_row('', 'Nome:', $row['nome']);
				echo form_default_row('', 'Dt. Encaminhamento:', $row['dt_encaminhamento']);
				echo form_default_row('', 'Tipo Documento:', $row['ds_doc_encaminhado_tipo_doc']);
			echo form_end_box('default_box');
			echo form_start_box('default_box', 'Cancelamento');
				echo form_default_hidden('cd_doc_encaminhado', '', $row['cd_doc_encaminhado']);
				if(trim($row['dt_cancelamento']) != '')
				{
					echo form_default_dropdown('fl_enviar_email', 'Enviar E-mail: (*)', $enviar_email, 'S');
				}
				echo form_default_textarea('ds_justificativa', 'Justificativa: (*)', $row['ds_justificativa']);
			echo form_end_box('default_box');
			echo form_command_bar_detail_start();

				if(trim($row['dt_envio_participante']) == '')
				{
					echo button_save('Salvar');

					if(trim($row['dt_cancelamento']) != '')
					{
						echo button_save('Enviar', 'enviar();', 'botao_verde');
					}
				}
            echo form_command_bar_detail_end();
		echo form_close();
	echo aba_end();

	$this->load->view('footer');
?>