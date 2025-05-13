<?php
set_title('Exame Médico Ingresso - Lista');
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		document.getElementById("result_div").innerHTML = "<?php echo loader_html(); ?>";
		$.post( '<?php echo base_url() . index_page(); ?>/ecrm/exame_medico_ingresso/exameMedicoListar',
		{
			dt_inclusao_ini : $('#dt_inclusao_ini').val(),
			dt_inclusao_fim : $('#dt_inclusao_fim').val(),
			dt_envio_ini    : $('#dt_envio_ini').val(),
			dt_envio_fim    : $('#dt_envio_fim').val(),
			dt_recebido_ini : $('#dt_recebido_ini').val(),
			dt_recebido_fim : $('#dt_recebido_fim').val()
			
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
		var ob_resul = new SortableTable(document.getElementById("table-1"),[
					"Number",
					"RE",
					"CaseInsensitiveString",
					"DateTimeBR",
					"DateTimeBR",
					"CaseInsensitiveString",
					"DateTimeBR",
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
		ob_resul.sort(0, true);
	}


	function novoExameMedicoIngresso()
	{
		location.href='<?php echo base_url().index_page(); ?>/ecrm/exame_medico_ingresso/detalhe';
	}
</script>
<?php
	$abas[0] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
	echo aba_start( $abas );

	$config['button'][]=array('Novo Exame Médico', 'novoExameMedicoIngresso();');
	echo form_list_command_bar($config);	
	echo form_start_box_filter('filter_bar', 'Filtros',FALSE);
		echo filter_date_interval('dt_inclusao_ini', 'dt_inclusao_fim', 'Data de cadastro:', calcular_data('','1 year'), date('d/m/Y'));
		echo filter_date_interval('dt_envio_ini', 'dt_envio_fim', 'Data de envio:');
		echo filter_date_interval('dt_recebido_ini', 'dt_recebido_fim', 'Data de recebido:');
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