<?php 
if($tabela)
{
	$ds_tabela_periodo = "<big>".$tabela[0]['ds_indicador']."</big>";
}
else
{
	$ds_tabela_periodo = "";
}

set_title($tabela[0]['ds_indicador']);
$this->load->view('header'); 
?>
<script>
	<?php echo form_default_js_submit(array(

		array("periodo_ini", "int")
		,array("periodo_fim", "int") 
		,array("cd_indicador_tabela", "int")

	),'_salvar(form)');	?>

	function _salvar(form)
	{
		if(confirm('Salvar?'))
		{
			form.submit();
		}
	}

	function ir_lista()
	{
		location.href='<?php echo site_url("indicador_plugin/administrativo_sat_colaborador"); ?>';
	}

    function manutencao()
    {
        location.href='<?php echo site_url("indicador/manutencao/"); ?>';
    }
</script>
<?php
$abas[] = array( 'aba_lista', 'Lista', false, 'manutencao();' );
$abas[] = array('aba_lista', 'Lan�amento', false, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');
echo aba_start( $abas );

echo form_open( 'indicador_plugin/administrativo_sat_colaborador/salvar' );
echo form_hidden( 'cd_administrativo_sat_colaborador', intval($row['cd_administrativo_sat_colaborador']) );

// Registros da tabela principal ...
echo form_start_box( "default_box", $tabela[0]['ds_indicador'] );

if( sizeof($tabela)==1 )
{
	echo form_default_hidden( 'cd_indicador_tabela', 'C�digo da tabela', $tabela[0]['cd_indicador_tabela'] ); 
	echo form_default_row( "", "Indicador e per�odo aberto", $tabela[0]['ds_indicador'] . ' - ' . $tabela[0]['ds_periodo'].br(2) );
}
elseif( sizeof($tabela)>1 )
{
	echo form_default_hidden( 'cd_indicador_tabela', 'C�digo da tabela', $tabela[0]['cd_indicador_tabela'] ); 
	echo form_default_row( "", "Indicador e per�odo aberto", $tabela[0]['ds_indicador'] . ' - ' . $tabela[0]['ds_periodo'] );
	echo form_default_row( "", "", "<span style='font-size:12;'>Existe mais de um per�odo aberto, no entanto s� ser� poss�vel incluir valores para o novo per�odo depois de fechar o mais antigo.</span>".br(2) );
}
else
{
	// nenhum per�odo aberto para esse indicador
	echo form_default_row(  "", "Indicador e per�odo aberto", "Nenhum per�odo aberto para criar a tabela do indicador." );
}

echo form_default_integer('periodo_ini', $label_0.' *',$row);
echo form_default_integer('periodo_fim', '',$row);

echo form_default_float("nr_valor_1", $label_1, app_decimal_para_php($row['nr_valor_1']), "class='indicador_text'"); 
echo form_default_float("nr_valor_2", $label_2, app_decimal_para_php($row['nr_valor_2']), "class='indicador_text'"); 
echo form_default_float("nr_meta", $label_4, app_decimal_para_php($row['nr_meta']), "class='indicador_text'"); 
echo form_default_textarea("observacao", $label_6, $row['observacao']);

echo form_end_box("default_box");

// Barra de comandos ...
echo form_command_bar_detail_start();
echo button_save();

if( intval($row['cd_administrativo_sat_colaborador'])>0  )
{
	echo button_delete("indicador_plugin/administrativo_sat_colaborador/excluir",$row["cd_administrativo_sat_colaborador"]);
}

echo form_command_bar_detail_end();
?>
<script>
	$('#periodo_ini').focus();
</script>
<?php
echo aba_end();
echo form_close();

$this->load->view('footer_interna');
?>