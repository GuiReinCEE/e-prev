<?php 
set_title($tabela[0]['ds_indicador']);
$this->load->view('header'); 
?>
<script>
	<?php 
	echo form_default_js_submit(array("ano_referencia", "cd_indicador_tabela", "nr_valor_3", "nr_valor_1", "nr_valor_2", "nr_meta"),"_salvar(form)");	?>

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
		location.href = "<?= site_url("indicador_plugin/exp_adesao_potencial_inpel") ?>";
	}
	
    function manutencao()
    {
        location.href = "<?= site_url("indicador/manutencao") ?>";
    }
	
	function excluir()
	{
		location.href = "<?= site_url("indicador_plugin/exp_adesao_potencial_inpel/excluir/".$row["cd_exp_adesao_potencial_inpel"]) ?>";
	}
		
	$(function() {
		$("#mes_referencia").focus();
	});
</script>
<?php

if(count($tabela) == 0)
{
	echo '<span style="font-size: 12pt; color:red; font-weight:bold;">Nenhum período aberto para criar a tabela do indicador.</span>';
	exit;
}
else if(count($tabela) > 1)
{
	echo '<span style="font-size: 12pt; color:red; font-weight:bold;">Existe mais de um período aberto, no entanto só será possível incluir valores para o novo período depois de fechar o mais antigo.</span>';
	exit;			
}

$abas[] = array('aba_lista', 'Lista', false, 'manutencao();');
$abas[] = array('aba_lista', 'Lançamento', false, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');
?>

<?= aba_start($abas)?>
	<?= form_open('indicador_plugin/exp_adesao_potencial_inpel/salvar')?>
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
			<?= form_default_hidden('cd_exp_adesao_potencial_inpel', '', intval($row['cd_exp_adesao_potencial_inpel'])) ?>
			<?= form_default_hidden('dt_referencia', '', $row['dt_referencia']) ?> 
			
			<?= form_default_integer('ano_referencia', 'Ano : (*)', $row['ano_referencia'])?>
			<?= form_default_integer('nr_valor_3', $label_1.': (*)', $row['nr_valor_3'], 'class="indicador_text"')?> 
			<?= form_default_integer('nr_valor_1', $label_2.': (*)', $row['nr_valor_1'], 'class="indicador_text"')?> 
			<?= form_default_integer('nr_valor_2', $label_3.': (*)', $row['nr_valor_2'], 'class="indicador_text"')?>
			<?= form_default_numeric('nr_meta', $label_5.': (*)', number_format($row['nr_meta'],2,",","."), 'class="indicador_text"')?>
			<?= form_default_textarea('observacao', $label_6.': ', $row['observacao'])?>
		<?= form_end_box('default_box')?>
		<?= form_command_bar_detail_start()?>
			<?= button_save()?>

			<? if(intval($row['cd_exp_adesao_potencial_inpel']) > 0) : ?>
				<?= button_save('Excluir', 'excluir();', 'botao_vermelho')?>
			<? endif; ?>

		<?= form_command_bar_detail_end()?>
	<?= form_close()?>
	<?= br()?>
<?= aba_end()?>
<?=$this->load->view('footer_interna')?>