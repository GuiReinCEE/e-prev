<?php
set_title('Solicitação de Cálculo de Taxa e Jóia');
$this->load->view('header');
?>
<script>
function filtrar()
{
	$('#result_div').html("<?php echo loader_html(); ?>");

	$.post('<?php echo site_url('/atividade/calculo_taxa_joia/listar')?>',
	{
		dt_solicitacao_inicio       : $('#dt_solicitacao_inicio').val(),
		dt_solicitacao_fim          : $('#dt_solicitacao_fim').val(),
		dt_conclusao_inicio         : $('#dt_conclusao_inicio').val(),
		dt_conclusao_fim            : $('#dt_conclusao_fim').val(),
		cd_solicitante              : $('#solicitante_dd').val(),
		cd_atendente                : $('#atendente_dd').val(),
	    descricao                   : $('#descricao').val(),
		cd_empresa                  : $('#cd_empresa').val(),
		cd_registro_empregado       : $('#cd_registro_empregado').val(),
		seq_dependencia             : $('#seq_dependencia').val(),
		numero                      : $('#numero').val()
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
		'RE',
		'CaseInsensitiveString',
		'Number',
		'DateBR',
		'CaseInsensitiveString',
		'CaseInsensitiveString',
		'CaseInsensitiveString',
		'CaseInsensitiveString',
		'DateBR'
	]);
	ob_resul.onsort = function()
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

function ir()
{
	if($('#numero').val() != "")
	{
		$.post('<?php echo site_url('atividade/minhas/buscarAtividade');?>',
		{
			cd_atividade : $('#numero').val()
		},
		function(data)
		{
			if(data.url != "")
			{
				location.href = data.url;
			}
		},
		'json');	
	}
	else
	{
		alert("Informe o número da atividade");
		$('#numero').focus();
	}	
}

$(function(){
	$('#dt_solicitacao_inicio_dt_solicitacao_fim_shortcut').val('currentYear');
	$('#dt_solicitacao_inicio_dt_solicitacao_fim_shortcut').change();
	
	filtrar();
});

</script>

<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

echo aba_start( $abas );
	echo form_list_command_bar();
	echo form_start_box_filter('filter_bar', 'Filtros');
		echo form_default_integer('numero', 'Número:');
		echo filter_date_interval('dt_solicitacao_inicio', 'dt_solicitacao_fim', 'Dt. Solicitação:');
		echo filter_date_interval('dt_conclusao_inicio', 'dt_conclusao_fim', 'Dt. Conclusão:');
		echo filter_dropdown('solicitante_dd', 'Solicitante:', $arr_solicitante);
		echo filter_dropdown('atendente_dd', 'Atendente:', $arr_atendente);
		echo form_default_text('descricao', 'Descrição:', '', 'style="width:300px;"');
		echo form_default_participante( array('cd_empresa','cd_registro_empregado','seq_dependencia', 'nome_participante'), 'Participante (EMP/RE/SEQ)',  $participante, TRUE, FALSE );
	echo form_end_box_filter();
	echo '
		<div id="result_div">
			<br><br>
			<span style="color:green;">
				<b>Realize um filtro para exibir a lista</b>
			</span>
		</div>';
	echo br();

echo aba_end(); 

$this->load->view('footer');
?>