<?php
set_title('Jogo - Pergunta');
$this->load->view('header');
?>
<script>

	<?php
		echo form_default_js_submit(Array('nr_ordem','fl_exibe_resposta','ds_pergunta'));
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

	function perguntaExcluir(cd_jogo,cd_jogo_pergunta)
	{
		if(confirm("Deseja excluir?"))
		{
			location.href='<?php echo site_url("ecrm/jogo/perguntaExcluir"); ?>' + "/" + cd_jogo + "/" + cd_jogo_pergunta;
		}
	}
	
	function jogoPergunta(cd_jogo)
	{
		location.href='<?php echo site_url("ecrm/jogo/pergunta"); ?>' + "/" + cd_jogo;
	}

	function jogoImagem(cd_jogo)
	{
		location.href='<?php echo site_url("ecrm/jogo/imagem"); ?>' + "/" + cd_jogo;
	}	

	function jogoResultado(cd_jogo)
	{
		location.href='<?php echo site_url("ecrm/jogo/resultado"); ?>' + "/" + cd_jogo;
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
	$abas[] = array('aba_pergunta', 'Pergunta', TRUE, 'location.reload();');
	$abas[] = array('aba_imagem', 'Imagens',  FALSE, "jogoImagem('".$cd_jogo."');");
	$abas[] = array('aba_resultado', 'Resultado',  FALSE, "jogoResultado('".$cd_jogo."');");
	$abas[] = array('aba_grafico', 'Gráfico', FALSE, "jogoGrafico('".$cd_jogo."');");
	
	echo aba_start( $abas );
	
	echo form_open('ecrm/jogo/perguntaSalvar');

	echo form_start_box( "default_box", "Jogo" );

		echo form_default_text('cd_jogo', "Código Jogo: ", $cd_jogo, "style='width:100%;border: 0px;' readonly" );
		echo form_default_text('cd_jogo_pergunta', "Código Pergunta: ", $row, "style='width:100%;border: 0px;' readonly" );
		echo form_default_text('dt_inclusao', "Dt. Inclusão: ", $row, "style='width:100%;border: 0px;' readonly" );
		echo form_default_text('dt_exclusao', "Dt. Exclusão: ", $row, "style='width:100%;border: 0px;' readonly" );
		echo form_default_integer('nr_ordem', "Ordem:*", $row, "style='width:100%;'");
		
		if($ar_jogo['tp_jogo'] == "A")
		{
			$ar_exibe_resposta = Array(Array('text' => 'Selecione', 'value' => ''),Array('text' => 'Sim', 'value' => 'S'),Array('text' => 'Não', 'value' => 'N')) ;
			echo form_default_dropdown('fl_exibe_resposta', 'Exibir resposta correta:*', $ar_exibe_resposta, Array($row['fl_exibe_resposta']));		
		}
		else
		{
			echo form_default_hidden('fl_exibe_resposta', "Exibir resposta correta:*", Array('fl_exibe_resposta' => 'N'));
		}
	
		
		echo form_default_textarea('ds_pergunta', "Pergunta:* ", $row, "style='width:500px;'");
		echo form_default_textarea("ds_complemento", "Complemento:" ,$row, "style='width:500px;'");
	echo form_end_box("default_box");

	echo form_command_bar_detail_start();
		if($row['dt_exclusao'] == "")
		{
			echo button_save("Salvar");
			if(intval($row['cd_jogo_pergunta']) > 0)
			{
				echo comando("btNovaPergunta","Nova Pergunta","jogoPergunta(".$cd_jogo.");");
				echo button_save("Excluir","perguntaExcluir(".$cd_jogo.",".$row['cd_jogo_pergunta'].")","botao_vermelho");
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