<?php
	set_title('Sistema de Avaliação - performance');
	$this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array('cd_bloco', 'ds_performance', 'cd_cargo')); ?>

    function ir_lista()
	{
		location.href = "<?= site_url('cadastro/rh_performance') ?>";
	}
</script>
<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

    echo aba_start($abas); 
        echo form_open('cadastro/rh_performance/salvar');
            echo form_start_box('default_box', 'Cadastro');
                echo form_default_hidden('cd_performance', '', $row);	
                echo form_default_dropdown('cd_grupo', 'Grupo: (*)', $grupo, $row['cd_grupo']);
                echo form_default_text('ds_performance_sigla', 'Sigla: (*)', $row);
                echo form_default_text('ds_performance', 'Conceito: (*)', $row);
                echo form_default_integer('nr_ponto', 'Pontos: (*)', $row);
                echo form_default_textarea('ds_performance_descricao', 'Descrição: (*)', $row);
            echo form_end_box('default_box');
            echo form_command_bar_detail_start();
				echo button_save('Salvar');	
			echo form_command_bar_detail_end();
        echo form_close();
        echo br(2);
	echo aba_end();

    $this->load->view('footer_interna');
?>