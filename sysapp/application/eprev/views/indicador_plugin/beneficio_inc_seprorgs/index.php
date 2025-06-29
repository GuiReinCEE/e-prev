<?php
	set_title($tabela[0]['ds_indicador']);
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
			
		$.post("<?= site_url('indicador_plugin/beneficio_inc_seprorgs/listar') ?>",
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
			null
		]);

		ob_resul.onsort = function()
		{
			var rows = ob_resul.tBody.rows;
			var l = rows.length;
			for(var i = 0; i < l; i++)
			{
				removeClassName( rows[i], i % 2 ? "sort-par" : "sort-impar" );
				addClassName( rows[i], i % 2 ? "sort-impar" : "sort-par" );
			}
		};

		ob_resul.sort(0, false);
	}

	function novo()
	{
		location.href='<?php echo site_url("indicador_plugin/beneficio_inc_seprorgs/cadastro"); ?>';
	}

	function manutencao()
	{
	    location.href='<?php echo site_url("indicador/manutencao/index/13/A"); ?>';
	}

    function gerar_grafico()
	{
		if(confirm("Atualizar Apresenta��o?"))
		{
			$.post("<?= site_url('indicador_plugin/beneficio_inc_seprorgs/criar_indicador') ?>", 
			function(data)
			{ 
				$("#output").html(data); 
			});
		}
	}

    function fechar_periodo()
	{
		if($("#contador_input").val() != "12")
		{
			alert("Falta algum m�s.");
		}
		else if($("#mes_input").val() < "12")
		{
			alert("�ltimo m�s deve ser dezembro.");
		}
		else if(confirm("Fechar o per�odo?"))
		{
			$.post("<?= site_url('indicador_plugin/beneficio_inc_seprorgs/criar_indicador') ?>", 
			function(data)
			{
				$("#div-output").html("Indicadores atualizados com sucesso, aguarde enquanto o per�odo � fechado ...");

				location.href = "<?= site_url('indicador_plugin/beneficio_inc_seprorgs/fechar_periodo') ?>";
			});
		}
	}

	$(function (){
		filtrar();
	});
</script>
<?php
	$abas[] = array( 'aba_lista', 'Lista', false, 'manutencao();' );
	$abas[] = array( 'aba_lista', 'Lan�amento', true, 'location.reload();' );

	if($tabela)
	{
		$ds_tabela_periodo = "<big>".$tabela[0]['ds_indicador'] . " - " . $tabela[0]['ds_periodo']."</big>";
	}
	else
	{
		$ds_tabela_periodo = "";
	}

	$config['button'][] = array('Informar valores', 'novo()');
	$config['button'][] = array('Atualizar apresenta��o', 'gerar_grafico()');
	$config['button'][] = array('Fechar Per�odo', 'fechar_periodo()');

	echo aba_start( $abas );
		echo "<div id='div-output'></div>";
		echo form_list_command_bar($config);
		echo form_start_box_filter('filter_bar', 'Filtros');
		echo form_default_row( "", $ds_tabela_periodo, "" );
		echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br();
	echo aba_end(''); 
	$this->load->view('footer');
?>
