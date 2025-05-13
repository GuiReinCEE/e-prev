<?php
set_title('Pendências das Auditorias ISO');
$this->load->view('header');
?>
<script type="text/javascript">
function filtrar()
{
	load();
}

function load()
{
	document.getElementById("result_div").innerHTML = "<?php echo loader_html(); ?>";

	$.post( '<?php echo base_url() . index_page(); ?>/gestao/iso/listar'
		,{
            cd_processo: $('#cd_processo').val(),
            cd_pendencia_auditoria_iso_tipo: $('#cd_pendencia_auditoria_iso_tipo').val(),
            dt_inicial: $('#dt_inicial').val(),
            dt_final: $('#dt_final').val(),
            fl_impacto: $('#fl_impacto').val(),
            cd_gerencia: $('#cd_gerencia').val(),
            fl_situacao: $('#fl_situacao').val(),
            cd_responsavel: $('#cd_responsavel').val()

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
        'CaseInsensitiveString',
        'CaseInsensitiveString',
        'DateBR',
        'Number',
        'CaseInsensitiveString',
        'CaseInsensitiveString',
        'CaseInsensitiveString',
        'CaseInsensitiveString',
        'CaseInsensitiveString',
        'CaseInsensitiveString',
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
	location.href='<?php echo site_url("gestao/iso/cadastro/0"); ?>';
}

</script>
<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$arr_impacto[] = Array('value' => 'S', 'text' => 'Sim');
$arr_impacto[] = Array('value' => 'N', 'text' => 'Não');

$arr_situacao[] = Array('value' => 'A', 'text' => 'Aberto');
$arr_situacao[] = Array('value' => 'E', 'text' => 'Encerrado');

echo aba_start( $abas );

    $config['button'][]=array('Novo', 'novo()');

    echo form_list_command_bar($config);


    echo form_start_box_filter();
        #echo filter_dropdown('cd_processo', 'Processo:', $processo);
		echo filter_processo('cd_processo', 'Processo:');
        echo filter_date_interval('dt_inicial', 'dt_final', 'Data :');
        echo filter_usuario_ajax('cd_responsavel','','','Responsável:', 'Gerência do Responsável:');
        echo filter_dropdown('cd_gerencia', 'Gerência da Pendência:', $gerencia);
        echo filter_dropdown('cd_pendencia_auditoria_iso_tipo', 'Auditoria:', $auditoria);
        echo filter_dropdown('fl_impacto', 'Impacto:', $arr_impacto);
        echo filter_dropdown('fl_situacao', 'Situação:', $arr_situacao,array('A'));
    echo form_end_box_filter();

echo aba_end();
?>

<div id="result_div"></div>
<br />

<script type="text/javascript">
	filtrar();
</script>

<?php $this->load->view('footer'); ?>.