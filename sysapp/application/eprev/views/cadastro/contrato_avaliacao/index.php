<?php
set_title('Cadastros, Contratos, Avaliação');
$this->load->view('header');
?>
<script>
function filtrar()
{
	$('#result_div').html("<?php echo loader_html(); ?>");

	$.post('<?php echo site_url('/cadastro/contrato_avaliacao/listar');?>', 
	{ 
		ds_empresa    : $('#ds_empresa').val(),
		ds_servico    : $('#ds_servico').val(),
		dt_inicio_ini : $('#dt_inicio_ini').val(),
		dt_inicio_fim : $('#dt_inicio_fim').val(),
		dt_fim_ini    : $('#dt_fim_ini').val(),
		dt_fim_fim    : $('#dt_fim_fim').val(),
		dt_limite_ini : $('#dt_limite_ini').val(),
		dt_limite_fim : $('#dt_limite_fim').val()
	}, 
	function(data)
	{
		$('#result_div').html(data);
		configure_result_table();
	});
}

function configure_result_table()
{
	var ob_resul = new SortableTable(document.getElementById("table-1"),
	[
		'Number',
		'Number',
		'CaseInsensitiveString', 
		'CaseInsensitiveString', 
		'Number',
		'CaseInsensitiveString', 
		'Number',
		'DateBR', 
		'DateBR', 
		'DateBR', 
		'DateTimeBR' 
		
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
	ob_resul.sort(0, true);
}

function nova()
{
	location.href='<?php echo site_url("cadastro/contrato_avaliacao/avaliacao"); ?>';
}

$(function(){
	filtrar();
});

</script>

<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$config['button'][] = array('Nova avaliação', 'nova()');

echo aba_start( $abas );
	echo form_list_command_bar($config);
	echo form_start_box_filter('filter_bar', 'Filtros:');
		echo form_default_text('ds_empresa', 'Empresa:');
		echo form_default_text('ds_servico', 'Serviço:');
		echo form_default_date_interval('dt_inicio_ini', 'dt_inicio_fim','Dt Início:');
		echo form_default_date_interval('dt_fim_ini', 'dt_fim_fim','Dt Fim:');
		echo form_default_date_interval('dt_limite_ini', 'dt_limite_fim','Dt Limite:');
	echo form_end_box_filter();
	echo '
		<div id="result_div">
			<br/><br/>
			<span style="color:green;">
				<b>Realize um filtro para exibir a lista</b>
			</span>
		</div>';
	echo br();
echo aba_end(); 

$this->load->view('footer');
?>