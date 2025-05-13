<?php
set_title('Avaliação - Comitê');
$this->load->view('header');
?>
<script>
function filtrar()
{
	$('#result_div').html("<?php echo loader_html(); ?>");

	$.post('<?php echo site_url('cadastro/avaliacao_comite/listar');?>',$('#filter_bar_form').serialize(),
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
	ob_resul.sort(1, false);
}

function encaminhar(cd_avaliacao_capa)
{
	if(confirm('Encaminhar para o comitê?'))
	{
		location.href='<?php echo site_url("cadastro/avaliacao_comite/encaminhar"); ?>/'+cd_avaliacao_capa+'/'+0;
	}
}

function enviar_email(cd_avaliacao_capa)
{
	if(confirm('Enviar e-mail para o comitê?'))
	{
		location.href='<?php echo site_url("cadastro/avaliacao_comite/enviar_email"); ?>/'+cd_avaliacao_capa;
	}
}

$(function (){
	filtrar();
});
</script>

<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$arr_status[] = array('value' => 'N', 'text' => 'Avaliação Finalizada');
$arr_status[] = array('value' => 'A', 'text' => 'Avaliação Iniciada');
$arr_status[] = array('value' => 'F', 'text' => 'Encaminhado ao Superior');
$arr_status[] = array('value' => 'S', 'text' => 'Encaminhado ao Comitê');
$arr_status[] = array('value' => 'E', 'text' => 'Aguardando nomeação do Comitê');
$arr_status[] = array('value' => 'C', 'text' => 'Aprovado pelo Comitê');

$arr_tipo[] = array('value' => 'H', 'text' => 'Horizontal');
$arr_tipo[] = array('value' => 'V', 'text' => 'Vertical');

echo aba_start( $abas );
	echo form_list_command_bar(array());
	echo form_start_box_filter('filter_bar', 'Filtros');
		echo form_default_usuario_ajax('cd_usuario', '', '', 'Usuário :', 'Gerência :');
		echo filter_dropdown('ano', 'Ano : ', $arr_ano, array(date('Y')));
		echo filter_dropdown('fl_status', 'Status : ', $arr_status);
		echo filter_dropdown('fl_tipo', 'Tipo : ', $arr_tipo);
	echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br(2);
echo aba_end(); 
$this->load->view('footer');
?>