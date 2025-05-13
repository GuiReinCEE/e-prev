<?php
set_title('Reclamatória');
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		load();
	}

	function load()
	{
		document.getElementById("result_div").innerHTML = "<?php echo loader_html(); ?>";

		$.post( '<?php echo base_url() . index_page(); ?>/ecrm/atendimento_reclamatoria/listar'
			,{
				cd_empresa            : $('#cd_empresa').val(),
				cd_registro_empregado : $('#cd_registro_empregado').val(),
				seq_dependencia       : $('#seq_dependencia').val(),
				dt_ini                : $('#dt_ini').val(),
				dt_fim                : $('#dt_fim').val()
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
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			'Number',
			'DateBR',
			'RE',
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			'Number'
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
		ob_resul.sort(1, true);
	}
	
	
	function encerraReclamatoria(cd)
	{
		if(confirm("Deseja encerrar esta reclamatória?"))
		{
			$.post( '<?php echo base_url() . index_page(); ?>/ecrm/atendimento_reclamatoria/encerra'
				,{
					cd_atendimento_reclamatoria : cd
				}
				,
			function(data)
				{
					filtrar();
				}
			);
		}
	}	

	function novo()
	{
		location.href='<?php echo site_url("ecrm/atendimento_reclamatoria/detalhe/0"); ?>';
	}
</script>

<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
	echo aba_start( $abas );

	$config['button'][]=array('Novo', 'novo();');
	echo form_list_command_bar($config);
	echo form_start_box_filter('filter_bar', 'Filtros');

	$participante['cd_empresa']            = $cd_empresa;
	$participante['cd_registro_empregado'] = $cd_registro_empregado;
	$participante['seq_dependencia']       = $seq_dependencia;
	$conf = array('cd_empresa','cd_registro_empregado','seq_dependencia', 'nome');
	echo form_default_participante( $conf, "Participante:", $participante, TRUE, FALSE );
	echo form_default_date_interval('dt_ini', 'dt_fim', 'Período de registro:');

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