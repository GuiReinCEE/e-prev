<?php 
set_title($tabela[0]['ds_indicador']);
$this->load->view('header'); 
?>
<script>
	<?php 
	echo form_default_js_submit(array("mes_referencia", "ano_referencia", "cd_indicador_tabela", "nr_valor_1", "nr_valor_2", "nr_valor_3", "nr_meta"),'_salvar(form)');	?>

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
		location.href='<?php echo site_url("indicador_plugin/ri_pub_presente_eventos_internos"); ?>';
	}
	
    function manutencao()
    {
        location.href='<?php echo site_url("indicador/manutencao/"); ?>';
    }
	
	function excluir()
	{
		location.href = '<?php echo site_url("indicador_plugin/ri_pub_presente_eventos_internos/excluir/".$row["cd_ri_pub_presente_eventos_internos"]); ?>';
	}
	
	function getValores()
	{
		if(($("#mes_referencia").val() != "") && ($("#ano_referencia").val() != ""))
		{
			$("#msg_importar").show();	
			
			$("#command_bar").hide();
			$.post('<?php echo site_url("/ecrm/ri_controle_evento/resumoListar"); ?>', 
			{
				nr_ano                  : $("#ano_referencia").val(),
				nr_mes                  : $("#mes_referencia").val(),
				cd_controle_evento_tipo : 2, //interno
				fl_json                 : "S"
			},
			function(data)
			{
				if(data[0])
				{
					$("#nr_valor_1").val(data[0].nr_convidado);
					$("#nr_valor_2").val(data[0].nr_estimado);
					$("#nr_valor_3").val(data[0].nr_presente);
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
	echo form_open('indicador_plugin/ri_pub_presente_eventos_internos/salvar');
		echo form_start_box("default_box", $tabela[0]['ds_indicador']);
			echo form_default_hidden('cd_indicador_tabela', 'Código indicador tabela', $tabela[0]['cd_indicador_tabela']);
			echo form_default_hidden('cd_ri_pub_presente_eventos_internos', 'Código da tabela', intval($row['cd_ri_pub_presente_eventos_internos']));
			echo form_default_text("", "Indicador:", $tabela[0]['ds_indicador'],'style="border: 0px; width: 500px; font-weight:bold;"'); 
			echo form_default_text("", "Período aberto:", $tabela[0]['ds_periodo'],'style="border: 0px; width: 500px; color:red; font-weight:bold;"'); 
			echo form_default_row("","","");
			echo form_default_hidden('dt_referencia', $label_0.": (*)", $row); 
			echo form_default_mes_ano('mes_referencia', 'ano_referencia', $label_0.' :*', $row['dt_referencia']);
			echo form_default_integer("nr_valor_1", $label_1.' :', ($row['nr_valor_1']), "class='indicador_text'"); 
			echo form_default_integer("nr_valor_2", $label_2.' :', ($row['nr_valor_2']), "class='indicador_text'");
			echo form_default_integer("nr_valor_3", $label_3.' :', ($row['nr_valor_3']), "class='indicador_text'");
			echo form_default_numeric("nr_meta", $label_5.' :', number_format($row['nr_meta'],2,",","."), "class='indicador_text'");
			echo form_default_textarea("observacao", $label_6.":", $row['observacao']);
			echo form_default_row("", "",'<span style="font-size: 130%; font-weight: bold; color:red; display:none;" id="msg_importar">Aguarde, importando os dados.</span>');
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();
			echo button_save();
			echo button_save('Importar Valores', 'getValores();', 'botao_verde');

			if(intval($row['cd_ri_pub_presente_eventos_internos']) > 0)
			{
				echo button_save('Excluir', 'excluir();', 'botao_vermelho');
			}

		echo form_command_bar_detail_end();
	echo form_close();
	echo br();
echo aba_end();
$this->load->view('footer_interna');
?>