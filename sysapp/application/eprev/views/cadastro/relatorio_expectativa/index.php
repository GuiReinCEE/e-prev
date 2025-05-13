<?php
set_title('Relatório Expectativas');
$this->load->view('header');
?>
<script type="text/javascript">
function pdf()
{
    filter_bar_form.method = "post";
    filter_bar_form.action = '<?php echo base_url() . index_page(); ?>/cadastro/relatorio_expectativa/pdf';
    filter_bar_form.target = "_blank";
    filter_bar_form.submit();
}

function filtrar()
{
	document.getElementById("result_div").innerHTML = "<?php echo loader_html(); ?>";

	$.post( '<?php echo site_url("cadastro/relatorio_expectativa/listar"); ?>/',
    {
		ano         : $('#ano').val(),
        cd_gerencia : $('#cd_gerencia').val(),
        cd_usuario  : $('#cd_usuario').val()
    },
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
		'Number'
		, 'CaseInsensitiveString'
        , 'CaseInsensitiveString'
        , 'CaseInsensitiveString'
        , 'CaseInsensitiveString'
        
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

</script>
<?php
$abas[] = array( 'aba_lista', 'Lista', true, 'location.reload();' );

$config['button'][]=array('Gerar PDF', 'pdf()');

echo aba_start( $abas );
    echo form_list_command_bar($config);
    echo form_start_box_filter();
        echo filter_integer('ano', 'Ano: ', date('Y'));
        echo filter_usuario_ajax(array('cd_gerencia','cd_usuario'),'','', 'Colaborador:', 'Gerência:');
    echo form_end_box_filter();
echo aba_end();
?>
<div id="result_div"></div>
<br />

<script type="text/javascript">
    filtrar();
</script>
<?php
$this->load->view('footer');
?>