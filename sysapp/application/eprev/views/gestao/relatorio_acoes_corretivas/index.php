<?php
set_title('Relatório de ações corretivas');
$this->load->view('header');
?>
<script type="text/javascript">
function quadro_resumo()
{
	document.getElementById("quadro_div").innerHTML = "<?php echo loader_html(); ?>";

	$.post( '<?php echo site_url("gestao/relatorio_acoes_corretivas/quadro_resumo"); ?>/'
		,{

		}
		,
	function(data)
		{
            document.getElementById("quadro_div").innerHTML = data;
		}
	);
}

function corretivas_ven()
{
	document.getElementById("corretivas_ven_div").innerHTML = "<?php echo loader_html(); ?>";

	$.post( '<?php echo site_url("gestao/relatorio_acoes_corretivas/corretivas_ven"); ?>/'
		,{

		}
		,
	function(data)
		{
            document.getElementById("corretivas_ven_div").innerHTML = data;
            configure_result_table_tb_corr_ven();
		}
	);
}

function corretivas_fora()
{
	document.getElementById("corretivas_fora_div").innerHTML = "<?php echo loader_html(); ?>";

	$.post( '<?php echo site_url("gestao/relatorio_acoes_corretivas/corretivas_fora"); ?>/'
		,{

		}
		,
	function(data)
		{
            document.getElementById("corretivas_fora_div").innerHTML = data;
            configure_result_table_tb_corr_fora();
		}
	);
}

function corretivas_impl_ven()
{
	document.getElementById("corretivas_imp_ven_div").innerHTML = "<?php echo loader_html(); ?>";

	$.post( '<?php echo site_url("gestao/relatorio_acoes_corretivas/corretivas_implementadas_ven"); ?>/'
		,{

		}
		,
	function(data)
		{
            document.getElementById("corretivas_imp_ven_div").innerHTML = data;
            configure_result_table_tb_corr_imp_ven();
		}
	);
}

function corretivas_impl_fora()
{
	document.getElementById("corretivas_imp_fora_div").innerHTML = "<?php echo loader_html(); ?>";

	$.post( '<?php echo site_url("gestao/relatorio_acoes_corretivas/corretivas_implementadas_fora"); ?>/'
		,{

		}
		,
	function(data)
		{
            document.getElementById("corretivas_imp_fora_div").innerHTML = data;
            configure_result_table_tb_corr_impl_fora();
		}
	);
}

function configure_result_table_tb_corr_ven()
{

	var ob_resul = new SortableTable(document.getElementById("tb_corr_ven"),
	[
		 'Number'
        , 'DateBR'
        , 'DateBR'
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

function configure_result_table_tb_corr_fora()
{

	var ob_resul = new SortableTable(document.getElementById("tb_corr_prazo"),
	[
		 'Number'
        , 'DateBR'
        , 'DateBR'
        , 'DateBR'
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

function configure_result_table_tb_corr_imp_ven()
{

	var ob_resul = new SortableTable(document.getElementById("tb_corr_impl_ven"),
	[
		 'Number'
        , 'DateBR'
        , 'DateBR'
        , 'DateBR'
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

function configure_result_table_tb_corr_impl_fora()
{

	var ob_resul = new SortableTable(document.getElementById("tb_corr_impl_fora"),
	[
		 'Number'
        , 'DateBR'
        , 'DateBR'
        , 'DateBR'
        , 'DateBR'
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
$abas[] = array('aba_lista', 'Relatório', TRUE, 'location.reload();');

echo aba_start( $abas );

?>
<div id="quadro_div">
</div>
<div id="corretivas_ven_div">
</div>
<div id="corretivas_fora_div">
</div>
<div id="corretivas_imp_ven_div">
</div>
<div id="corretivas_imp_fora_div">
</div>
<br />
<script>
quadro_resumo();
corretivas_ven();
corretivas_fora();
corretivas_impl_ven();
corretivas_impl_fora();
</script>
<?php $this->load->view('footer'); ?>
