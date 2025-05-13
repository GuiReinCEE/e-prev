<?php
	set_title('Indicadores Apresentação');
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

		$.post( '<?php echo base_url().index_page(); ?>/indicador/apresentacao/listar',
			$("#filter_bar_form").serialize(),
			function(data)
			{
				$("#result_div").html(data);
				configure_result_table();
			}
		);		
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			'Number',
			'CaseInsensitiveString',
			'Number',
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			'DateBR',
			'CaseInsensitiveString',
			'DateTimeBR',
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			null,
			null,
			'Number',
			'DateBR'
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

	$(document).ready(function() {
		filtrar();
	});	
</script>
<?php
	$abas[] = array('aba_lista', 'Indicadores', TRUE, 'location.reload();');
	
	$fl_filtro[] = array('text' => 'Sim', 'value' => 'S');
	$fl_filtro[] = array('text' => 'Não', 'value' => 'N');
	
	echo aba_start( $abas );
		echo form_list_command_bar();	
		echo form_start_box_filter('filter_bar', 'Filtros');
			echo filter_dropdown('cd_indicador_grupo', 'Grupo: ', $ar_grupo, array($cd_grupo));
			echo filter_dropdown('cd_tipo', 'Tipo: ', $tipo, array($cd_tipo));
			echo filter_dropdown('cd_indicador_periodo', 'Período: ', $ar_periodo, array($cd_periodo));	
			echo filter_processo('cd_processo', 'Processo:');
			echo filter_dropdown('cd_indicador_controle', 'Controle: ', $ar_controle);
			echo filter_dropdown('fl_igp', 'PE: ', $fl_filtro);
			echo filter_dropdown('fl_poder', 'PODER: ', $fl_filtro);
			echo filter_dropdown('fl_encerrado', 'Encerrado: ', $fl_filtro, array('N'));
		echo form_end_box_filter();
		echo '<div id="result_div">Realize um filtro para exibir a lista</div>';
		echo br(5);
	echo aba_end(); 
$this->load->view('footer');
?>