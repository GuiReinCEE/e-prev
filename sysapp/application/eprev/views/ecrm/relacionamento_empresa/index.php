<?php
set_title('Relacionamento - Empresas');
$this->load->view('header');
?>
<!-- Maps API Javascript -->
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
<script src="<?php echo base_url()."js/markerclusterer.js";?>"></script>
<script>
function filtrar()
{
	var fl_busca = true;

	if($.trim($('#fl_contato').val()) != "S")
	{
		if(
			($.trim($('#ds_empresa').val()) == "") &&
			($.trim($('#uf').val()) == "") &&
			($.trim($('#cidade').val()) == "") &&
			($('input[name="fl_nr_colaborador[]"]:checked').length == 0) &&
			($('input[name="grupos[]"]:checked').length == 0) &&
			($('input[name="segmentos[]"]:checked').length == 0) &&
			($('input[name="evento[]"]:checked').length == 0) &&
			($('input[name="origem[]"]:checked').length == 0)
		  )
		{
			alert("É necessário informar mais um filtro para pesquisar");
			fl_busca = false;
		}
	}
	
	if(fl_busca)
	{
		$('#result_div').html("<?php echo loader_html(); ?>");
		$.post( '<?php echo site_url('/ecrm/relacionamento_empresa/listar'); ?>',
		$("#filter_bar_form").serialize(),
		function(data)
		{
			$('#result_div').html(data);
			configure_result_table();
		});
	}
}

function configure_result_table()
{
	var ob_resul = new SortableTable(document.getElementById("table-1"),
	[
		'Number', 
		'CaseInsensitiveString', 
		'Number', 
		'CaseInsensitiveString', 
		'CaseInsensitiveString', 
		'CaseInsensitiveString', 
		'CaseInsensitiveString', 
		'DateTimeBR', 
		'Number', 
		'Number',
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
	ob_resul.sort(7, true);
}

function nova()
{
	location.href='<?php echo site_url( "ecrm/relacionamento_empresa/cadastro" ); ?>';
}

function ir_relatorio()
{
	location.href='<?php echo site_url( "ecrm/relacionamento_empresa/relatorio" ); ?>';
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
		$.post("<?php echo site_url('ecrm/relacionamento_empresa/cidades'); ?>", 
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
	if($.trim($('#fl_contato').val()) == "")
	{
		$('#fl_contato').val("S");
		$('#fl_contato').change();
	}
	
	filtrar();
	
	$('#grupos_row').css('height', '90px');
});
</script>

<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
$abas[] = array('aba_lista', 'Rel. Atividade', FALSE, 'ir_relatorio();');

$ar_nr_colaborador[] = array('value' => 'Z', 'text' => 'igual a 0');
$ar_nr_colaborador[] = array('value' => 'A', 'text' => 'de 1 a 249');
$ar_nr_colaborador[] = array('value' => 'B', 'text' => 'de 250 a 499');
$ar_nr_colaborador[] = array('value' => 'C', 'text' => 'de 500 a 749');
$ar_nr_colaborador[] = array('value' => 'D', 'text' => 'de 750 a 999');
$ar_nr_colaborador[] = array('value' => 'E', 'text' => 'de 1.000 a 1.499');
$ar_nr_colaborador[] = array('value' => 'F', 'text' => 'de 1.500 a 1.999');
$ar_nr_colaborador[] = array('value' => 'G', 'text' => 'de 2.000 a 2.499');
$ar_nr_colaborador[] = array('value' => 'H', 'text' => 'de 2.500 a 2.999');
$ar_nr_colaborador[] = array('value' => 'I', 'text' => 'mais de 3.000');

$config['button'][] = array('Nova Empresa', 'nova();');
$ar_contato = Array(Array('text' => 'Todos', 'value' => ''),Array('text' => 'Sim', 'value' => 'S'),Array('text' => 'Não', 'value' => 'N')) ;
$ar_exibe = Array(Array('text' => 'Lista', 'value' => ''),Array('text' => 'Mapa', 'value' => 'M')) ;
echo aba_start($abas);
	echo form_list_command_bar($config);	
	echo form_start_box_filter('filter_bar', 'Filtros', false);
	    echo filter_dropdown("fl_contato", "Contato:", $ar_contato);
		echo form_default_text('ds_empresa', 'Empresa:', '', 'style="width:400px"');
		echo form_default_dropdown("uf", "UF:", $arr_uf, array(), "onchange='filtrar_cidade(this.value);'");
		echo form_default_dropdown("cidade", "Cidade:", array());
		echo form_default_row('','','');
		echo form_default_checkbox_group('fl_nr_colaborador', 'Qt de Colaboradores/Associados:', $ar_nr_colaborador, array(), 70);
		echo form_default_row('','','');
		echo form_default_checkbox_group('grupos', 'Grupos:', $arr_grupo, array(), 70);
		echo form_default_row('','','');
		echo form_default_checkbox_group('segmentos', 'Segmentos:', $arr_segmento, array(), 70);
		echo form_default_row('','','');
		echo form_default_checkbox_group('evento', 'Eventos:', $arr_evento, array(), 70);
		echo form_default_row('','','');
		echo form_default_checkbox_group('origem', 'Origem:', $arr_origem, array(), 70);
		
		echo filter_dropdown("fl_exibe", "Exibição:", $ar_exibe);
		
	echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br();
echo aba_end();

$this->load->view('footer');
?>