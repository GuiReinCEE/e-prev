<?php
set_title($tabela[0]['ds_indicador']);
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<br/><center><?php echo loader_html(); ?></center>");
		
		$.post('<?php echo site_url('indicador_plugin/juridico_sucesso_acoes_juchem/listar'); ?>',
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
			'MesAno',
			'NumberFloat','NumberFloat','NumberFloat',
			'NumberFloat','NumberFloat','NumberFloat',
			'NumberFloat'
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

		ob_resul.sort(0, true);
	}
	
	function fechar_periodo()
	{
		if( $('#contador_input').val()!= '4' )
		{
			alert("Falta alguma Fase.");
		}
		else if( confirm('Fechar o per�odo?') )
		{
			$.post('<?php echo site_url("indicador_plugin/juridico_sucesso_acoes_juchem/criar_indicador/"); ?>',
			function(data)
			{
				$('#output_tela').html("Indicadores atualizados com sucesso, aguarde enquanto o per�odo � fechado ...<BR><BR>" );

				location.href = '<?php echo site_url("indicador_plugin/juridico_sucesso_acoes_juchem/fechar_periodo")?>';
			});
		}
	}
	
	function gerar_graficos()
	{
		if( confirm('Atualizar Indicadores?') )
		{
			$.post('<?php echo site_url("indicador_plugin/juridico_sucesso_acoes_juchem/criar_indicador"); ?>', 
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
		location.href='<?php echo site_url("indicador_plugin/juridico_sucesso_acoes_juchem/detalhe"); ?>';
	}
	
	$(function(){
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
$abas[] = array('aba_lista', 'Lan�amento', true, 'location.reload();');

echo aba_start( $abas );
	echo form_start_box("default_box", "Cadastro", TRUE);
		echo form_default_row("", "Indicador:", '<span class="label label-inverse">'.$tabela[0]['ds_indicador'].'</span>'); 
		echo form_default_row("", "Per�odo aberto:", '<span class="label label-important">'.$tabela[0]['ds_periodo'].'</span>'); 
		echo form_default_row("","","");
		echo form_default_row("","",button_save('Informar valores', 'novo()') . button_save('Atualizar apresenta��o', 'gerar_graficos()','botao_disabled'). button_save('Fechar Per�odo', 'fechar_periodo()','botao_disabled'));
	echo form_end_box("default_box");	
	echo '
		<div id="output_tela"></div>
		<div id="result_div">'.br(2).'
			<center>
				<span class="label label-success">Realize um filtro para exibir a lista</span>
			</center>
		</div>';
	echo br();
echo aba_end(); 
$this->load->view('footer');
?>