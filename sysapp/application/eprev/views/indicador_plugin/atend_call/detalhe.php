<?php
	set_title($tabela[0]['ds_indicador']);
	$this->load->view('header'); 
?>
<script>
	<?php 
		echo form_default_js_submit(
			array(
					"cd_indicador_tabela",
					"mes_referencia",
					"ano_referencia",
					"nr_valor_1",
                    "nr_valor_2",
                    'nr_valor_time_2',
					"nr_meta"
				  ),
				  '_salvar(form);'
				);	
	?>
	
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
		location.href='<?php echo site_url("indicador_plugin/atend_call"); ?>';
	}

    function manutencao()
    {
        location.href='<?php echo site_url("indicador/manutencao/"); ?>';
    }	
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
?>
<? if(intval($row['qt_ano']) == 0): ?>

	<div style="width:100%; text-align:center;">
		<span style="font-size: 12pt; color:red; font-weight:bold;">
			Informar no campo de 'observa��es' se pretende manter ou fazer ajustes na meta do indicador, e justificar essa decis�o.
		</span>
	</div>

<? endif; ?>
<?
echo form_open('indicador_plugin/atend_call/salvar');
	echo form_start_box("default_box", "Cadastro");
		echo form_default_hidden('cd_indicador_tabela', 'C�digo indicador tabela', $tabela[0]['cd_indicador_tabela']);
		echo form_default_hidden('cd_atend_call', 'C�digo da tabela', intval($row['cd_atend_call']));

		echo form_default_text("", "Indicador:", $tabela[0]['ds_indicador'],'style="border: 0px; width: 500px; font-weight:bold;"'); 
		echo form_default_text("", "Per�odo aberto:", $tabela[0]['ds_periodo'],'style="border: 0px; width: 500px; color:red; font-weight:bold;"'); 

		echo form_default_row("","","");		

		echo form_default_mes_ano('mes_referencia', 'ano_referencia', $label_0.": (*)", $row['dt_referencia']);
		echo form_default_hidden('dt_referencia', $label_0.": (*)", $row); 
		//echo form_default_integer("nr_valor_1",   $label_1.": (*)", intval($row['nr_valor_1']), "class='indicador_text'");
		echo form_default_integer("nr_valor_2",   $label_2.": (*)", intval($row['nr_valor_2']), "class='indicador_text'");
		echo form_default_time("nr_valor_time_2",   $label_3.": (*)", $row['nr_valor_time_2'], "class='indicador_text'");
		//echo form_default_numeric("nr_percentual_f", $label_3.": (*)", number_format($row['nr_percentual_f'],2,',','.'), "class='indicador_text'");
		echo form_default_numeric("nr_meta",      $label_4.": (*)", number_format($row['nr_meta'],2,',','.'), "class='indicador_text'");
		echo form_default_textarea("observacao",  $label_6, $row['observacao']);
	echo form_end_box("default_box");

	echo form_command_bar_detail_start();

		echo button_save();

		if( intval($row['cd_atend_call'])>0  )
		{
			echo button_delete("indicador_plugin/atend_call/excluir",$row["cd_atend_call"]);
		}
	echo form_command_bar_detail_end();
echo form_close();
?>
