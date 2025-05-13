<?php
set_title('Jogo - Resumo');
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		load();
	}
	
	function load()
	{
		$("#result_div").html("<?php echo loader_html(); ?>");
		
		$.post('<?php echo site_url("/ecrm/jogo/resumoListar"); ?>', 
		$('#filter_bar_form').serialize(),
		function(data)
		{
			$("#result_div").html(data);
			configure_result_table();
		});
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),[
			'CaseInsensitiveString',
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
		ob_resul.sort(0, false);
	}
	
	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/jogo"); ?>';
	}	
	
	$(function() {
		filtrar();
	});	
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_resumo', 'Resumo', TRUE, 'location.reload();');

$ar_sexo = array(array("value" => "M", "text" => "Masculino"),array("value" => "F", "text" => "Feminino"));

echo aba_start($abas);
	echo form_list_command_bar();		
	echo form_start_box_filter('filter_bar', 'Filtros', TRUE);
		echo form_default_dropdown("cd_jogo", "Jogo:", $ar_jogo);
		echo form_default_dropdown("cd_tipo_participante", "Categoria:", $ar_tipo_participante);
		echo form_default_dropdown("cd_sexo", "Sexo:", $ar_sexo);
		echo form_default_dropdown("cd_idade", "Idade:", $ar_idade);
		echo form_default_dropdown("cd_renda", "Renda:", $ar_renda);
		echo form_default_dropdown("cd_cidade", "Localidade:", $ar_cidade);
	echo form_end_box_filter();
	
	echo '<div id="result_div"><br><br><span style="color:green;"><b>Realize um filtro para exibir a lista</b></span></div>';
	
	echo br(5);
echo aba_end('');
$this->load->view('footer');
?>