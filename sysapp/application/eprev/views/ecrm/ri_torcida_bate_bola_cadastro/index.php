<?php
set_title('Torcida - Bate Bola Cadastro');
$this->load->view('header');
?>
<script>
function filtrar()
{
	load();
}

function liberar( cd_md5 )
{
	if( confirm('Liberar?') )
	{
		url="<?php echo site_url('ecrm/ri_torcida_bate_bola_cadastro/liberar'); ?>";
		$.post( url, {cd:cd_md5}, function(data){ if(data=='true'){ load(); } else { alert(data); } } );
	}
}

function bloquear( cd_md5 )
{
	if( confirm('Bloquear?') )
	{
		url="<?php echo site_url('ecrm/ri_torcida_bate_bola_cadastro/bloquear'); ?>";
		$.post( url, {cd:cd_md5}, function(data){ if(data=='true'){ load(); } else { alert(data); } } );
	}
}

function load()
{
	document.getElementById("result_div").innerHTML = "<?php echo loader_html(); ?>";

	$.post( '<?php echo base_url() . index_page(); ?>/ecrm/ri_torcida_bate_bola_cadastro/listar',{    },function(data){ $("#result_div").html(data);configure_result_table(); } );
}

function configure_result_table()
{
	var ob_resul = new SortableTable(document.getElementById("table-1"),
	[
		'Number','CaseInsensitiveString','DateTimeBR',null,null
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
	location.href='<?php echo site_url("ecrm/ri_torcida_bate_bola_cadastro/detalhe/0"); ?>';
}

$(document).ready( rodar_ao_iniciar );

function rodar_ao_iniciar()
{
	 $("#filter_bar").hide(); $("#exibir_filtro_button").hide(); 
	
	filtrar();
}

</script>

<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
echo aba_start( $abas );

$config['button'][]=array('Novo', 'novo()');
echo form_list_command_bar($config);
echo form_start_box_filter('filter_bar', 'Filtros');

echo form_end_box_filter();
?>

<div id="result_div"><br><br><span style='color:green;'><b>Realize um filtro para exibir a lista</b></span></div>
<br />

<?php
echo aba_end(''); 
$this->load->view('footer');
?>