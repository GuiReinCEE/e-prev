<?php 
set_title($tabela[0]['ds_indicador']);
$this->load->view('header'); 
?>
<script>
	<?php 
	echo form_default_js_submit(array("mes_referencia", "ano_referencia", "cd_indicador_tabela", "nr_total_participantes", "nr_total_reclamacoes", "nr_procede",  "nr_nao_procede", "nr_abertas", "nr_meta"),'_salvar(form)');	?>

	function _salvar(form)
	{
		$('#dt_referencia').val('01/'+$('#mes_referencia').val()+'/'+$('#ano_referencia').val());

		if(confirm('Salvar?'))
		{
			form.submit();
		}
	}

	function ir_lista()
	{
		location.href='<?php echo site_url("indicador_plugin/atend_indice_recl"); ?>';
	}
	
    function manutencao()
    {
        location.href='<?php echo site_url("indicador/manutencao/"); ?>';
    }
	
	function excluir()
	{
		location.href = '<?php echo site_url("indicador_plugin/atend_indice_recl/excluir/".$row["cd_atend_indice_recl"]); ?>';
	}
	
	function getValores()
	{
		if(($("#mes_referencia").val() != "") && ($("#ano_referencia").val() != ""))
		{
			$("#msg_importar").show();	
			
			$("#command_bar").hide();
			
			$.post('<?php echo site_url("indicador_plugin/atend_indice_recl/get_valores"); ?>', 
			{
				nr_ano : $("#ano_referencia").val(),
				nr_mes : $("#mes_referencia").val()
			},
			function(data)
			{
				if(data)
				{
					$("#nr_total_reclamacoes").val(data.total);
					$("#nr_nao_procede").val(data.nao_procede);
					$("#nr_procede").val(data.procede);
                    $("#nr_abertas").val(data.abertas);
					$("#observacao").val(data.observacao);
				}
				
				$("#msg_importar").hide();	
				$("#command_bar").show();
			},
			'json');
		}
		else
		{
			alert("Informe o M�s e Ano");
		}
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
<?= form_open('indicador_plugin/atend_indice_recl/salvar') ?>
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
	<?= form_start_box('default_box', 'Cadastro')?>
			<?= form_default_hidden('cd_indicador_tabela', 'C�digo indicador tabela', $tabela[0]['cd_indicador_tabela'])?>
			<?= form_default_hidden('cd_atend_indice_recl', 'C�digo da tabela', intval($row['cd_atend_indice_recl']))?>
			<?= form_default_hidden('dt_referencia', $label_0.": (*)", $row)?> 
			<?= form_default_mes_ano('mes_referencia', 'ano_referencia', $label_0.' :*', $row['dt_referencia'])?>
			<?= form_default_integer("nr_total_participantes", $label_1.' :', ($row['nr_total_participantes']), "class='indicador_text'")?> 
			<?= form_default_integer("nr_total_reclamacoes", $label_2.' :', ($row['nr_total_reclamacoes']), "class='indicador_text'")?>
            <?= form_default_integer("nr_procede", $label_3.' :', ($row['nr_procede']), "class='indicador_text'")?>
			<?= form_default_integer("nr_nao_procede", $label_4.' :', ($row['nr_nao_procede']), "class='indicador_text'")?>
            <?= form_default_integer("nr_abertas", $label_6.' :', ($row['nr_abertas']), "class='indicador_text'")?>
			<?= form_default_numeric("nr_meta", $label_14.' :', number_format($row['nr_meta'],2,",","."), "class='indicador_text'")?>
			<?= form_default_textarea("observacao", $label_16.":", $row['observacao'])?>
			<?= form_default_row("", "",'<span style="font-size: 130%; font-weight: bold; color:red; display:none;" id="msg_importar">Aguarde, importando os dados.</span>')?>
		<?= form_end_box("default_box")?>
		<?= form_command_bar_detail_start()?>
			<?= button_save()?>
			<?= button_save('Importar Valores', 'getValores();', 'botao_verde')?>

			<? if(intval($row['cd_atend_indice_recl']) > 0):?>
			
				<?= button_save('Excluir', 'excluir();', 'botao_vermelho')?>
			<? endif; ?>

		<?= form_command_bar_detail_end()?>
	<?= form_close()?>
<? endif; ?>
	<?= br()?>
<?= aba_end()?>
<?= $this->load->view('footer_interna')?>
