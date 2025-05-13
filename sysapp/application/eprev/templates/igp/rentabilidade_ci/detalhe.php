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
		, array("nr_rentabilidade", "float")
		, array("nr_benchmark", "float")
		, array("nr_peso_igp", "float")	
		), 'salvar_ajax(form)' ); ?>

	function salvar_ajax(form)
	{
		$('#dt_referencia').val(  '01/'+$('#mes_referencia').val()+'/'+$('#ano_referencia').val() );

		if(confirm('Salvar?'))
		{
			url="<?php echo site_url('igp/rentabilidade_ci/salvar'); ?>";
			$.post(url,{ 
				cd_rentabilidade_ci:$('#cd_rentabilidade_ci').val()
				, dt_referencia:$('#dt_referencia').val()
				, nr_rentabilidade:$('#nr_rentabilidade').val()
				, nr_benchmark:$('#nr_benchmark').val()
				, nr_peso_igp:$('#nr_peso_igp').val()
			}, function(data) {	/*alert(data);*/ ir_lista(); });
		}
	}

	function ir_lista()
	{
		location.href='<?php echo site_url("igp/rentabilidade_ci"); ?>';
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

if( intval($row['cd_rentabilidade_ci'])==0)
{
	$sql = "SELECT to_char(dt_referencia + '1 month'::interval,'DD/MM/YYYY') AS mes_referencia, nr_peso_igp
	FROM igp.rentabilidade_ci
	WHERE dt_exclusao IS NULL 
	ORDER BY dt_referencia DESC LIMIT 1
	";
	$query = $this->db->query( $sql );
	$anterior = $query->row_array();
	if($anterior)
	{
		$row['dt_referencia'] = $anterior['mes_referencia'];
		$row['nr_peso_igp'] = $anterior['nr_peso_igp'];
	}
}

echo form_open('igp/rentabilidade_ci/salvar');
echo form_hidden( 'cd_rentabilidade_ci', intval($row['cd_rentabilidade_ci']) );

// Registros da tabela principal ...
echo form_start_box( "default_box", $tabela[0]['ds_indicador']);
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

echo form_default_float("nr_rentabilidade", "Rentabilidade *", app_decimal_para_php($row['nr_rentabilidade']), "style='font-size:24px;'"); 
echo form_default_float("nr_benchmark", "Benchmark *", app_decimal_para_php($row['nr_benchmark']), "style='font-size:24px;'"); 
echo form_default_float("nr_peso_igp", $label_18." *", app_decimal_para_php($row['nr_peso_igp']), "style='font-size:24px;'");

echo form_end_box("default_box");

// Barra de comandos ...
echo form_command_bar_detail_start();
echo button_save();

if( intval($row['cd_rentabilidade_ci'])>0  )
{
	echo button_delete("igp/rentabilidade_ci/excluir",$row["cd_rentabilidade_ci"]);
}

echo form_command_bar_detail_end();
?>
<script>
	$('#nr_rentabilidade').focus();
</script>
<?php
echo aba_end();
echo form_close();

$this->load->view('footer_interna');
?>