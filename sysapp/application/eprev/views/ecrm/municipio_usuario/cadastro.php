<?php
	set_title('Família Munícipios - Usuários');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('cd_empresa', 'ds_nome', 'ds_usuario', 'ds_email', 'ds_senha', 'fl_troca_senha', 'fl_interno')); ?>

	function ir_lista()
    {
        location.href = "<?= site_url('ecrm/municipio_usuario') ?>";
    }

</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cancelar', 'Cadastro', TRUE, 'location.reload();');

	$dropdown = array(
		array('value' => 'S', 'text' => 'Sim'),
		array('value' => 'N', 'text' => 'Não'),
	);

	echo aba_start($abas);
		
		echo form_open('ecrm/municipio_usuario/salvar');
			
			echo form_start_box('default_box', 'Cadastro');
				echo form_default_hidden('cd_usuario', '', $row['cd_usuario']);
				echo form_default_dropdown('cd_empresa', 'Empresa: (*)', $empresa, $row['cd_empresa']);
				echo form_default_text('ds_nome', 'Nome: (*)', $row,'style="width: 300px;"');
				echo form_default_text('ds_usuario', 'Usuário: (*)', $row['ds_usuario'],'style="width: 300px;"');
				echo form_default_text('ds_email', 'E-mail: (*)', $row,'style="width: 300px;"');
				echo form_default_password('ds_senha', 'Senha: (*)', $row['ds_senha'],'style="width: 300px;"');
				echo form_default_hidden('ds_senha_old', '', $row['ds_senha']);
				echo form_default_dropdown('fl_troca_senha', 'Troca a Senha no Primeiro Acesso: (*)', $dropdown, $row['fl_troca_senha']);
				echo form_default_dropdown('fl_interno', 'Usuário Interno: (*)', $dropdown, $row['fl_interno']);
			echo form_end_box('default_box');
			echo form_command_bar_detail_start();
				echo button_save('Salvar');
			echo form_command_bar_detail_end();
		echo form_close();
		
	echo aba_end();

	$this->load->view('footer');
?>