<?php
set_title('Atendimento Invidualizado');
$this->load->view('header');
?>
<script>
function filtrar()
{
	$("#result_div").html("<?php echo loader_html(); ?>");
	
    $.post('<?php echo site_url('ecrm/atendimento_individual/listar');?>',
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
		'RE',
		'CaseInsensitiveString',
		'CaseInsensitiveString',
        'DateTimeBR',
        'DateTimeBR',
		'CaseInsensitiveString',
		'CaseInsensitiveString',
		'CaseInsensitiveString',
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

function novo()
{
    location.href='<?php echo site_url("ecrm/atendimento_individual/cadastro/"); ?>';
}

function excluir(cd_documento_pre_protocolo)
{
	if(confirm('Deseja Excluir?'))
	{
		$.post('<?php echo site_url('ecrm/atendimento_individual/excluir'); ?>',
		{
			cd_documento_pre_protocolo : cd_documento_pre_protocolo
		},
		function(data)
		{
			filtrar();
		});
	}
}

$(function(){
	filtrar();
})

</script>
<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$arr_status[] = array('value' => 'S', 'text' => 'Cadastrado');
$arr_status[] = array('value' => 'E', 'text' => 'Encaminhado');
$arr_status[] = array('value' => 'C', 'text' => 'Encerrado');

$arr_participante = array('cd_empresa', 'cd_registro_empregado', 'seq_dependencia', 'nome');


$config['button'][] = array('Novo Atendimento', 'novo()');

echo aba_start( $abas );
    echo form_list_command_bar($config);
    echo form_start_box_filter();
		echo filter_participante($arr_participante, 'Participante :', '', true, true);
		echo filter_text('nome', 'Nome :', '', 'style="width:300px;"');
		echo filter_date_interval('dt_cadastro_ini', 'dt_cadastro_fim', 'Dt Cadastro :');
		echo filter_date_interval('dt_encaminhamento_ini', 'dt_encaminhamento_fim', 'Dt Início :');
		echo filter_date_interval('dt_encerramento_ini', 'dt_encerramento_fim', 'Dt Encerrado :');
		echo filter_dropdown('cd_usuario_inclusao', 'Cadastrado por :', $arr_solicitante);     
		echo filter_dropdown('cd_usuario_encaminhamento', 'Início por :', $arr_encaminhado);     
		echo filter_dropdown('cd_usuario_encerramento', 'Encerrado por :', $arr_encerrado);     
		echo filter_dropdown('fl_status', 'Status :', $arr_status);     
    echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br();
echo aba_end();
$this->load->view('footer'); 
?>