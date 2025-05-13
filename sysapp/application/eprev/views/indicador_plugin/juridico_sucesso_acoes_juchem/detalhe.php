<?php 
set_title($tabela[0]['ds_indicador']);
$this->load->view('header'); 
?>
<script>
	<?php 
	echo form_default_js_submit(array("ano_referencia", 
									  "cd_situacao", 
									  "cd_indicador_tabela", 
									  "cd_etapa",
									  "nr_inicial", 
									  "nr_improcede", 
									  "nr_parcial", 
									  "nr_procede"),
									  '_salvar(form)');	?>

	function _salvar(form)
	{
		if($('#nr_ano_periodo').val() != $('#ano_referencia').val())
		{
			alert("ERRO\n\nANO ("+$('#ano_referencia').val()+") do lan�amento diferente do ANO ("+$('#nr_ano_periodo').val()+") do per�odo\n\n");
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
		location.href='<?php echo site_url("indicador_plugin/juridico_sucesso_acoes_juchem"); ?>';
	}
	
    function manutencao()
    {
        location.href='<?php echo site_url("indicador/manutencao/"); ?>';
    }
	
	function excluir()
	{
		location.href = '<?php echo site_url("indicador_plugin/juridico_sucesso_acoes_juchem/excluir/".$row["cd_juridico_sucesso_acoes_juchem"]); ?>';
	}
	
	function getValores()
	{
		if(($("#mes_referencia").val() != "") && ($("#ano_referencia").val() != ""))
		{
			$("#msg_importar").show();	
			
			$("#command_bar").hide();
			
			$.post('<?php echo site_url("indicador_plugin/juridico_sucesso_acoes_juchem/get_valores"); ?>', 
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
			alert("Informe o M�s e Ano");
		}
	}
			
	$(function() {
		$("#ano_referencia").focus();
	});
</script>
<?php

if(count($tabela) == 0)
{
	echo '<span style="font-size: 12pt; color:red; font-weight:bold;">Nenhum per�odo aberto para criar a tabela do indicador.</span>';
	exit;
}
else if(count($tabela) > 1)
{
	echo '<span style="font-size: 12pt; color:red; font-weight:bold;">Existe mais de um per�odo aberto, no entanto s� ser� poss�vel incluir valores para o novo per�odo depois de fechar o mais antigo.</span>';
	exit;			
}

$abas[] = array('aba_lista', 'Lista', false, 'manutencao();');
$abas[] = array('aba_lista', 'Lan�amento', false, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');

echo aba_start($abas);
	echo form_open('indicador_plugin/juridico_sucesso_acoes_juchem/salvar');
		echo form_start_box("default_box", 'Cadastro');
			echo form_default_hidden('cd_indicador_tabela', 'C�digo indicador tabela', $tabela[0]['cd_indicador_tabela']);
			echo form_default_hidden('nr_ano_periodo', 'Ano refer�ncia per�odo', $tabela[0]['nr_ano_referencia']);
			echo form_default_hidden('cd_juridico_sucesso_acoes_juchem', 'C�digo da tabela', intval($row['cd_juridico_sucesso_acoes_juchem']));
			echo form_default_row("", "Indicador:", '<span class="label label-inverse">'.$tabela[0]['ds_indicador'].'</span>'); 
			echo form_default_row("", "Per�odo aberto:", '<span class="label label-important">'.$tabela[0]['ds_periodo'].'</span>'); 
			echo form_default_row("","","");
			echo form_default_hidden('dt_referencia', "dt_referencia: (*)", $row); 
			
			echo form_default_integer('ano_referencia', 'Ano: (*)', (intval($row['ano_referencia']) == 0 ? intval($tabela[0]['nr_ano_referencia']) : intval($row['ano_referencia'])));
			echo form_default_dropdown('cd_etapa', $label_0.': (*)', $ar_fase, Array($row['cd_etapa']));
			
			echo form_default_integer("nr_inicial", $label_1.': (*)', intval($row['nr_inicial']), "class='indicador_text'"); 
			echo form_default_integer("nr_improcede", $label_2.': (*)', intval($row['nr_improcede']), "class='indicador_text'");
			echo form_default_integer("nr_parcial", $label_4.': (*)', intval($row['nr_parcial']), "class='indicador_text'");
			echo form_default_integer("nr_procede", $label_6.': (*)', intval($row['nr_procede']), "class='indicador_text'");			
			
			echo form_default_textarea("observacao", $label_10.':', $row['observacao'], 'style="height: 80px;"');
			echo form_default_row("", "",'<span style="font-size: 130%; font-weight: bold; color:red; display:none;" id="msg_importar">Aguarde, importando os dados.</span>');
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();
			echo button_save();

			if(intval($row['cd_juridico_sucesso_acoes_juchem']) > 0)
			{
				echo button_save('Excluir', 'excluir();', 'botao_vermelho');
			}

		echo form_command_bar_detail_end();
	echo form_close();
	echo br();
echo aba_end();
$this->load->view('footer_interna');
?>