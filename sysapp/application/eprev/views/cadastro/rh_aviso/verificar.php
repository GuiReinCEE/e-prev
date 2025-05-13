<?php
	set_title('Recursos Humanos - Aviso - Verificar');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(); ?>
</script>
<?php
	$abas[] = array('aba_verificar', 'Verificar', TRUE, 'location.reload();');

	echo aba_start($abas);
		echo form_open('cadastro/rh_aviso/verificar_salvar');
			echo form_start_box('default_box', 'Verificar');
				echo form_default_hidden('cd_rh_aviso_verificacao', '', intval($row['cd_rh_aviso_verificacao']));
				echo form_default_row('', 'Descrição:', '<span class="label label-inverse">'.$row['ds_descricao'].'</span>');
				echo form_default_row('', 'Periodicidade:', '<span class="label label-info">'.$row['ds_periodicidade'].'</span>');
				echo form_default_row('', 'Dt Referência:',  '<span class="label '.((trim($row['dt_verificacao']) == '') ? 'label-important' : '').'">'.$row['dt_referencia'].'</span>');
				echo form_default_row('', '', '<span class="label label-success">'.$row['ds_verificado'].'</span>');
			echo form_end_box('default_box');
			echo form_command_bar_detail_start();
				if (trim($row['dt_verificacao']) == '')
				{
					echo button_save('Verificar');
				}
			echo form_command_bar_detail_end();
		echo form_close();
		echo br(2);
	echo aba_end();
	$this->load->view('footer_interna');
?>