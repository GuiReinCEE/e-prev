<?php 
set_title($tabela[0]['ds_indicador']);
$this->load->view('header'); 
?>
<script>
	<?php 
	echo form_default_js_submit(array("mes_referencia", "ano_referencia", "cd_indicador_tabela", "nr_abertas_mes", "nr_atendidas_mes"),'_salvar(form)');	?>

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
		location.href='<?php echo site_url("indicador_plugin/controladoria_informativo_gerencial"); ?>';
	}
	
    function manutencao()
    {
        location.href='<?php echo site_url("indicador/manutencao/"); ?>';
    }
	
	function excluir()
	{
		location.href = '<?php echo site_url("indicador_plugin/controladoria_informativo_gerencial/excluir/".$row["cd_controladoria_informativo_gerencial"]); ?>';
	}
	
			
	$(function() {
		$("#mes_referencia").focus();
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
	echo form_open('indicador_plugin/controladoria_informativo_gerencial/salvar');
		echo form_start_box("default_box", 'Cadastro');
			echo form_default_hidden('cd_indicador_tabela', 'C�digo indicador tabela', $tabela[0]['cd_indicador_tabela']);
			echo form_default_hidden('cd_controladoria_informativo_gerencial', 'C�digo da tabela', intval($row['cd_controladoria_informativo_gerencial']));
			echo form_default_row("", "Indicador:", '<span class="label label-inverse">'.$tabela[0]['ds_indicador'].'</span>'); 
			echo form_default_row("", "Per�odo aberto:", '<span class="label label-important">'.$tabela[0]['ds_periodo'].'</span>'); 
			echo form_default_row("","","");

			echo form_default_hidden("dt_referencia", $label_0.": (*)", $row); 
			echo form_default_integer("ano_referencia", "Ano :*", $row['ano_referencia']);
			echo form_default_dropdown("mes_referencia", "Trimestre :*", array(array("value" => "01", "text" => "01"), array("value" => "02", "text" => "02"), array("value" => "03", "text" => "03"), array("value" => "04", "text" => "04")), $row['mes_referencia']);
			echo form_default_integer("nr_repondente", $label_1.' :', app_decimal_para_php($row['nr_repondente']), "class='indicador_text'"); 
			//echo form_default_numeric("nr_meta", $label_8.' :', app_decimal_para_php($row['nr_meta']), "class='indicador_text'");
			echo form_default_textarea("observacao", $label_7.' :', $row['observacao'],'style="height: 80px;"');
			echo form_default_row("", "",'<span style="font-size: 130%; font-weight: bold; color:red; display:none;" id="msg_importar">Aguarde, importando os dados.</span>');
		echo form_end_box("default_box");

		echo form_start_box("default_clareza_box", $label_2);
			echo form_default_numeric("nr_clareza_meta", 'Meta :', app_decimal_para_php($row['nr_clareza_meta']), "class='indicador_text'");
			echo form_default_integer("nr_clareza_1", 'Muito Satisfeito :', app_decimal_para_php($row['nr_clareza_1']), "class='indicador_text'"); 
			echo form_default_integer("nr_clareza_2", 'Satisfeito :', app_decimal_para_php($row['nr_clareza_2']), "class='indicador_text'"); 
			echo form_default_integer("nr_clareza_3", 'Indiferente :', app_decimal_para_php($row['nr_clareza_3']), "class='indicador_text'"); 
			echo form_default_integer("nr_clareza_4", 'Insatisfeito :', app_decimal_para_php($row['nr_clareza_4']), "class='indicador_text'"); 
			echo form_default_integer("nr_clareza_5", 'Muito insatisfeito :', app_decimal_para_php($row['nr_clareza_5']), "class='indicador_text'"); 
		echo form_end_box("default_clareza_box");

		echo form_start_box("default_exatidao_box", $label_3);
			echo form_default_numeric("nr_exatidao_meta", 'Meta :', app_decimal_para_php($row['nr_exatidao_meta']), "class='indicador_text'");
			echo form_default_integer("nr_exatidao_1", 'Muito Satisfeito :', app_decimal_para_php($row['nr_exatidao_1']), "class='indicador_text'"); 
			echo form_default_integer("nr_exatidao_2", 'Satisfeito :', app_decimal_para_php($row['nr_exatidao_2']), "class='indicador_text'"); 
			echo form_default_integer("nr_exatidao_3", 'Indiferente :', app_decimal_para_php($row['nr_exatidao_3']), "class='indicador_text'"); 
			echo form_default_integer("nr_exatidao_4", 'Insatisfeito :', app_decimal_para_php($row['nr_exatidao_4']), "class='indicador_text'"); 
			echo form_default_integer("nr_exatidao_5", 'Muito insatisfeito :', app_decimal_para_php($row['nr_exatidao_5']), "class='indicador_text'"); 
		echo form_end_box("default_exatidao_box");

		echo form_start_box("default_tempestividadea_box", $label_4);
			echo form_default_numeric("nr_tempestividade_meta", 'Meta :', app_decimal_para_php($row['nr_tempestividade_meta']), "class='indicador_text'");
			echo form_default_integer("nr_tempestividade_1", 'Muito Satisfeito :', app_decimal_para_php($row['nr_tempestividade_1']), "class='indicador_text'"); 
			echo form_default_integer("nr_tempestividade_2", 'Satisfeito :', app_decimal_para_php($row['nr_tempestividade_2']), "class='indicador_text'"); 
			echo form_default_integer("nr_tempestividade_3", 'Indiferente :', app_decimal_para_php($row['nr_tempestividade_3']), "class='indicador_text'"); 
			echo form_default_integer("nr_tempestividade_4", 'Insatisfeito :', app_decimal_para_php($row['nr_tempestividade_4']), "class='indicador_text'"); 
			echo form_default_integer("nr_tempestividade_5", 'Muito insatisfeito :', app_decimal_para_php($row['nr_tempestividade_5']), "class='indicador_text'"); 
		echo form_end_box("default_tempestividade_box");

		echo form_start_box("default_relevancia_box", $label_5);
			echo form_default_numeric("nr_relevancia_meta", 'Meta :', app_decimal_para_php($row['nr_relevancia_meta']), "class='indicador_text'");
			echo form_default_integer("nr_relevancia_1", 'Muito Satisfeito :', app_decimal_para_php($row['nr_relevancia_1']), "class='indicador_text'"); 
			echo form_default_integer("nr_relevancia_2", 'Satisfeito :', app_decimal_para_php($row['nr_relevancia_2']), "class='indicador_text'"); 
			echo form_default_integer("nr_relevancia_3", 'Indiferente :', app_decimal_para_php($row['nr_relevancia_3']), "class='indicador_text'"); 
			echo form_default_integer("nr_relevancia_4", 'Insatisfeito :', app_decimal_para_php($row['nr_relevancia_4']), "class='indicador_text'"); 
			echo form_default_integer("nr_relevancia_5", 'Muito insatisfeito :', app_decimal_para_php($row['nr_relevancia_5']), "class='indicador_text'"); 
		echo form_end_box("default_relevancia_box");

		echo form_command_bar_detail_start();
			echo button_save();

			if(intval($row['cd_controladoria_informativo_gerencial']) > 0)
			{
				echo button_save('Excluir', 'excluir();', 'botao_vermelho');
			}

		echo form_command_bar_detail_end();
	echo form_close();
	echo br();
echo aba_end();
$this->load->view('footer_interna');
?>