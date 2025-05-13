<?php
set_title('Cadastro de Pessoas');
$this->load->view('header');
?>
<script>
function filtrar()
{
	var segmentos = [];
	var grupos    = [];
	
	$('#result_div').html("<?php echo loader_html(); ?>");
		
    $("input[name='grupos[]']:checked").each(function() {
		grupos.push($(this).val()); 
    });
	
    $("input[name='segmentos[]']:checked").each(function() {
        segmentos.push($(this).val()); 
    });
			
	$.post( '<?php echo site_url('/ecrm/relacionamento_pessoa/listar'); ?>',
	{
		cd_pessoa_empresa      : $('#cd_pessoa_empresa').val(),
		cd_pessoa_departamento : $('#cd_pessoa_departamento').val(),
		cd_pessoa_cargo        : $('#cd_pessoa_cargo').val(),
		ds_pessoa              : $('#ds_pessoa').val(),
		uf                     : $('#uf').val(),
		cidade                 : $('#cidade').val(),
		'grupos[]'             : grupos,
		'segmentos[]'          : segmentos
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
		'Number', 
		'CaseInsensitiveString', 
		'CaseInsensitiveString', 
		'CaseInsensitiveString', 
		'CaseInsensitiveString'
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
	ob_resul.sort(0, true);
}

function novo()
{
	location.href='<?php echo site_url( "ecrm/relacionamento_pessoa/cadastro" ); ?>';
}

function filtrar_cidade(uf)
{
	var select = $('#cidade');
	
	if(select.prop) 
	{
	   var options = select.prop('options');
	}
	else 
	{
	   var options = select.attr('options');
	}
	
	$('option', select).remove();
	
	options[options.length] = new Option('Selecione', '');

	if(uf != '')
	{
		$.post("<?php echo site_url('ecrm/relacionamento_pessoa/cidades'); ?>", 
		{
			uf        : uf,
			fl_filtro : 'S'
		}, 
		function(data)
		{ 
			$.each(data, function(val, text) {
				options[options.length] = new Option(text.cidade, text.cidade);
			});
		}, 'json');
	}
}

$(function(){
	filtrar();
	
	$('#grupos_row').css('height', '90px');
})
</script>

<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$config['button'][] = array('Nova', 'novo()');

echo aba_start( $abas );
	echo form_list_command_bar($config);
	echo form_start_box_filter('filter_bar', 'Filtros');
		echo form_default_text('ds_pessoa', 'Pessoa:', '', 'style="width:400px"');
		echo form_default_dropdown("cd_pessoa_empresa", "Empresa:", $arr_empresa);
		echo form_default_dropdown("cd_pessoa_departamento", "Departamento:", $arr_departamento);
		echo form_default_dropdown("cd_pessoa_cargo", "Cargo:", $arr_cargo);
		echo form_default_dropdown("uf", "UF:", $arr_uf, array(), "onchange='filtrar_cidade(this.value);'");
		echo form_default_dropdown("cidade", "Cidade:", array());
		echo form_default_checkbox_group('grupos', 'Grupos:', $arr_grupo, array(), 70);
		echo form_default_checkbox_group('segmentos', 'Segmentos:', $arr_segmento, array(), 70);
	echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br();
echo aba_end();

$this->load->view('footer');
?>