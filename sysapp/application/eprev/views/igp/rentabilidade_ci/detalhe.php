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
		            "nr_rentabilidade",
		            "nr_benchmark",
		            "nr_peso_igp"
	              ),
				  '_salvar(form);'
				);	
	?>

	function _salvar(form)
	{
		
		if($('#nr_ano_periodo').val() != $('#ano_referencia').val())
		{
			alert("ERRO\n\nANO ("+$('#ano_referencia').val()+") do lançamento diferente do ANO ("+$('#nr_ano_periodo').val()+") do período\n\n");
			$('#ano_referencia').focus();
		}
		else
		{
			$('#dt_referencia').val('01/'+$('#mes_referencia').val()+'/'+$('#ano_referencia').val());

			if(confirm('Salvar?'))
			{
				form.submit();
			}
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

$abas[] = array('aba_lista', 'Lista', false, 'manutencao();' );
$abas[] = array('aba_lanca', 'Lançamento', false, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');
echo aba_start( $abas );

echo form_open('igp/rentabilidade_ci/salvar');
	echo form_start_box("default_box", "Cadastro");
		echo form_default_hidden('cd_indicador_tabela', 'Código indicador tabela', $tabela[0]['cd_indicador_tabela']);
		echo form_default_hidden('nr_ano_periodo', 'Ano referência período', $tabela[0]['nr_ano_referencia']);
		echo form_default_hidden('cd_rentabilidade_ci', 'Código da tabela', intval($row['cd_rentabilidade_ci']));

		echo form_default_row("", "Indicador:", '<span class="label label-inverse">'.$tabela[0]['ds_indicador'].'</span>'); 
		echo form_default_row("", "Período aberto:", '<span class="label label-important">'.$tabela[0]['ds_periodo'].'</span>'); 		

		echo form_default_row("","","");

		echo form_default_mes_ano('mes_referencia', 'ano_referencia', $label_0.": (*)", $row['dt_referencia']);
		echo form_default_hidden('dt_referencia', $label_0.": (*)", $row); 

		echo form_default_numeric("nr_rentabilidade", $label_1.": (*)", number_format($row['nr_rentabilidade'],4,',','.'), "class='indicador_text'", array("centsLimit" => 4));
		echo form_default_numeric("nr_benchmark", $label_2.": (*)", number_format($row['nr_benchmark'],4,',','.'), "class='indicador_text'", array("centsLimit" => 4));
		echo form_default_numeric("nr_peso_igp", $label_18.": (*)", number_format($row['nr_peso_igp'],2,',','.'), "class='indicador_text'");


	echo form_end_box("default_box");

	echo form_command_bar_detail_start();
	
		echo button_save();

		if(intval($row['cd_rentabilidade_ci']) > 0)
		{
			echo button_delete("igp/rentabilidade_ci/excluir",$row["cd_rentabilidade_ci"]);
		}
	echo form_command_bar_detail_end();
echo form_close();
?>
<script>
	$(document).ready(function() {
		$("#mes_referencia").focus();
	});	
</script>
<?php
echo aba_end();
$this->load->view('footer_interna');
?>
















<?php
/*
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
*/
?>