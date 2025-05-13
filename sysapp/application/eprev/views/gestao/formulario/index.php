<?php
set_title('Cadastro de Formulários');
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?php echo loader_html(); ?>");
				
		$.post('<?php echo site_url('gestao/formulario/listar');?>',
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
		    "Number",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
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
		ob_resul.sort(1, false);
	}
					
	function novo()
	{
		location.href='<?php echo site_url("gestao/formulario/cadastro"); ?>';
	}
	
	function excluir_formulario(cd_formulario)
	{	
		if(confirm("Deseja excluir o formulário?"))
		{
			location.href='<?php echo site_url("gestao/formulario/excluir"); ?>/'+cd_formulario;
		}
	}

	$(function(){
		filtrar();
	});

</script>
<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$config['button'][]=array('Novo Formulário', 'novo()');

echo aba_start( $abas );
	echo form_list_command_bar($config);
	echo form_start_box_filter(); 
		echo filter_integer('nr_formulario', 'Código :');
		echo filter_text('ds_formulario', 'Descrição :', '', 'style="width:300px;"');
    echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br();
echo aba_end();
$this->load->view('footer');
?>