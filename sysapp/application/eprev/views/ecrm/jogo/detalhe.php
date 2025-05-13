<?php
set_title('Jogo - Cadastro');
$this->load->view('header');
?>
<script>
	<?php
		echo form_default_js_submit
			 (
				Array
					(
						'ds_jogo',
						array('dt_inicio','data'),
						array('hr_inicio','hora'),
						array('dt_final','data'),
						array('hr_final','hora'), 
						'tp_jogo',
						'cor_fundo', 
						'cor_pergunta',
						'cor_acerto', 
						'cor_acerto_mensagem',
						'nr_margem_pergunta',
						'nr_largura_pergunta',
						'nr_altura_pergunta',
						'fl_audio',
						'fl_randomico',
						'fl_exibe_resultado',
						'fl_tempo_exibe'
					),
				'formValida(form)'
			 );
	?>
	
	function formValida(form)
	{
/*
		else if(($('#fl_audio').val() == "S") && (($("#arquivo_audio_nome").val() == "") || ($("#arquivo_audio").val() == "")))
		{
			alert("Informe o arquivo de áudio (*.mp3)");
			$("#fl_audio").focus();
			return false;		
		}

*/

		if(($('#fl_randomico').val() == "S") && (($('#qt_randomico').val() == "") || ($('#qt_randomico').val() == 0)))
		{
			alert("Informe a quantidade de perguntas randômicas");
			$("#qt_randomico").focus();
			return false;		
		}
		else if(($('#fl_audio').val() == "S") && ($("#arquivo_audio").val() != "") && ($('#arquivo_audio').val().split('.').pop().toLowerCase() != "mp3"))
		{
			alert("O arquivo de áudio somente *.mp3");
			remover_arquivo_arquivo_audio();
			return false;	
		}
		else
		{
			if(confirm('Salvar?'))
			{
				form.submit();
			}
		}
	}
	
	
	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/jogo"); ?>';
	}
	
	function jogoTestar()
	{
		if($('#cd_jogo').val() == "")
		{
			alert("Jogo não encontrado");
		}
		else if($('#cd_empresa').val() == "")
		{
			alert("Informe a empresa");
			$('#cd_empresa').focus();
		}
		else if($('#cd_registro_empregado').val() == "")
		{
			alert("Informe o RE");
			$('#cd_registro_empregado').focus();
		}
		else if($('#seq_dependencia').val() == "")
		{
			alert("Informe a sequência");
			$('#seq_dependencia').focus();
		}
		else
		{
			if($("#cd_empresa").val().length < 2)
			{
				var x = 2 - $("#cd_empresa").val().length;
				for (i = 0; i < x; i++)
				{
					$("#cd_empresa").val("0" + $("#cd_empresa").val());
				}
			}
			
			if($("#cd_registro_empregado").val().length < 6)
			{
				var x = 6 - $("#cd_registro_empregado").val().length;
				for (i = 0; i < x; i++)
				{
					$("#cd_registro_empregado").val("0" + $("#cd_registro_empregado").val());
				}
			}	

			if($("#seq_dependencia").val().length < 2)
			{
				var x = 2 - $("#seq_dependencia").val().length;
				for (i = 0; i < x; i++)
				{
					$("#seq_dependencia").val("0" + $("#seq_dependencia").val());
				}
			}			

			var chave = $.md5($("#cd_empresa").val() + $("#cd_registro_empregado").val() + $("#seq_dependencia").val());
			var link  = $("#link_jogo").val();
				link  = link.replace("[RE_CRIPTO]",chave);
			window.open(link);
		}
	}		
	
	function jogoEstrutura(cd_jogo)
	{
		location.href='<?php echo site_url("ecrm/jogo/estrutura"); ?>' + "/" + cd_jogo;
	}	
	
	function jogoImagem(cd_jogo)
	{
		location.href='<?php echo site_url("ecrm/jogo/imagem"); ?>' + "/" + cd_jogo;
	}	
	
	function jogoResultado(cd_jogo)
	{
		location.href='<?php echo site_url("ecrm/jogo/resultado"); ?>' + "/" + cd_jogo;
	}	
	
	function jogoExcluir(cd_jogo)
	{
		if(confirm("Deseja excluir?"))
		{
			location.href='<?php echo site_url("ecrm/jogo/jogoExcluir"); ?>' + "/" + cd_jogo;
		}
	}
	
	function jogoExcluirResposta(cd_jogo)
	{
		if(confirm("ATENÇÃO\n\nEsta ação excluirá todas as respostas do jogo.\n\nEsta ação NÃO poderá ser desfeita.\n\nDeseja excluir?"))
		{
			location.href='<?php echo site_url("ecrm/jogo/jogoExcluirResposta"); ?>' + "/" + cd_jogo;
		}
	}	
	
	function jogoGrafico(cd_jogo)
	{
		location.href='<?php echo site_url("ecrm/jogo/grafico"); ?>' + "/" + cd_jogo;
	}	
	
	function randomico_check()
	{
		if($('#fl_randomico').val() == "S")
		{
			$('#qt_randomico_row').show();
		}
		else
		{
			$('#qt_randomico_row').hide();
		}
	}
	
	function audio_check()
	{
		if($('#fl_audio').val() == "S")
		{
			$('#arquivo_audio_row').show();
			$('#link_audio_row').show();
			
		}
		else
		{
			$('#arquivo_audio_row').hide();
			$('#link_audio_row').hide();
		}
	}	
	
	$(document).ready(function()
	{
		$('#fl_randomico').change(function(){ randomico_check(); });
		$('#fl_audio').change(function(){ audio_check(); });
		
		randomico_check();
		audio_check();
	});	
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_jogo', 'Cadastro', TRUE, 'location.reload();');
	if($row['cd_jogo'] > 0)
	{
		$abas[] = array('aba_estrutura', 'Estrutura', FALSE, "jogoEstrutura('".$cd_jogo."');");
		$abas[] = array('aba_imagem', 'Imagens',  FALSE, "jogoImagem('".$cd_jogo."');");
		$abas[] = array('aba_resultado', 'Resultado',  FALSE, "jogoResultado('".$cd_jogo."');");
		$abas[] = array('aba_grafico', 'Gráfico', FALSE, "jogoGrafico('".$cd_jogo."');");
	}
	
	echo aba_start( $abas );
	
	echo form_open('ecrm/jogo/jogoSalvar');
	echo form_start_box( "default_box", "Jogo" );
		echo form_default_text('cd_jogo', "Código:", $cd_jogo, "style='width:100%;border: 0px;' readonly" );
		echo form_default_text('dt_inclusao', "Dt. Inclusão:", $row, "style='width:100%;border: 0px;' readonly" );
		echo form_default_text('dt_exclusao', "Dt. Exclusão:", $row, "style='width:100%;border: 0px;' readonly" );
		
		echo form_default_text('ds_jogo', "Nome:*", $row, "style='width:500px;'");
		echo form_default_date('dt_inicio', "Data início:*", $row);
		echo form_default_time('hr_inicio', "Hora início:*", $row);
		echo form_default_date('dt_final', "Data final:*", $row);
		echo form_default_time('hr_final', "Hora final:*", $row);		
		
		$ar_audio = Array(Array('text' => 'Selecione', 'value' => ''),Array('text' => 'Quantidade de acertos', 'value' => 'A'),Array('text' => 'Somatório de pontos', 'value' => 'V')) ;
		echo form_default_dropdown('tp_jogo', 'Tipo:*', $ar_audio, Array($row['tp_jogo']));			
		
		echo form_default_color('cor_fundo', "Cor do fundo:*", $row);
		echo form_default_color('cor_pergunta', "Cor texto da Pergunta:* ", $row);
		echo form_default_color('cor_acerto', "Cor texto Resultado:* ", $row);
		echo form_default_color('cor_acerto_mensagem', "Cor da Mensagem Resultado:* ", $row);

		echo ($cd_jogo == 0 ? form_default_hidden("nr_tamanho_fonte_pergunta", "", $row["nr_tamanho_fonte_pergunta"])               : form_default_integer('nr_tamanho_fonte_pergunta', "Tamanho Fonte Pergunta (pt):*", $row));
		echo ($cd_jogo == 0 ? form_default_hidden("nr_tamanho_fonte_resposta", "", $row["nr_tamanho_fonte_resposta"])               : form_default_integer('nr_tamanho_fonte_resposta', "Tamanho Fonte Resposta (pt):*", $row));
		echo ($cd_jogo == 0 ? form_default_hidden("nr_tamanho_fonte_acerto", "", $row["nr_tamanho_fonte_acerto"])                   : form_default_integer('nr_tamanho_fonte_acerto', "Tamanho Fonte Resultado (pt):*", $row));
		echo ($cd_jogo == 0 ? form_default_hidden("nr_tamanho_fonte_acerto_mensagem", "", $row["nr_tamanho_fonte_acerto_mensagem"]) : form_default_integer('nr_tamanho_fonte_acerto_mensagem', "Tamanho Fonte Mensagem Resultado (pt):*", $row));
		
		echo ($cd_jogo == 0 ? form_default_hidden("nr_margem_pergunta", "", $row["nr_margem_pergunta"])   : form_default_integer('nr_margem_pergunta', "Margem da pergunta (px):*", $row));
		echo ($cd_jogo == 0 ? form_default_hidden("nr_largura_pergunta", "", $row["nr_largura_pergunta"]) : form_default_integer('nr_largura_pergunta', "Largura da pergunta (px):*", $row));
		echo ($cd_jogo == 0 ? form_default_hidden("nr_altura_pergunta", "", $row["nr_altura_pergunta"])   : form_default_integer('nr_altura_pergunta', "Altura da pergunta (px):*", $row));
		
		$ar_randomico = Array(Array('text' => 'Selecione', 'value' => ''),Array('text' => 'Sim', 'value' => 'S'),Array('text' => 'Não', 'value' => 'N')) ;
		echo form_default_dropdown('fl_randomico', 'Perguntas randômicas:*', $ar_randomico, Array($row['fl_randomico']));		
		echo form_default_integer('qt_randomico', "Qt perguntas randômicas (total: ".$qt_pergunta."):*", $row);

		$ar_audio = Array(Array('text' => 'Selecione', 'value' => ''),Array('text' => 'Sim', 'value' => 'S'),Array('text' => 'Não', 'value' => 'N')) ;
		echo form_default_dropdown('fl_audio', 'Áudio:*', $ar_audio, Array($row['fl_audio']));	

		echo (file_exists("./../eletroceee/img/jogo/".intval($cd_jogo)."_musica.mp3") ? form_default_row("link_audio", "Arquivo áudio:", '<a href="../../../../../eletroceee/img/jogo/'.intval($cd_jogo).'_musica.mp3" target="_blank" title="Clique para ouvir o áudio">[<img src="'.base_url().'img/icone_audio.gif" border="0"> Ouvir]</a>') : "");
		echo form_default_upload_iframe("arquivo_audio","jogo","Arquivo de áudio (*.mp3):*", "", "");		
		
		$ar_exibe_resultado = Array(Array('text' => 'Selecione', 'value' => ''),Array('text' => 'Sim', 'value' => 'S'),Array('text' => 'Não', 'value' => 'N')) ;
		echo form_default_dropdown('fl_exibe_resultado', 'Exibir Resultado:*', $ar_exibe_resultado, Array($row['fl_exibe_resultado']));	

		$ar_tempo_exibe = Array(Array('text' => 'Selecione', 'value' => ''),Array('text' => 'Sim', 'value' => 'S'),Array('text' => 'Não', 'value' => 'N')) ;
		echo form_default_dropdown('fl_tempo_exibe', 'Exibir tempo do jogo:*', $ar_tempo_exibe, Array($row['fl_tempo_exibe']));
		
		echo form_default_integer('cd_jogo_pre', "Código Jogo Anterior:", $row);
		echo form_default_integer('cd_jogo_pos', "Código Jogo Posterior:", $row);		
		
	echo form_end_box("default_box");

	echo form_command_bar_detail_start();
		if($row['dt_exclusao'] == "")
		{
			echo button_save("Salvar");
			if(intval($cd_jogo) > 0)
			{
				echo button_save("Excluir Jogo","jogoExcluir(".$cd_jogo.")","botao_vermelho");
			}			
		}
	echo form_command_bar_detail_end();
	echo form_close();
	
	if(intval($cd_jogo) > 0)
	{	
		echo form_start_box( "default_box_testar", "Testar" );
	
			$link_jogo = "http://www.fundacaoceee.com.br/jogo.php?j=".$cd_jogo."&p=[RE_CRIPTO]";
			if(($_SERVER['SERVER_ADDR'] != "10.63.255.5") and ($_SERVER['SERVER_ADDR'] != "10.63.255.7"))
			{
				$link_jogo = "http://".$_SERVER['SERVER_ADDR']."/eletroceee/jogo.php?j=".$cd_jogo."&p=[RE_CRIPTO]";
			}
			
			echo form_default_participante(); 
			echo form_default_text('link_jogo', "Link para divulgação: ", $link_jogo, "style='width:500px;border: 0px;' readonly" );
		
		echo form_end_box("default_box_testar");	
		echo form_command_bar_detail_start();
			if($row['dt_exclusao'] == "")
			{
				echo button_save("Testar","jogoTestar()");
				echo button_save("Excluir Respostas","jogoExcluirResposta(".$cd_jogo.")","botao_vermelho");
			}
		echo form_command_bar_detail_end();	
	}
	echo aba_end();
	$this->load->view('footer_interna');
?>