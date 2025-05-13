<?php
	set_title($tabela[0]['ds_indicador']);
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
		
		$.post("<?= site_url('indicador_plugin/atuarial_exigencia_previc_novo_plano/listar') ?>",
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
			null
		]);

		ob_resul.onsort = function()
		{
			var rows = ob_resul.tBody.rows;
			var l = rows.length;
			for(var i = 0; i < l; i++)
			{
				removeClassName(rows[i], i % 2 ? "sort-par" : "sort-impar");
				addClassName(rows[i], i % 2 ? "sort-impar" : "sort-par");
			}
		};

		ob_resul.sort(0, false);
	}

	function atualizar_grafico()
	{
		if(confirm("Atualizar Indicadores?"))
		{
			$.post("<?= site_url('indicador_plugin/atuarial_exigencia_previc_novo_plano/criar_indicador') ?>", 
			function(data)
			{ 
				$("#output_tela").html(data); 
			});
		}
	}
	
	function gerar_graficos()
	{
		if( confirm('Atualizar Indicadores?') )
		{
			$.post('<?= site_url("indicador_plugin/atuarial_exigencia_previc_novo_plano/criar_indicador") ?>', 
			function(data)
			{ 
				$('#output_tela').html(data); 
			});
		}
	}	
	
	function manutencao()
	{
		location.href = '<?= site_url("indicador/manutencao") ?>';
	}
	
	function novo()
	{
		location.href = '<?= site_url("indicador_plugin/atuarial_exigencia_previc_novo_plano/cadastro") ?>';
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

	?>
	<?= aba_start($abas) ?>

		<? if(count($tabela) == 0): ?>

			<div style="width:100%; text-align:center;">
				<span style="font-size: 12pt; color:red; font-weight:bold;">
					Nenhum per�odo aberto para criar a tabela do indicador.
				</span>
			</div>

		<? elseif(count($tabela) > 1): ?>

			<div style="width:100%; text-align:center;">
				<span style="font-size: 12pt; color:red; font-weight:bold;">
					Existe mais de um per�odo aberto, no entanto s� ser� poss�vel incluir valores para o novo per�odo depois de fechar o mais antigo.
				</span>
			</div>

		<? else: ?>

			<?= form_start_box('default_box', 'Cadastro') ?>
				<tr>
					<td class="coluna-padrao-form">
						<label class="label-padrao-form">Indicador:</label>
					</td>
					<td>
						<span class="label label-inverse"><?= $tabela[0]['ds_indicador'] ?></span>
					</td>
				</tr>

				<tr>
					<td colspan="2" style="padding-top:10px;">
						<?= button_save('Informar valores', 'novo()') ?>
						<?= button_save('Atualizar apresenta��o', 'atualizar_grafico()','botao_disabled') ?>
					</td>
				</tr>
			<?= form_end_box('default_box') ?>
			
			<div id="output_tela"></div>

			<div id="result_div" style="width:100%; text-align:center;">
				</br></br>
				<span style="font-size: 12pt; color:green; font-weight:bold;">
					Realize um filtro para exibir a lista
				</span>
			</div>

		<? endif; ?>
		</br>
	<?= aba_end() ?>

<?= $this->load->view('footer') ?>