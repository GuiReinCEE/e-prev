<?php
	set_title($tabela[0]['ds_indicador']);
	$this->load->view('header'); 
?>
<script>
	<?php 
		echo form_default_js_submit(array("cd_indicador_tabela", "mes_referencia", "ano_referencia", "nr_valor_1", "nr_valor_2", "nr_meta_2" , "nr_meta"),'_salvar(form);');	
	?>
	
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
		location.href='<?php echo site_url("indicador_plugin/controladoria_variacao_orcamentaria"); ?>';
	}

    function manutencao()
    {
        location.href='<?php echo site_url("indicador_plugin/controladoria_variacao_orcamentaria"); ?>';
    }	
	
	function excluir()
	{
		if(confirm('Deseja Excluir?'))
		{
			location.href='<?php echo site_url("indicador_plugin/controladoria_variacao_orcamentaria/excluir/".$row["cd_controladoria_variacao_orcamentaria"]); ?>';
		}
	}
	
	function get_valores()
	{
		if(($("#mes_referencia").val() != "") && ($("#ano_referencia").val() != ""))
		{
			$("#msg_importar").show();	
			
			$("#command_bar").hide();
			
			$.post('<?php echo site_url("indicador_plugin/controladoria_variacao_orcamentaria/get_valores"); ?>', 
			{
				ano_referencia : $("#ano_referencia").val(),
				mes_referencia : $("#mes_referencia").val()
			},
			function(data)
			{
				if(data)
				{
					$("#nr_valor_1").val(data.nr_valor_2);
					$("#nr_valor_2").val(data.nr_valor_1);
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

$abas[] = array( 'aba_lista', 'Lista', false, 'manutencao();' );
$abas[] = array('aba_lista', 'Lançamento', false, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');

echo aba_start( $abas );
?>
	<? if(intval($row['qt_ano']) == 0): ?>

		<div style="width:100%; text-align:center;">
			<span style="font-size: 12pt; color:red; font-weight:bold;">
				Informar no campo de 'observações' se pretende manter ou fazer ajustes na meta do indicador, e justificar essa decisão.
			</span>
		</div>

	<? endif; ?>
	
	<?
echo form_open('indicador_plugin/controladoria_variacao_orcamentaria/salvar');
	echo form_start_box("default_box", "Cadastro");
		echo form_default_hidden('cd_indicador_tabela', 'Código indicador tabela', $tabela[0]['cd_indicador_tabela']);
		echo form_default_hidden('nr_ano_periodo', 'Ano referência período', $tabela[0]['nr_ano_referencia']);
		echo form_default_hidden('cd_controladoria_variacao_orcamentaria', 'Código da tabela', intval($row['cd_controladoria_variacao_orcamentaria']));
		echo form_default_row("", "Indicador:", '<span class="label label-inverse">'.$tabela[0]['ds_indicador'].'</span>'); 
		echo form_default_row("", "Período aberto:", '<span class="label label-important">'.$tabela[0]['ds_periodo'].'</span>'); 		
		echo form_default_row("","","");
		echo form_default_mes_ano('mes_referencia', 'ano_referencia', $label_0.": (*)", $row['dt_referencia']);
		echo form_default_hidden('dt_referencia', $label_0.": (*)", $row); 
		echo form_default_numeric("nr_valor_1", $label_1.": (*)", number_format($row['nr_valor_1'],2,',','.'), "class='indicador_text'");
		echo form_default_numeric("nr_valor_2", $label_2.": (*)", number_format($row['nr_valor_2'],2,',','.'), "class='indicador_text'");
		echo form_default_numeric("nr_meta_2", $label_4.": (*)", number_format($row['nr_meta_2'],2,',','.'), "class='indicador_text'");
		echo form_default_numeric("nr_meta", $label_5.": (*)", number_format($row['nr_meta'],2,',','.'), "class='indicador_text'");
		echo form_default_textarea("observacao", $label_6.":", $row['observacao']);
		echo form_default_row("", "",'<span style="font-size: 130%; font-weight: bold; color:red; display:none;" id="msg_importar">Aguarde, importando os dados.</span>');
	echo form_end_box("default_box");
	echo form_command_bar_detail_start();
		echo button_save();
		echo button_save('Importar Valores', 'get_valores();', 'botao_verde');
		if(intval($row['cd_controladoria_variacao_orcamentaria']) > 0)
		{
			echo button_save('Excluir', 'excluir('.$row["cd_controladoria_variacao_orcamentaria"].');', 'botao_vermelho');
		}
	echo form_command_bar_detail_end();
echo form_close();

echo aba_end();
$this->load->view('footer_interna');
?>
