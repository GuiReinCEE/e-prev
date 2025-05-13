<?php 
set_title($tabela[0]['ds_indicador']);
$this->load->view('header'); 
?>
<script>
	<?php 
	echo form_default_js_submit(array("mes_referencia", "ano_referencia", "cd_indicador_tabela", "nr_valor_1", "nr_valor_2", "nr_meta"),'_salvar(form)');	?>

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
		location.href = '<?= site_url("indicador_plugin/juridico_pareceres_fora_prazo") ?>';
	}
	
    function manutencao()
    {
        location.href = '<?= site_url("indicador/manutencao") ?>';
    }
	
	function excluir()
	{
		location.href = '<?= site_url("indicador_plugin/juridico_pareceres_fora_prazo/excluir/".$row["cd_juridico_pareceres_fora_prazo"]) ?>';
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
	echo form_open('indicador_plugin/juridico_pareceres_fora_prazo/salvar');
		echo form_start_box("default_box", 'Cadastro');
			echo form_default_hidden('cd_indicador_tabela', 'Código indicador tabela', $tabela[0]['cd_indicador_tabela']);
			echo form_default_hidden('cd_juridico_pareceres_fora_prazo', 'Código da tabela', intval($row['cd_juridico_pareceres_fora_prazo']));
			echo form_default_row("", "Indicador:", '<span class="label label-inverse">'.$tabela[0]['ds_indicador'].'</span>'); 
			echo form_default_row("", "Período aberto:", '<span class="label label-important">'.$tabela[0]['ds_periodo'].'</span>'); 
			echo form_default_row("","","");
			echo form_default_hidden('dt_referencia', $label_0.": (*)", $row); 
			echo form_default_mes_ano('mes_referencia', 'ano_referencia', $label_0.' :*', $row['dt_referencia']);
			echo form_default_numeric("nr_meta", $label_4.' :', number_format($row['nr_meta'],2,",","."), "class='indicador_text'");
			echo form_default_textarea("observacao", $label_6.":", $row['observacao']);
		echo form_end_box("default_box");

		echo form_start_box("default_box_1", 'AI');
			echo form_default_integer('nr_pareceres_ai', $label_1.' :', $row['nr_pareceres_ai'], "class='indicador_text'");
			echo form_default_integer('nr_pareceres_prazo_ai', $label_2.' :', $row['nr_pareceres_prazo_ai'], "class='indicador_text'");
		echo form_end_box("default_box_1");
		/*
		echo form_start_box("default_box_2", 'GRC');
			echo form_default_integer('nr_pareceres_grc', $label_1.' :', $row['nr_pareceres_grc'], "class='indicador_text'");
			echo form_default_integer('nr_pareceres_prazo_grc', $label_2.' :', $row['nr_pareceres_prazo_grc'], "class='indicador_text'");
		echo form_end_box("default_box_2");
		*/
		echo form_start_box("default_box_3", 'GJ');
			echo form_default_integer('nr_pareceres_gj', $label_1.' :', $row['nr_pareceres_gj'], "class='indicador_text'");
			echo form_default_integer('nr_pareceres_prazo_gj', $label_2.' :', $row['nr_pareceres_prazo_gj'], "class='indicador_text'");
		echo form_end_box("default_box_3");
		echo form_start_box("default_box_4", 'GC');
			echo form_default_integer('nr_pareceres_gc', $label_1.' :', $row['nr_pareceres_gc'], "class='indicador_text'");
			echo form_default_integer('nr_pareceres_prazo_gc', $label_2.' :', $row['nr_pareceres_prazo_gc'], "class='indicador_text'");
		echo form_end_box("default_box_4");
		echo form_start_box("default_box_5", 'GS');
			echo form_default_integer('nr_pareceres_gti', $label_1.' :', $row['nr_pareceres_gti'], "class='indicador_text'");
			echo form_default_integer('nr_pareceres_prazo_gti', $label_2.' :', $row['nr_pareceres_prazo_gti'], "class='indicador_text'");
		echo form_end_box("default_box_5");
		echo form_start_box("default_box_6", 'GIN');
			echo form_default_integer('nr_pareceres_gin', $label_1.' :', $row['nr_pareceres_gin'], "class='indicador_text'");
			echo form_default_integer('nr_pareceres_prazo_gin', $label_2.' :', $row['nr_pareceres_prazo_gin'], "class='indicador_text'");
		echo form_end_box("default_box_6");
		echo form_start_box("default_box_7", 'GFC');
			echo form_default_integer('nr_pareceres_gfc', $label_1.' :', $row['nr_pareceres_gfc'], "class='indicador_text'");
			echo form_default_integer('nr_pareceres_prazo_gfc', $label_2.' :', $row['nr_pareceres_prazo_gfc'], "class='indicador_text'");
		echo form_end_box("default_box_7");
		/*
		echo form_start_box("default_box_8", 'GRSC');
			echo form_default_integer('nr_pareceres_grsc', $label_1.' :', $row['nr_pareceres_grsc'], "class='indicador_text'");
			echo form_default_integer('nr_pareceres_prazo_grsc', $label_2.' :', $row['nr_pareceres_prazo_grsc'], "class='indicador_text'");
		echo form_end_box("default_box_8");
		*/
		echo form_start_box("default_box_13", 'GNR');
			echo form_default_integer('nr_pareceres_gn', $label_1.' :', $row['nr_pareceres_gn'], "class='indicador_text'");
			echo form_default_integer('nr_pareceres_prazo_gn', $label_2.' :', $row['nr_pareceres_prazo_gn'], "class='indicador_text'");
		echo form_end_box("default_box_13");
		echo form_start_box("default_box_9", 'GAP');
			echo form_default_integer('nr_pareceres_gp', $label_1.' :', $row['nr_pareceres_gp'], "class='indicador_text'");
			echo form_default_integer('nr_pareceres_prazo_gp', $label_2.' :', $row['nr_pareceres_prazo_gp'], "class='indicador_text'");
		echo form_end_box("default_box_9");
		echo form_start_box("default_box_10", 'DE');
			echo form_default_integer('nr_pareceres_de', $label_1.' :', $row['nr_pareceres_de'], "class='indicador_text'");
			echo form_default_integer('nr_pareceres_prazo_de', $label_2.' :', $row['nr_pareceres_prazo_de'], "class='indicador_text'");
		echo form_end_box("default_box_10");
		echo form_start_box("default_box_11", 'CF');
			echo form_default_integer('nr_pareceres_cf', $label_1.' :', $row['nr_pareceres_cf'], "class='indicador_text'");
			echo form_default_integer('nr_pareceres_prazo_cf', $label_2.' :', $row['nr_pareceres_prazo_cf'], "class='indicador_text'");
		echo form_end_box("default_box_11");
		echo form_start_box("default_box_12", 'CD');
			echo form_default_integer('nr_pareceres_cd', $label_1.' :', $row['nr_pareceres_cd'], "class='indicador_text'");
			echo form_default_integer('nr_pareceres_prazo_cd', $label_2.' :', $row['nr_pareceres_prazo_cd'], "class='indicador_text'");
		echo form_end_box("default_box_12");
		echo form_command_bar_detail_start();
			echo button_save();

			if(intval($row['cd_juridico_pareceres_fora_prazo']) > 0)
			{
				echo button_save('Excluir', 'excluir();', 'botao_vermelho');
			}

		echo form_command_bar_detail_end();
	echo form_close();
	echo br();
echo aba_end();
$this->load->view('footer_interna');
?>