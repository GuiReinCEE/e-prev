<?php
set_title('Atendimento Óbito - Lista');
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		load();
	}

	function load()
	{
		$("#result_div").html("<?php echo loader_html(); ?>");
		
		$.post( '<?php echo base_url() . index_page(); ?>/ecrm/atendimento_obito/listar',
			{
				cd_empresa            : $('#cd_empresa').val(),
				cd_registro_empregado : $('#cd_registro_empregado').val(),
				seq_dependencia       : $('#seq_dependencia').val(),
				nome                  : $('#nome').val(),
				dt_obito_ini          : $('#dt_obito_ini').val(),
				dt_obito_fim          : $('#dt_obito_fim').val(),
				dt_dig_obito_ini      : $('#dt_dig_obito_ini').val(),
				dt_dig_obito_fim      : $('#dt_dig_obito_fim').val()
			},
			function(data)
			{
				$("#result_div").html(data);
				configure_result_table();
			}
		);
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),[
					"RE",
					"CaseInsensitiveString",
					"DateBR",
					"DateBR",
					"DateTimeBR",
					"DateTimeBR"
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

</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
	echo aba_start($abas);
	
	echo form_list_command_bar();	
	
	echo form_start_box_filter('filter_bar', 'Filtros');
		echo filter_participante(array('cd_empresa','cd_registro_empregado','seq_dependencia', 'nome'), "Participante:", Array(), TRUE, FALSE );
		echo filter_text('nome', 'Nome:','','style="width: 350px;"');
		echo filter_date_interval('dt_obito_ini', 'dt_obito_fim', 'Dt Óbito:');
		echo filter_date_interval('dt_dig_obito_ini', 'dt_dig_obito_fim', 'Dt Digita Óbito:', calcular_data('','1 year'), date('d/m/Y'));
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