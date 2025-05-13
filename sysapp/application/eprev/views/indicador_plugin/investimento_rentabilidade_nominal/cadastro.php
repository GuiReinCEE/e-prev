<?php 
set_title($tabela[0]['ds_indicador']);
$this->load->view('header'); 
?>
<script>
	<?php 
	echo form_default_js_submit(array("mes_referencia", "ano_referencia", "cd_indicador_tabela", "nr_valor_1", "nr_valor_2", "nr_valor_3"),'_salvar(form)');	?>

	function _salvar(form)
	{
		$('#dt_referencia').val('01/'+$('#mes_referencia').val()+'/'+$('#ano_referencia').val());

		if(confirm('Salvar?'))
		{
			form.submit();
		}
	}

	function ir_lista()
	{
		location.href = '<?= site_url("indicador_plugin/investimento_rentabilidade_nominal") ?>';
	}
	
    function manutencao()
    {
        location.href = '<?= site_url("indicador/manutencao") ?>';
    }
	
	function excluir()
	{
		location.href = '<?= site_url("indicador_plugin/investimento_rentabilidade_nominal/excluir/".$row["cd_investimento_rentabilidade_nominal"]) ?>';
	}
		
	function get_valores()
	{
		if(($("#mes_referencia").val() != "") && ($("#ano_referencia").val() != ""))
		{
			$("#msg_importar").show();	
			
			$("#command_bar").hide();
			
			$.post("<?= site_url('indicador_plugin/investimento_rentabilidade_nominal/get_valores') ?>", 
			{
				nr_ano : $("#ano_referencia").val(),
				nr_mes : $("#mes_referencia").val()
			},
			function(data)
			{
				if(data)
				{
					$.each(data, function (name, value) {
						$("#"+value.ds_caderno_cci_integracao_indicador_campo).val(value.nr_valor);
					});
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

if($row["nr_valor_1_label"] > 0)
{
	$label_3 = str_replace("1", number_format($row["nr_valor_1_label"],2,",","."), $label_3);
}
/*
if($row["nr_valor_2_label"] > 0)
{
	$label_4 = str_replace("2", number_format($row["nr_valor_2_label"],2,",","."), $label_4);
}
*/
echo aba_start($abas);
	echo form_open('indicador_plugin/investimento_rentabilidade_nominal/salvar');
		echo form_start_box("default_box", 'Cadastro');
			echo form_default_hidden('cd_indicador_tabela', 'Código indicador tabela', $tabela[0]['cd_indicador_tabela']);
			echo form_default_hidden('cd_investimento_rentabilidade_nominal', 'Código da tabela', intval($row['cd_investimento_rentabilidade_nominal']));
			echo form_default_row("", "Indicador:", '<span class="label label-inverse">'.$tabela[0]['ds_indicador'].'</span>'); 
			echo form_default_row("", "Período aberto:", '<span class="label label-important">'.$tabela[0]['ds_periodo'].'</span>'); 
			echo form_default_row("","","");
			echo form_default_hidden('dt_referencia', $label_0.": (*)", $row); 
			echo form_default_mes_ano('mes_referencia', 'ano_referencia', $label_0.' :*', $row['dt_referencia']);
			echo form_default_numeric("nr_valor_1", $label_3.' :', number_format($row['nr_valor_1'],4,",","."), "class='indicador_text'", array("centsLimit" => 4));  
			//echo form_default_numeric("nr_valor_2", $label_4.' :', number_format($row['nr_valor_2'],4,",","."), "class='indicador_text'", array("centsLimit" => 4)); 
			echo form_default_numeric("nr_valor_3", $label_1.' :', number_format($row['nr_valor_3'],4,",","."), "class='indicador_text'", array("centsLimit" => 4)); 
			//echo form_default_numeric("nr_valor_4", $label_2.' :', number_format($row['nr_valor_4'],4,",","."), "class='indicador_text'", array("centsLimit" => 4));   
			echo form_default_textarea("observacao", $label_9.":", $row['observacao']);
			echo form_default_row("", "",'<span style="font-size: 130%; font-weight: bold; color:red; display:none;" id="msg_importar">Aguarde, importando os dados.</span>');
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();
			echo button_save();
			echo button_save('Importar Valores', 'get_valores();', 'botao_verde');
			
			if(intval($row['cd_investimento_rentabilidade_nominal']) > 0)
			{
				echo button_save('Excluir', 'excluir();', 'botao_vermelho');
			}

		echo form_command_bar_detail_end();
	echo form_close();
	echo br();
echo aba_end();
$this->load->view('footer_interna');
?>