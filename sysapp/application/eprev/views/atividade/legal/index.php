<?php
set_title('Atividade - Legal');
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?php echo loader_html(); ?>");
				
		$.post('<?php echo site_url('atividade/legal/listar');?>',
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
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"DateBR",
			"DateBR",
			"DateBR"
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
					
	function novo()
	{
		location.href='<?php echo site_url("ecrm/atendimento_recadastro/cadastro"); ?>';
	}

	$(function(){
		filtrar();
	});

</script>
<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$status_filtro = "";
$status_filtro .= form_checkbox(array('name'=>'nao_pertinente', 'id'=>'nao_pertinente'), 'S', FALSE) . " <label for='nao_pertinente'>Não Pertinente</label><br />";
$status_filtro .= form_checkbox(array('name'=>'pertinente_com_reflexo', 'id'=>'pertinente_com_reflexo'), 'S', FALSE) . " <label for='pertinente_com_reflexo'>Pertinente com reflexo</label><br />";
$status_filtro .= form_checkbox(array('name'=>'pertinente_sem_reflexo', 'id'=>'pertinente_sem_reflexo'), 'S', FALSE) . " <label for='pertinente_sem_reflexo'>Pertinente sem reflexo</label><br />";
$status_filtro .= form_checkbox(array('name'=>'nao_verificado', 'id'=>'nao_verificado'), 'S', TRUE) . " <label for='nao_verificado'>Aguardando verificação</label><br />";
$status_filtro .= br();

echo aba_start( $abas );
	echo form_list_command_bar(array());
	echo form_start_box_filter(); 
		echo form_default_row(' pertinencia_row', 'Pertinência :', $status_filtro);
		echo filter_date_interval("dt_ini", "dt_fim", "Dt Solicitação :");
    echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br(5);
echo aba_end();
$this->load->view('footer');
?>