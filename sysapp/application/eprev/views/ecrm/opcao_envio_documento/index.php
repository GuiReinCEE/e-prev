<?php
set_title('Opção envio documentos');
$this->load->view('header');
?>
<script>
	$(function(){
	   filtrar(); 
	   
	   $('#dt_solicitacao_ini_dt_solicitacao_fim_shortcut').val('last30days');
	   $('#dt_solicitacao_ini_dt_solicitacao_fim_shortcut').change();
	});
	
	function filtrar()
	{
		$('#result_div').html("<?php echo loader_html(); ?>");

		$.post( '<?php echo site_url('ecrm/opcao_envio_documento/listar'); ?>',$('form').serialize(),
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
			'RE',
			'CaseInsensitiveString',
			'DateTimeBR',
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
		ob_resul.sort(0, true);
	}
	
</script>

<?php 
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

echo aba_start( $abas );
    echo form_list_command_bar(array());
	echo form_start_box_filter();
		echo form_default_participante(array('cd_empresa','cd_registro_empregado','seq_dependencia', 'nome_participante'), 'Participante:', false, true, false);
		echo filter_text('nome', 'Nome:', '', 'style="width:350px;"');
		echo filter_date_interval('dt_solicitacao_ini', 'dt_solicitacao_fim', 'Dt Solicitação:');
	echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br();
echo aba_end();

$this->load->view('footer'); 
?>