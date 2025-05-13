<?php
set_title('Não Conformidades Auditor');
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		load();
	}
	
	function load()
    {
		$('#result_div').html("<?php echo loader_html(); ?>");

		$.post( '<?php echo site_url('/gestao/nao_conformidade_auditor/listar') ?>',
		$("#filter_bar_form").serialize(),
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
	
	$(function(){
		if($("#fl_vigente").val() == "")
		{
			$("#fl_vigente").val("S");
		}
		
		filtrar();
	});
</script>
<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

echo aba_start( $abas );
    echo form_list_command_bar();
    
    echo form_start_box_filter();
		echo filter_dropdown('cd_auditor', 'Auditor:', $ar_auditor);
		#echo filter_dropdown('cd_processo', 'Processo:', $ar_processo);
		echo filter_processo('cd_processo', 'Processo:');
		echo filter_dropdown("fl_vigente", "Vigente:", array(array("value" => "S", "text" => "Sim"), array("value" => "N", "text" => "Não")));
    echo form_end_box_filter();
   
echo '<div id="result_div"></div>';
echo br(); 

echo aba_end();
$this->load->view('footer'); 
?>
