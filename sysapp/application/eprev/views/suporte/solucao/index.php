<?php
set_title("Soluções do Suporte");
$this->load->view('header');
?>
<script>
function filtrar()
{
	$("#result_div").html("<?php echo loader_html(); ?>");

    $.post( '<?php echo site_url('/suporte/solucao/listar');?>',$('#filter_bar_form').serialize(),
	function(data)
	{
		$("#result_div").html(data);
		configure_result_table();
	});
}

function configure_result_table()
{
    var ob_resul = new SortableTable(document.getElementById("table-1"),
    [
		'Number',
		'DateBR',
		'DateBR',
		'CaseInsensitiveString',
		'CaseInsensitiveString',
		'CaseInsensitiveString',
		'CaseInsensitiveString'
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

$(function(){
	filtrar();
});
</script>
<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
	
echo aba_start( $abas );
	echo form_list_command_bar();
	echo form_start_box_filter();
		echo filter_text("numero", "Número da Atividade :");
		echo filter_date_interval("dt_cadastro_ini", "dt_cadastro_fim", "Dt. Cadastro :");
		echo filter_date_interval("dt_conclusao_ini", "dt_conclusao_fim", "Dt. Conclusão :");
		echo filter_text("descricao", "Descrição :", '', 'style="width:300px;"');
		echo filter_text("solucao", "Solução :", '', 'style="width:300px;"');
		echo filter_dropdown('cd_categoria', 'Categoria :', $arr_categoria);
		echo filter_text("assunto", "Assunto :", '', 'style="width:300px;"');
    echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br();
echo aba_end();

$this->load->view('footer');
?>