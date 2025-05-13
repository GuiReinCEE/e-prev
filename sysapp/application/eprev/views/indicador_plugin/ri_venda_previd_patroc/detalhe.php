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
	<?php echo form_default_js_submit(array(

		array("mes_referencia", "int")
		,array("ano_referencia", "int") 
		,array("cd_indicador_tabela", "int")

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
		location.href='<?php echo site_url("indicador_plugin/ri_venda_previd_patroc"); ?>';
	}
    function manutencao()
    {
        location.href='<?php echo site_url("indicador/manutencao/"); ?>';
    }
	
	function getValores()
	{
		if(($("#mes_referencia").val() != "") && ($("#ano_referencia").val() != ""))
		{
			$("#msg_importar").show();	
			
			$("#command_bar").hide();
			$.post('<?php echo site_url("/ecrm/prevenda/relatorioListar"); ?>', 
			{
				nr_ano     : $("#ano_referencia").val(),
				nr_mes     : $("#mes_referencia").val(),
				cd_empresa : "",
				tp_empresa : "P",
				fl_json    : "S"
			},
			function(data)
			{
				$("#nr_valor_1").val(data[0].quantos_locais);
				$("#nr_valor_2").val(data[0].quantos_contatos);
				$("#nr_valor_3").val(data[0].quantos_contatos_enviados);
				$("#msg_importar").hide();	
				$("#command_bar").show();
			},
			'json');
		}
		else
		{
			alert("Informe o Mês e Ano");
		}
	}	
</script>
<?php
$abas[] = array( 'aba_lista', 'Lista', false, 'manutencao();' );
$abas[] = array('aba_lista', 'Lançamento', false, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');
echo aba_start( $abas );

echo form_open( 'indicador_plugin/ri_venda_previd_patroc/salvar' );
echo form_hidden( 'cd_ri_venda_previd_patroc', intval($row['cd_ri_venda_previd_patroc']) );

// Registros da tabela principal ...
echo form_start_box( "default_box", $tabela[0]['ds_indicador'] );

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

echo form_default_float("nr_valor_1", $label_1, app_decimal_para_php($row['nr_valor_1']), "class='indicador_text'"); 
echo form_default_float("nr_valor_2", $label_2, app_decimal_para_php($row['nr_valor_2']), "class='indicador_text'");
echo form_default_float("nr_valor_3", $label_3, app_decimal_para_php($row['nr_valor_3']), "class='indicador_text'");
echo form_default_float("nr_meta", $label_5, app_decimal_para_php($row['nr_meta']), "class='indicador_text'");
echo form_default_textarea("observacao", $label_7, $row['observacao'],'style="height: 80px;"');
echo form_default_row("", "",'<span style="font-size: 130%; font-weight: bold; color:red; display:none;" id="msg_importar">Aguarde, importando os dados.</span>');

echo form_end_box("default_box");

// Barra de comandos ...
echo form_command_bar_detail_start();
echo button_save();
echo button_save('Importar Valores', 'getValores();', 'botao_verde');

if( intval($row['cd_ri_venda_previd_patroc'])>0  )
{
	echo button_delete("indicador_plugin/ri_venda_previd_patroc/excluir",$row["cd_ri_venda_previd_patroc"]);
}

echo form_command_bar_detail_end();
?>
<script>
	$('#nr_valor_1').focus();
</script>
<?php
echo aba_end();
echo form_close();

$this->load->view('footer_interna');
?>