h<?php 
	set_title($tabela[0]['ds_indicador']);
	$this->load->view('header'); 
?>
<script>
	<?= form_default_js_submit(array(
			'mes_referencia', 
			'ano_referencia', 
			'cd_indicador_tabela', 
			'nr_patrimonio_ano', 
			'nr_patrimonio_ffp',
			'nr_meta',
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
		location.href = "<?= site_url('indicador_plugin/controladoria_crescimento_patrimonial') ?>";
	}
	
    function manutencao()
    {
        location.href = "<?= site_url('indicador/manutencao') ?>";
    }
	
	function excluir()
	{
		location.href = "<?= site_url('indicador_plugin/controladoria_crescimento_patrimonial/excluir/'.$row['cd_controladoria_crescimento_patrimonial']) ?>";
	}
		
	$(function() {
		$("#mes_referencia").focus();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', false, 'manutencao();');
	$abas[] = array('aba_lista', 'Lan�amento', false, 'ir_lista();');
	$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');
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
			
			<? if(intval($row['qt_ano']) == 0): ?>

				<div style="width:100%; text-align:center;">
					<span style="font-size: 12pt; color:red; font-weight:bold;">
						Informar no campo de 'observa��es' se pretende manter ou fazer ajustes na meta do indicador, e justificar essa decis�o.
					</span>
				</div>

			<? endif; ?>

			<?= form_open('indicador_plugin/controladoria_crescimento_patrimonial/salvar')?>
				<?= form_start_box("default_box", 'Cadastro')?>
					<?= form_default_hidden('cd_indicador_tabela', 'C�digo indicador tabela', $tabela[0]['cd_indicador_tabela'])?>
					<?= form_default_hidden('cd_controladoria_crescimento_patrimonial', 'C�digo da tabela', intval($row['cd_controladoria_crescimento_patrimonial']))?>
					<?= form_default_row('', 'Indicador:', '<span class="label label-inverse">'.$tabela[0]['ds_indicador'].'</span>')?>
					<?= form_default_row('', 'Per�odo aberto:', '<span class="label label-important">'.$tabela[0]['ds_periodo'].'</span>')?> 
					<?= form_default_row('','','')?>
					<?= form_default_hidden('dt_referencia', $label_0.': (*)', $row)?> 
					<?= form_default_mes_ano('mes_referencia', 'ano_referencia', $label_0.': (*)', $row['dt_referencia'])?>
					<?= form_default_integer('nr_patrimonio_ffp', $label_1.': (*)', number_format($row['nr_patrimonio_ffp'], 2, ',', '.'), 'class="indicador_text"')?> 
					<?= form_default_integer('nr_patrimonio_ano', $label_2.': (*)', number_format($row['nr_patrimonio_ano'], 2, ',', '.'), 'class="indicador_text"')?>
					<?= form_default_numeric('nr_meta', $label_4.': (*)', number_format($row['nr_meta'], 2, ',', '.'), 'class="indicador_text"')?>
					<?= form_default_textarea('ds_observacao', $label_5.':', $row['ds_observacao'])?>
				<?= form_end_box("default_box")?>
				<?= form_command_bar_detail_start()?>
					<?= button_save()?>

					<? if(intval($row['cd_controladoria_crescimento_patrimonial']) > 0) : ?>
						<?= button_save('Excluir', 'excluir();', 'botao_vermelho')?>
					<? endif; ?>

				<?= form_command_bar_detail_end()?>
			<?= form_close()?>
		<? endif; ?>
	</br></br>
<?= aba_end()?>
<?=$this->load->view('footer_interna')?>
