<?php 
	set_title('Eventos Institucionais - Cadastro');
	$this->load->view('header'); 
?>
<script>
	
	<?php echo form_default_js_submit(array(
		"nome",
		"fl_acompanhante",
		"fl_arquivo",
		"fl_observacao",
		"dt_inicio", 
		"hr_inicio", 
		"fl_participante",
		"dt_ini_inscricao", 
		"hr_ini_inscricao", 
		"dt_fim_inscricao", 
		"hr_fim_inscricao",
		"cd_cidade"
	));	?>

	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/ri_evento_institucional"); ?>';
	}
	
	function imagem(cd_evento)
	{
		location.href='<?php echo site_url("ecrm/ri_evento_institucional/imagem"); ?>' + "/" + cd_evento;
	}

	function certificado(cd_evento)
	{
		location.href='<?php echo site_url("ecrm/ri_evento_institucional/certificado"); ?>' + "/" + cd_evento;
	}	

	function emailConfirma(cd_evento)
	{
		var aviso = "ATENÇÃO\n\nEsta ação é IRREVERSÍVEL.\n\nDeseja ENVIAR o email de confirmação?\n\n\nSIM clique [Ok]\n\nNÃO clique [Cancelar]\n\n";
		
		if(confirm(aviso))
		{
			location.href='<?php echo site_url("ecrm/ri_evento_institucional/emailConfirma"); ?>' + "/" + cd_evento;
		}
	}
        
	function codigo_barras6183(cd_evento)
	{
		location.href='<?php echo site_url("ecrm/ri_evento_institucional/codigo_barras_6183"); ?>/'+ cd_evento;	
	}
	
	function cracha6183(cd_evento)
	{
		location.href='<?php echo site_url("ecrm/ri_evento_institucional/cracha_6183"); ?>/'+ cd_evento;	
	}
	
	function codigo_barras6182(cd_evento)
	{
		location.href='<?php echo site_url("ecrm/ri_evento_institucional/codigo_barras_6182"); ?>/'+ cd_evento;	
	}
	
	function cracha6182(cd_evento)
	{
		location.href='<?php echo site_url("ecrm/ri_evento_institucional/cracha_6182"); ?>/'+ cd_evento;	
	}

	function lista_presente(cd_evento)
	{
		location.href='<?php echo site_url("ecrm/ri_evento_institucional/lista_presente"); ?>/'+ cd_evento;	
	}		
	
	$(function(){
		$("#fl_participante").change(function() {
			$("#ar_participante_tipo_row").hide();
			$("#participante_msg_valida_row").hide();
			
			if($("#fl_participante").val() == "S")
			{
				$("#ar_participante_tipo_row").show();
				$("#participante_msg_valida_row").show();
			}
		});

		$("#fl_participante").change();
	});		
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_detalhe', 'Cadastro', TRUE, 'location.reload();');
	if(intval($row['cd_evento']) > 0)
	{
		$abas[] = array('aba_imagem', 'Imagens', FALSE,  "imagem('".intval($row['cd_evento'])."');");
		$abas[] = array('aba_certificado', 'Certificado', FALSE,  "certificado('".intval($row['cd_evento'])."');");
	}

echo aba_start( $abas );

echo form_open('ecrm/ri_evento_institucional/salvar');
	echo form_start_box( "default_box", "Eventos Institucionais" );
		echo form_default_text('cd_evento', "Código: ", intval($row['cd_evento']), "style='width:500px;border: 0px;' readonly" );
		if( intval($row['cd_evento'])>0 )
		{
			echo form_default_row("", "Link para inscrição:", anchor("https://www.fundacaofamiliaprevidencia.com.br/inscricao_evento.php?id=".intval($row['cd_evento']), "https://www.fundacaofamiliaprevidencia.com.br/inscricao_evento.php?id=".intval($row['cd_evento']), 'target="_blank"'));
		}		
		echo form_default_text("nome", "Nome:* ", $row, "style='width:100%;'");
		echo form_default_date("dt_inicio", "Dt Início Evento:* ", $row);
		echo form_default_time("hr_inicio", "Hr Início Evento:* ", $row);
		$ar_participante = Array(Array('text' => 'Sim', 'value' => 'S'),Array('text' => 'Não', 'value' => 'N')) ;
		echo form_default_dropdown('fl_participante', 'Somente Participantes: *', $ar_participante, (trim($row['fl_participante']) != '' ? array($row['fl_participante']) : array('S')));
		
		$ar_participante_tipo = Array(
										Array('text' => 'APOSENTADO',    'value' => 'APOS'),
										Array('text' => 'ATIVO',         'value' => 'ATIV'),
										Array('text' => 'AUXILIO DOENCA','value' => 'AUXD'),
										Array('text' => 'CTP',           'value' => 'CTP'),
										Array('text' => 'EX-AUTARQUICO', 'value' => 'EXAU'),
										Array('text' => 'PENSIONISTA',   'value' => 'PENS'),
										Array('text' => 'SEM PLANO',     'value' => 'SEMP')
									  );
		echo form_default_checkbox_group('ar_participante_tipo', 'Tipo Participante :*', $ar_participante_tipo, $ar_participante_tipo_checked, 120);
		echo form_default_textarea("participante_msg_valida", "Mensagem validação do participante :*", $row, "style='width:100%; height: 50px;'");
		
		$ar_acompanha = Array(Array('text' => 'Sim', 'value' => 'S'),Array('text' => 'Não', 'value' => 'N')) ;
		echo form_default_dropdown('fl_acompanhante', 'Acompanhante: *', $ar_acompanha, $row['fl_acompanhante']);	

		$ar_arquivo = Array(Array('text' => 'Sim', 'value' => 'S'),Array('text' => 'Não', 'value' => 'N')) ;
		echo form_default_dropdown('fl_arquivo', 'Arquivo: *', $ar_arquivo, $row['fl_arquivo']);	

		$ar_obs = Array(Array('text' => 'Sim', 'value' => 'S'),Array('text' => 'Não', 'value' => 'N')) ;
		echo form_default_dropdown('fl_observacao', 'Observação: *', $ar_obs, $row['fl_observacao']);	

		echo form_default_text("ds_observacao", "Rótulo Obs.: ", $row, "style='width:100%;'");		
		
		echo form_default_integer("qt_inscricao", "Inscrições (0 = ilimitado):* ", $row);
		echo form_default_date("dt_ini_inscricao", "Dt Início Inscrição:* ", $row);
		echo form_default_time("hr_ini_inscricao", "Hr Início Inscrição:* ", $row);
		echo form_default_date("dt_fim_inscricao", "Dt Fim Inscrição:* ", $row);
		echo form_default_time("hr_fim_inscricao", "Hr Fim Inscrição:* ", $row);		
		echo form_default_dropdown_db("cd_cidade", "Cidade:* ", array( "expansao.cidades", "cd_municipio_ibge", "nome_cidade" ), array( $row["cd_cidade"] ), "", "", FALSE, "sigla_uf='RS'");
		echo form_default_text("local_evento", "Local: ", $row, "style='width:100%;'");
		echo form_default_text("email_assunto", "Assunto email confirma inscrição: ", $row, "style='width:100%;'");
		echo form_default_textarea("email_texto", "Texto email confirma inscrição: ", $row, "style='width:100%; height: 100px;'");
		echo form_default_textarea("texto_encerramento", "Texto que encerra inscrições: ", $row, "style='width:100%; height: 100px;'");
	echo form_end_box("default_box");

	echo form_command_bar_detail_start();
		echo button_save();

		if( intval($row['cd_evento'])>0 )
		{
			echo button_save("Email confirmação","emailConfirma(".$row["cd_evento"].")","botao_amarelo");
			echo button_delete("ecrm/ri_evento_institucional/excluir",$row["cd_evento"]);
			echo button_save("Lista","lista_presente(".$row["cd_evento"].")","botao_disabled");
			echo button_save("Crachá 6183","cracha6183(".$row["cd_evento"].")","botao_disabled");
			echo button_save("Cód Barras 6183","codigo_barras6183(".$row["cd_evento"].")","botao_disabled");
			echo button_save("Crachá 6182","cracha6182(".$row["cd_evento"].")","botao_disabled");
			echo button_save("Cód Barras 6182","codigo_barras6182(".$row["cd_evento"].")","botao_disabled");			
		}
	echo form_command_bar_detail_end();

	echo br(5);
echo aba_end();

echo form_close();

$this->load->view('footer_interna');
?>