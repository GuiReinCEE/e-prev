<?php 
set_title($tabela[0]['ds_indicador']);
$this->load->view('header'); 
?>
<script>
	<?php 
	echo form_default_js_submit(array("mes_referencia", "ano_referencia", "cd_indicador_tabela", "nr_valor_1", "nr_valor_2", "nr_meta"),"_salvar(form)");?>

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
		location.href = "<?= site_url("indicador_plugin/controladoria_cobertura_benf_cd_municipio") ?>";
	}
	
    function manutencao()
    {
        location.href = "<?= site_url("indicador/manutencao") ?>";
    }
	
	function excluir()
	{
		location.href = "<?= site_url("indicador_plugin/controladoria_cobertura_benf_cd_municipio/excluir/".$row["cd_controladoria_cobertura_benf_cd_municipio"]) ?>";
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
	<?= form_open('indicador_plugin/controladoria_cobertura_benf_cd_municipio/salvar')?>
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
		<?= form_start_box('default_box', 'Cadastro')?>
			<?= form_default_hidden('cd_indicador_tabela', 'C�digo indicador tabela', $tabela[0]['cd_indicador_tabela'])?>
			<?= form_default_hidden('cd_controladoria_cobertura_benf_cd_municipio', 'C�digo da tabela', intval($row['cd_controladoria_cobertura_benf_cd_municipio']))?>
			<?= form_default_row('','','')?>
			<?= form_default_hidden('dt_referencia', $label_0.': (*)', $row)?>
			<?= form_default_integer('ano_referencia', $label_0.' : (*)', $row['ano_referencia'])?>
			<?= form_default_numeric('nr_valor_1', $label_1.' : (*)', ($row['nr_valor_1']), "class='indicador_text'")?> 
			<?= form_default_numeric('nr_valor_2', $label_2.' : (*)', ($row['nr_valor_2']), "class='indicador_text'")?>
			<?= form_default_numeric('nr_meta', $label_4.' : (*)', number_format($row['nr_meta'],2,',','.'), "class='indicador_text'")?>
			<?= form_default_textarea('observacao', $label_6.':', $row['observacao'])?>
		<?= form_end_box('default_box')?>
		<?= form_command_bar_detail_start()?>
			<?= button_save()?>
			<?if(intval($row['cd_controladoria_cobertura_benf_cd_municipio']) > 0) ?>
				<?= button_save('Excluir', 'excluir();', 'botao_vermelho')?>
			<? endif; ?>
		<?= form_command_bar_detail_end()?>
	<?= form_close()?>
	<?= br()?>

<?= aba_end()?>
<?=$this->load->view('footer_interna')?>
