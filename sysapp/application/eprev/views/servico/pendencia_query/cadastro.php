<?php
	set_title('Pendêcias - Query');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('cd_pendencia_minha', 'ds_descricao', 'fl_superior')) ?>

	function ir_lista()
    {
        location.href = "<?= site_url('servico/pendencia_query') ?>";
    }
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

	$superior = array(
		array('text' => 'Sim', 'value' => 'S'), 
		array('text' => 'Não', 'value' => 'N')
	);

	echo aba_start($abas);
		echo form_open('servico/pendencia_query/salvar');
			echo form_start_box('default_box', 'Cadastro');
				echo form_default_hidden('cd_pendencia_minha_query', '', $row['cd_pendencia_minha_query']);
				echo form_default_dropdown('cd_pendencia_minha', 'Pendêcia: (*)', $pendencia_minha, $row['cd_pendencia_minha']);
				echo form_default_text('ds_descricao', 'Descrição: (*)', $row['ds_descricao'], "style='width:600px; '");
				echo form_default_dropdown('fl_superior', 'Superior: (*) ', $superior, $row['fl_superior']);			
		    	echo form_default_editor_code('ds_pendencia_minha_query', 'Query: (*)', $row['ds_pendencia_minha_query'], "style='width:1000px; height: 300px;'");
			echo form_end_box('default_box');	
			echo form_command_bar_detail_start();
				echo button_save('Salvar');         	            
		    echo form_command_bar_detail_end();
		echo form_close();
		echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?>