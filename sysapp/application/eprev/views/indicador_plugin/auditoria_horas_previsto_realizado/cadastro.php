<?php 
set_title($tabela[0]['ds_indicador']);
$this->load->view('header'); 
?>
<script>
	<?php 
	echo form_default_js_submit(array("mes_referencia", "ano_referencia", "cd_indicador_tabela", "nr_horas_previstas","nr_horas_realiazadas", "nr_meta", 'ds_evento'),'_salvar(form)');	?>

	function _salvar(form)
	{
		$('#dt_referencia').val('01/01/'+$('#nr_ano_referencia').val());

		if(confirm('Salvar?'))
		{
			form.submit();
		}
	}

	function ir_lista()
	{
		location.href = '<?= site_url("indicador_plugin/auditoria_horas_previsto_realizado") ?>';
	}
	
    function manutencao()
    {
        location.href = '<?= site_url("indicador/manutencao") ?>';
    }
	
	function excluir()
	{
		location.href = '<?= site_url("indicador_plugin/auditoria_horas_previsto_realizado/excluir/".$row["cd_auditoria_horas_previsto_realizado"]) ?>';
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
	echo form_open('indicador_plugin/auditoria_horas_previsto_realizado/salvar');
		echo form_start_box("default_box", 'Cadastro');
			echo form_default_hidden('cd_indicador_tabela', 'C�digo indicador tabela', $tabela[0]['cd_indicador_tabela']);

			echo form_default_hidden('cd_auditoria_horas_previsto_realizado', 'C�digo da tabela', intval($row['cd_auditoria_horas_previsto_realizado']));

			echo form_default_row("", "Indicador:", '<span class="label label-inverse">'.$tabela[0]['ds_indicador'].'</span>');

			echo form_default_row("","","");
			echo form_default_hidden('dt_referencia', $label_0.": (*)", $row); 

			echo form_default_text('ds_evento', $label_0.': (*)', $row['ds_evento'], "class='indicador_text'");

			echo form_default_integer("nr_horas_previstas", $label_1.': (*)', number_format($row['nr_horas_previstas'],0,",","."), "class='indicador_text'"); 

			echo form_default_integer("nr_horas_realizadas", $label_2.': (*)', number_format($row['nr_horas_realizadas'],0,",","."), "class='indicador_text'");

			echo form_default_numeric("nr_percentual_acima_meta", $label_4.': (*)', number_format($row['nr_percentual_acima_meta'],2,",","."), "class='indicador_text'");   

			echo form_default_textarea("observacao", $label_6.":", $row['observacao']);
		echo form_end_box("default_box");

		echo form_command_bar_detail_start();
			echo button_save();

			if(intval($row['cd_auditoria_horas_previsto_realizado']) > 0)
			{
				echo button_save('Excluir', 'excluir();', 'botao_vermelho');
			}

		echo form_command_bar_detail_end();
	echo form_close();
	echo br();
echo aba_end();
$this->load->view('footer_interna');
?>