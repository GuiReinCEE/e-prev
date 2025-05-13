<?php
set_title('Envio de FAX');
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

	$.post( '<?php echo base_url() . index_page(); ?>/ecrm/envio_fax/listar'
		,{
			cd_empresa            : $('#cd_empresa').val(),
			cd_registro_empregado : $('#cd_registro_empregado').val(),
			seq_dependecia        : $('#seq_dependecia').val(),
			nr_telefone           : $('#nr_telefone').val(),
			cd_usuario           : $('#cd_usuario').val(),
			dt_envio_inicio       : $('#dt_envio_inicio').val(),
			dt_envio_fim          : $('#dt_envio_fim').val()

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
		'CaseInsensitiveString',
		'DateTimeBR', 
		'CaseInsensitiveString',
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
	ob_resul.sort(0, true);
}

function novo()
{
	location.href='<?php echo site_url("ecrm/envio_fax/detalhe/0"); ?>';
}
</script>

<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
	$abas[] = array('aba_envia_fax', 'Enviar FAX', FALSE, 'novo();');
	echo aba_start( $abas );

	echo form_list_command_bar();

	echo form_start_box_filter('filter_bar', 'Filtros');

	$conf = array('cd_empresa','cd_registro_empregado','seq_dependencia', 'nome');
	echo form_default_participante( $conf, "Participante:", Array(), TRUE, FALSE );
	echo form_default_date_interval('dt_envio_inicio', 'dt_envio_fim', 'Período de envio');
	echo form_default_text('nr_telefone', 'Nr FAX:');
	echo form_default_dropdown('cd_usuario','Usuário',$combo_usuario);

	echo form_end_box_filter();
?>

<div id="result_div"><br><span style='color:green;'><b>Realize um filtro para exibir a lista</b></span></div>
<br />

<?php
echo aba_end(''); 
?>

<script type="text/javascript">
	// filtrar();
	jQuery(function($){
	   $("#nr_telefone").mask("(99) 99999999");
	});	
</script>

<?php
$this->load->view('footer');
?>