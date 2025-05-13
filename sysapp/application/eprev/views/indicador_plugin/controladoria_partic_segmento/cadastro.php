<?php 
	set_title($tabela[0]['ds_indicador']);
	$this->load->view('header'); 
?>
<script>
	<?= form_default_js_submit(array(
			'mes_referencia', 
			'ano_referencia', 
			'cd_indicador_tabela', 
			'nr_invest_segmento', 
			'nr_invest_fceee'
		),'_salvar(form)')	
	?>

	function _salvar(form)
	{
		$("#dt_referencia").val("01/"+$("#mes_referencia").val()+"/"+$("#ano_referencia").val());

		if(confirm("Salvar?"))
		{
			form.submit();
		}
	}

	function ir_lista()
	{
		location.href = "<?= site_url('indicador_plugin/controladoria_partic_segmento') ?>";
	}
	
    function manutencao()
    {
        location.href = "<?= site_url('indicador/manutencao') ?>";
    }
	
	function excluir()
	{
		location.href = "<?= site_url('indicador_plugin/controladoria_partic_segmento/excluir/'.$row['cd_controladoria_partic_segmento']) ?>";
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
			<?= form_open('indicador_plugin/controladoria_partic_segmento/salvar')?>
				<?= form_start_box("default_box", 'Cadastro')?>
					<?= form_default_hidden('cd_indicador_tabela', 'Código indicador tabela', $tabela[0]['cd_indicador_tabela'])?>
					<?= form_default_hidden('cd_controladoria_partic_segmento', 'Código da tabela', intval($row['cd_controladoria_partic_segmento']))?>
					<?= form_default_row('', 'Indicador:', '<span class="label label-inverse">'.$tabela[0]['ds_indicador'].'</span>')?>
					<?= form_default_row('', 'Período aberto:', '<span class="label label-important">'.$tabela[0]['ds_periodo'].'</span>')?> 
					<?= form_default_row('','','')?>
					<?= form_default_hidden('dt_referencia', $label_0.': (*)', $row)?> 
					<?= form_default_integer('ano_referencia', 'Ano: (*)', $row['ano_referencia'], 'class="indicador_text"') ?>
					<?= form_default_dropdown('mes_referencia', 'Trimestre: (*)', array(array('value' => '01', 'text' => '01'), array('value' => '02', 'text' => '02'), array('value' => '03', 'text' => '03'), array('value' => '04', 'text' => '04')), $row['mes_referencia'], 'class="indicador_text"') ?>
					<?= form_default_numeric('nr_invest_segmento', $label_1.': (*)', number_format($row['nr_invest_segmento'], 2, ',', '.'), 'class="indicador_text"')?> 
					<?= form_default_numeric('nr_invest_fceee', $label_2.': (*)', number_format($row['nr_invest_fceee'], 2, ',', '.'), 'class="indicador_text"')?>
					<?= form_default_textarea('ds_observacao', $label_5.':', $row['ds_observacao'])?>
				<?= form_end_box("default_box")?>
				<?= form_command_bar_detail_start()?>
					<?= button_save()?>

					<? if(intval($row['cd_controladoria_partic_segmento']) > 0) : ?>
						<?= button_save('Excluir', 'excluir();', 'botao_vermelho')?>
					<? endif; ?>

				<?= form_command_bar_detail_end()?>
			<?= form_close()?>
		<? endif; ?>
	</br></br>
<?= aba_end()?>
<?=$this->load->view('footer_interna')?>
