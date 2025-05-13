<?php
set_title('Diálogo Inscrições - Cadastro');
$this->load->view('header');
?>
<script>
	<?php
		$ar_validar = Array('nome');
		echo form_default_js_submit($ar_validar);
	?>
	
	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/dialogo_inscricao"); ?>';
	}
	
	function inscricaoExcluir(cd_dialogo_inscricao)
	{
		if(confirm("Deseja excluir?"))
		{
			location.href='<?php echo site_url("ecrm/dialogo_inscricao/inscricaoExcluir"); ?>' + "/" + cd_dialogo_inscricao;
		}
	}	
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_inscricao', 'Cadastro', TRUE, 'location.reload();');
		
	echo aba_start( $abas );
	
	echo form_open('ecrm/dialogo_inscricao/dialogoInscricaoSalvar');
	echo form_start_box( "default_box", "Cadastro" );

		echo form_default_text('cd_dialogo_inscricao', "Cod. Inscrição: ", $row,  "style='width:100%;border: 0px;' readonly" );
		echo form_default_text('dt_inclusao', "Dt Inscrição: ", $row,  "style='width:100%;border: 0px;' readonly" );
		echo form_default_text('nome', "Nome:* ", $row,  "style='width:600px;'");
		echo form_default_text('empresa', "Empresa: ", $row,  "style='width:100%;'");
		echo form_default_text('cargo', "Cargo: ", $row,  "style='width:100%;'");
		echo form_default_text('endereco', "Endereço: ", $row,  "style='width:100%;'");
		echo form_default_integer('numero', "Número: ", $row,  "style='width:100%;'");
		echo form_default_text('complemento', "Complemento: ", $row,  "style='width:100%;'");
		echo form_default_text('bairro', "Bairro: ", $row,  "style='width:100%;'");
		echo form_default_text('cidade', "Cidade: ", $row,  "style='width:100%;'");
		echo form_default_text('uf', "UF: ", $row,  "style='width:100%;'");
		echo form_default_text('cep', "CEP: ", $row,  "style='width:100%;'");
		
		echo form_default_integer('telefone_ddd', "DDD Telefone: ", $row,  "style='width:100%;'");
		echo form_default_integer('telefone', "Telefone: ", $row,  "style='width:100%;'");
		echo form_default_integer('telefone_ramal', "Ramal: ", $row,  "style='width:100%;'");
		echo form_default_integer('fax_ramal', "DDD FAX: ", $row,  "style='width:100%;'");
		echo form_default_integer('fax', "FAX: ", $row,  "style='width:100%;'");
		echo form_default_integer('celular_ddd', "DDD Celular: ", $row,  "style='width:100%;'");
		echo form_default_integer('celular', "Celular: ", $row,  "style='width:100%;'");
		
		
		
		$participante['cd_empresa']            = $row['cd_empresa'];
		$participante['cd_registro_empregado'] = $row['cd_registro_empregado'];
		$participante['seq_dependencia']       = $row['seq_dependencia'];
		$conf = array('cd_empresa','cd_registro_empregado','seq_dependencia', 'nome');
		echo form_default_participante($conf, "Participante:", $participante, TRUE, FALSE );	
		
		
		echo form_default_text('email', "Email: ", $row, "style='width:100%;'");
		#$ar_tipo = Array(Array('text' => 'Sim', 'value' => 'S'),Array('text' => 'Não', 'value' => 'N')) ;
		#echo form_default_dropdown('fl_presente', 'Presente:', $ar_tipo, array($row['fl_presente']), "style='width:100%;'");		
			
		
	echo form_end_box("default_box");

	echo form_command_bar_detail_start();
		#echo button_save("Salvar");
		if(intval($row['cd_dialogo_inscricao']) > 0)
		{
			echo button_save("Excluir","inscricaoExcluir(".intval($row['cd_dialogo_inscricao']).")","botao_vermelho");
		}		
	echo form_command_bar_detail_end();
	echo form_close();
	echo aba_end();
?>
<script>
	jQuery(function($){
	   $("#cep").mask("99999-999");
	   $("#uf").mask("aa");
	});	
</script>
<?php	
	$this->load->view('footer_interna');
?>