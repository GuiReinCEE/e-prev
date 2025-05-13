<?php
	set_title('Log Erro de Sistema');
	$this->load->view('header');
?>
<script>
	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			'DateBR',
			'CaseInsensitiveString',
			false
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
		configure_result_table();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	$head = array( 
		'Data', 
		'Arquivo',
		''
	);

	$body = array();

	foreach($collection as $item)
	{
	    $body[] = array(
	    	anchor('servico/app_log_erro_sistema/log/'.$item['ds_log'], $item['dt_log']),
	    	anchor('servico/app_log_erro_sistema/log/'.$item['ds_log'], $item['ds_log']),
	        ''
		);
	}	

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo aba_start($abas);
		echo form_list_command_bar();
		echo form_start_box_filter();
		echo form_end_box_filter();
		echo $grid->render();
		echo br(2);
	echo aba_end();

	$this->load->view('footer');
?>