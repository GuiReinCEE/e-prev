<?php 
set_title($tabela[0]['ds_indicador']);
$this->load->view('header'); 
?>
<script>
	<?php 
	echo form_default_js_submit(array("ano_referencia", 
									  "cd_situacao", 
									  "cd_indicador_tabela", 
									  "nr_valor_1", 
									  "nr_valor_2", 
									  "nr_valor_3", 
									  "nr_valor_4"),
									  '_salvar(form)');	?>

	function _salvar(form)
	{
		if($('#nr_ano_periodo').val() != $('#ano_referencia').val())
		{
			alert("ERRO\n\nANO ("+$('#ano_referencia').val()+") do lançamento diferente do ANO ("+$('#nr_ano_periodo').val()+") do período\n\n");
			$('#ano_referencia').focus();
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
		location.href='<?php echo site_url("indicador_plugin/juridico_sucesso_acoes_re_trabalhista"); ?>';
	}
	
    function manutencao()
    {
        location.href='<?php echo site_url("indicador/manutencao/"); ?>';
    }
	
	function excluir()
	{
		location.href = '<?php echo site_url("indicador_plugin/juridico_sucesso_acoes_re_trabalhista/excluir/".$row["cd_juridico_sucesso_acoes_re_trabalhista"]); ?>';
	}
	
	function getValores()
	{
		if(($("#mes_referencia").val() != "") && ($("#ano_referencia").val() != ""))
		{
			$("#msg_importar").show();	
			
			$("#command_bar").hide();
			
			$.post('<?php echo site_url("indicador_plugin/juridico_sucesso_acoes_re_trabalhista/get_valores"); ?>', 
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
		$("#ano_referencia").focus();
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
	echo form_open('indicador_plugin/juridico_sucesso_acoes_re_trabalhista/salvar');
		echo form_start_box("default_box", 'Cadastro');
			echo form_default_hidden('cd_indicador_tabela', 'Código indicador tabela', $tabela[0]['cd_indicador_tabela']);
			echo form_default_hidden('nr_ano_periodo', 'Ano referência período', $tabela[0]['nr_ano_referencia']);
			echo form_default_hidden('cd_juridico_sucesso_acoes_re_trabalhista', 'Código da tabela', intval($row['cd_juridico_sucesso_acoes_re_trabalhista']));
			echo form_default_row("", "Indicador:", '<span class="label label-inverse">'.$tabela[0]['ds_indicador'].'</span>'); 
			echo form_default_row("", "Período aberto:", '<span class="label label-important">'.$tabela[0]['ds_periodo'].'</span>'); 
			echo form_default_row("","","");
			echo form_default_hidden('dt_referencia', "dt_referencia: (*)", $row); 
			
			echo form_default_integer('ano_referencia', 'Ano: (*)', (intval($row['ano_referencia']) == 0 ? intval($tabela[0]['nr_ano_referencia']) : intval($row['ano_referencia'])));
			echo form_default_dropdown('cd_etapa', $label_0.': (*)', $ar_fase, Array($row['cd_etapa']));
			
			echo form_default_integer("nr_valor_1", $label_1.': (*)', intval($row['nr_valor_1']), "class='indicador_text'"); 
			echo form_default_integer("nr_valor_2", $label_3.': (*)', intval($row['nr_valor_2']), "class='indicador_text'");
			echo form_default_integer("nr_valor_3", $label_5.': (*)', intval($row['nr_valor_3']), "class='indicador_text'");
			echo form_default_integer("nr_valor_4", $label_7.': (*)', intval($row['nr_valor_4']), "class='indicador_text'");			
			
			echo form_default_textarea("observacao", $label_10.':', $row['observacao'], 'style="height: 80px;"');
			echo form_default_row("", "",'<span style="font-size: 130%; font-weight: bold; color:red; display:none;" id="msg_importar">Aguarde, importando os dados.</span>');
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();
			echo button_save();

			if(intval($row['cd_juridico_sucesso_acoes_re_trabalhista']) > 0)
			{
				echo button_save('Excluir', 'excluir();', 'botao_vermelho');
			}

		echo form_command_bar_detail_end();
	echo form_close();
	echo br();
echo aba_end();
$this->load->view('footer_interna');
?>