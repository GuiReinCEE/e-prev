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
	<?php echo form_default_js_submit(array("mes_referencia","nr_valor_1", "ano_referencia","nr_meta","nr_valor_2","cd_indicador_tabela"),'_salvar(form)');?>

	$('#nr_valor_1').focus();

	function _salvar(form)
	{
		$('#dt_referencia').val(  '01/'+$('#mes_referencia').val()+'/'+$('#ano_referencia').val() );

		if(confirm('Salvar?'))
		{
			form.submit();
		}
	}

	function ir_lista()
	{
		location.href = '<?= site_url("indicador_plugin/secretaria_sumulas_cd") ?>';
	}
    function manutencao()
    {
         location.href = '<?= site_url("indicador/manutencao") ?>';
    }

    function excluir()
	{
		location.href = '<?= site_url("indicador_plugin/secretaria_sumulas_cd/excluir/".$row["cd_secretaria_sumulas_cd"]) ?>';
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
$abas[] = array('aba_cadastro', 'Cadastro', true, 'location.reload();');

echo aba_start( $abas );
	echo form_open( 'indicador_plugin/secretaria_sumulas_cd/salvar' );
		echo form_start_box("default_box", 'Cadastro');
			echo form_default_hidden('cd_indicador_tabela', 'Código indicador tabela', $tabela[0]['cd_indicador_tabela']);
			echo form_default_hidden('cd_secretaria_sumulas_cd', 'Código da tabela', intval($row['cd_secretaria_sumulas_cd']));
			echo form_default_row("", "Indicador :", '<span class="label label-inverse">'.$tabela[0]['ds_indicador'].'</span>'); 
			echo form_default_row("", "Período aberto :", '<span class="label label-important">'.$tabela[0]['ds_periodo'].'</span>'); 
			echo form_default_row("","","");
			echo form_default_hidden('dt_referencia', $label_0.": (*)", $row); 
			echo form_default_mes_ano('mes_referencia', 'ano_referencia', $label_0." : (*)", $row['dt_referencia']);
			echo form_default_float("nr_valor_1", $label_1." : (*)", app_decimal_para_php($row['nr_valor_1']), "class='indicador_text'"); 
			echo form_default_float("nr_valor_2", $label_2." : (*)", app_decimal_para_php($row['nr_valor_2']), "class='indicador_text'"); 
			echo form_default_float("nr_meta", "Meta : (*)", number_format($row['nr_meta'],2,",","."), "class='indicador_text'");
			echo form_default_textarea("observacao", $label_6." :", $row['observacao']);
	echo form_end_box("default_box");
	echo form_command_bar_detail_start();
		echo button_save();

		if( intval($row['cd_secretaria_sumulas_cd']) > 0)
		{
			echo button_save('Excluir', 'excluir();', 'botao_vermelho');
		}

	echo form_command_bar_detail_end();
	echo form_close();
	echo br();
	echo aba_end();
$this->load->view('footer_interna');
?>