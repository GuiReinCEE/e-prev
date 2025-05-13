<?= set_title($tabela[0]['ds_indicador']) ?>
<?= $this->load->view('header') ?>

<script>
	<?= form_default_js_submit(array('cd_indicador_tabela', 'ano_referencia', 'nr_resultado', 'nr_meta'), 'validacao(form);') ?>
	
	function validacao(form)
	{
		$("#dt_referencia").val("01/01/"+$("#ano_referencia").val());

		if(confirm('Salvar?'))
		{
			form.submit();
		}
	}	

    function ir_lista()
	{
		location.href = "<?= site_url('indicador_plugin/controladoria_certificado_iso') ?>";
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
			location.href = "<?= site_url('indicador_plugin/controladoria_certificado_iso/excluir/'.$row['cd_controladoria_certificado_iso']) ?>";
		}
	}
	
	$(function() {
		$("#ano_referencia").focus();
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

	<?= form_open('indicador_plugin/controladoria_certificado_iso/salvar') ?>
		<?= form_start_box('indicador_box', 'Indicador') ?>
			<tr>
				<td class="coluna-padrao-form">
					<label class="label-padrao-form">Indicador:</label>
				</td>
				<td>
					<span class="label label-inverse"><?= $tabela[0]['ds_indicador'] ?></span>
				</td>
			</tr>
		<?= form_end_box('indicador_box') ?>

		<?= form_start_box('default_box', 'Cadastro') ?>
			<?= form_default_hidden('cd_indicador_tabela', '', $tabela[0]['cd_indicador_tabela']) ?>
			<?= form_default_hidden('nr_ano_periodo', '', $tabela[0]['nr_ano_referencia']) ?>
			<?= form_default_hidden('cd_controladoria_certificado_iso', '', intval($row['cd_controladoria_certificado_iso'])) ?>
			<?= form_default_hidden('dt_referencia', '', $row['dt_referencia']) ?>

			<?= form_default_integer('ano_referencia', $label_0.' (*) : ', $row['ano_referencia']) ?>
			<?= form_default_dropdown('nr_resultado', $label_1.' (*) : ', $drop, $row['nr_resultado']) ?>
			<?= form_default_dropdown('nr_meta', $label_2.' (*) : ', $drop, $row['nr_meta']) ?>
			<?= form_default_textarea('observacao', $label_3.' :', $row['observacao']) ?>
		<?= form_end_box('default_box') ?>

		<?= form_command_bar_detail_start() ?>

			<?= button_save() ?>

			<? if(intval($row['cd_controladoria_certificado_iso']) > 0) :?>	
				<?= button_save('Excluir', 'excluir()', 'botao_vermelho') ?>
			<? endif; ?>
		<?= form_command_bar_detail_end() ?>
	<?= form_close() ?>

<? endif; ?>
</br></br>
<?= aba_end() ?>

<?= $this->load->view('footer') ?>