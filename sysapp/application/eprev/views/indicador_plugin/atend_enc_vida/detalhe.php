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

		array("mes_referencia", "int")
		,array("ano_referencia", "int") 
		,array("cd_indicador_tabela", "int")
		,array("nr_valor_1", "int")
		,array("nr_valor_2", "int")
		,array("nr_valor_3", "int")

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
		location.href='<?php echo site_url("indicador_plugin/atend_enc_vida"); ?>';
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

echo form_open( 'indicador_plugin/atend_enc_vida/salvar' );
echo form_hidden( 'cd_atend_enc_vida', intval($row['cd_atend_enc_vida']) );

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

#echo form_default_mes_ano( 'mes_referencia', 'ano_referencia', $label_0.' *', $row['dt_referencia'] ); 
echo form_default_hidden('mes_referencia', 'M�s', 1);
echo form_default_integer('ano_referencia', $label_0.': (*)', $row['ano_referencia'], "class='indicador_text'"); 
echo form_default_hidden('dt_referencia', 'M�s', $row);

echo form_default_integer("nr_valor_1", $label_1.': (*)', ($row['nr_valor_1']), "class='indicador_text'"); 
echo form_default_integer("nr_valor_2", $label_2.': (*)', ($row['nr_valor_2']), "class='indicador_text'"); 
echo form_default_integer("nr_valor_3", $label_3.': (*)', ($row['nr_valor_3']), "class='indicador_text'");
echo form_default_textarea("observacao", $label_5.':', $row['observacao']);

if( $row['fl_media']=='S' )
{
	//echo form_default_row("", "M�dia", "<input id='fl_media' name='fl_media' type='checkbox' checked value='S' />");
}
else
{
	//echo form_default_row("", "M�dia", "<input id='fl_media' name='fl_media' type='checkbox' value='S' />");
}

echo form_end_box("default_box");

// Barra de comandos ...
echo form_command_bar_detail_start();
echo button_save();

if( intval($row['cd_atend_enc_vida'])>0  )
{
	echo button_delete("indicador_plugin/atend_enc_vida/excluir",$row["cd_atend_enc_vida"]);
}

echo form_command_bar_detail_end();
?>
<script>
	$('#ano_referencia').focus();
</script>
<?php
echo aba_end();
echo form_close();

$this->load->view('footer_interna');
?>