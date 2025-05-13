<?php 
set_title($tabela[0]['ds_indicador']);
$this->load->view('header'); 
?>
<script>
	<?php 
	echo form_default_js_submit(array("mes_referencia", "ano_referencia", "cd_indicador_tabela", "qt_assistidos", "qt_acoes", "qt_reincidentes"),'_salvar(form)');	?>

	function _salvar(form)
	{
		if($('#nr_ano_periodo').val() != $('#ano_referencia').val())
		{
			alert("ERRO\n\nANO ("+$('#ano_referencia').val()+") do lançamento diferente do ANO ("+$('#nr_ano_periodo').val()+") do período\n\n");
			$('#ano_referencia').focus();
		}
		else
		{
			$('#dt_referencia').val('01/'+$('#mes_referencia').val()+'/'+$('#ano_referencia').val());

			if(confirm('Salvar?'))
			{
				form.submit();
			}
		}		
	}

	function ir_lista()
	{
		location.href='<?php echo site_url("indicador_plugin/juridico_ass_acoes_jud"); ?>';
	}
	
    function manutencao()
    {
        location.href='<?php echo site_url("indicador/manutencao/"); ?>';
    }
	
	function excluir()
	{
		location.href = '<?php echo site_url("indicador_plugin/juridico_ass_acoes_jud/excluir/".$row["cd_juridico_ass_acoes_jud"]); ?>';
	}
	
	function getValores()
	{
		if(($("#mes_referencia").val() != "") && ($("#ano_referencia").val() != ""))
		{
			$("#msg_importar").show();	
			
			$("#command_bar").hide();
			
			$.post('<?php echo site_url("indicador_plugin/juridico_ass_acoes_jud/get_valores"); ?>', 
			{
				nr_ano : $("#ano_referencia").val(),
				nr_mes : $("#mes_referencia").val()
			},
			function(data)
			{
				if(data)
				{
					$("#nr_abertas_mes").val(data.nr_abertas_mes);
					$("#nr_atendidas_mes").val(data.nr_atendidas_mes);
				}
				
				$("#msg_importar").hide();	
				$("#command_bar").show();
			},
			'json');
		}
		else
		{
			alert("Informe o Mês e Ano");
		}
	}
			
	$(function() {
		$("#mes_referencia").focus();
	});
</script>
<?php

if(count($tabela) == 0)
{
	echo '<span style="font-size: 12pt; color:red; font-weight:bold;">Nenhum período aberto para criar a tabela do indicador.</span>';
	exit;
}
else if(count($tabela) > 1)
{
	echo '<span style="font-size: 12pt; color:red; font-weight:bold;">Existe mais de um período aberto, no entanto só será possível incluir valores para o novo período depois de fechar o mais antigo.</span>';
	exit;			
}

$abas[] = array('aba_lista', 'Lista', false, 'manutencao();');
$abas[] = array('aba_lista', 'Lançamento', false, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');

echo aba_start($abas);
	echo form_open('indicador_plugin/juridico_ass_acoes_jud/salvar');
		echo form_start_box("default_box", 'Cadastro');
			echo form_default_hidden('cd_indicador_tabela', 'Código indicador tabela', $tabela[0]['cd_indicador_tabela']);
			echo form_default_hidden('nr_ano_periodo', 'Ano referência período', $tabela[0]['nr_ano_referencia']);
			echo form_default_hidden('cd_juridico_ass_acoes_jud', 'Código da tabela', intval($row['cd_juridico_ass_acoes_jud']));
			echo form_default_row("", "Indicador:", '<span class="label label-inverse">'.$tabela[0]['ds_indicador'].'</span>'); 
			echo form_default_row("", "Período aberto:", '<span class="label label-important">'.$tabela[0]['ds_periodo'].'</span>'); 
			echo form_default_row("","","");
			echo form_default_hidden('dt_referencia', $label_0.": (*)", $row); 
			echo form_default_mes_ano('mes_referencia', 'ano_referencia', $label_0.': (*)', $row['dt_referencia']);
			
			echo form_default_integer("qt_assistidos",   $label_6.': (*)', intval(app_decimal_para_php($row['qt_assistidos'])), "class='indicador_text'"); 
			echo form_default_integer("qt_acoes",        $label_4.': (*)', intval(app_decimal_para_php($row['qt_acoes'])), "class='indicador_text'"); 
			echo form_default_integer("qt_reincidentes", $label_3.': (*)', intval(app_decimal_para_php($row['qt_reincidentes'])), "class='indicador_text'"); 
			
			echo form_default_row("qt_novos", $label_1.':', (substr($row['qt_novos'],0) == "=" ? intval(app_decimal_para_php($row['qt_novos'])) : $row['qt_novos']));
			echo form_default_row("nr_percentual_reincidentes", $label_5.':', (substr($row['nr_percentual_reincidentes'],0) == "=" ? intval(app_decimal_para_php($row['nr_percentual_reincidentes'])) : $row['nr_percentual_reincidentes']));
			echo form_default_row("nr_percentual_assistidos_com", $label_7.':', (substr($row['nr_percentual_assistidos_com'],0) == "=" ? intval(app_decimal_para_php($row['nr_percentual_assistidos_com'])) : $row['nr_percentual_assistidos_com']));
			
			echo form_default_textarea("observacao", $label_9.':', $row['observacao']);
			echo form_default_row("", "",'<span style="font-size: 130%; font-weight: bold; color:red; display:none;" id="msg_importar">Aguarde, importando os dados.</span>');
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();
			echo button_save();

			if(intval($row['cd_juridico_ass_acoes_jud']) > 0)
			{
				echo button_save('Excluir', 'excluir();', 'botao_vermelho');
			}

		echo form_command_bar_detail_end();
	echo form_close();
	echo br();
echo aba_end();
$this->load->view('footer_interna');
?>