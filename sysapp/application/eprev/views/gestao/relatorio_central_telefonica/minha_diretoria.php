<?php
set_title('Relatório Telefone - Minha Diretoria');
$this->load->view('header');
?>
<script>
function filtrar()
{
	if($('#tp_relatorio').val() == "")
	{
		alert("Informe o Tipo de Relatório")
	}
	else
	{
		load();
	}
}

function load()
{
	document.getElementById("result_div").innerHTML = "<?php echo loader_html(); ?>";

	$.post( '<?php echo base_url() . index_page(); ?>/gestao/relatorio_central_telefonica/minhaDiretoriaResumo'
		,{
			dt_ini : $('#dt_ini').val(),
			dt_fim : $('#dt_fim').val(),
			tp_relatorio : $('#tp_relatorio').val(),
			nr_top : $('#nr_top').val()
		}
		,
	function(data)
		{
			document.getElementById("result_div").innerHTML = data;
			configure_result_table();
		}
	);
}

function configure_result_table()
{
	if ($('#tp_relatorio').val() == "V")
	{
		var valor = "NumberFloatBR";
	}

	if ($('#tp_relatorio').val() == "D")
	{
		var valor = "TimeBR";
	}

	if ($('#tp_relatorio').val() == "Q")
	{
		var valor = "Number";
	}
	
	var ob_resul = new SortableTable(document.getElementById("table-1"),
	[
		'Number',
		'Number',
		'CaseInsensitiveString',
		valor
	]);
	ob_resul.onsort = function ()
	{
		var rows = ob_resul.tBody.rows;
		var l = rows.length;
		for (var i = 0; i < l; i++)
		{
			removeClassName( rows[i], i % 2 ? "sort-par" : "sort-impar" );
			addClassName( rows[i], i % 2 ? "sort-impar" : "sort-par" );
		}
	};
	ob_resul.sort(3, true);
}


function relMinhaDiretoriaDestino()
{
	location.href='<?php echo site_url("gestao/relatorio_central_telefonica/minha_diretoria_destino"); ?>';
}

</script>

<?php

	$abas[] = array('aba_lista', 'Ramais', TRUE, 'location.reload();');
	$abas[] = array('aba_lista', 'Destinos', FALSE, 'relMinhaDiretoriaDestino();');


	echo aba_start( $abas );

	echo form_list_command_bar();

	echo form_start_box_filter('filter_bar', 'Filtros');
	echo form_default_date_interval('dt_ini', 'dt_fim', 'Período:',date("01/m/Y"),date('d/m/Y'));

	echo form_label("Tipo Relatório: ");
	$ar_valor = array(''=>'::selecione::','V'=>'Valor','D'=>'Duração','Q'=>'Quantidade');
	echo form_dropdown('tp_relatorio', $ar_valor,array('V'), "id='tp_relatorio'");	

	echo "<BR>";
	
	echo form_label("Quantidade de ramais: ");
	$ar_valor = array(''=>'::selecione::','05'=>'05','10'=>'10','15'=>'15','20'=>'20','25'=>'25','30'=>'30','35'=>'35','40'=>'40','45'=>'45','50'=>'50');
	echo form_dropdown('nr_top', $ar_valor,array('10'), "id='nr_top'");
	
	
	echo form_end_box_filter();
?>

<div id="result_div"><br><span style='color:green;'><b>Realize um filtro para exibir a lista</b></span></div>
<br />

<?php
echo aba_end(''); 
?>

<script type="text/javascript">
	filtrar();
</script>

<?php
$this->load->view('footer');
?>