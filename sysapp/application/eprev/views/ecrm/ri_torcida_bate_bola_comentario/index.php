<?php
set_title('Torcida - Bate Bola Comentários');
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
		url="<?php echo site_url('ecrm/ri_torcida_bate_bola_comentario/liberar'); ?>";
		$.post( url, {cd:cd_md5}, function(data){ if(data=='true'){ load(); } else { alert(data); } } );
	}
}

function bloquear( cd_md5 )
{
	if( confirm('Bloquear?') )
	{
		url="<?php echo site_url('ecrm/ri_torcida_bate_bola_comentario/bloquear'); ?>";
		$.post( url, {cd:cd_md5}, function(data){ if(data=='true'){ load(); } else { alert(data); } } );
	}
}

function load()
{
	document.getElementById("result_div").innerHTML = "<?php echo loader_html(); ?>";

	$.post( '<?php echo base_url() . index_page(); ?>/ecrm/ri_torcida_bate_bola_comentario/listar',{  cd_bate_bola: $('#cd_bate_bola').val()
  },function(data){ $("#result_div").html(data);configure_result_table(); } );
}

function configure_result_table()
{
	var ob_resul = new SortableTable(document.getElementById("table-1"),
	[
		'Number','CaseInsensitiveString','CaseInsensitiveString','CaseInsensitiveString','CaseInsensitiveString','DateTimeBR','DateTimeBR',null
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

$(document).ready( rodar_ao_iniciar );

function rodar_ao_iniciar()
{
	filtrar();
}

</script>

<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
echo aba_start( $abas );

echo form_list_command_bar();
echo form_start_box_filter('filter_bar', 'Filtros');
//echo filter_text('cd_bate_bola', 'Bate Bola');
echo filter_dropdown('cd_bate_bola', 'Bate Bola',$bate_bola_dd);
echo form_end_box_filter();
?>

<div id="result_div"><br><br><span style='color:green;'><b>Realize um filtro para exibir a lista</b></span></div>
<br />

<?php
echo aba_end(''); 
$this->load->view('footer');
?>