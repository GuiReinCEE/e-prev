<?php
set_title('Fam�lia Previd�ncia - Usu�rio - Cadastro');
$this->load->view('header');
?>
<script>
	<?php
		echo form_default_js_submit(Array('nome',
				                          'usuario',
										  'senha',
										  'tp_usuario',
										  'fl_troca_senha'));
	?>
	function ir_lista()
	{
		location.href='<?php echo site_url("planos/familia_previdencia_usuario"); ?>';
	}
	
	
	function excluir(cd_usuario)
	{
		if(confirm("Deseja excluir?"))
		{
			location.href='<?php echo site_url("planos/familia_previdencia_usuario/excluir"); ?>' + "/" + cd_usuario;
		}
	}
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');
	
	echo aba_start( $abas );
	
	echo form_open('planos/familia_previdencia_usuario/salvar');
	echo form_start_box( "default_box", "Usu�rio" );
		echo form_default_text('cd_usuario', "C�digo: ", $row, "style='width:100%;border: 0px;' readonly" );
		echo form_default_hidden('senha_old', "", $row['senha']);
		if(intval($row['cd_usuario']) > 0)
		{
			echo form_default_text('dt_inclusao', "Dt. Inclus�o: ", $row, "style='width:100%;border: 0px;' readonly" );
		}
		
		if((intval($row['cd_usuario']) > 0) and (trim($row['dt_exclusao']) != ""))
		{		
			echo form_default_text('dt_exclusao', "Dt. Exclus�o: ", $row, "style='color: red; width:100%;border: 0px;' readonly" );
		}
		
		echo form_default_text('nome', "Nome:* ", $row, "style='width:500px;'");
		echo form_default_text('usuario', "Usu�rio:* ", $row, "style='width: 100%;'");
		echo form_default_password('senha', "Senha:* ", $row, "style='width: 100%;'");
		
		$ar_tp_usuario = Array(Array('text' => 'Selecione', 'value' => ''),Array('text' => 'Funda��o CEEE', 'value' => 'F'),Array('text' => 'AFCEEE', 'value' => 'A')) ;
		echo form_default_dropdown('tp_usuario', 'Tipo:*', $ar_tp_usuario, Array($row['tp_usuario']));			

		$ar_fl_troca_senha = Array(Array('text' => 'Selecione', 'value' => ''),Array('text' => 'Sim', 'value' => 'S'),Array('text' => 'N�o', 'value' => 'N')) ;
		echo form_default_dropdown('fl_troca_senha', 'Trocar senha 1� acesso:*', $ar_fl_troca_senha, Array($row['fl_troca_senha']));	
		
		echo form_default_text('email', "Email: ", $row, "style='width: 100%;'");
		echo form_default_telefone('telefone_1', "Telefone 1: ", $row);
		echo form_default_telefone('telefone_2', "Telefone 2: ", $row);
		echo form_default_text('funcao', "Fun��o: ", $row, "style='width: 100%;'");
		echo form_default_text('delegacia', "Delegacia: ", $row, "style='width: 100%;'");
		
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