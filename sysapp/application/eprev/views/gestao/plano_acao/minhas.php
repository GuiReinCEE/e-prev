<?php
set_title('Plano de A��o');
$this->load->view('header');
?>
<script>
    function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
				
		$.post("<?= site_url('gestao/plano_acao/minhas_listar') ?>",
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
		    'CaseInsensitiveString',
		    'CaseInsensitiveString',
		    'CaseInsensitiveString',
		    'Number',
		    'DateBr',
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
		ob_resul.sort(0, true);
	}

	
    $(function(){
		filtrar();
	});
</script>
<?php
$abas[] = array('aba_minhas', 'Lista', TRUE, 'location.reload();');

$status = array(array('value' => 'N', 'text'=> 'N�o iniciada'),array('value' => 'A', 'text'=> 'Em andamento'), array('value' => 'E', 'text'=> 'Encerrada'));

echo aba_start($abas);
    echo form_list_command_bar();
		echo form_start_box_filter(); 
			echo filter_integer_ano('nr_ano','nr_plano_acao' , 'Ano/N�mero :','');
			echo filter_date_interval('dt_prazo_ini','dt_prazo_fim', 'Dt. Prazo :');
			echo filter_dropdown('fl_status', 'Status :', $status);
			echo filter_dropdown('fl_acao', 'Resposta :', array(array('value' => 'N', 'text' => 'N�o'), array('value' => 'S', 'text' => 'Sim')));
	    echo form_end_box_filter();
	echo form_command_bar_detail_end();
	echo form_close();
    echo '<div id="result_div"></div>';
	echo br(2);
echo aba_end();
$this->load->view('footer_interna');
?>
