<?php
	set_title('Parecer Período de Experiência');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('cd_formulario_periodo_experiencia_solic', 'cd_usuario_avaliador', 'cd_usuario_avaliado', 'dt_limite')) ?>

	function ir_lista()
    {
        location.href = "<?= site_url('cadastro/formulario_periodo_experiencia/index') ?>";
    }
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

    echo aba_start($abas);
    	echo form_open('cadastro/formulario_periodo_experiencia/salvar');
            echo form_start_box('default_box', 'Cadastro'); 
                echo form_default_hidden('cd_formulario_periodo_experiencia_solic', '', $row['cd_formulario_periodo_experiencia_solic']);
                echo form_default_dropdown('cd_formulario_periodo_experiencia', 'Formulário: (*)', $formulario, $row['cd_formulario_periodo_experiencia']);
                echo form_default_dropdown('cd_usuario_avaliador', 'Avaliador: (*)', $usuario, $row['cd_usuario_avaliador']);
                echo form_default_dropdown('cd_usuario_avaliado', 'Avaliado: (*)', $usuario, $row['cd_usuario_avaliado']);
                echo form_default_date('dt_limite', 'Dt. Limite: (*)', $row['dt_limite']);
                echo form_default_upload_iframe('arquivo', 'formulario_periodo_experiencia', 'Formulário Preenchido:', array($row['arquivo'], $row['arquivo_nome']), 'formulario_periodo_experiencia', true);    
            echo form_end_box('default_box');

	    	echo form_command_bar_detail_start();
                echo button_save('Salvar');
            echo form_command_bar_detail_end();
    	echo form_close();
    echo aba_end();

    $this->load->view('footer_interna');
?>