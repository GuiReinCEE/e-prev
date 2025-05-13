<?php
set_title('Jogo - Pergunta');
$this->load->view('header');
?>
<script>

	<?php
		echo form_default_js_submit(Array('nr_ordem','ds_item','fl_certo','vl_resposta'));
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

	function jogoPerguntaItem(cd_jogo,cd_jogo_pergunta)
	{
		location.href='<?php echo site_url("ecrm/jogo/pergunta_item"); ?>' + "/" + cd_jogo + "/" + cd_jogo_pergunta;
	}	
	
	function perguntaItemExcluir(cd_jogo,cd_jogo_pergunta, cd_jogo_pergunta_item)
	{
		if(confirm("Deseja excluir?"))
		{
			location.href='<?php echo site_url("ecrm/jogo/perguntaItemExcluir"); ?>' + "/" + cd_jogo + "/" + cd_jogo_pergunta + "/" + cd_jogo_pergunta_item;
		}
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
	$abas[] = array('aba_pergunta_item', 'Resposta', TRUE, 'location.reload();');
	$abas[] = array('aba_imagem', 'Imagens',  FALSE, "jogoImagem('".$cd_jogo."');");
	$abas[] = array('aba_resultado', 'Resultado',  FALSE, "jogoResultado('".$cd_jogo."');");
	$abas[] = array('aba_grafico', 'Gráfico', FALSE, "jogoGrafico('".$cd_jogo."');");
	
	echo aba_start( $abas );
	
	echo form_open('ecrm/jogo/perguntaItemSalvar');

	echo form_start_box( "default_box", "Jogo" );
		echo form_default_text('cd_jogo', "Código Jogo: ", $cd_jogo, "style='width:100%;border: 0px;' readonly" );
		echo form_default_text('cd_jogo_pergunta', "Código Perguta: ", $cd_jogo_pergunta, "style='width:100%;border: 0px;' readonly" );
		echo form_default_text('cd_jogo_pergunta_item', "Código Resposta: ", $cd_jogo_pergunta_item, "style='width:100%;border: 0px;' readonly" );
		echo form_default_text('dt_inclusao', "Dt. Inclusão: ", $row, "style='width:100%;border: 0px;' readonly" );
		echo form_default_text('dt_exclusao', "Dt. Exclusão: ", $row, "style='width:100%;border: 0px;' readonly" );
		
		echo form_default_row('', 'Perguta:', '<span style="width:100%;border: 0px; font-size: 10pt; font-weight: bold;">'.$ar_pergunta['nr_ordem']." - ".$ar_pergunta['ds_pergunta'].'</span>');
	
		echo form_default_integer('nr_ordem', "Ordem:*", $row);
		echo form_default_text('ds_item', "Resposta:* ", $row, "style='width:500px;'");
		
		if($ar_jogo['tp_jogo'] == "A")
		{
			echo form_default_dropdown('fl_certo', 'Resposta certa:*', Array(Array("value" => "S", "text" => "Sim"), Array("value" => "N", "text" => "Não")), Array($row['fl_certo']));
		}
		else
		{
			echo form_default_hidden('fl_certo', "Resposta certa:*", Array('fl_certo' => 'N'));
		}
				
		if($ar_jogo['tp_jogo'] == "V")
		{		
			echo form_default_integer('vl_resposta', "Valor Resposta:*", $row);
		}
		else
		{
			echo form_default_hidden('vl_resposta', "Valor Resposta:*", Array('vl_resposta' => 0));
		}		
		
	echo form_end_box("default_box");

	echo form_command_bar_detail_start();
		
		
		if($row['dt_exclusao'] == "")
		{
			echo button_save("Salvar");
			
			if(intval($row['cd_jogo_pergunta_item']) > 0)
			{
				echo button_save("Nova Resposta","jogoPerguntaItem(".$cd_jogo.",".$cd_jogo_pergunta.")");
				echo button_save("Excluir","perguntaItemExcluir(".$cd_jogo.",".$cd_jogo_pergunta.",".$cd_jogo_pergunta_item.")","botao_vermelho");
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