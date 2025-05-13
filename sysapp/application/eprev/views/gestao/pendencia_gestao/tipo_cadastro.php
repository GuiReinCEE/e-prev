<?php
    set_title('Pendências Gestão - Cadastro');
    $this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('ds_reuniao_sistema_gestao_tipo')) ?>

	function ir_lista()
	{
		location.href = '<?= site_url('gestao/pendencia_gestao/tipo') ?>';
	}
</script>
<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

    echo aba_start($abas);
    	echo form_open('gestao/pendencia_gestao/tipo_salvar');
    		echo form_start_box('default_box', 'Cadastro');
    			echo form_default_hidden('cd_reuniao_sistema_gestao_tipo', '', $row['cd_reuniao_sistema_gestao_tipo']);	
    			echo form_default_text('ds_reuniao_sistema_gestao_tipo', 'Tipo de Reunião: (*)', $row['ds_reuniao_sistema_gestao_tipo'], 'style="width:350px;"');
    		echo form_end_box('default_box');
    		echo form_command_bar_detail_start();
    			echo button_save('Salvar');	
    		echo form_command_bar_detail_end();
    	echo form_close();
        echo br(2);
    echo aba_end();
    $this->load->view('footer');
?>