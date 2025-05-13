<?php 
set_title($tabela[0]['ds_indicador']);
$this->load->view('header'); 
?>
<script>
	<?php 
	echo form_default_js_submit(array("mes_referencia", "ano_referencia", "cd_indicador_tabela", "vl_indenizacao", "nr_liquidada", "nr_demandante", "nr_meta"),'_salvar(form)');	?>

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
		location.href='<?php echo site_url("indicador_plugin/juridico_inden_deman"); ?>';
	}
	
    function manutencao()
    {
        location.href='<?php echo site_url("indicador/manutencao/"); ?>';
    }
	
	function excluir()
	{
		location.href = '<?php echo site_url("indicador_plugin/juridico_inden_deman/excluir/".$row["cd_juridico_inden_deman"]); ?>';
	}
			
	$(function() {
		$("#mes_referencia").focus();
		$("#nr_meta_row").hide();
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
	echo form_open('indicador_plugin/juridico_inden_deman/salvar');
		echo form_start_box("default_box", 'Cadastro');
			echo form_default_hidden('cd_indicador_tabela', 'Código indicador tabela', $tabela[0]['cd_indicador_tabela']);
			echo form_default_hidden('nr_ano_periodo', 'Ano referência período', $tabela[0]['nr_ano_referencia']);
			echo form_default_hidden('cd_juridico_inden_deman', 'Código da tabela', intval($row['cd_juridico_inden_deman']));
			echo form_default_row("", "Indicador:", '<span class="label label-inverse">'.$tabela[0]['ds_indicador'].'</span>'); 
			echo form_default_row("", "Período aberto:", '<span class="label label-important">'.$tabela[0]['ds_periodo'].'</span>'); 
			echo form_default_row("","","");
			echo form_default_hidden('dt_referencia', $label_0.": (*)", $row); 
			echo form_default_mes_ano('mes_referencia', 'ano_referencia', $label_0.' :*', $row['dt_referencia']);
			
			echo form_default_numeric("vl_indenizacao", $label_1.' :', number_format($row['vl_indenizacao'],2,',','.'), "class='indicador_text'"); 
			echo form_default_integer("nr_liquidada", $label_2.' :', intval($row['nr_liquidada']), "class='indicador_text'"); 
			echo form_default_integer("nr_demandante", $label_3.' :', intval($row['nr_demandante']), "class='indicador_text'"); 
			
			echo form_default_numeric("nr_meta", $label_6.' :', number_format($row['nr_meta'],2,',','.'), 'class="indicador_text"');
			echo form_default_textarea("observacao", $label_8.' :', $row['observacao'],'style="height: 80px;"');
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();
			echo button_save();

			if(intval($row['cd_juridico_inden_deman']) > 0)
			{
				echo button_save('Excluir', 'excluir();', 'botao_vermelho');
			}

		echo form_command_bar_detail_end();
	echo form_close();
	echo br();
echo aba_end();
$this->load->view('footer_interna');
?>