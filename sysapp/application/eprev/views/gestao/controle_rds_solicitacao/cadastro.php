<?php
	set_title('Solicitação de Número de RDS - Cadastro');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('ds_solicitacao', 'dt_solicitacao')); ?>

	function ir_lista()
	{
		location.href = "<?= site_url('gestao/controle_rds_solicitacao') ?>";
	}
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

	echo aba_start($abas);
		echo form_open('gestao/controle_rds_solicitacao/salvar');
            echo form_start_box('default_box', 'Cadastro');
            	echo form_default_hidden('cd_controle_rds_solicitacao', '', $row['cd_controle_rds_solicitacao']);
            	if(intval($row['cd_controle_rds_solicitacao']) > 0)
            	{
            		echo form_default_row('', 'Número:', '<span class="label label-info">'.$row['nr_controle_rds_solicitacao'].'</span>');
            	}

            	if($fl_permissao)
            	{
            		echo form_default_dropdown('cd_gerencia', 'Gerência:', $gerencia, $row['cd_gerencia']);
            	}
            	else
            	{
            		echo form_default_hidden('cd_gerencia', '', $row['cd_gerencia']);
            	}

            	echo form_default_textarea('ds_controle_rds_solicitacao', 'Assunto: (*)', $row);
            	echo form_default_date('dt_controle_rds_solicitacao', 'Data: (*)', $row);
            echo form_end_box('default_box');
            echo form_command_bar_detail_start();
            	if(intval($row['cd_controle_rds_solicitacao']) == 0 OR $fl_permissao)
            	{
            		echo button_save('Salvar');	
            	}
			echo form_command_bar_detail_end();
        echo form_close();
        echo br(2);
	echo aba_end();
	$this->load->view('footer');
?>