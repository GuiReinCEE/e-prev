<?php
set_title('Documentos do Participante');
$this->load->view('header');
?>
<script>
function filtrar()
{
	if(($('#cd_empresa').val() == "") || ($('#cd_registro_empregado').val() == "") || ($('#seq_dependencia').val() == ""))
	{
		alert("Informe o Participante");
	}
	else
	{	
		load();
	}
}

function load()
{
	document.getElementById("result_div").innerHTML = "<?php echo loader_html(); ?>";

	$.post( '<?php echo base_url() . index_page(); ?>/ecrm/documento_participante/listar'
		,{
			cd_empresa            : $('#cd_empresa').val(),
			cd_registro_empregado : $('#cd_registro_empregado').val(),
			seq_dependencia        : $('#seq_dependencia').val(),
			dt_documento_ini       : $('#dt_documento_ini').val(),
			dt_documento_fim       : $('#dt_documento_fim').val()
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
		'RE',
		'CaseInsensitiveString',
		'DateBR',
		'CaseInsensitiveString'
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

	echo form_start_box_filter('filter_bar', 'Filtros');

	$conf = array('cd_empresa','cd_registro_empregado','seq_dependencia', 'nome');
	echo form_default_participante( $conf, "Participante:", Array(), TRUE, FALSE );
	echo form_default_date_interval('dt_documento_ini', 'dt_documento_fim', 'Período do documento:');

	echo form_end_box_filter();
?>

<div id="result_div"><br><span style='color:green;'><b>Realize um filtro para exibir a lista</b></span></div>
<br />

<?php
echo aba_end(''); 
?>

<script type="text/javascript">
	// filtrar();
</script>

<?php
$this->load->view('footer');
?>