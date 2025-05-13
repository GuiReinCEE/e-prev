<?php 
set_title($tabela[0]['ds_indicador']);
$this->load->view('header'); 
?>
<script>
	<?php 
	echo form_default_js_submit(array("mes_referencia", 
	                                  "ano_referencia", 
									  "cd_indicador_tabela", 
		                              'nr_sg',
		                              'nr_gri',
		                              'nr_gap',
		                              'nr_gb',
		                              'nr_ga',
		                              'nr_gc',
		                              'nr_gf',
		                              'nr_gin',
		                              'nr_rh',
		                              'nr_gad',
		                              'nr_gi',
									  "nr_meta"),'_salvar(form)');	?>

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
		location.href='<?php echo site_url("indicador_plugin/juridico_num_sol_par_gere"); ?>';
	}
	
    function manutencao()
    {
        location.href='<?php echo site_url("indicador/manutencao/"); ?>';
    }
	
	function excluir()
	{
		location.href = '<?php echo site_url("indicador_plugin/juridico_num_sol_par_gere/excluir/".$row["cd_juridico_num_sol_par_gere"]); ?>';
	}
			
	$(function() {
		$("#nr_meta_row").hide();
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
	echo form_open('indicador_plugin/juridico_num_sol_par_gere/salvar');
		echo form_start_box("default_box", 'Cadastro');
			echo form_default_hidden('cd_indicador_tabela', 'Código indicador tabela', $tabela[0]['cd_indicador_tabela']);
			echo form_default_hidden('nr_ano_periodo', 'Ano referência período', $tabela[0]['nr_ano_referencia']);
			echo form_default_hidden('cd_juridico_num_sol_par_gere', 'Código da tabela', intval($row['cd_juridico_num_sol_par_gere']));
			echo form_default_row("", "Indicador:", '<span class="label label-inverse">'.$tabela[0]['ds_indicador'].'</span>'); 
			echo form_default_row("", "Período aberto:", '<span class="label label-important">'.$tabela[0]['ds_periodo'].'</span>'); 
			echo form_default_row("","","");
			echo form_default_hidden('dt_referencia', $label_0.": (*)", $row); 
			echo form_default_mes_ano('mes_referencia', 'ano_referencia', $label_0.': (*)', $row['dt_referencia']);
			
			echo form_default_integer("nr_sg", $label_1.': (*)', intval($row['nr_sg']), "class='indicador_text'"); 
			echo form_default_integer("nr_gri", $label_2.': (*)', intval($row['nr_gri']), "class='indicador_text'"); 
			echo form_default_integer("nr_gap", $label_3.': (*)', intval($row['nr_gap']), "class='indicador_text'"); 
			echo form_default_integer("nr_gb", $label_4.': (*)', intval($row['nr_gb']), "class='indicador_text'"); 
			echo form_default_integer("nr_ga", $label_5.': (*)', intval($row['nr_ga']), "class='indicador_text'"); 
			echo form_default_integer("nr_gc", $label_6.': (*)', intval($row['nr_gc']), "class='indicador_text'"); 
			echo form_default_integer("nr_gf", $label_7.': (*)', intval($row['nr_gf']), "class='indicador_text'"); 
			echo form_default_integer("nr_gin", $label_8.': (*)', intval($row['nr_gin']), "class='indicador_text'"); 
			echo form_default_integer("nr_rh", $label_9.': (*)', intval($row['nr_rh']), "class='indicador_text'"); 
			echo form_default_integer("nr_gad", $label_10.': (*)', intval($row['nr_gad']), "class='indicador_text'"); 
			echo form_default_integer("nr_gi", $label_11.': (*)', intval($row['nr_gi']), "class='indicador_text'"); 
			
			echo form_default_integer("nr_meta", $label_17.': (*)', intval($row['nr_meta']), "class='indicador_text'");
			echo form_default_textarea("observacao", $label_18.':', $row['observacao'],'style="height: 80px;"');
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();
			echo button_save();

			if(intval($row['cd_juridico_num_sol_par_gere']) > 0)
			{
				echo button_save('Excluir', 'excluir();', 'botao_vermelho');
			}

		echo form_command_bar_detail_end();
	echo form_close();
	echo br();
echo aba_end();
$this->load->view('footer_interna');
?>