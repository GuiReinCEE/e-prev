<?php
	set_title('Sistema de Avaliação - Cargo/Área de Atuação');
	$this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array('cd_cargo', 'cd_gerencia')); ?>

	function ir_lista()
	{
		location.href = "<?= site_url('cadastro/rh_cargo_area_atuacao') ?>";
	}
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

	$area_atuacao = array(
		'rh_avaliacao.area_atuacao',
		'cd_area_atuacao',
		'ds_area_atuacao'
	);

    echo aba_start($abas);
        echo form_open('cadastro/rh_cargo_area_atuacao/salvar');
            echo form_start_box('default_box', 'Cadastro');
                echo form_default_hidden('cd_cargo_area_atuacao', '', $row);	
	        	echo form_default_dropdown('cd_gerencia', 'Gerência (*):', $gerencia, $row['cd_gerencia']);
	        	echo form_default_dropdown('cd_cargo', 'Cargo: (*)', $cargo, $row['cd_cargo']);
	        	//echo form_default_dropdown('cd_area_atuacao', 'Área de Atuação', $area_atuacao, $row['cd_area_atuacao']);
	        	echo form_default_dropdown_db('cd_area_atuacao', 'Área de Atuação:', $area_atuacao, $row['cd_area_atuacao'], '', '', TRUE);
	        	echo form_default_textarea('ds_conhecimento_especifico', 'Conhecimentos Específicos:', $row);
            echo form_end_box('default_box');
            echo form_command_bar_detail_start();
				echo button_save('Salvar');	
			echo form_command_bar_detail_end();
        echo form_close();
		echo br(2);
    echo aba_end();

    $this->load->view('footer');
?>