<?php 
	set_title($tabela[0]['ds_indicador']);
	$this->load->view('header'); 
?>
<script>
	<?= form_default_js_submit(array(
			'cd_indicador_tabela', 
			'ds_evento', 
			'nr_houve_exigencia', 
			'nr_meta'
		),'_salvar(form)')	
	?>

	function _salvar(form)
	{
		$("#dt_referencia").val("01/01/"+$("#ano_referencia").val());

		if(confirm("Salvar?"))
		{
			form.submit();
		}
	}

	function ir_lista()
	{
		location.href = "<?= site_url('indicador_plugin/atuarial_exigencia_previc_novo_plano') ?>";
	}
	
    function manutencao()
    {
        location.href = "<?= site_url('indicador/manutencao') ?>";
    }
	
	function excluir()
	{
		location.href = "<?= site_url('indicador_plugin/atuarial_exigencia_previc_novo_plano/excluir/'.$row['cd_atuarial_exigencia_previc_novo_plano']) ?>";
	}
		
	$(function() {
		$("#mes_referencia").focus();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', false, 'manutencao();');
	$abas[] = array('aba_lista', 'Lançamento', false, 'ir_lista();');
	$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');
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
			<?= form_open('indicador_plugin/atuarial_exigencia_previc_novo_plano/salvar')?>
				<?= form_start_box("default_box", 'Cadastro')?>
					<?= form_default_hidden('cd_indicador_tabela', 'Código indicador tabela', $tabela[0]['cd_indicador_tabela'])?>
					<?= form_default_hidden('cd_atuarial_exigencia_previc_novo_plano', 'Código da tabela', intval($row['cd_atuarial_exigencia_previc_novo_plano']))?>
					<?= form_default_hidden('nr_ano_referencia', '', $tabela[0]['nr_ano_referencia']) ?>
					<?= form_default_row('', 'Indicador:', '<span class="label label-inverse">'.$tabela[0]['ds_indicador'].'</span>')?>
					<?= form_default_row('','','')?>
					<?= form_default_text('ds_evento', $label_0.': (*)', $row['ds_evento'], "class='indicador_text'") ?>
					<?= form_default_dropdown('nr_houve_exigencia', $label_1.': (*)', $drop, $row['nr_houve_exigencia']) ?>
					<?= form_default_dropdown('nr_meta', $label_2.': (*)', $drop, $row['nr_meta']) ?>
					<?= form_default_textarea('ds_observacao', $label_4.':', $row['ds_observacao'])?>
				<?= form_end_box('default_box')?>
				<?= form_command_bar_detail_start()?>
					<?= button_save()?>

					<? if(intval($row['cd_atuarial_exigencia_previc_novo_plano']) > 0) : ?>
						<?= button_save('Excluir', 'excluir();', 'botao_vermelho')?>
					<? endif; ?>

				<?= form_command_bar_detail_end()?>
			<?= form_close()?>
		<? endif; ?>
	</br></br>
<?= aba_end()?>
<?=$this->load->view('footer_interna')?>
