<?php
	set_title('Documentos Encaminhados');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('ds_descricao')); ?>

	function ir_lista()
    {
        location.href = "<?= site_url('ecrm/doc_encaminhado') ?>";
    }

	function ir_cadastro()
	{
		location.href = "<?= site_url('ecrm/doc_encaminhado/cadastro/'.intval($row['cd_doc_encaminhado'])) ?>";
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
	$abas[] = array('aba_cancelar', 'Cadastro', FALSE, 'ir_cadastro();');
	$abas[] = array('aba_acompanhamento', (trim($fl_andamento) == 'S' ? 'Andamento' : 'Acompanhamento'), TRUE, 'location.reload();');

	$enviar_email = array(
		array('value' => 'S', 'text' => 'Sim'),
		array('value' => 'N', 'text' => 'Não')
	);

	$head = array( 
		'Data',
		'Usuário',
		'Descrição'
	);

	$body = array();

	foreach($collection as $item)
	{
		$body[] = array(
			$item['dt_inclusao'],
			array($item['ds_usuario'], 'text-align:left'),
			array($item['ds_descricao'], 'text-align:justify')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo aba_start($abas);
		
		echo form_start_box('default_box', 'Documento Encaminhado');
			echo form_default_row('', 'Cód:', '<label class="label label-inverse">'.$row['cd_doc_encaminhado'].'</label>');
			echo form_default_row('', 'Status:', '<label class="'.$row['ds_class_status'].'">'.$row['ds_status'].'</label>');
			echo form_default_row('', 'RE:', $row['cd_empresa'].'/'.$row['cd_registro_empregado'].'/'.$row['seq_dependencia']);
			echo form_default_row('', 'Nome:', $row['nome']);
			echo form_default_row('', 'Dt. Encaminhamento:', $row['dt_encaminhamento']);
			echo form_default_row('', 'Tipo Documento:', $row['ds_doc_encaminhado_tipo_doc']);
			if(trim($row['dt_cancelamento']) != '')
			{
				echo form_default_row('', 'Dt. Cancelamento:', $row['dt_cancelamento']);
				echo form_default_row('justificativa', 'Justificativa:', nl2br($row['ds_justificativa']));
			}

			if(trim($row['dt_confirmacao']) != '')
			{
				echo form_default_row('', 'Dt. Confirmação:', $row['dt_confirmacao']);
			}

			if(trim($row['dt_andamento']) != '')
			{
				echo form_default_row('', 'Dt. Andamento:', $row['dt_andamento']);
			}

			if(trim($row['dt_validado']) != '')
			{
				echo form_default_row('', 'Dt. Validado:', $row['dt_validado']);
				echo form_default_row('validado', 'Descrição:', $row['ds_validado']);
			}
			//echo form_default_row('', 'Documento:',(anchor(base_url().'up/doc_encaminhado/'.$row['ds_documento'], $row['ds_documento'], array('target' => '_blank'))));
		echo form_end_box('default_box');
		echo form_open('ecrm/doc_encaminhado/salvar_acompanhamento', 'id="form_documento"');
			echo form_start_box('acompanhamento_box', (trim($fl_andamento) == 'S' ? 'Andamento' : 'Acompanhamento'));
				echo form_default_hidden('cd_doc_encaminhado', '', $row['cd_doc_encaminhado']);
				echo form_default_hidden('fl_andamento', '', $fl_andamento);
				echo form_default_textarea('ds_descricao', 'Descrição: (*)');
			echo form_end_box('acompanhamento_box');
			echo form_command_bar_detail_start();
				echo button_save('Salvar');
			echo form_command_bar_detail_end();
		echo form_close();
		echo $grid->render();
	echo aba_end();

	$this->load->view('footer');
?>