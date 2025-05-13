<?php
set_title('Recadastro Devolução');
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$('#result_div').html("<?php echo loader_html(); ?>");
		
		$.post('<?php echo site_url('ecrm/recadastro_devolucao/listar');?>',
		$('#filter_bar_form').serialize(),
		function(data)
		{
			$('#result_div').html(data);

			configure_result_table();
		});
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),[
			"Number",
			"RE",
			"CaseInsensitiveString",
			"DateBR",
			"CaseInsensitiveString",
			"DateTimeBR",
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
		ob_resul.sort(2, false);
	}
	
	function nova()
	{
		location.href='<?php echo site_url('ecrm/recadastro_devolucao/cadastro');?>';
	}	
	
	$(function(){
		filtrar();
	});
</script>
<?php
$abas[0] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$config['button'][]=array('Nova Devolução', 'nova();');

$participante['cd_empresa']            = $cd_empresa;
$participante['cd_registro_empregado'] = $cd_registro_empregado;
$participante['seq_dependencia']       = $seq_dependencia;
	
echo aba_start( $abas );
	echo form_list_command_bar($config);
	echo form_start_box_filter('filter_bar', 'Filtros');
		echo filter_participante(array('cd_empresa','cd_registro_empregado','seq_dependencia', 'nome'), "Participante:", $participante, TRUE, TRUE );	
		echo filter_text('nome', "Nome: ", '', "style='width:400px;'");	
		echo filter_dropdown('cd_atendimento_recadastro_devolucao_motivo', 'Motivo:', $ar_devolucao_motivo);	
		echo filter_date_interval('dt_devolucao_ini', 'dt_devolucao_fim', 'Dt Devolução:',date('01/01/Y'), date('d/m/Y'));
	echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br(2);
echo aba_end('');

$this->load->view('footer');
?>