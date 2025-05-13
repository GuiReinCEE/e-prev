<?php
set_title('Relacionamento - Empresas');
$this->load->view('header');
?>
<script>
function filtrar()
{
	$('#result_div').html("<?php echo loader_html(); ?>");
		
	$.post( '<?php echo site_url('/ecrm/relacionamento_empresa/listar_relatorio'); ?>',
	$('#filter_bar_form').serialize(),
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
		'DateBR'
	]);
	
	ob_resul.onsort = function()
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


function ir_lista()
{
	location.href='<?php echo site_url( "ecrm/relacionamento_empresa" ); ?>';
}

$(function(){
	$('#dt_ini_dt_fim_shortcut').val('currentMonth');
	$('#dt_ini_dt_fim_shortcut').change();
	
	filtrar();
});
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_lista', 'Rel. Atividade', TRUE, 'location.reload();');

echo aba_start($abas);
	echo form_list_command_bar(array());	
	echo form_start_box_filter('filter_bar', 'Filtros');
	    echo filter_dropdown('cd_empresa', 'Empresa:', $arr_empresa);
		echo filter_text('ds_empresa', 'Empresa:', '', 'style="width:400px"');
		echo filter_date_interval('dt_ini', 'dt_fim', 'Dt. Contato:');
		echo filter_dropdown('cd_empresa_contato_atividade', 'Atividade:', $arr_atividade);
		echo filter_dropdown('cd_usuario_relatorio', 'Usuário:', $arr_usuario);
		
		
	echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br();
echo aba_end();

$this->load->view('footer');
?>