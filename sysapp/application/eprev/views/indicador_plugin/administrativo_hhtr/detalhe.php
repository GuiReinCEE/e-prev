<?php 
$tabela = indicador_tabela_aberta(intval( $CD_INDICADOR ));
if($tabela)
{
	$ds_tabela_periodo = "<big>".$tabela[0]['ds_indicador'] . " - " . $tabela[0]['ds_periodo']."</big>";
}
else
{
	$ds_tabela_periodo = "";
}

set_title('Administrativo - Hora/Homem Treinamento');
$this->load->view('header'); 
?>
<script>
	<?php echo form_default_js_submit(array(
		"mes_referencia",
		"ano_referencia", 
		"cd_indicador_tabela",
	),'_salvar(form)');	?>

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
		location.href='<?php echo site_url("indicador_plugin/administrativo_hhtr"); ?>';
	}

    function manutencao()
    {
        location.href='<?php echo site_url("indicador/manutencao/"); ?>';
    }
</script>
<?php
$abas[] = array( 'aba_lista', 'Lista', false, 'manutencao();' );
$abas[] = array('aba_lista', 'Lançamento', false, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');
echo aba_start( $abas );

echo form_open( 'indicador_plugin/administrativo_hhtr/salvar' );
echo form_hidden( 'cd_administrativo_hhtr', intval($row['cd_administrativo_hhtr']) );

// Registros da tabela principal ...
echo form_start_box( "default_box", "Administrativo - Hora/Homem Treinamento" );

if( sizeof($tabela)==1 )
{
	echo form_default_hidden( 'cd_indicador_tabela', 'Código da tabela', $tabela[0]['cd_indicador_tabela'] ); 
	echo form_default_row( "", "Indicador e período aberto", $tabela[0]['ds_indicador'] . ' - ' . $tabela[0]['ds_periodo'].br(2) );
}
elseif( sizeof($tabela)>1 )
{
	echo form_default_hidden( 'cd_indicador_tabela', 'Código da tabela', $tabela[0]['cd_indicador_tabela'] ); 
	echo form_default_row( "", "Indicador e período aberto", $tabela[0]['ds_indicador'] . ' - ' . $tabela[0]['ds_periodo'] );
	echo form_default_row( "", "", "<span style='font-size:12;'>Existe mais de um período aberto, no entanto só será possível incluir valores para o novo período depois de fechar o mais antigo.</span>".br(2) );
}
else
{
	// nenhum período aberto para esse indicador
	echo form_default_row(  "", "Indicador e período aberto", "Nenhum período aberto para criar a tabela do indicador." );
}

echo form_default_mes_ano( 'mes_referencia', 'ano_referencia', $label_0.' *', $row['dt_referencia'] );
echo form_default_hidden('dt_referencia', 'Mês', $row);

echo form_default_numeric("nr_total_hora", $label_1, number_format($row['nr_total_hora'],2,",","."), "class='indicador_text'", array("centsLimit" => 2));
echo form_default_float("nr_efetivo", $label_2, app_decimal_para_php($row['nr_efetivo']), "class='indicador_text'");
echo form_default_numeric("nr_meta", "Meta ", number_format($row['nr_meta'],2,",","."), "class='indicador_text'", array("centsLimit" => 2));
echo form_default_numeric("nr_referencial", "Referencial ", number_format($row['nr_referencial'],2,",","."), "class='indicador_text'", array("centsLimit" => 2));

echo form_default_textarea("observacao", $label_7, $row['observacao']);

if( $row['fl_media']=='S' )
{
	//echo form_default_row("", "Média", "<input id='fl_media' name='fl_media' type='checkbox' checked value='S' />");
}
else
{
	//echo form_default_row("", "Média", "<input id='fl_media' name='fl_media' type='checkbox' value='S' />");
}

echo form_end_box("default_box");

// Barra de comandos ...
echo form_command_bar_detail_start();
echo button_save();

if( intval($row['cd_administrativo_hhtr'])>0  )
{
	echo button_delete("indicador_plugin/administrativo_hhtr/excluir",$row["cd_administrativo_hhtr"]);
}

echo form_command_bar_detail_button("Voltar para lista", "if( confirm('Voltar?') ){ location.href='".site_url('indicador_plugin/administrativo_hhtr')."'; }");
echo form_command_bar_detail_end();
?>
<script>
	$('#nr_total_hora').focus();
</script>
<?php
echo aba_end();
echo form_close();

$this->load->view('footer_interna');
?>