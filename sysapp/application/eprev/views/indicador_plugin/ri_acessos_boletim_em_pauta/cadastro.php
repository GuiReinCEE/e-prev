<?php 
if($tabela)
{
	$ds_tabela_periodo = "<big>".$tabela[0]['ds_indicador'] . " - " . $tabela[0]['ds_periodo']."</big>";
}
else
{
	$ds_tabela_periodo = "";
}

set_title($tabela[0]['ds_indicador']);
$this->load->view('header'); 
?>
<script>
	<?php 
	echo form_default_js_submit(array(
		array("mes_referencia", "int"),
		array("ano_referencia", "int"),
		array("cd_indicador_tabela", "int")
	),'_salvar(form)');	?>

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
		location.href='<?php echo site_url("indicador_plugin/ri_acessos_boletim_em_pauta"); ?>';
	}
	
    function manutencao()
    {
        location.href='<?php echo site_url("indicador/manutencao/"); ?>';
    }
	
	function excluir()
	{
		location.href = '<?php echo site_url("indicador_plugin/ri_acessos_boletim_em_pauta/excluir/".$row["cd_ri_acessos_boletim_em_pauta"]); ?>';
	}
	
	$(function() {
		$('#nr_valor_1').focus();
    });			
</script>
<?php
$abas[] = array('aba_lista', 'Lista', false, 'manutencao();');
$abas[] = array('aba_lista', 'Lançamento', false, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');

echo aba_start($abas);
	echo form_open('indicador_plugin/ri_acessos_boletim_em_pauta/salvar');
		echo form_start_box("default_box", $tabela[0]['ds_indicador']);
			echo form_hidden('cd_ri_acessos_boletim_em_pauta', '',intval($row['cd_ri_acessos_boletim_em_pauta']));
			echo form_default_hidden('dt_referencia', 'Mês', $row);
			
			if(sizeof($tabela) == 1)
			{
				echo form_default_hidden('cd_indicador_tabela', 'Código da tabela', $tabela[0]['cd_indicador_tabela']); 
				echo form_default_row("", "Indicador e período aberto", $tabela[0]['ds_indicador'].' - '.$tabela[0]['ds_periodo'].br(2));
			}
			elseif(sizeof($tabela) > 1)
			{
				echo form_default_hidden('cd_indicador_tabela', 'Código da tabela', $tabela[0]['cd_indicador_tabela']); 
				echo form_default_row("", "Indicador e período aberto", $tabela[0]['ds_indicador'].' - '.$tabela[0]['ds_periodo']);
				echo form_default_row("", "", "<span style='font-size:12;'>Existe mais de um período aberto, no entanto só será possível incluir valores para o novo período depois de fechar o mais antigo.</span>".br(2));
			}
			else
			{
				echo form_default_row("", "Indicador e período aberto", "Nenhum período aberto para criar a tabela do indicador.");
			}

			echo form_default_mes_ano('mes_referencia', 'ano_referencia', $label_0.' :*', $row['dt_referencia']);
			echo form_default_integer("nr_valor_1", $label_1.' :', ($row['nr_valor_1']), "class='indicador_text'"); 
			echo form_default_integer("nr_valor_2", $label_2.' :', ($row['nr_valor_2']), "class='indicador_text'");
			echo form_default_numeric("nr_meta", $label_4.' :', number_format($row['nr_meta'],2,",","."), "class='indicador_text'");


		echo form_end_box("default_box");
		echo form_command_bar_detail_start();
			echo button_save();

			if(intval($row['cd_ri_acessos_boletim_em_pauta']) > 0)
			{
				echo button_save('Excluir', 'excluir();', 'botao_vermelho');
			}

		echo form_command_bar_detail_end();
	echo form_close();
	echo br(5);
echo aba_end();
$this->load->view('footer_interna');
?>