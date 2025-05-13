<?php
set_title('Controle - Tarefas');
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

	$.post( '<?php echo site_url('/atividade/controle_tarefa/listar'); ?>',
	{
		prioridade               : $('#prioridade').val(),
		cd_solicitante           : $('#cd_solicitante').val(),
		cd_atendente             : $('#cd_atendente').val(),
		dt_encaminhamento_inicio : $('#dt_encaminhamento_inicio').val(),
		dt_encaminhamento_fim    : $('#dt_encaminhamento_fim').val(),
		dt_concluido_inicio      : $('#dt_concluido_inicio').val(),
		dt_concluido_fim         : $('#dt_concluido_fim').val(),
		cd_atividade             : $('#cd_atividade').val(),
		cd_tarefa                : $('#cd_tarefa').val(),
		status_aman              : ( document.getElementById('status_aman').checked )?$('#status_aman').val():'',
		status_eman              : ( document.getElementById('status_eman').checked )?$('#status_eman').val():'',
		status_susp              : ( document.getElementById('status_susp').checked )?$('#status_susp').val():'',
		status_libe              : ( document.getElementById('status_libe').checked )?$('#status_libe').val():'',
		status_conc              : ( document.getElementById('status_conc').checked )?$('#status_conc').val():''
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
		'Number',
		'Number',		
		'CaseInsensitiveString',
		'Number',
		'CaseInsensitiveString',
		'CaseInsensitiveString',	
		'DateBR',
		'DateBR',		
		'DateBR',		
		'DateBR',		
		'DateBR',		
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
	ob_resul.sort(3, true);
}

$(function(){
	filtrar();
});

</script>
<?php
	$abas[0] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
	
	$dados[] = array('value' => 'S', 'text' => 'Sim');
	$dados[] = array('value' => 'N', 'text' => 'Não');
	
	$status_filtro = br();
	$status_filtro .= form_checkbox(array('name'=>'status_aman', 'id'=>'status_aman'), 'AMAN', TRUE) . " <label for='status_aman'>Aguardando manutenção</label><br />";
	$status_filtro .= form_checkbox(array('name'=>'status_eman', 'id'=>'status_eman'), 'EMAN', TRUE) . " <label for='status_eman'> Em manutenção</label><br />";
	$status_filtro .= form_checkbox(array('name'=>'status_susp', 'id'=>'status_susp'), 'SUSP', TRUE) . " <label for='status_susp'> Em manutenção (Pausa)</label><br />";
	$status_filtro .= form_checkbox(array('name'=>'status_libe', 'id'=>'status_libe'), 'LIBE', TRUE) . " <label for='status_libe'> Liberadas</label><br />";
	$status_filtro .= form_checkbox(array('name'=>'status_conc', 'id'=>'status_conc'), 'CONC') . " <label for='status_conc'> Concluídas</label>";
	$status_filtro .= br(2);
	
	echo aba_start( $abas );
	echo form_list_command_bar(array());
	echo form_start_box_filter('filter_bar', 'Filtros', false);
		echo form_default_row( 'status_filtro', 'Status', $status_filtro );
		echo filter_dropdown('prioridade', 'Prioridade', $dados );
		echo filter_dropdown('cd_solicitante', 'Solicitante', $solicitante_dd );
		echo filter_dropdown('cd_atendente', 'Atendente', $atendente_dd );
		echo filter_date_interval("dt_encaminhamento_inicio", "dt_encaminhamento_fim", "Data de encaminhamento");
		echo filter_date_interval("dt_concluido_inicio", "dt_concluido_fim", "Data de conclusão");
		echo filter_integer('cd_atividade', 'Atividade');
		echo filter_integer('cd_tarefa', 'Tarefa');
	echo form_end_box_filter();
echo '<div id="result_div">'.br(2).'<span style="color:green;"><b>Realize um filtro para exibir a lista</b></span></div>'.br();
echo aba_end(); 
$this->load->view('footer');
