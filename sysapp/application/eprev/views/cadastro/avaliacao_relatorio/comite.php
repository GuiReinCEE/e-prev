<?php
set_title('Avaliação - Relatório');
$this->load->view('header');
?>
<script>
function filtrar()
{
	$('#result_div').html("<?php echo loader_html(); ?>");
	
	$.post( '<?php echo site_url('/cadastro/avaliacao_relatorio/listar_comite'); ?>',
	{
		ano                          : $('#ano').val(),
		cd_usuario_avaliado_gerencia : $('#cd_usuario_avaliado_gerencia').val(),
		cd_usuario_avaliado          : $('#cd_usuario_avaliado').val(),
		tipo_promocao                : $('#tipo_promocao').val(),
		fl_avaliado                  : $('#fl_avaliado').val()
	},
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
		'Number',		
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
	ob_resul.sort(0, false);
}

function ir_index()
{
	location.href='<?php echo site_url("cadastro/avaliacao_relatorio/index/"); ?>';
}

$(function(){
	filtrar();
});

</script>

<?php
$abas[] = array('aba_lista', 'Avaliações Finalizadas', false, 'ir_index();');
$abas[] = array('aba_lista', 'Avaliações no Comitê', TRUE, 'location.reload();');

$arr_tipo[] = array('text' => 'Horizontal', 'value' => 'H');
$arr_tipo[] = array('text' => 'Vertical', 'value' => 'V');

$arr_avaliado[] = array('text' => 'Sim', 'value' => 'S');
$arr_avaliado[] = array('text' => 'Não', 'value' => 'N');

echo aba_start( $abas );
	echo form_list_command_bar();
	echo form_start_box_filter('filter_bar', 'Filtros');
		echo filter_integer('ano', 'Ano: ', date('Y'));
		echo filter_usuario_ajax('cd_usuario_avaliado', '', '', "Avaliado: ", "Gerência  do Avaliado: ");
		echo filter_dropdown('tipo_promocao', 'Tipo', $arr_tipo);
		echo filter_dropdown('fl_avaliado', 'Avaliado Comitê', $arr_avaliado);
	echo form_end_box_filter();
	echo '
		<center>
			<div id="result_div">
				'.br(2).'
				<span style="color:green; text-align:center;">
					<b>Realize um filtro para exibir a lista</b>
				</span>
			</div>
		</center>';
	echo br();
echo aba_end();
$this->load->view('footer');
?>