<?php
	set_title('Grupo Ocupacional');
	$this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array('ds_grupo_ocupacional', 'ds_descricao')); ?>

    function ir_lista()
	{
		location.href = "<?= site_url('cadastro/rh_grupo_ocupacional') ?>";
	}
</script>
<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

    echo aba_start($abas); 
        echo form_open('cadastro/rh_grupo_ocupacional/salvar');
            echo form_start_box('default_box', 'Cadastro');
                echo form_default_hidden('cd_grupo_ocupacional', '', $row);	
                echo form_default_text('ds_grupo_ocupacional', 'Grupo Ocupacional: (*)', $row, 'style="width:400px;"');
            echo form_end_box('default_box');
            echo form_command_bar_detail_start();
				echo button_save('Salvar');	
			echo form_command_bar_detail_end();
        echo form_close();
        echo br(2);
	echo aba_end();

    $this->load->view('footer_interna');
?>