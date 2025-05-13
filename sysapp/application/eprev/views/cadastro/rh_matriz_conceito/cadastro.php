<?php
	set_title('Sistema de Avaliação - Matriz Conceito');
	$this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array('cd_grupo', 'nr_matriz_conceito', 'nr_nota_min', 'nr_nota_max')); ?>

    function ir_lista()
	{
		location.href = "<?= site_url('cadastro/rh_matriz_conceito') ?>";
	}
</script>
<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

    echo aba_start($abas); 
        echo form_open('cadastro/rh_matriz_conceito/salvar');
            echo form_start_box('default_box', 'Cadastro');
                echo form_default_hidden('cd_matriz_conceito', '', $row);	
                echo form_default_dropdown('cd_grupo', 'Grupo: (*)', $grupo, $row['cd_grupo']);
                echo form_default_integer('nr_matriz_conceito', 'Nº Matriz: (*)', $row);
                echo form_default_numeric('nr_nota_min', 'Nota Minima: (*)', number_format($row['nr_nota_min'], 2, ',', '.'));
                echo form_default_numeric('nr_nota_max', 'Nota Máxima: (*)', number_format($row['nr_nota_max'], 2, ',', '.'));
            echo form_end_box('default_box');
            echo form_command_bar_detail_start();
				echo button_save('Salvar');	
			echo form_command_bar_detail_end();
        echo form_close();
        echo br(2);
	echo aba_end();

    $this->load->view('footer_interna');
?>