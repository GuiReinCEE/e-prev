<?php 
set_title( 'Resumo de atividades' );
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

	$.post( '<?php echo site_url("atividade/resumo_atividades_gri/listar"); ?>/'
		,{
            ano: $('#ano').val()
           
		}
		,
	function(data)
		{
            document.getElementById("result_div").innerHTML = data;
            configure_result_table_atividades();
            configure_result_table_atendimento();
            configure_result_table_programas();
		}
	);
}

function configure_result_table_atividades()
{

	var ob_resul = new SortableTable(document.getElementById("tb_atividades"),
	[
		'Number'
		, 'Number'
        , 'Number'
        , 'Number'
        , 'Number'
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
	ob_resul.sort(0, false);
}

function configure_result_table_result_atividades()
{

	var ob_resul = new SortableTable(document.getElementById("tb_result_atendimento"),
	[
		'CaseInsensitiveString'
		, 'CaseInsensitiveString'
        , 'CaseInsensitiveString'
        , 'CaseInsensitiveString'
        , 'DateTimeBR'
        , 'DateTimeBR'
        , 'DateTimeBR'
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

function configure_result_table_atendimento()
{
	var ob_resul = new SortableTable(document.getElementById("tb_atendimento"),
	[
		'CaseInsensitiveString'
		, 'CaseInsensitiveString'
        , 'Number'
        , 'NumberFloat'
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
	ob_resul.sort(0, false);
}

function configure_result_table_programas()
{

	var ob_resul = new SortableTable(document.getElementById("tb_programas"),
	[
		'CaseInsensitiveString'
		, 'Number'
        , 'Number'
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
	ob_resul.sort(0, false);
}



function carregar_atividades(mes, ano)
{
    $.post( '<?php echo site_url("atividade/resumo_atividades_gri/carregaAtividades"); ?>/'
		,{
            mes: mes
            ,ano: ano

		}
		,
	function(data)
		{
            document.getElementById("result_atividade").innerHTML = data;
            configure_result_table_result_atividades();
		}
	);
}

</script>

<?php
$abas[] = array( 'aba_lista', 'Relatório', true, 'location.reload();' );

echo aba_start( $abas );

    echo form_list_command_bar();
    echo form_start_box_filter();
    echo filter_integer('ano', 'Ano :', date('Y'));
    echo form_end_box_filter();

echo aba_end();
?>

<div id="result_div"></div>
<br />

	<script type="text/javascript">
		filtrar();
	</script>

<?php $this->load->view('footer'); ?>