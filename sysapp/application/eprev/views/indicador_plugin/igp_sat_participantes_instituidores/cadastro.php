<?php
	set_title($tabela[0]['ds_indicador']);
	$this->load->view('header'); 
?>
<script>
	<?php 
		echo form_default_js_submit(array("cd_indicador_tabela", "mes_referencia", "ano_referencia", "nr_valor_1", "nr_resultado", "nr_meta"),'_salvar(form);');	
	?>
	
	function _salvar(form)
	{
		$('#dt_referencia').val('01/01/'+$('#ano_referencia').val());

		if(confirm('Salvar?'))
		{
			form.submit();
		}
	}	

    function ir_lista()
	{
		location.href='<?php echo site_url("indicador_plugin/igp_sat_participantes_instituidores"); ?>';
	}

    function manutencao()
    {
        location.href='<?php echo site_url("indicador_plugin/igp_sat_participantes_instituidores"); ?>';
    }	
	
	function excluir()
	{
		if(confirm('Deseja Excluir?'))
		{
			location.href='<?php echo site_url("indicador_plugin/igp_sat_participantes_instituidores/excluir/".$row["cd_igp_sat_participantes_instituidores"]); ?>';
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

echo form_open('indicador_plugin/igp_sat_participantes_instituidores/salvar');
	echo form_start_box("default_box", "Cadastro");
		echo form_default_hidden('cd_indicador_tabela', 'Código indicador tabela', $tabela[0]['cd_indicador_tabela']);
		echo form_default_hidden('nr_ano_periodo', 'Ano referência período', $tabela[0]['nr_ano_referencia']);
		echo form_default_hidden('cd_igp_sat_participantes_instituidores', 'Código da tabela', intval($row['cd_igp_sat_participantes_instituidores']));
		echo form_default_row("", "Indicador:", '<span class="label label-inverse">'.$tabela[0]['ds_indicador'].'</span>'); 		
		echo form_default_row("","","");
		echo form_default_hidden('dt_referencia', $label_0.": (*)", $row); 
		echo form_default_integer('ano_referencia', " Ano: (*)", $row['ano_referencia'], "class='indicador_text'");
		echo form_default_integer("nr_valor_1", $label_1.": (*)", number_format($row['nr_valor_1'],0,',','.'), "class='indicador_text'");
		echo form_default_numeric("nr_resultado", $label_3.": (*)", number_format($row['nr_resultado'],2,',','.'), "class='indicador_text'");
		echo form_default_numeric("nr_meta", $label_4.": (*)", number_format($row['nr_meta'],2,',','.'), "class='indicador_text'");
		echo form_default_textarea("observacao", $label_5.":", $row['observacao']);
	echo form_end_box("default_box");

	echo form_command_bar_detail_start();

		echo button_save();

		if(intval($row['cd_igp_sat_participantes_instituidores']) > 0)
		{
			echo button_save('Excluir', 'excluir('.$row["cd_igp_sat_participantes_instituidores"].');', 'botao_vermelho');
		}
	echo form_command_bar_detail_end();
echo form_close();

echo aba_end();
$this->load->view('footer_interna');
?>
