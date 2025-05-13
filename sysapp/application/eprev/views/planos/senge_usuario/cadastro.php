<?php
set_title('Senge - Usuários Cadastro');
$this->load->view('header');
?>
<script>
	<?php
		echo form_default_js_submit(Array('nome', 'cpf', 'usuario', 'senha', 'tp_usuario', 'fl_troca_senha', 'email'));
	?>
	function ir_lista()
	{
		location.href='<?php echo site_url("planos/senge_usuario"); ?>';
	}
	
	function excluir(cd_usuario)
	{
		if(confirm("Deseja excluir?"))
		{
			location.href='<?php echo site_url("planos/senge_usuario/excluir/".$row['cd_usuario']); ?>';
		}
	}
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');
	
$ar_tp_usuario[] = Array('text' => 'Fundação CEEE', 'value' => 'F');
$ar_tp_usuario[] = Array('text' => 'Consultor', 'value' => 'C');
$ar_tp_usuario[] = Array('text' => 'SINPRO', 'value' => 'S');

$ar_fl_troca_senha[] = Array('text' => 'Sim', 'value' => 'S');
$ar_fl_troca_senha[] = Array('text' => 'Não', 'value' => 'N');
	
echo aba_start( $abas );
	echo form_open('planos/senge_usuario/salvar');
	echo form_start_box( "default_box", "Usuário" );
		echo form_default_hidden('senha_old', "", $row['senha']);		
		if(intval($row['cd_usuario']) > 0)
		{
			echo form_default_text('cd_usuario', "Código: ", $row, "style='width:100%;border: 0px;' readonly" );
			echo form_default_text('dt_inclusao', "Dt. Inclusão: ", $row, "style='width:100%;border: 0px;' readonly" );
		}
		
		if((intval($row['cd_usuario']) > 0) and (trim($row['dt_exclusao']) != ""))
		{		
			echo form_default_text('dt_exclusao', "Dt. Exclusão: ", $row, "style='color: red; width:100%;border: 0px;' readonly" );
		}
		echo form_default_text('nome', "Nome:* ", $row, "style='width:500px;'");
		echo form_default_cpf('cpf', 'CPF:*', $row);
		echo form_default_text('usuario', "Usuário:* ", $row, "style='width: 100%;'");
		echo form_default_password('senha', "Senha:* ", $row, "style='width: 100%;'");
		echo form_default_dropdown('tp_usuario', 'Tipo:*', $ar_tp_usuario, Array($row['tp_usuario']));			
		echo form_default_dropdown('fl_troca_senha', 'Trocar senha 1º acesso:*', $ar_fl_troca_senha, Array($row['fl_troca_senha']));	
		echo form_default_text('email', "Email: ", $row, "style='width: 100%;'");
		echo form_default_telefone('telefone_1', "Telefone 1: ", $row);
		echo form_default_telefone('telefone_2', "Telefone 2: ", $row);
	echo form_end_box("default_box");
	echo form_command_bar_detail_start();
		if(trim($row['dt_exclusao']) == "")
		{
			echo button_save("Salvar");
			if(intval($row['cd_usuario']) > 0)
			{
				echo button_save("Excluir","excluir(".intval($row['cd_usuario']).")","botao_vermelho");
			}			
		}
	echo form_command_bar_detail_end();
	echo form_close();
echo aba_end();
$this->load->view('footer_interna');
?>