<?php
	set_title('Ação de Vendas');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('cd_usuario_responsavel', 'ds_acao_vendas', 'dt_acao_vendas', 'hr_acao_vendas')) ?>

	function ir_lista()
    {
        location.href = "<?= site_url('planos/acao_vendas/index') ?>";
    }
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

	echo aba_start($abas);
		echo form_open('planos/acao_vendas/salvar');
			echo form_start_box('default_box', 'Cadastro');
                echo form_default_hidden('cd_acao_vendas', '', $row['cd_acao_vendas']);
                echo form_default_dropdown('cd_usuario_responsavel', 'Responsável: (*)', $usuario_responsavel, $row['cd_usuario_responsavel']);
                echo form_default_text('ds_acao_vendas', 'Ação: (*)', $row['ds_acao_vendas'], 'style="width:350px;"');
                echo form_default_date('dt_acao_vendas', 'Data: (*)', $row['dt_acao_vendas']);
                echo form_default_time('hr_acao_vendas', 'Hora: (*)', $row['dt_acao_vendas']);
                echo form_default_integer('nr_contatos', 'Nº de Contatos Realizados:', $row['nr_contatos']);
                echo form_default_integer('nr_fechamento', 'Nº de Fechamentos:', $row['nr_fechamento']);
			echo form_end_box('default_box');	
			echo form_command_bar_detail_start();
				echo button_save('Salvar');         	            
		    echo form_command_bar_detail_end();
		echo form_close();
		echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?>
