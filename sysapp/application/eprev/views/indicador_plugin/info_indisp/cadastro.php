<?php
set_title($tabela[0]['ds_indicador']);
$this->load->view('header'); 
?>
<script>
	<?= form_default_js_submit(array('cd_indicador_tabela', 'mes_referencia', 'ano_referencia', 'nr_expediente', 'nr_minutos_a', 'nr_minutos_b', 'nr_percentual_a',	'nr_percentual_b', 'nr_meta'), 'validacao(form);') ?>
	
	function validacao(form)
	{
		$("#dt_referencia").val("01/"+$("#mes_referencia").val()+"/"+$("#ano_referencia").val());

		if(confirm("Salvar?"))
		{
			form.submit();
		}
	}	

    function ir_lista()
	{
		location.href = "<?= site_url('indicador_plugin/info_indisp') ?>";
	}

    function manutencao()
    {
        location.href = "<?= site_url('indicador/manutencao/') ?>";
    }
	
	function excluir()
	{
		var confirmacao = "Deseja Excluir?\n\n"+
                          "Clique [Ok] para Sim\n\n"+
                          "Clique [Cancelar] para N�o\n\n"; 

		if(confirm(confirmacao))
		{
			location.href = "<?= site_url('indicador_plugin/info_indisp/excluir/'.$row['cd_info_indisp']) ?>";
		}
	}
	
	$(function(){
		$("#mes_referencia").focus();
	});	
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', false, 'manutencao();');
	$abas[] = array('aba_lancamento','Lan�amento', false, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', true, 'location.reload();');
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

	<?= form_open('indicador_plugin/info_indisp/salvar') ?>
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
					<label class="label-padrao-form">Per�odo Aberto:</label>
				</td>
				<td>
					<span class="label label-important"><?= $tabela[0]['ds_periodo'] ?></span>
				</td>
			</tr>

		<?= form_end_box('indicador_box') ?>
		<?= form_start_box('default_box', 'Cadastro') ?>
			<?= form_default_hidden('cd_indicador_tabela', '', $tabela[0]['cd_indicador_tabela']) ?>
			<?= form_default_hidden('cd_info_indisp', '', intval($row['cd_info_indisp'])) ?>

			<?= form_default_mes_ano('mes_referencia', 'ano_referencia', $label_0.': (*)', $row['dt_referencia']) ?>
			<?= form_default_hidden('dt_referencia', 'M�s', $row) ?>
			<?= form_default_integer('nr_expediente', $label_1.': ', $row['nr_expediente'], 'class="indicador_text"') ?>
			<?= form_default_integer('nr_minutos_a', $label_2.': ', $row['nr_minutos_a'], 'class="indicador_text"') ?>
			<?= form_default_integer('nr_minutos_b', $label_3.': ', $row['nr_minutos_b'], 'class="indicador_text"') ?>
			<?= form_default_numeric('nr_percentual_a', $label_4.': ', number_format($row['nr_percentual_a'], 2, ',', '.'), 'class="indicador_text"') ?>
			<?= form_default_numeric('nr_percentual_b', $label_5.': ', number_format($row['nr_percentual_b'], 2, ',', '.'), 'class="indicador_text"') ?>
			<?= form_default_numeric('nr_meta',  $label_6.': ', number_format($row['nr_meta'], 2, ',', '.'), 'class="indicador_text"') ?>
			<?= form_default_textarea('observacao', $label_8.': ', $row['observacao']) ?>
		<?= form_end_box('default_box') ?>
		<?= form_command_bar_detail_start() ?>

			<?= button_save() ?>

			<? if(intval($row['cd_info_indisp']) > 0) :?>	
				<?= button_save('Excluir', 'excluir()', 'botao_vermelho') ?>
			<? endif; ?>
		<?= form_command_bar_detail_end() ?>
	<?= form_close() ?>

<? endif; ?>
</br></br>
<?= aba_end() ?>

<?= $this->load->view('footer') ?>