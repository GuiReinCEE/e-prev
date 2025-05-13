<?php 
set_title($tabela[0]['ds_indicador']);
$this->load->view('header'); 
?>
<script>
	<?php 
	echo form_default_js_submit(array("cd_indicador_tabela", "nr_ano_periodo","dt_referencia","nr_valor_1","nr_valor_2","nr_meta"),"_salvar(form)");?>

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
		location.href = "<?= site_url("indicador_plugin/atuarial_rge") ?>";
	}
	
    function manutencao()
    {
        location.href = "<?= site_url("indicador/manutencao") ?>";
    }
	
	function excluir()
	{
		location.href = "<?= site_url("indicador_plugin/atuarial_rge/excluir/".$row["cd_atuarial_rge"]) ?>";
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
<?= form_open('indicador_plugin/atuarial_rge/salvar') ?>
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
		<?= form_default_hidden('cd_atuarial_rge', '', intval($row['cd_atuarial_rge'])) ?>
		<?= form_default_hidden('dt_referencia', $label_0.': (*)', $row)?>
		<?= form_default_mes_ano('mes_referencia', 'ano_referencia', $label_0.' : (*)', $row['dt_referencia'])?>
		<?= form_default_numeric('nr_valor_1', $label_1.' : (*)', number_format($row['nr_valor_1'],2,',','.'), 'class="indicador_text"')?>
		<?= form_default_numeric('nr_valor_2', $label_2.' : (*)', number_format($row['nr_valor_2'],2,',','.'), 'class="indicador_text"')?>
		<?= form_default_numeric('nr_meta', $label_4.' : (*)', number_format($row['nr_meta'],2,',','.'), 'class="indicador_text"')?>
		<?= form_default_textarea('observacao', $label_6.':', $row['observacao'])?>
	<?= form_end_box('default_box')?>
	<?= form_command_bar_detail_start()?>
		<?= button_save()?>
		<? if(intval($row['cd_atuarial_rge']) > 0) :?>
			<?= button_save('Excluir', 'excluir();', 'botao_vermelho')?>
		<? endif; ?>
	<?= form_command_bar_detail_end()?>
<?= form_close()?>
<? endif; ?>
<?= br()?>
<?= aba_end()?>
<?=$this->load->view('footer_interna')?>
