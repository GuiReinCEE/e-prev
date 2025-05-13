<?php
	set_title('Sistema de Avaliação - Matriz Quadro');
	$this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array('ds_matriz_acao', 'fl_progrecao', 'fl_promocao', 'cor_fundo', 'cor_texto')); ?>

    function ir_lista()
	{
		location.href = "<?= site_url('cadastro/rh_matriz_quadro') ?>";
	}
</script>
<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

    echo aba_start($abas); 
        echo form_open('cadastro/rh_matriz_quadro/salvar');
            echo form_start_box('default_box', 'Cadastro');
                echo form_default_hidden('cd_matriz_quadro', '', $row);	
                echo form_default_dropdown('cd_matriz_conceito_a', 'Conceito A: (*)', $conceito, $row['cd_matriz_conceito_a']);
                echo form_default_dropdown('cd_matriz_conceito_b', 'Conceito B: (*)', $conceito, $row['cd_matriz_conceito_b']);
                echo form_default_dropdown('cd_matriz_acao', 'Ação: (*)', $acao, $row['cd_matriz_acao']);
            echo form_end_box('default_box');
            echo form_command_bar_detail_start();
				echo button_save('Salvar');	
			echo form_command_bar_detail_end();
        echo form_close();
        echo br(2);
	echo aba_end();

    $this->load->view('footer_interna');
?>