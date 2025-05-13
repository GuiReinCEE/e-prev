<?php
set_title('Família Previdência - Delegacia - Cadastro');
$this->load->view('header');
?>
<script>
	<?php
		echo form_default_js_submit(Array('nome','endereco','cidade','uf'));
	?>
	function ir_lista()
	{
		location.href='<?php echo site_url("planos/familia_previdencia_delegacia"); ?>';
	}
	
	
	function excluir(cd_delegacia)
	{
		if(confirm("Deseja excluir?"))
		{
			location.href='<?php echo site_url("planos/familia_previdencia_delegacia/excluir"); ?>' + "/" + cd_delegacia;
		}
	}
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');
	
	echo aba_start( $abas );
	
	echo form_open('planos/familia_previdencia_delegacia/salvar');
	echo form_start_box( "default_box", "Delegacia" );
		echo form_default_text('cd_delegacia', "Código: ", $row, "style='width:100%;border: 0px;' readonly" );
		if(intval($row['cd_delegacia']) > 0)
		{
			echo form_default_text('dt_inclusao', "Dt. Inclusão: ", $row, "style='width:100%;border: 0px;' readonly" );
		}
		
		if((intval($row['cd_delegacia']) > 0) and (trim($row['dt_exclusao']) != ""))
		{		
			echo form_default_text('dt_exclusao', "Dt. Exclusão: ", $row, "style='color: red; width:100%;border: 0px;' readonly" );
		}
		
		echo form_default_text('nome', "Nome:* ", $row, "style='width:500px;'");
		echo form_default_text('endereco', "Endereço:* ", $row, "style='width:100%;'");
		echo form_default_text('cidade', "Cidade:* ", $row, "style='width:100%;'");
		echo form_default_text('uf', "UF:* ", $row, "style='width:100%;'");
		echo form_default_telefone('telefone', "Telefone: ", $row);
		echo form_default_text('email', "Email: ", $row, "style='width:100%;'");		
		
	echo form_end_box("default_box");

	echo form_command_bar_detail_start();
		if(trim($row['dt_exclusao']) == "")
		{
			echo button_save("Salvar");
			if(intval($row['cd_delegacia']) > 0)
			{
				echo button_save("Excluir","excluir(".intval($row['cd_delegacia']).")","botao_vermelho");
			}			
		}
	echo form_command_bar_detail_end();
	echo form_close();
	

	echo aba_end();
	$this->load->view('footer_interna');
?>