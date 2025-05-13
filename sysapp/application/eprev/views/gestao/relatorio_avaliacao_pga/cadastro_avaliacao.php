<?php
    set_title('Indicadores de Gestão do PGA – Avaliação da Diretoria Executiva');
    $this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array('ds_avaliacao')) ?>
   
    function ir_lista()
    {
        location.href = "<?= site_url('gestao/relatorio_avaliacao_pga/index') ?>";
    }
	
    function ir_indicador()
    {
        location.href = "<?= site_url('gestao/relatorio_avaliacao_pga/indicador/'.$row['cd_relatorio_avaliacao_pga']) ?>";
    }
</script>

<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_indicador', 'Indicador', FALSE, 'ir_indicador();');
    $abas[] = array('aba_cadastro_avaliacao', 'Cadastro Avaliação', TRUE, 'location.reload();');

    echo aba_start($abas);
		echo form_open('gestao/relatorio_avaliacao_pga/salvar_indicador');
			echo form_start_box('default_box', 'Cadastro'); 
				echo form_default_hidden('cd_relatorio_avaliacao_pga_indicador', '', $row['cd_relatorio_avaliacao_pga_indicador']);
				echo form_default_hidden('cd_relatorio_avaliacao_pga', '', $row['cd_relatorio_avaliacao_pga']);
				echo form_default_hidden('cd_indicador', '', $row['cd_indicador']);
				echo form_default_textarea('ds_avaliacao', 'Avaliação: (*)', $row['ds_avaliacao']);
			echo form_end_box('default_box'); 
			echo form_command_bar_detail_start();   
                echo button_save('Salvar');
            echo form_command_bar_detail_end();
        echo form_close();
        echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?>