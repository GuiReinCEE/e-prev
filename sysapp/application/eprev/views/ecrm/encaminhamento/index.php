<?php 
set_title('Encaminhamentos');
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

		$.post( '<?php echo site_url("ecrm/encaminhamento/listar"); ?>/',
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
			'Number',
			'RE',
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			'Number',
			'DateTimeBR',
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
		$("#inicio_fim_shortcut").val("last30days");
		$("#inicio_fim_shortcut").change();
	
		<?php
			if(intval($cd_atendimento_encaminhamento_tipo) > 0)
			{
				echo '
						$("#cd_atendimento_encaminhamento_tipo").val('.intval($cd_atendimento_encaminhamento_tipo).');
						$("#cd_atendimento_encaminhamento_tipo").change();
						
						
						if(($("#cd_empresa").val() != "") && ($("#cd_registro_empregado").val() != "") && ($("#seq_dependencia").val() != ""))
						{
							$("#situacao_filtro").val("t");
							$("#situacao_filtro").change();						
						}
				     ';
					 
			}
		?>
	
		filtrar();
	});	
</script>

<?php
$abas[] = array( 'aba_lista', 'Lista', true, 'location.reload();');

echo aba_start($abas);
	echo form_list_command_bar();
	echo form_start_box_filter();
		echo filter_participante(array('cd_empresa','cd_registro_empregado','seq_dependencia', 'nome'), 'Participante:', array('cd_empresa'=>$cd_empresa,'cd_registro_empregado'=>$cd_registro_empregado,'seq_dependencia'=>$seq_dependencia), TRUE, TRUE);
		echo filter_text('nome', 'Nome:','','style="width: 500px;"');
		echo filter_date_interval('inicio', 'fim', 'Data:');
		echo filter_dropdown('atendente', 'Atendente:', $atendente_dd);

		$ar_tipo = Array(Array('text' => 'Aberto', 'value' => 'a'),Array('text' => 'Processado', 'value' => 'e'),Array('text' => 'Cancelado', 'value' => 'c'),Array('text' => 'Todos', 'value' => 't')) ;
		echo filter_dropdown('situacao_filtro', 'Situação:', $ar_tipo, Array('a'));
		echo filter_dropdown('cd_atendimento_encaminhamento_tipo', 'Tipo:', $ar_atendimento_encaminhamento_tipo);
	echo form_end_box_filter();

	echo '<div id="result_div"><br><br><span style="color:green;"><b>Realize um filtro para exibir a lista</b></span></div>';
	echo br(5);

echo aba_end();

$this->load->view('footer');