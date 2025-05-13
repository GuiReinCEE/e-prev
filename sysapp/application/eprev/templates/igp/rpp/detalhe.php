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
		, array("ano_referencia", "int")
		, array("nr_inpc", "float")
		, array("nr_indice_mes", "float")
		, array("nr_indice_ano", "float")
		, array("nr_wacc", "float")
		, array("nr_peso", "float")	), 'salvar_ajax(form)' ); ?>

	function salvar_ajax(form)
	{
		$('#dt_referencia').val(  '01/'+$('#mes_referencia').val()+'/'+$('#ano_referencia').val() );

		if(confirm('Salvar?'))
		{
			url="<?php echo site_url('igp/rpp/salvar'); ?>";
			$.post(url,{ 
				cd_rpp:$('#cd_rpp').val()
				, dt_referencia:$('#dt_referencia').val()
				, nr_inpc:$('#nr_inpc').val()
				, nr_indice_mes:$('#nr_indice_mes').val()
				, nr_indice_ano:$('#nr_indice_ano').val()
				, nr_wacc:$('#nr_wacc').val()
				, nr_peso:$('#nr_peso').val()
			}, function(data) {	alert(data); ir_lista(); });
		}
	}

	function ir_lista()
	{
		location.href='<?php echo site_url("igp/rpp"); ?>';
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

if( intval($row['cd_rpp'])==0)
{
	$sql = "SELECT to_char(dt_referencia + '1 month'::interval,'DD/MM/YYYY') AS mes_referencia, nr_peso, nr_indice_ano FROM igp.rpp WHERE dt_exclusao IS NULL ORDER BY dt_referencia DESC LIMIT 1";
	$query = $this->db->query( $sql );
	$anterior = $query->row_array();

	$row['dt_referencia'] = $anterior['mes_referencia'];
	$row['nr_peso'] = $anterior['nr_peso'];
	$row['nr_indice_ano'] = $anterior['nr_indice_ano'];
}

echo form_open('igp/rpp/salvar');
echo form_hidden( 'cd_rpp', intval($row['cd_rpp']) );

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

// echo form_default_dropdown('mes_referencia', 'M�s', $meses );
echo form_default_mes_ano( 'mes_referencia', 'ano_referencia', $label_0.' *', $row['dt_referencia'] );
echo form_default_hidden('dt_referencia', 'M�s', $row); 
echo form_default_float("nr_inpc", $label_1." *", $row, "class='indicador_text'");
echo form_default_float("nr_indice_mes", $label_2." *", $row, "class='indicador_text'");
echo form_default_float("nr_indice_ano", $label_5."�ndice do Ano *", $row, "class='indicador_text'");
echo form_default_text("nr_wacc", $label_7." *", $row, "class='indicador_text'");
echo form_default_float("nr_peso", $label_9."Peso *", app_decimal_para_php($row['nr_peso']), "class='indicador_text'");
echo form_end_box("default_box");

// Barra de comandos ...
echo form_command_bar_detail_start();
echo button_save();

if( intval($row['cd_rpp'])>0  )
{
	echo button_delete("igp/rpp/excluir",$row["cd_rpp"]);
}

echo form_command_bar_detail_end();
?>
<script>
	$('#nr_inpc').focus();
</script>
<?php
echo aba_end();
echo form_close();

$this->load->view('footer_interna');
?>