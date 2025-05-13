<?php
set_title('Cronograma - Lista');
$this->load->view('header');
?>
<script>
function filtrar()
{
	load();
}
function load()
{
	$('#result_div').html("<?php echo loader_html(); ?>");
	$.post( '<?php echo base_url() . index_page(); ?>/atividade/atividade_cronograma/cronogramaListar',
		{
			dt_inclusao_ini : $('#dt_inclusao_ini').val(),
			dt_inclusao_fim : $('#dt_inclusao_fim').val(),	
			cd_analista     : $('#cd_analista').val(),	
			token_gerencia  : $('#token_gerencia').val()		
		},
		function(data)
		{
			$('#result_div').html(data);
			configure_result_table();
		}
	);
}

function configure_result_table()
{
	var ob_resul = new SortableTable(document.getElementById("table-1"),[
				"Number",
				"CaseInsensitiveString",
				null,
				"CaseInsensitiveString",
				"DateBR",
				"DateBR",
				"DateTimeBR",
				"DateBR",
				"Number",
				"Number",
				"Number",
				"Number"
				
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


function novoCronograma()
{
	location.href='<?php echo base_url().index_page(); ?>/atividade/atividade_cronograma/cadastro';
}
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
	echo aba_start( $abas );

	$config['button'][]=array('Novo Cronograma', 'novoCronograma();');
	echo form_list_command_bar($config);	
	echo form_start_box_filter('filter_bar', 'Filtros', TRUE);
		echo filter_date_interval('dt_inclusao_ini', 'dt_inclusao_fim', 'Dt Cadastro:');
		echo filter_dropdown('cd_analista', 'Responsável: ', $ar_analista);
		echo filter_hidden("token_gerencia", "Token Gerência:", $token_gerencia);
	echo form_end_box_filter();
?>
<div id="result_div"><br><br><span style='color:green;'><b>Realize um filtro para exibir a lista</b></span></div>
<br />
<?php echo aba_end(''); ?>
<script>
	filtrar();
</script>
<?php
$this->load->view('footer');
?>