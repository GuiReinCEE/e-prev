<?php
set_title('Avaliação - Matriz Salarial ');
$this->load->view('header');
?>
<script>
function filtrar()
{
	$('#result_div').html("<?php echo loader_html(); ?>");

	$.post( '<?php echo site_url("cadastro/matriz/lista_colaboradores"); ?>',
	{
		cd_usuario_gerencia : $('#cd_usuario_gerencia').val(),
		cd_usuario          : $('#cd_usuario').val(),
		fl_tipo             : $('#fl_tipo').val(),
		cd_familia          : $('#cd_familia').val(),
		faixa               : $('#faixa').val()
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
        'CaseInsensitiveString',
        'CaseInsensitiveString',
        'DateBR',
        'DateBR',
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
	ob_resul.sort(0, false);
}

function ir_matriz_salarial()
{
    location.href='<?php echo site_url('/cadastro/matriz/matriz_salarial');?>';
}

$(function(){
	filtrar();
});

</script>

<?php
$abas[] = array( 'aba_lista', 'Colaboradores', true, 'location.reload();' );
$abas[] = array( 'aba_lista', 'Matriz Salarial', false, 'ir_matriz_salarial();' );

$arr_tipo[] = array('text' => 'Horizontal', 'value' => 'H');
$arr_tipo[] = array('text' => 'Vertical', 'value' => 'V');

echo aba_start( $abas );
    echo form_list_command_bar(array());
    echo form_start_box_filter();
		echo filter_usuario_ajax('cd_usuario', '', '', "Usuário: ", "Gerência: ");
		echo filter_dropdown('fl_tipo', 'Tipo:', $arr_tipo);
		echo filter_dropdown('cd_familia', 'Classe:', $ar_classes);
		echo filter_dropdown('faixa', 'Faixa:', $ar_faixas);
    echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br();
echo aba_end();
$this->load->view('footer');
?>