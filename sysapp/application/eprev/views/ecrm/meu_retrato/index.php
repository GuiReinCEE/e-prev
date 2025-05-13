<?php
	set_title('Meu Retrato - Lista');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		load();
	}

	function load()
	{
		if(($('#cd_empresa').val() != "") || ($('#cd_registro_empregado').val() != "") || ($('#seq_dependencia').val() != ""))
		{
			$("#result_div").html("<?php echo loader_html(); ?>");

			$.post( '<?php echo base_url() . index_page(); ?>/ecrm/meu_retrato/listar',
			{
				cd_empresa            : $('#cd_empresa').val(),
				cd_registro_empregado : $('#cd_registro_empregado').val(),
				seq_dependencia       : $('#seq_dependencia').val()				
			}
			,
			function(data)
			{
				$("#result_div").html(data);
				configure_result_table();
			}
			);
		}
		else
		{
			alert("Informe o Participante");
			$("#cd_empresa").focus();
		}
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			'Number',  
			'CaseInsensitiveString',
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
		ob_resul.sort(2, true);
	}
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
	echo aba_start( $abas );
	echo form_list_command_bar();
	echo form_start_box_filter('filter_bar', 'Filtros', true);
		$conf = array('cd_empresa','cd_registro_empregado','seq_dependencia', 'nome_participante');
		echo filter_participante($conf, "Participante:*", array(), FALSE, TRUE, TRUE );	
	echo form_end_box_filter();	
?>
<div id="result_div"><br><br><span style='color:green;'><b>Realize um filtro para exibir a lista</b></span></div>
<br>
<?php
	echo br(8);
	echo aba_end(''); 
?>
<script type="text/javascript">
	filtrar();
</script>
<?php
	$this->load->view('footer');
?>