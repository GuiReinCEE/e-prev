<?php
set_title('Atas CCI - Lista');
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

    $.post( '<?php echo site_url('gestao/atas_cci/listar') ?>', $('#filter_bar_form').serialize(),
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
		'Number',
		null,
		'Number',
		'DateBR',
		'CaseInsensitiveString',
		'CaseInsensitiveString',
		'CaseInsensitiveString',
		'DateBR',
		'Number',
		'CaseInsensitiveString',
		'Number',
		'CaseInsensitiveString',
		'CaseInsensitiveString',
		'DateTimeBR', 
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
    ob_resul.sort(3, true);
}

function novo()
{
    location.href='<?php echo site_url("gestao/atas_cci/cadastro/"); ?>';
}

$(function(){

	if($.trim($("#nr_ano").val()) == "")
	{
		$("#nr_ano").val((new Date).getFullYear());
	}

	filtrar();
});

</script>
<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$config['button'][] = array('Novo', 'novo()');

$arr_dropdown[] = array('value' => 'S', 'text' => 'Sim');
$arr_dropdown[] = array('value' => 'N', 'text' => 'Não');

$arr_drop_etapa[] = array('value' => 'S', 'text' => 'Concluída');
$arr_drop_etapa[] = array('value' => 'N', 'text' => 'Não Concluída');

echo aba_start( $abas );
    echo form_list_command_bar(((gerencia_in(array('GC'))) ? $config : array()));
    
    echo form_start_box_filter();
        echo filter_integer('nr_ano', 'Ano:');
		echo filter_text('nr_reuniao', 'Nº Reunião:');
		echo filter_dropdown('fl_ata_cci', 'Ata CCI:', $arr_dropdown);
		echo filter_dropdown('fl_sumula_cci', 'Súmula CCI:', $arr_dropdown);
		echo filter_dropdown('fl_anexo_cci', 'Anexos CCI:', $arr_dropdown);
		echo filter_dropdown('fl_homologado_diretoria', 'Homologado DE:', $arr_dropdown);
		echo filter_dropdown('fl_homologado_conselho_fiscal', 'Homologado CD:', $arr_dropdown);
		echo filter_dropdown('fl_publicado_alchemy', 'Publicado no Liquid:', $arr_dropdown);
		echo filter_dropdown('fl_publicada_eprev', 'Publicado no E-prev:', $arr_dropdown);
		echo filter_dropdown('fl_etapa', 'Etapas:', $arr_drop_etapa);
    echo form_end_box_filter();
   
	echo '<div id="result_div"></div>';
	echo br(5); 

echo aba_end();
$this->load->view('footer'); 
?>