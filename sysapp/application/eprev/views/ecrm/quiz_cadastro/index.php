<?php
set_title('Quiz Jogadores');
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		document.getElementById("current_page").value = 0;
		load();
	}
	
	function load()
	{
		document.getElementById("result_div").innerHTML = "<?php echo loader_html(); ?>";
		$.post( '<?php echo base_url() . index_page(); ?>/ecrm/quiz_cadastro/inscricaoListar'
			,{
				current_page : $('#current_page').val()				
			}
			,
		function(data)
			{
				document.getElementById("result_div").innerHTML = data;
				configure_result_table();
			}
		);
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),[
					"Number",
					"CaseInsensitiveString",
					"CaseInsensitiveString",
					"CaseInsensitiveString",
					"CaseInsensitiveString",
					"CaseInsensitiveString",
					"CaseInsensitiveString",
					"CaseInsensitiveString",
					"Number",
					"DateTimeBR"					
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
	
</script>
<?php
	$abas[0] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
	echo aba_start( $abas );

?>
<div id="result_div"><br><br><span style='color:green;'><b>Realize um filtro para exibir a lista</b></span></div>
<br />
<?php echo aba_end(''); ?>
<script>
	filtrar();
</script>
<?php
$this->load->view('footer');
?>