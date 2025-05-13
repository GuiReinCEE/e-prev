<?php
set_title('Inscrição Part. S/ Email');
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

	$.post( '<?php echo base_url() . index_page(); ?>/ecrm/inscricao_partic_sem_email/listar'
		,{
			cd_empresa: $('#cd_empresa').val(),
            cd_plano   : $("#cd_plano").val(),
            fl_plano: $('#fl_plano').val(),
            fl_dt_cancela_inscricao: $('#fl_dt_cancela_inscricao').val(),
            fl_dt_desligamento_eletro: $('#fl_dt_desligamento_eletro').val(),
            dt_inclusao_inicio: $('#dt_inclusao_inicio').val(),
            dt_inclusao_fim: $('#dt_inclusao_fim').val(),
            dt_ingresso_inicio: $('#dt_ingresso_inicio').val(),
            dt_ingresso_fim: $('#dt_ingresso_fim').val()
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
        'CaseInsensitiveString', 'RE','CaseInsensitiveString', 'DateBR', 'DateBR', 'DateBR','DateBR','CaseInsensitiveString'
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
</script>


<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$tipo[] = array('text'=>'Sim', 'value'=>'S');
$tipo[] = array('text'=>'Não', 'value'=>'N');

echo aba_start( $abas );
echo form_start_box_filter('filter_bar', 'Filtros');
    echo filter_plano_ajax('cd_plano', $cd_plano_empresa, $cd_plano, 'Empresa:', 'Plano:','I');
    echo filter_dropdown('fl_plano', 'Participante:', $tipo);
    echo filter_dropdown('fl_dt_cancela_inscricao', 'Inscrição Cancelada:', $tipo);
    echo filter_dropdown('fl_dt_desligamento_eletro', 'Desligamento:', $tipo);
    echo filter_date_interval('dt_inclusao_inicio', 'dt_inclusao_fim', 'Data Participante:');
    echo filter_date_interval('dt_ingresso_inicio', 'dt_ingresso_fim', 'Data Ingresso:');

echo form_end_box_filter();
?>
<div id="result_div"></div>
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