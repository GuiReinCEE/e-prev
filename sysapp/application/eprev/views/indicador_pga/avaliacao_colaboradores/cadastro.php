<?php
	set_title($tabela[0]['ds_indicador']);
	$this->load->view('header'); 
?>
<script>
	<?php 
		echo form_default_js_submit(array("cd_indicador_tabela", "ano_referencia", "nr_valor_1", "nr_valor_2", "nr_meta", "nr_meta_2"),'_salvar(form);');	
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
		location.href='<?php echo site_url("indicador_pga/avaliacao_colaboradores"); ?>';
	}

    function manutencao()
    {
        location.href='<?php echo site_url("indicador/manutencao"); ?>';
    }	
	
	function excluir()
	{
		if(confirm('Deseja Excluir?'))
		{
			location.href='<?php echo site_url("indicador_pga/avaliacao_colaboradores/excluir/".$row["cd_avaliacao_colaboradores"]); ?>';
		}
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

$abas[] = array( 'aba_lista', 'Lista', false, 'manutencao();' );
$abas[] = array('aba_lista', 'Lan�amento', false, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');

echo aba_start( $abas );

echo form_open('indicador_pga/avaliacao_colaboradores/salvar');
	echo form_start_box("default_box", "Cadastro");
		echo form_default_hidden('cd_indicador_tabela', 'C�digo indicador tabela', $tabela[0]['cd_indicador_tabela']);
		echo form_default_hidden('nr_ano_periodo', 'Ano refer�ncia per�odo', $tabela[0]['nr_ano_referencia']);
		echo form_default_hidden('cd_avaliacao_colaboradores', 'C�digo da tabela', intval($row['cd_avaliacao_colaboradores']));
		echo form_default_row("", "Indicador:", '<span class="label label-inverse">'.$tabela[0]['ds_indicador'].'</span>'); 	
		echo form_default_row("","","");
		echo form_default_integer('ano_referencia', $label_0.": (*)", $row['ano_referencia'], "class='indicador_text'");
		echo form_default_hidden('dt_referencia', $label_0.": (*)", $row); 
		echo form_default_numeric("nr_valor_1", $label_1.": (*)", number_format($row['nr_valor_1'],2,',','.'), "class='indicador_text'");
		echo form_default_integer("nr_valor_2", $label_2.": (*)", number_format($row['nr_valor_2'],0), "class='indicador_text'");
		echo form_default_numeric("nr_meta", $label_4.": (*)", number_format($row['nr_meta'],2,',','.'), "class='indicador_text'");
		//echo form_default_numeric("nr_meta_2", $label_7.": (*)", number_format($row['nr_meta_2'],2,',','.'), "class='indicador_text'");
		echo form_default_textarea("observacao", $label_6.":", $row['observacao']);
	echo form_end_box("default_box");

	echo form_command_bar_detail_start();

		echo button_save();

		if(intval($row['cd_avaliacao_colaboradores']) > 0)
		{
			echo button_save('Excluir', 'excluir('.$row["cd_avaliacao_colaboradores"].');', 'botao_vermelho');
		}
	echo form_command_bar_detail_end();
echo form_close();

echo aba_end();
$this->load->view('footer_interna');
?>
