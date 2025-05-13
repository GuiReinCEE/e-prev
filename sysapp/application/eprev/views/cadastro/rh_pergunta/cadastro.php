<?php
	set_title('Sistema de Avaliação - Pergunta');
	$this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array('cd_bloco', 'ds_pergunta', 'cd_cargo')); ?>

    function ir_lista()
	{
		location.href = "<?= site_url('cadastro/rh_pergunta') ?>";
	}
</script>
<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

    echo aba_start($abas); 
        echo form_open('cadastro/rh_pergunta/salvar');
            echo form_start_box('default_box', 'Cadastro');
                echo form_default_hidden('cd_pergunta', '', $row);	
                echo form_default_dropdown('cd_bloco', 'Bloco: (*)', $bloco, $row['cd_bloco']);
                echo form_default_textarea('ds_pergunta', 'Descrição: (*)', $row);
                echo form_default_checkbox_group('classe', 'Classes:', $classe, $pergunta_classe);
            echo form_end_box('default_box');
            echo form_command_bar_detail_start();
				echo button_save('Salvar');	
			echo form_command_bar_detail_end();
        echo form_close();
        echo br(2);
	echo aba_end();

    $this->load->view('footer_interna');
?>