<?php
set_title('(GRI) Cadastro');
$this->load->view('header');
?>
<script>
	<?php
		$ar_validar = Array('cd_cadastro_origem','nome');
		echo form_default_js_submit($ar_validar);
	?>
	
	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/ri_cadastro"); ?>';
	}
	
	function retornoCEP(retorno)
	{
		//alert(retorno);
		//$out = "cidade:'".$row["ds_localidade"]."',uf:'".$row["cd_uf"]."',endereco:'".$row["tp_logradouro"]." ".$row["ds_logradouro"]."',bairro:'".$row["ds_bairro_ini"]."'";
	}
	
	function excluir(cd_cadastro)
	{
		if(confirm("Deseja excluir?"))
		{
			location.href='<?php echo site_url("ecrm/ri_cadastro/excluir"); ?>' + "/" + cd_cadastro;
		}
	}	
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro',  TRUE, 'location.reload();');
	echo aba_start( $abas );
	
	echo form_open('ecrm/ri_cadastro/cadastroSalvar');

	echo form_start_box( "default_box", "Dados" );
		echo form_default_hidden('cd_cadastro', "Cod. Cadastro: ", $row);
		if(trim($row['dt_exclusao']) != "")
		{
			echo form_default_text('dt_exclusao', "Dt Exclusão:", $row, "style='border: 0px; width:100%; color: red; font-weight: bold;' readonly");
		}		
		echo form_default_dropdown('cd_cadastro_origem', 'Origem*:', $ar_cadastro_origem, array($row['cd_cadastro_origem']));	
		echo form_default_text('nome', "Nome*:", $row, "style='width:600px;'");
		echo form_default_text('cargo', "Cargo:", $row, "style='width:100%;'");
		echo form_default_text('empresa', "Empresa:", $row, "style='width:100%;'");
		echo form_default_text('endereco', "Empresa:", $row, "style='width:100%;'");
		echo form_default_text('cidade', "Cidade:", $row, "style='width:100%;'");
		echo form_default_cep('cep', "CEP:", $row, array("db"=>FALSE, 'callback_function'=>'retornoCEP', 'return_type'=>'string'));
		echo form_default_text('uf', "UF:", $row, "style='width:100%;'");
		echo form_default_text('pais', "País:", $row, "style='width:100%;'");
		echo form_default_text('telefone_ddd', "DDD Telefone:", $row, "style='width:100%;'");
		echo form_default_text('telefone', "Telefone:", $row, "style='width:100%;'");
		echo form_default_text('celular_ddd', "DDD Celular:", $row, "style='width:100%;'");
		echo form_default_text('celular', "Celular:", $row, "style='width:100%;'");
		echo form_default_text('email', "email:", $row, "style='width:100%;'");

	echo form_end_box("default_box");

	echo form_command_bar_detail_start();
		if(trim($row['dt_exclusao']) == "")
		{
			echo button_save("Salvar");
			if(intval($row['cd_cadastro']) > 0)
			{
				echo button_save("Excluir","excluir(".intval($row['cd_cadastro']).")","botao_vermelho");
			}
		}
	echo form_command_bar_detail_end();
	
	echo form_close();
	echo aba_end();
	
	$this->load->view('footer_interna');
?>