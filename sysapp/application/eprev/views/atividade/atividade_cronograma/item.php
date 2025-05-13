<?php
set_title('Cronograma - Itens');
$this->load->view('header');
?>
<script>
	<?php
		echo form_default_js_submit(Array('cd_atividade'), 'verifica_atividade(form)');
	?>
		
	function ir_lista()
	{
		location.href='<?php echo site_url("atividade/atividade_cronograma"); ?>';
	}
	
	function verifica_atividade(form)
	{
		
	
		if($('#cd_atividade_cronograma_item').val() == 0)
		{
			$.post( '<?php echo site_url("/atividade/atividade_cronograma/verifica_atividade")?>',
			{
				cd_atividade_cronograma       : $('#cd_atividade_cronograma').val(),
				cd_atividade                  : $('#cd_atividade').val()
			},
			function(data)
			{
				if(data == 1)
				{
					alert('Atividade já está cadastrada no cronograma.');
					return false;
				}
				else
				{
					if(confirm('Salvar?'))
					{
						form.submit();
					}
				}
			});
		}
		else
		{
			if(confirm('Salvar?'))
			{
				form.submit();
			}
		}
	}
	
	function cronograma(cd_atividade_cronograma)
	{
		location.href='<?php echo site_url("atividade/atividade_cronograma/cadastro"); ?>' + "/" + cd_atividade_cronograma;
	}
	
	function cronogramaItem(cd_atividade_cronograma)
	{
		location.href='<?php echo site_url("atividade/atividade_cronograma/cronograma"); ?>' + "/" + cd_atividade_cronograma;
	}

	function ir_acompanhamento(cd_atividade_cronograma)
	{
		location.href = '<?php echo site_url("atividade/atividade_cronograma/acompanhamento"); ?>' + "/" + cd_atividade_cronograma;
	}	
	
	function ir_quadro_resumo(cd_atividade_cronograma)
	{
		location.href = '<?php echo site_url("atividade/atividade_cronograma/quadro_resumo"); ?>' + "/" + cd_atividade_cronograma;
	}
	
	function ir_concluidas_fora(cd_atividade_cronograma)
	{
		location.href = '<?php echo site_url("atividade/atividade_cronograma/concluidas_fora"); ?>' + "/" + cd_atividade_cronograma;
	}


</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	//$abas[] = array('aba_cadastro', 'Cadastro', FALSE, "cronograma('".$cd_atividade_cronograma."');");
	$abas[] = array('aba_cronograma', 'Cronograma', FALSE, "cronogramaItem('".$cd_atividade_cronograma."');");
	$abas[] = array('aba_item', 'Atividade', TRUE, "location.reload();");
	$abas[] = array('aba_cronograma', 'Acompanhamento', FALSE, "ir_acompanhamento('".$cd_atividade_cronograma."');");
	$abas[] = array('aba_cronograma', 'Quadro Resumo', FALSE, "ir_quadro_resumo('".$cd_atividade_cronograma."');");
	$abas[] = array('aba_cronograma', 'Concluídas Fora', FALSE, "ir_concluidas_fora('".$cd_atividade_cronograma."');");

	echo aba_start( $abas );
	echo form_start_box( "default_box", "Cronograma" );
			echo form_default_text('descricao', "Descrição: ", $row2, "style='width:300%;border: 0px;' readonly" );
			echo form_default_text('periodo', "Período: ", $row2['dt_inicio'] .' á '. $row2['dt_final'], "style='width:300%;border: 0px;' readonly" );
			echo form_default_text('nome', "Responsável: ", $row2, "style='width:300%;border: 0px;' readonly" );
		echo form_end_box("default_box");
	echo form_open('atividade/atividade_cronograma/salvar_item');
	echo form_start_box( "default_box", "Atividade" );
		echo form_default_hidden('cd_atividade_cronograma', '', $cd_atividade_cronograma);
		echo form_default_hidden('cd_atividade_cronograma_item', '', $cd_atividade_cronograma_item);
		#echo form_default_text('cd_atividade_cronograma', "Código: ", $cd_atividade_cronograma, "style='width:100%;border: 0px;' readonly" );
		#echo form_default_text('cd_atividade_cronograma_item', "Item: ", $cd_atividade_cronograma_item, "style='width:100%;border: 0px;' readonly" );
		#echo form_default_text('dt_inclusao', "Dt. Inclusão: ", $row, "style='width:100%;border: 0px;' readonly" );
		#echo form_default_text('dt_exclusao', "Dt. Exclusão: ", $row, "style='width:100%;border: 0px;' readonly" );		
		echo form_default_integer('cd_atividade', "Cód. Atividade:*",$row);
		echo form_default_dropdown_db('cd_atividade_cronograma_grupo', 'Grupo:', array('projetos.atividade_cronograma_grupo', 'cd_atividade_cronograma_grupo', 'ds_atividade_cronograma_grupo'), array($row['cd_atividade_cronograma_grupo']), '', '', TRUE);
	echo form_end_box("default_box");

	echo form_command_bar_detail_start();
		if($row['dt_exclusao'] == "" AND $dt_encerra == '')
		{
			echo button_save("Salvar");
			if(intval($cd_atividade_cronograma_item) > 0)
			{
				//echo button_save("Excluir","cronogramaItemExcluir(".$cd_atividade_cronograma.",".$cd_atividade_cronograma_item.")","botao_vermelho");
			}			
		}	
		echo button_save("Voltar","cronogramaItem(".$cd_atividade_cronograma.")","botao_disabled");
	echo form_command_bar_detail_end();
	echo form_close();
	
	echo aba_end();
	$this->load->view('footer_interna');
?>