<?php
set_title('Seminário Econômico - Cadastro');
$this->load->view('header');
?>
<script>
	<?php
		echo form_default_js_submit(Array('nome'));
	?>
	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/seminario_economico"); ?>';
	}
	
	function excluir(cd_inscricao)
	{
		if(confirm("Deseja excluir?"))
		{
			location.href='<?php echo site_url("ecrm/seminario_economico/excluir"); ?>' + "/" + cd_inscricao;
		}
	}
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');
	
	echo aba_start( $abas );
	
	echo form_open('ecrm/seminario_economico/salvar');
	echo form_start_box( "default_box", "Inscrição" );
		echo form_default_text('cd_inscricao', "Código: ", intval($row['cd_inscricao']), "style='width:100%;border: 0px;' readonly" );
		if($row['dt_inclusao'] != "")
		{
			echo form_default_text('dt_inclusao', "Dt. Inclusão: ", $row, "style='width:100%;border: 0px;' readonly" );
		}
		
		if($row['dt_exclusao'] != "")
		{
			echo form_default_text('dt_exclusao', "Dt. Exclusão: ", $row, "style='width:100%;border: 0px;' readonly" );
		}		
		
		echo form_default_text('ds_seminario_edicao', "Edição: ", $row, "style='font-weight: bold; width:100%;border: 0px;' readonly" );
		
		$participante['cd_empresa']            = $row['cd_empresa'];
		$participante['cd_registro_empregado'] = $row['cd_registro_empregado'];
		$participante['seq_dependencia']       = $row['seq_dependencia'];
		$conf = array('cd_empresa','cd_registro_empregado','seq_dependencia', 'nome');
		echo form_default_participante($conf, "Participante:", $participante, TRUE, FALSE );			
		
		echo form_default_text('nome', "Nome:* ", $row, 'style="width: 500px;"');
		
		echo form_default_text('email', "Email: ", $row,'style="width: 100%;"' );
		
		echo form_default_text('cargo', "Cargo: ", $row,'style="width: 100%;"' );
		echo form_default_text('empresa', "Empresa: ", $row,'style="width: 100%;"');
		
		echo form_default_text('cep', "CEP ", $row);
		echo form_default_text('endereco', "Endereço: ", $row,'style="width: 100%;"');
		echo form_default_text('numero', "Número: ", $row);
		echo form_default_text('complemento', "Complemento ", $row);
		echo form_default_text('cidade', "Cidade: ", $row,'style="width: 100%;"');
		echo form_default_text('uf', "UF: ", $row);
		
		echo form_default_integer('telefone_ddd', "DDD: ", $row);
		echo form_default_integer('telefone', "Telefone: ", $row);
		echo form_default_integer('telefone_ramal', "Ramal: ", $row);
		echo form_default_integer('celular_ddd', "DDD: ", $row);
		echo form_default_integer('celular', "Celular: ", $row);		
		echo form_default_integer('fax_ddd', "DDD: ", $row);
		echo form_default_integer('fax', "Fax: ", $row);
		echo form_default_integer('fax_ramal', "Ramal: ", $row);	
		
		$ar_presente = Array(Array('text' => 'Selecione', 'value' => ''),Array('text' => 'Sim', 'value' => 'S'),Array('text' => 'Não', 'value' => 'N')) ;
		echo form_default_dropdown('fl_presente', 'Presente: ', $ar_presente, Array($row['fl_presente']));		
		
		echo form_default_text('dt_envio_certificado', "Dt. Envio certificado: ", $row, "style='width:100%;border: 0px;' readonly" );

	echo form_end_box("default_box");

	echo form_command_bar_detail_start();
		if($row['dt_exclusao'] == "")
		{
			echo button_save("Salvar");

			if(intval($row['cd_inscricao']) > 0)
			{
				echo button_save("Excluir","excluir(".intval($row['cd_inscricao']).")","botao_vermelho");
			}	
		}
		echo button_save("Voltar","ir_lista()","botao_disabled");
	echo form_command_bar_detail_end();
	echo form_close();
	echo aba_end();
?>
<script>
	jQuery(function($)
	{
	   $("#cep").mask("99999-999");
	});
</script>
<?php
	$this->load->view('footer_interna');
?>