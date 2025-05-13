<?php
set_title('Controle IGP');
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

	$.post( '<?php echo site_url('indicador/controle_igp/listar'); ?>',
	{
		ano          : $('#ano').val(),
		fl_encerrado : $('#fl_encerrado').val()
	},
	function(data)
	{
		$('#result_div').html(data);
		configure_result_table();
	});
}

function configure_result_table()
{
	var ob_resul = new SortableTable(document.getElementById("table-1"),
	[
		'CaseInsensitiveString',
		'CaseInsensitiveString',
		'DateTimeBR',
		null
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

function encerrar(cd_igp, dt_referencia)
{
	var confirmacao = 'ATENÇÃO\n\n'+
		    'Após o encerramento não será mais possível atualizar os indicadores para o '+dt_referencia+'\n\n'+
	        'Deseja encerrar?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';
	
	if(confirm(confirmacao))
	{
	   location.href='<?php echo site_url("indicador/controle_igp/encerrar"); ?>/'+cd_igp;
	}
}

$(function(){
	filtrar();
});
</script>

<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$arr[] = array('value' => 'S', 'text' => 'Sim'); 
$arr[] = array('value' => 'N', 'text' => 'Não');

echo aba_start( $abas );
	echo form_list_command_bar(array());
	echo form_start_box_filter();
        echo filter_integer('ano', 'Ano:', date('Y'));
		echo filter_dropdown('fl_encerrado', 'Encerrado:', $arr);
    echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br();
echo aba_end(); 

$this->load->view('footer');
?>