<?= set_title($tabela[0]['ds_indicador']) ?>
<?= $this->load->view('header') ?>

<script>
	<?= form_default_js_submit(array('cd_indicador_tabela', 'mes_referencia', 'ano_referencia', 'nr_fgts', 'nr_inss', 'nr_balancete', 'nr_demostracoes', 'nr_dctf', 'nr_di', 'nr_decweb','nr_efd_contribuicoes','nr_e_financeira','nr_efd_reinf', 'nr_raiz', 'nr_dirf', 'nr_caged', 'nr_meta'), 'validacao(form);') ?>
	
	function validacao(form)
	{
		if($('#nr_ano_periodo').val() != $('#ano_referencia').val())
		{
			var alert = "ERRO\n\n"+
                        "ANO ("+$('#ano_referencia').val()+") do lançamento diferente do ANO ("+$('#nr_ano_periodo').val()+") do período."; 

			alert(alert);

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
		location.href = "<?= site_url('indicador_plugin/controladoria_obrigacoes_legais') ?>";
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
			location.href = "<?= site_url('indicador_plugin/controladoria_obrigacoes_legais/excluir/'.$row['cd_controladoria_obrigacoes_legais']) ?>";
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
	<? if(intval($row['qt_ano']) == 0): ?>

		<div style="width:100%; text-align:center;">
			<span style="font-size: 12pt; color:red; font-weight:bold;">
				Informar no campo de 'observações' se pretende manter ou fazer ajustes na meta do indicador, e justificar essa decisão.
			</span>
		</div>

	<? endif; ?>
	
	<?= form_open('indicador_plugin/controladoria_obrigacoes_legais/salvar') ?>
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
			<?= form_default_hidden('cd_controladoria_obrigacoes_legais', '', intval($row['cd_controladoria_obrigacoes_legais'])) ?>
			<?= form_default_hidden('dt_referencia', '', $row['dt_referencia']) ?>

			<?= form_default_mes_ano('mes_referencia', 'ano_referencia', $label_0.' (*) : ', $row['dt_referencia']) ?>
			<?= ''//form_default_numeric('nr_orcado', $label_1.' (*) :', number_format($row['nr_orcado'], 2, ',', '.'), 'class="indicador_text"') ?>
			<?= ''//form_default_numeric('nr_realizado', $label_2.' (*) :', number_format($row['nr_realizado'], 2, ',', '.'), 'class="indicador_text"') ?>
			<?= form_default_dropdown('nr_fgts', $label_1.' :', $drop, $row['nr_fgts']) ?>
			<?= ''//form_default_dropdown('nr_inss', $label_2.' :', $drop, $row['nr_inss']) ?>
			<?= form_default_dropdown('nr_balancete', $label_3.' :', $drop, $row['nr_balancete']) ?>
			<?= form_default_dropdown('nr_demostracoes', $label_4.' :', $drop, $row['nr_demostracoes']) ?>
			<?= form_default_dropdown('nr_dctf', $label_5.' :', $drop, $row['nr_dctf']) ?>
			<?= form_default_dropdown('nr_di', $label_6.' :', $drop, $row['nr_di']) ?>
			<?= ''//form_default_dropdown('nr_raiz', $label_12.' :', $drop, $row['nr_raiz']) ?>
			<?= form_default_dropdown('nr_decweb', $label_16.' :', $drop, $row['nr_decweb']) ?>
			<?= form_default_dropdown('nr_efd_contribuicoes', $label_17.' :', $drop, $row['nr_efd_contribuicoes']) ?>
			<?= form_default_dropdown('nr_e_financeira', $label_18.' :', $drop, $row['nr_e_financeira']) ?>
			<?= form_default_dropdown('nr_efd_reinf', $label_19.' :', $drop, $row['nr_efd_reinf']) ?>
			<?= form_default_dropdown('nr_dirf', $label_13.' :', $drop, $row['nr_dirf']) ?>
			<?= form_default_dropdown('nr_caged', $label_14.' :', $drop, $row['nr_caged']) ?>
			<?= form_default_dropdown('nr_tce', $label_15.' :', $drop, $row['nr_tce']) ?>
			<?= form_default_numeric('nr_meta', $label_10.' (*) :', number_format($row['nr_meta'], 2, ',', '.'), 'class="indicador_text"') ?>
			<?= form_default_textarea('observacao', $label_11.' :', $row['observacao']) ?>
		<?= form_end_box('default_box') ?>

		<?= form_command_bar_detail_start() ?>

			<?= button_save() ?>

			<? if(intval($row['cd_controladoria_obrigacoes_legais']) > 0) :?>	
				<?= button_save('Excluir', 'excluir()', 'botao_vermelho') ?>
			<? endif; ?>
		<?= form_command_bar_detail_end() ?>
	<?= form_close() ?>

<? endif; ?>
</br></br>
<?= aba_end() ?>

<?= $this->load->view('footer') ?>