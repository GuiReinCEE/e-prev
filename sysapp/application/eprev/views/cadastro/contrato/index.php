<?php
set_title('Contrato');
$this->load->view('header');
?>
<script>
function filtrar()
{
	$("#result_div").html("<?php echo loader_html(); ?>");

	$.post( '<?php echo site_url('cadastro/contrato/listar');?>',
	$("#filter_bar_form").serialize(),
	function(data)
	{
		$("#result_div").html(data);
		configure_result_table();
	});
}

function configure_result_table()
{
	var ob_resul = new SortableTable(document.getElementById("table-1"),
	[
		"Number",
		"Number",
		"CaseInsensitiveString",
		"CaseInsensitiveString",
		"CaseInsensitiveString",
		"CaseInsensitiveString",
		"CaseInsensitiveString",
		"NumberFloatBR",
		"CaseInsensitiveString",
		"DateBR", 
		"DateBR", 
		"DateBR",
		"CaseInsensitiveString"
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
	ob_resul.sort(0, false);
}

function novo_contrato()
{
	location.href='<?php echo site_url('cadastro/contrato/cadastro'); ?>';
}

$(function(){
	filtrar();
})

</script>

<?php
$abas[0] = array('aba_lista', 'Lista', true, 'location.reload();');

$config['button'][] = array('Novo Contrato', 'novo_contrato()');

$ar_status[] = array('value' => 'A', 'text' => 'Ativo');
$ar_status[] = array('value' => 'A,S', 'text' => 'Ativo/Suspenso');
$ar_status[] = array('value' => 'S', 'text' => 'Suspenso');
$ar_status[] = array('value' => 'C', 'text' => 'Concluído');
$ar_status[] = array('value' => 'R', 'text' => 'Rescindido');

$ar_avaliar[] = array('value' => 'S', 'text' => 'Sim');
$ar_avaliar[] = array('value' => 'N', 'text' => 'Não');

echo aba_start( $abas );
	echo ((gerencia_in(array('GAD'))) ? form_list_command_bar($config) : form_list_command_bar());
	echo form_start_box_filter();
		echo filter_dropdown('cd_gerencia', 'Gerência:', $arr_gerencias);
		echo filter_text('ds_empresa', 'Empresa:', '', 'style="width:400px;"');
		echo filter_text('ds_servico', 'Serviço:', '', 'style="width:400px;"');
		echo filter_dropdown('status_contrato', "Status:", $ar_status);
		echo filter_dropdown('fl_avaliar', "Avaliar:", $ar_avaliar);
		echo filter_date_interval('dt_inicio_ini', 'dt_inicio_fim', 'Dt Início:');
		echo filter_date_interval('dt_encerramento_ini', 'dt_encerramento_fim', 'Dt Encerramento:');
		echo filter_date_interval('dt_reajuste_ini', 'dt_reajuste_fim', 'Dt Reajuste:');
    echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br();
echo aba_end();

$this->load->view('footer_interna');
?>