<?php
	set_title('Sistema de Avaliação - Bloco');
	$this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array('cd_grupo', 'ds_bloco', 'fl_conhecimento')); ?>

    function ir_lista()
	{
		location.href = "<?= site_url('cadastro/rh_bloco') ?>";
	}
</script>
<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

    $drop = array(
        array('value' => 'N', 'text' => 'Não'),
        array('value' => 'S', 'text' => 'Sim')
    );

    echo aba_start($abas); 
        echo form_open('cadastro/rh_bloco/salvar');
            echo form_start_box('default_box', 'Cadastro');
                echo form_default_hidden('cd_bloco', '', $row);	
                echo form_default_dropdown('cd_grupo', 'Grupo: (*)', $grupo, $row['cd_grupo']);
                echo form_default_text('ds_bloco', 'Nome: (*)', $row, 'style="width:400px;"');
                echo form_default_textarea('ds_bloco_descricao', 'Descrição:', $row);
                echo form_default_dropdown('fl_conhecimento', 'Habilitar Info de Conhecimentos:(*)', $drop, $row['fl_conhecimento']);
            echo form_end_box('default_box');
            echo form_command_bar_detail_start();
				echo button_save('Salvar');	
			echo form_command_bar_detail_end();
        echo form_close();
        echo br(2);
	echo aba_end();

    $this->load->view('footer');
?>