<?= set_title($tabela[0]['ds_indicador']) ?>
<?= $this->load->view('header') ?>

<script>
	<?= form_default_js_submit(array('cd_indicador_tabela', 'mes_referencia', 'ano_referencia', 'nr_valor_1', 'nr_valor_2', 'nr_valor_3', 'nr_meta'), 'validacao(form);') ?>
	
	function validacao(form)
	{
		if($("#nr_ano_periodo").val() != $("#ano_referencia").val())
		{
			var mensagem = "ERRO\n\n"+
                           "ANO ("+$("#ano_referencia").val()+") do lançamento diferente do ANO ("+$('#nr_ano_periodo').val()+") do período."; 

			alert(mensagem);

			$("#ano_referencia").focus();
		}
		else
		{
			$("#dt_referencia").val("01/"+$("#mes_referencia").val()+"/"+$("#ano_referencia").val());

			if(confirm('Salvar?'))
			{
				form.submit();
			}
		}
	}	

    function ir_lista()
	{
		location.href = "<?= site_url('indicador_plugin/administrativo_resul_superior') ?>";
	}

    function manutencao()
    {
        location.href = "<?= site_url('indicador/manutencao') ?>";
    } 	
	
	function excluir()
	{
		var confirmacao = "Deseja Excluir?\n\n"+
                          "Clique [Ok] para Sim\n\n"+
                          "Clique [Cancelar] para Não\n\n"; 

		if(confirm(confirmacao))
		{
			location.href = "<?= site_url('indicador_plugin/administrativo_resul_superior/excluir/'.$row['cd_administrativo_resul_superior']) ?>";
		}
	}

	function get_valores()
	{
		if(($("#mes_referencia").val() != "") && ($("#ano_referencia").val() != ""))
		{
			$("#msg_importar").show();	
			
			$("#command_bar").hide();
			
			$.post("<?= site_url('indicador_plugin/administrativo_resul_superior/get_valores') ?>", 
			{
				ano_referencia : $("#ano_referencia").val(),
				mes_referencia : $("#mes_referencia").val()
			},
			function(data)
			{
				if(data)
				{
					$("#nr_media").val(data.nr_media);
					$("#nr_meta").val(data.nr_meta);
				}
				
				$("#msg_importar").hide();	
				$("#command_bar").show();
			},
			'json');
		}
		else
		{
			alert("Informe o Semestre e o Ano");
		}
	}
	
	$(function() {
		$("#mes_referencia").focus();
	});	
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', false, 'manutencao();');
	$abas[] = array('aba_lancamento','Lançamento', false, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', true, 'location.reload();');

	$semestre = array(
		array('text' => '01', 'value' => '01'), 
		array('text' => '02', 'value' => '02')
	);
?>

<?= aba_start($abas) ?>

<? if(count($tabela) == 0): ?>

	<div style="width:100%; text-align:center;">
		<span style="font-size: 12pt; color:red; font-weight:bold;">
			Nenhum período aberto para criar a tabela do indicador.
		</span>
	</div>

<? elseif(count($tabela) > 1): ?>

	<div style="width:100%; text-align:center;">
		<span style="font-size: 12pt; color:red; font-weight:bold;">
			Existe mais de um período aberto, no entanto só será possível incluir valores para o novo período depois de fechar o mais antigo.
		</span>
	</div>

<? else: ?>

	<?= form_open('indicador_plugin/administrativo_resul_superior/salvar') ?>
		<?= form_start_box('indicador_box', 'Indicador') ?>
			<tr>
				<td class="coluna-padrao-form">
					<label class="label-padrao-form">Indicador:</label>
				</td>
				<td>
					<span class="label label-inverse"><?= $tabela[0]['ds_indicador'] ?></span>
				</td>
			</tr>

			<tr>
				<td class="coluna-padrao-form">
					<label class="label-padrao-form">Período Aberto:</label>
				</td>
				<td>
					<span class="label label-important"><?= $tabela[0]['ds_periodo'] ?></span>
				</td>
			</tr>

		<?= form_end_box('indicador_box') ?>

		<?= form_start_box('default_box', 'Cadastro') ?>
			<?= form_default_hidden('cd_indicador_tabela', '', $tabela[0]['cd_indicador_tabela']) ?>
			<?= form_default_hidden('nr_ano_periodo', '', $tabela[0]['nr_ano_referencia']) ?>
			<?= form_default_hidden('cd_administrativo_resul_superior', '', intval($row['cd_administrativo_resul_superior'])) ?>
			<?= form_default_hidden('dt_referencia', '', $row['dt_referencia']) ?>

			<?= form_default_dropdown('mes_referencia', $label_0.': (*)', $semestre, $row['mes_referencia']) ?>
			<?= form_default_integer('ano_referencia', " Ano: (*)", $row['ano_referencia'], "class='indicador_text'") ?>

			<?= form_default_integer('nr_valor_1', $label_1.': (*)', number_format($row['nr_valor_1'], 0, ',', '.'), "class='indicador_text'") ?>
			<?= form_default_integer('nr_valor_2', $label_3.': (*)', number_format($row['nr_valor_2'], 0, ',', '.'), "class='indicador_text'") ?>
			<?= form_default_integer('nr_valor_3', $label_5.': (*)', number_format($row['nr_valor_3'], 0, ',', '.'), "class='indicador_text'") ?>
			<?= form_default_numeric('nr_meta', $label_8.': (*)', number_format($row['nr_meta'], 2, ',', '.'), "class='indicador_text'") ?>

			<?= form_default_textarea('observacao', $label_9.":", $row['observacao']) ?>
			<?= form_default_row("", "",'<span style="font-size: 130%; font-weight: bold; color:red; display:none;" id="msg_importar">Aguarde, importando os dados.</span>') ?>
		<?= form_end_box('default_box') ?>

		<?= form_command_bar_detail_start() ?>

			<?= button_save() ?>
			<? if(intval($row['cd_administrativo_resul_superior']) > 0): ?>	
				<?= button_save('Excluir', 'excluir()', 'botao_vermelho') ?>
			<? endif; ?>
		<?= form_command_bar_detail_end() ?>
	<?= form_close() ?>

<? endif; ?>
</br></br>
<?= aba_end() ?>

<?= $this->load->view('footer') ?>