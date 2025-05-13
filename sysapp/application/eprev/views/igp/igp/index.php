<?php
	set_title($tabela[0]['ds_indicador']);
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		load();
	}

	function load()
	{
		var qt_limit = parseInt($("#qt_limit").val());

		if(qt_limit >= 12)
		{
			$("#result_div").html("<?php echo loader_html(); ?>");
			$.post('<?php echo base_url().index_page(); ?>/igp/igp/listar',
			{
				qt_limit : qt_limit
			}, 
			function(data)
			{ 
				$("#result_div").html(data);
				configure_result_table(); 
			});
		}
		else
		{
			alert("Quantidade m�nima de meses � 12");
			$("#qt_limit").focus();
		}
	}	

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			'MesAno',
			'NumberFloat','NumberFloat','NumberFloat',
			'NumberFloat','NumberFloat','NumberFloat',
			'NumberFloat','NumberFloat','NumberFloat',
			'NumberFloat','NumberFloat','NumberFloat',
			'NumberFloat','NumberFloat'
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
		
		
		var ob_resul = new SortableTable(document.getElementById("table-2"),
		[
			'MesAno',
			'NumberFloat','NumberFloat','NumberFloat',
			'NumberFloat','NumberFloat','NumberFloat',
			'NumberFloat','NumberFloat','NumberFloat',
			'NumberFloat','NumberFloat','NumberFloat',
			'NumberFloat','NumberFloat','NumberFloat'
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

		
		var ob_resul = new SortableTable(document.getElementById("table-3"),
		[
			'MesAno',
			'NumberFloat','NumberFloat','NumberFloat',
			'NumberFloat','NumberFloat','NumberFloat',
			'NumberFloat','NumberFloat','NumberFloat',
			'NumberFloat','NumberFloat','NumberFloat',
			'NumberFloat','NumberFloat'
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
		
		var ob_resul = new SortableTable(document.getElementById("table-4"),
		[
			'Number',
			'NumberFloat'
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
	
	function gerar_graficos()
	{
		if( confirm('Atualizar Indicadores?') )
		{
			$.post('<?php echo site_url("igp/igp/criar_indicador"); ?>', 
				{
				}, 
				function(data)
				{ 
					$('#output_tela').html(data); 
				});
		}
	}	

	function manutencao()
	{
		location.href='<?php echo site_url("indicador/manutencao/"); ?>';
	}

	$(document).ready(function() {
		$("#filter_bar").hide(); 
		$("#exibir_filtro_button").hide(); 		
		filtrar();
	});	
</script>

<?php
if(count($tabela) == 0)
{
	echo '<span style="font-size: 12pt; color:red; font-weight:bold;">Nenhum per�odo aberto para criar a tabela do indicador.</span>';
	exit;
}
else if(count($tabela) > 1)
{
	echo '<span style="font-size: 12pt; color:red; font-weight:bold;">Existe mais de um per�odo aberto, no entanto s� ser� poss�vel incluir valores para o novo per�odo depois de fechar o mais antigo.</span>';
	exit;			
}

$abas[] = array('aba_lista', 'Lista', false, 'manutencao();');
$abas[] = array('aba_detalhe', 'Detalhe', true, 'location.reload();');
echo aba_start( $abas );

echo form_start_box("default_box", "Cadastro");
	echo form_default_text("", "Indicador:", $tabela[0]['ds_indicador'],'style="border: 0px; width: 500px; font-weight:bold;"'); 
	echo form_default_integer('qt_limit', "Qt de meses (m�nimo 12):*", 12);
	echo form_default_row("","","");
	echo form_default_row("","",button_save('Filtrar', 'filtrar()') . button_save('Atualizar apresenta��o', 'gerar_graficos()','botao_disabled'));
echo form_end_box("default_box");

echo "<div id='output_tela'></div>";
?>

<div id="result_div"><br><br><span style='color:green;'><b>Realize um filtro para exibir a lista</b></span></div>
<br />

<?php
echo aba_end(''); 
$this->load->view('footer');
?>