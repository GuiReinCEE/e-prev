<?php
	set_title('Treinamento Colaborador');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('ds_justificativa_finalizado')) ?>

	function ir_lista()
	{
		location.href = "<?= site_url('cadastro/treinamento_colaborador') ?>";
	}

	function ir_cadastro()
    {
        location.href = "<?= site_url('cadastro/treinamento_colaborador/cadastro/'.$row['ano'].'/'.$row['numero']) ?>";
    }
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
	$abas[] = array('aba_encerrar', 'Encerrar', TRUE, 'location.reload();');

	echo aba_start($abas);
		echo form_start_box('default_box', 'Avaliação de Treinamento');
			echo form_default_row('', 'Número:', '<span class="label label-inverse">'.$row['ds_ano_numero'].'</span>');
			echo form_default_row('', 'Nome do Evento:', $row['ds_nome_evento']);
			echo form_default_row('', 'Avaliado:', $row['nome']);
		echo form_end_box('default_box');
		echo form_open('cadastro/treinamento_colaborador/salvar_encerramento');
	    	echo form_start_box('default_encerramento_box', 'Cadastro');
	    		echo form_default_hidden('cd_treinamento_colaborador_resposta', '', $row['cd_treinamento_colaborador_resposta']);
	    		echo form_default_hidden('ds_ano_numero', '', $row['ds_ano_numero']);
	    		echo form_default_textarea('ds_justificativa_finalizado', 'Justificativa: (*)', '' , "style='height:120px;'");
	    	echo form_end_box('default_encerramento_box');
	    	echo form_command_bar_detail_start();
	    		echo button_save('Salvar');
	    	echo form_command_bar_detail_end();
	    echo form_close();
		echo br(2);	
	echo aba_end();

	$this->load->view('footer_interna');