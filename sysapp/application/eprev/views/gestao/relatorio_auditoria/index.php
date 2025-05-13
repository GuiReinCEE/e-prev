<?php
set_title('Relatório Auditoria');
$this->load->view('header');
?>
<script type="text/javascript">
function filtrar()
{
	$('#result_div').html("<?php echo loader_html(); ?>");

	$.post('<?php echo site_url('gestao/relatorio_auditoria/listar'); ?>',$('#filter_bar_form').serialize(),
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
        'CaseInsensitiveString',
        'CaseInsensitiveString',
        'CaseInsensitiveString',
        'CaseInsensitiveString',
        'CaseInsensitiveString',
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
	ob_resul.sort(0, true);
}

function novo()
{
	location.href='<?php echo site_url("gestao/relatorio_auditoria/cadastro/0"); ?>';
}

function excluir(cd_relatorio_auditoria)
{
    if(confirm("ATENÇÃO\n\nDeseja excluir?\n\n"))
    {
        location.href='<?php echo site_url("gestao/relatorio_auditoria/excluir_processo/"); ?>' + "/" + cd_relatorio_auditoria;
    }
}

function impacto()
{
    if($('#tipo').val() == 'O')
    {
        $('#fl_impacto_row').show();
    }
    else
    {
        $('#fl_impacto_row').hide();
    }
}

function gera_pdf(cd_relatorio_auditoria)
{
    location.href='<?php echo base_url() . index_page(); ?>/gestao/relatorio_auditoria/gera_pdf/'+cd_relatorio_auditoria;
}

$(function(){
    impacto();

    $('#tipo').change(function(){
        impacto();
    });
	
	filtrar();
});

</script>
<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$config[] = array();

$ar_tipo[] = array('value' => 'N', 'text' => 'Não Conformidade');
$ar_tipo[] = array('value' => 'O', 'text' => 'Observação');

$ar_impacto[] = array('value' => 'S', 'text' => 'Sim');
$ar_impacto[] = array('value' => 'N', 'text' => 'Não');

$auditoria[] = array('value' => 'I', 'text' => 'Interna');
$auditoria[] = array('value' => 'E', 'text' => 'Externa');


if($fl_permissao)
{
	$config['button'][]=array('Novo', 'novo()');
}

echo aba_start( $abas );
    echo form_list_command_bar($config);
    echo form_start_box_filter();
        echo filter_integer('ano', 'Ano :');
        echo filter_dropdown('fl_tipo', 'Auditoria:', $auditoria);
        #echo filter_dropdown('cd_auditor_lider', 'Auditor Líder:', $arr_auditor);
        #echo filter_dropdown('cd_processo', 'Processo :', $arr_processo);
		echo filter_processo('cd_processo', 'Processo:');
        echo filter_dropdown('cd_usuario_equipe', 'Equipe:', $arr_equipe);
        echo filter_dropdown('tipo', 'Constatação Tipo:', $ar_tipo);
        echo filter_dropdown('fl_impacto', 'Impacto significativo:', $ar_impacto);
    echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br(3);
echo aba_end();

$this->load->view('footer'); 

?>