<?php
    set_title('Indicadores de Gestão do PGA – Avaliação da Diretoria Executiva');
    $this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array('nr_ano')) ?>
   
    function ir_lista()
    {
        location.href = "<?= site_url('gestao/relatorio_avaliacao_pga/index') ?>";
    }
</script>

<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

    echo aba_start($abas);
        echo form_open('gestao/relatorio_avaliacao_pga/salvar');
            echo form_start_box('default_box', 'Cadastro'); 
				echo form_default_hidden('cd_relatorio_avaliacao_pga', '', $row['cd_relatorio_avaliacao_pga']);
				echo form_default_integer('nr_ano', 'Ano: (*)', $row['nr_ano']);
				echo form_default_dropdown('nr_trimestre', 'Trimestre: (*)', $trimestres, $row['nr_trimestre']);
			echo form_end_box('default_box'); 
			echo form_command_bar_detail_start();   
                echo button_save('Salvar');
            echo form_command_bar_detail_end();
        echo form_close();
        echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?>