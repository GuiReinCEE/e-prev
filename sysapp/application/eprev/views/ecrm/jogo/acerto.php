<?php
set_title('Jogo - Mensagem de Acerto');
$this->load->view('header');
?>
<script>

	<?php
		echo form_default_js_submit(Array('qt_inicio','qt_final','ds_mensagem'));
	?>
	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/jogo"); ?>';
	}
	
	function jogo(cd_jogo)
	{
		location.href='<?php echo site_url("ecrm/jogo/detalhe"); ?>' + "/" + cd_jogo;
	}		
	
	function jogoEstrutura(cd_jogo)
	{
		location.href='<?php echo site_url("ecrm/jogo/estrutura"); ?>' + "/" + cd_jogo;
	}
	
	function jogoResultado(cd_jogo)
	{
		location.href='<?php echo site_url("ecrm/jogo/resultado"); ?>' + "/" + cd_jogo;
	}	

	function acertoExcluir(cd_jogo,cd_jogo_acerto)
	{
		if(confirm("Deseja excluir?"))
		{
			location.href='<?php echo site_url("ecrm/jogo/acertoExcluir"); ?>' + "/" + cd_jogo + "/" + cd_jogo_acerto;
		}
	}
	
	function jogoImagem(cd_jogo)
	{
		location.href='<?php echo site_url("ecrm/jogo/imagem"); ?>' + "/" + cd_jogo;
	}	

	function jogoGrafico(cd_jogo)
	{
		location.href='<?php echo site_url("ecrm/jogo/grafico"); ?>' + "/" + cd_jogo;
	}	
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_jogo', 'Cadastro', FALSE, "jogo('".$cd_jogo."');");
	$abas[] = array('aba_estrutura', 'Estrutura', FALSE, "jogoEstrutura('".$cd_jogo."');");
	$abas[] = array('aba_pergunta', 'Acerto', TRUE, 'location.reload();');
	$abas[] = array('aba_imagem', 'Imagens',  FALSE, "jogoImagem('".$cd_jogo."');");
	$abas[] = array('aba_resultado', 'Resultado',  FALSE, "jogoResultado('".$cd_jogo."');");
	$abas[] = array('aba_grafico', 'Gráfico', FALSE, "jogoGrafico('".$cd_jogo."');");
	
	
	echo aba_start( $abas );
	
	echo form_open('ecrm/jogo/acertoSalvar');

	echo form_start_box( "default_box", "Cadastro" );

		echo form_default_text('cd_jogo', "Código Jogo: ", $cd_jogo, "style='width:100%;border: 0px;' readonly" );
		echo form_default_text('cd_jogo_acerto', "Código Acerto: ", $cd_jogo_acerto, "style='width:100%;border: 0px;' readonly" );
		echo form_default_text('dt_inclusao', "Dt. Inclusão: ", $row, "style='width:100%;border: 0px;' readonly" );
		echo form_default_text('dt_exclusao', "Dt. Exclusão: ", $row, "style='width:100%;border: 0px;' readonly" );
		echo form_default_integer('qt_inicio', "Resultado Inicial:*", $row);
		echo form_default_integer('qt_final', "Resultado Final:*", $row);
		echo form_default_textarea('ds_mensagem', "Mensagem:* ", $row, "style='width:500px;'");
		echo form_default_row("", "", '<span style="width:100%; font-size: 10pt; font-style:italic;">Para exibir o tempo de jogo, marque SIM para exibir, depois utilize a palavra chave [TEMPO] na mensagem para mostra o tempo.</span>');
	echo form_end_box("default_box");

	echo form_command_bar_detail_start();
		
		
		if($row['dt_exclusao'] == "")
		{
			echo button_save("Salvar");
			if(intval($cd_jogo_acerto) > 0)
			{
				echo button_save("Excluir","acertoExcluir(".$cd_jogo.",".$cd_jogo_acerto.")","botao_vermelho");
				
			}
		}
		echo button_save("Voltar","jogoEstrutura(".$cd_jogo.")","botao_disabled");
	echo form_command_bar_detail_end();
?>
<?php
	echo aba_end();
	// FECHAR FORM
	echo form_close();

	$this->load->view('footer_interna');
?>