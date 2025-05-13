<?php
set_title('Desenquadramento - Lista');
$this->load->view('header');
?>
<script>
function filtrar()
{
	$("#result_div").html("<?php echo loader_html(); ?>");
		
    $.post('<?php echo site_url('gestao/desenquadramento_cci/listar');?>',
    $("#filter_bar_form").serialize(),
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
		'CaseInsensitiveString',
		'DateBR',
		'CaseInsensitiveString',
		'CaseInsensitiveString',
		'CaseInsensitiveString',
		'CaseInsensitiveString',
		'DateTimeBR', 
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

function relatorioPDF()
{
	$("#filter_bar_form").attr('action', '<?php echo site_url('gestao/desenquadramento_cci/relatorioPDF');?>');
    $("#filter_bar_form").attr('target', '_blank');
    $("#filter_bar_form").attr('method', 'post');
    $("#filter_bar_form").submit();
}

function novo()
{
    location.href='<?php echo site_url("gestao/desenquadramento_cci/cadastro/"); ?>';
}

$(function(){
	filtrar();
})

</script>
<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$arr_yes_or_no[] = Array('value' => 'S', 'text' => 'Sim');
$arr_yes_or_no[] = Array('value' => 'N', 'text' => 'Não');

$arr_status[] = Array('value' => 'P', 'text' => 'Desenquadrado');
$arr_status[] = Array('value' => 'D', 'text' => 'Desenquadramento Passivo');
$arr_status[] = Array('value' => 'R', 'text' => 'Regularizado');

$config['button'][]=array('Relatório', 'relatorioPDF()');
$config['button'][]=array('Novo Desenquadramento', 'novo()');

echo aba_start( $abas );
    echo (form_list_command_bar($config));
    echo form_start_box_filter();
		echo filter_date_interval('dt_ini', 'dt_fim', 'Dt Cadastro :');
        echo filter_date_interval('dt_relatorio_ini', 'dt_relatorio_fim', 'Dt Relatório :');
		echo filter_dropdown('fl_status', 'Status :', $arr_status); 
		echo form_default_row('','',''); 
		echo filter_dropdown('fl_encaminhado', 'Encaminhado :', $arr_yes_or_no);   
		echo filter_dropdown('fl_envio', 'Confirmado:', $arr_yes_or_no);   
		echo form_default_integer_ano('nr_ano', 'nr_numero', 'Ano/Número :');
		echo filter_date_interval('dt_encaminhado_ini', 'dt_encaminhado_fim', 'Dt Confirmação:');
		echo filter_dropdown('cd_desenquadramento_cci_fundo', 'Fundo/Carteira :', $arr_fundo);     
    echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br();
echo aba_end();
$this->load->view('footer'); 
?>