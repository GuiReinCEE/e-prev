<?php
set_title($tabela[0]['ds_indicador']);
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?php echo loader_html(); ?>");

		$.post('<?php echo site_url('indicador_pga/despesa_sobre_receita/listar'); ?>',
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
			'MesAno',
			'Number'
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
	
	function fechar_periodo()
	{
		if( $('#contador_input').val()!='12' )
		{
			alert( "Falta algum mês." );
		}
		else if( $('#mes_input').val()!='12' )
		{
			alert( "Último mês deve ser dezembro." );
		}
		else if( confirm('Fechar o período?') )
		{
			$.post('<?php echo site_url("indicador_pga/despesa_sobre_receita/criar_indicador/"); ?>',
			function(data)
			{
				$('#output_tela').html("Indicadores atualizados com sucesso, aguarde enquanto o período é fechado ..." );
				location.href = '<?php echo site_url("indicador_pga/despesa_sobre_receita/fechar_periodo")?>';
			});
		}
	}	
	
	function gerar_graficos()
	{
		if( confirm('Atualizar Indicadores?') )
		{
			$.post('<?php echo site_url("indicador_pga/despesa_sobre_receita/criar_indicador"); ?>', 
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

	function novo()
	{
		location.href='<?php echo site_url("indicador_pga/despesa_sobre_receita/cadastro"); ?>';
	}
	
	$(function(){
		filtrar();
	});
</script>

<?php
if(count($tabela) == 0)
{
	echo '<span style="font-size: 12pt; color:red; font-weight:bold;">Nenhum período aberto para criar a tabela do indicador.</span>';
	exit;
}
else if(count($tabela) > 1)
{
	echo '<span style="font-size: 12pt; color:red; font-weight:bold;">Existe mais de um período aberto, no entanto só será possível incluir valores para o novo período depois de fechar o mais antigo.</span>';
	exit;			
}

$abas[] = array( 'aba_lista', 'Lista', false, 'manutencao();' );
$abas[] = array( 'aba_lista', 'Lançamento', true, 'location.reload();' );

echo aba_start( $abas );
	echo form_start_box("default_box", "Cadastro");
		echo form_default_row("", "Indicador:", '<span class="label label-inverse">'.$tabela[0]['ds_indicador'].'</span>'); 
		echo form_default_row("", "Período aberto:", '<span class="label label-important">'.$tabela[0]['ds_periodo'].'</span>'); 	
		echo form_default_row("","","");
		echo form_default_row("","",button_save('Informar valores', 'novo()') . button_save('Atualizar apresentação', 'gerar_graficos()','botao_disabled'). button_save('Fechar Período', 'fechar_periodo()','botao_disabled'));
	echo form_end_box("default_box");	
echo "<div id='output_tela'></div>";
echo '<div id="result_div"><br><br><span style="color:green;"><b>Realize um filtro para exibir a lista</b></span></div>';
echo br();

echo aba_end(); 
$this->load->view('footer');
?>
