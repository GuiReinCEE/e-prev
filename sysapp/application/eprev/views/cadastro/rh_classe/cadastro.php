<?php
	set_title('Sistema de Avaliação - Classe');
	$this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array('cd_cargo')); ?>

	function ir_lista()
	{
		location.href = "<?= site_url('cadastro/rh_classe') ?>";
	}
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

    echo aba_start($abas);
        echo form_open('cadastro/rh_classe/salvar');
            echo form_start_box('default_box', 'Cadastro');
                echo form_default_hidden('cd_classe', '', $row);	
                echo form_default_dropdown('cd_cargo', 'Cargo: (*)', $cargo, $row['cd_cargo']);
                echo form_default_text('ds_classe', 'Classe:', $row, 'style="width:400px;"');
                echo form_default_checkbox_group('padrao', 'Padrão:', $padrao, $classe_padrao, 325, 400);
            echo form_end_box('default_box');
            echo form_command_bar_detail_start();
				echo button_save('Salvar');	
			echo form_command_bar_detail_end();
        echo form_close();
		echo br(2);
    echo aba_end();

    $this->load->view('footer');
?>