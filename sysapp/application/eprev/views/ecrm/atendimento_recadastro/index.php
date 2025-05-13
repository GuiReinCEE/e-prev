<?php
set_title('Recadastramento GAP');
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?php echo loader_html(); ?>");
				
		$.post('<?php echo site_url('ecrm/atendimento_recadastro/listar');?>',
		$("#filter_bar_form").serialize(),
		function(data)
		{
			$("#result_div").html(data);
			configure_result_table();
		});
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),[
			"RE",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"DateTimeBR",
			"DateTimeBR",
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
					
	function novo()
	{
		location.href='<?php echo site_url("ecrm/atendimento_recadastro/cadastro"); ?>';
	}

	$(function(){
		filtrar();
		
		$('#dt_criacao_ini_dt_criacao_fim_shortcut').val('next30days');
		$('#dt_criacao_ini_dt_criacao_fim_shortcut').change();
	});

</script>
<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$config['button'][]=array('Novo Recadastro', 'novo()');

$conf = array('cd_empresa','cd_registro_empregado','seq_dependencia', 'nome');

echo aba_start( $abas );
	echo form_list_command_bar($config);
	echo form_start_box_filter(); 
		echo filter_participante($conf, "Participante :", array(), TRUE, FALSE);
		echo filter_date_interval('dt_criacao_ini', 'dt_criacao_fim', 'Dt Remessa :');
    echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br();
echo aba_end();
$this->load->view('footer');
?>