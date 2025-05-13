<?php
set_title('Recadastramento de Dependente');
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
				
		$.post("<?= site_url('ecrm/recadastramento_dependente/listar') ?>",
		$("#filter_bar_form").serialize(),
		function(data)
		{
			$("#result_div").html(data);
			configure_result_table();
		});	
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById('table-1'),
		[
		    'RE',
		    'CaseInsensitiveString',
		    'CaseInsensitiveString',
		    'DateTimeBR',
		    'DateTimeBR',
		    'DateTimeBR',
		    'CaseInsensitiveString',
		    'DateTimeBR',
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
		ob_resul.sort(4, false);
	}
					
	$(function(){
		filtrar();
	});
</script>
<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

echo aba_start($abas);
	echo form_list_command_bar();
	echo form_start_box_filter();
		echo filter_participante(array('cd_empresa','cd_registro_empregado','seq_dependencia',''), "RE:", array() , TRUE, FALSE );	
		echo filter_date_interval('dt_envio_participante_ini','dt_envio_participante_fim', 'Dt. Envio Part. :'); 
		echo filter_date_interval('dt_confirmacao_ini','dt_confirmacao_fim', 'Dt. Confirmação :');
		echo filter_dropdown('fl_confirmado', 'Confirmado :', array(array('value' => 'N', 'text' => 'Não'), array('value' => 'S', 'text' => 'Sim')), array('N'));
		echo filter_dropdown('fl_cancelado', 'Cancelado :', array(array('value' => 'N', 'text' => 'Não'), array('value' => 'S', 'text' => 'Sim')), array('N'));
		
		echo filter_dropdown('fl_pendente', 'Pendente:', $ar_pendente);	
		echo filter_dropdown('fl_pendente_participante', 'Pendente Participante:', $ar_pendente_participante);		
		
    echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br(2);
echo aba_end();
$this->load->view('footer');
?>