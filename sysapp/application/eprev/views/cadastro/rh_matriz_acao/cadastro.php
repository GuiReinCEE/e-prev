<?php
	set_title('Sistema de Avalia��o - Matriz A��o');
	$this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array('ds_matriz_acao', 'fl_progressao', 'fl_promocao', 'cor_fundo', 'cor_texto')); ?>

    function ir_lista()
	{
		location.href = "<?= site_url('cadastro/rh_matriz_acao') ?>";
	}
</script>
<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

    echo aba_start($abas); 
        echo form_open('cadastro/rh_matriz_acao/salvar');
            echo form_start_box('default_box', 'Cadastro');
                echo form_default_hidden('cd_matriz_acao', '', $row);	
                echo form_default_text('ds_matriz_acao', 'Descri��o: (*)', $row, 'style="width:450px;"');
                echo form_default_dropdown('fl_progressao', 'Progress�o: (*)', $drop, $row['fl_progressao']);
                echo form_default_dropdown('fl_promocao', 'Promo��o: (*)', $drop, $row['fl_promocao']);
                echo form_default_text('cor_fundo', 'Cor Texto: (*)', $row);
                echo form_default_text('cor_texto', 'Cor Fundo: (*)', $row);
            echo form_end_box('default_box');
            echo form_command_bar_detail_start();
				echo button_save('Salvar');	
			echo form_command_bar_detail_end();
        echo form_close();
        echo br(2);
	echo aba_end();

    $this->load->view('footer_interna');
?>