<?php
	set_title('Eventos Institucionais - Certificado');
	$this->load->view('header');
?>
<script>

	<?php
		echo form_default_js_submit(Array('cd_evento',
											'certificado_nome_pos_x',
											'certificado_nome_pos_y',
											'certificado_nome_tamanho',
											'certificado_nome_fonte',
											'certificado_nome_cor',
											'certificado_nome_alinha'
										  ));
	?>
	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/ri_evento_institucional"); ?>';
	}
	
	function detalhe(cd_evento)
	{
		location.href='<?php echo site_url("ecrm/ri_evento_institucional/detalhe"); ?>' + "/" + cd_evento;
	}	
	
	function imagem(cd_evento)
	{
		location.href='<?php echo site_url("ecrm/ri_evento_institucional/imagem"); ?>' + "/" + cd_evento;
	}	
	
	function emailCertificado(cd_evento)
	{
		var aviso = "ATENÇÃO\n\nEsta ação é IRREVERSÍVEL.\n\nDeseja ENVIAR o email do certificado para todos presentes?\n\n\nSIM clique [Ok]\n\nNÃO clique [Cancelar]\n\n";
		
		if(confirm(aviso))
		{
			location.href='<?php echo site_url("ecrm/ri_evento_institucional/emailCertificadoEvento"); ?>' + "/" + cd_evento;
		}
	}	
</script>

<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', FALSE, "detalhe('".intval($row['cd_evento'])."');");
	$abas[] = array('aba_imagem', 'Imagens', FALSE, "imagem('".intval($row['cd_evento'])."');");
	$abas[] = array('aba_certificado', 'Certificado', TRUE, "location.reload();");


	echo form_open('ecrm/ri_evento_institucional/salvarCertificado');
	echo aba_start( $abas );
	
	
	echo form_start_box("default_imagem", "Dados" );
		echo form_default_hidden('cd_evento', "Código: ", intval($row['cd_evento']), "style='width:100%;border: 0px;' readonly" );
		
		echo form_default_row("", "Link:", anchor("http://www.fundacaoceee.com.br/evento_certificado.php?e=".intval($row['cd_evento']), "[Visualizar o certificado]", 'target="_blank"'));
		
		echo form_default_integer('certificado_nome_pos_x', "Posição X:* ", $row);
		echo form_default_integer('certificado_nome_pos_y', "Posição Y:* ", $row);
		echo form_default_color('certificado_nome_cor', "Cor:* ", $row);
		
		$ar_nome_fonte = Array(Array('text' => 'Arial', 'value' => 'arial'),Array('text' => 'Courier', 'value' => 'courier'),Array('text' => 'Verdana', 'value' => 'verdana')) ;
		echo form_default_dropdown('certificado_nome_fonte', 'Nome da fonte:*', $ar_nome_fonte, $row['certificado_nome_fonte']);		
		echo form_default_integer('certificado_nome_tamanho', "Tamanho da fonte:* ", $row);
				
		$ar_nome_alinha = Array(Array('text' => 'Direita', 'value' => 'L'),Array('text' => 'Esquerda', 'value' => 'R'),Array('text' => 'Centralizado', 'value' => 'C'),Array('text' => 'Justificado', 'value' => 'J'));
		echo form_default_dropdown('certificado_nome_alinha', 'Alinhamento:*', $ar_nome_alinha, $row['certificado_nome_alinha']);		
		

		echo form_default_upload_iframe('certificado_img_frente', 'evento_institucional', 'Certificado Frente (.jpg):* ');
		if($row['certificado_img_frente'] != "")
		{
			echo form_default_row('', '', '<img src="../../../../../eletroceee/img/evento_institucional/'.$row['certificado_img_frente'].'" border="0" width="50%" height="50%">');
		}

		echo form_default_upload_iframe('certificado_img_verso', 'evento_institucional', 'Certificado Verso: (.jpg)');
		if($row['certificado_img_verso'] != "")
		{
			echo form_default_row('', '', '<img src="../../../../../eletroceee/img/evento_institucional/'.$row['certificado_img_verso'].'" border="0"  width="20%" height="20%">');		
		}
		
		echo form_default_text("assunto","Assunto:","Certificado - [NOME_DO_EVENTO]","style='width:100%;border: 0px;' readonly");
		echo form_default_textarea("email","Email:","Prezado(a): [NOME]

Clique no link abaixo para imprimir o certificado do evento [NOME_DO_EVENTO].

[LINK_PARA_O_CERTIFICADO]


Fundação CEEE - Previdência Privada
http://www.fundacaoceee.com.br
Siga-nos! http://twitter.com/fundacaoceee

**** ATENÇÃO ****
Este e-mail é somente para leitura.
Caso queira falar conosco clique no link abaixo:
https://www.fundacaoceee.com.br/fale_conosco.php"," style='border: 1px solid gray;' readonly");
		
	echo form_end_box("default_imagem");	
	
	echo form_command_bar_detail_start();
		echo button_save();
		echo button_save("Enviar Email Certificado","emailCertificado(".$row["cd_evento"].")","botao_vermelho");
	echo form_command_bar_detail_end();	

?>
<br><br>
<?php
	echo aba_end();
	echo form_close();
	$this->load->view('footer_interna');
?>