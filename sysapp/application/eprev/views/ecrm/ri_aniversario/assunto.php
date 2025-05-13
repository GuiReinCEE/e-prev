<?php
set_title('Aniversário - Assunto');
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
		
		$.post('<?php echo site_url("/ecrm/ri_aniversario/assuntoListar"); ?>', 
		$("#filter_bar_form").serialize(),
		function(data)
		{
			$("#result_div").html(data);
			configure_result_table();
		});
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),[
					"Number",
					"CaseInsensitiveString",
					"DateTimeBR",
					"CaseInsensitiveString"
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
		ob_resul.sort(2, true);
	}
	
	function novoAssunto()
	{
		location.href = '<?php echo site_url("ecrm/ri_aniversario/assuntoCadastro/0"); ?>';
	}	
	
	$(function() {
		filtrar();
	});	
</script>
<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$ar_origem = Array(Array('text' => 'Todos', 'value' => ''),Array('text' => 'Usuário', 'value' => 'USU'),Array('text' => 'Cadastrado', 'value' => 'CAD')) ;
$ar_data = Array(Array('text' => 'Selecione', 'value' => ''),Array('text' => 'Sim', 'value' => 'S'),Array('text' => 'Não', 'value' => 'N')) ;

echo aba_start($abas);
	$config['button'][] = array('Novo Assunto', 'novoAssunto();');
	echo form_list_command_bar($config);		
	echo form_start_box_filter('filter_bar', 'Filtros', TRUE);
		echo filter_text('assunto', "Assunto: ","", "style='width:400px;'");
	echo form_end_box_filter();
	
	echo '<div id="result_div"><br><br><span style="color:green;"><b>Realize um filtro para exibir a lista</b></span></div>';
	
	echo br(5);
echo aba_end('');
$this->load->view('footer');
?>