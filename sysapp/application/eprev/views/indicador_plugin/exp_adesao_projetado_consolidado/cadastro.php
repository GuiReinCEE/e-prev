<?php 
set_title($tabela[0]['ds_indicador']);
$this->load->view('header'); 
?>
<script>
	<?php 
	echo form_default_js_submit(array("mes_referencia", "ano_referencia", "cd_indicador_tabela", "nr_meta"),"_salvar(form)");?>

	function _salvar(form)
	{
		$("#dt_referencia").val('01/'+$('#mes_referencia').val()+'/'+$('#ano_referencia').val());

		if(confirm("Salvar?"))
		{
			form.submit();
		}
	}

	function excluir()
	{
		location.href = '<?= site_url("indicador_plugin/exp_adesao_projetado_consolidado/excluir/".$row["cd_exp_adesao_projetado_consolidado"])?>';
	}

	function ir_lista()
	{
		location.href = "<?= site_url("indicador_plugin/exp_adesao_projetado_consolidado") ?>";
	}
	
    function manutencao()
    {
        location.href = "<?= site_url("indicador/manutencao") ?>";
    }
	
	$(function() {
		$("#mes_referencia").focus();
	});
</script>
<?php

if(count($tabela) == 0)
{
	echo '<span style="font-size: 12pt; color:red; font-weight:bold;">Nenhum per�odo aberto para criar a tabela do indicador.</span>';
	exit;
}
else if(count($tabela) > 1)
{
	echo '<span style="font-size: 12pt; color:red; font-weight:bold;">Existe mais de um per�odo aberto, no entanto s� ser� poss�vel incluir valores para o novo per�odo depois de fechar o mais antigo.</span>';
	exit;			
}
$abas[] = array('aba_lista', 'Lista', false, 'manutencao();');
$abas[] = array('aba_lista', 'Lan�amento', false, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');
?>
<?= aba_start($abas)?>

	<? if(intval($row['qt_ano']) == 0): ?>

		<div style="width:100%; text-align:center;">
			<span style="font-size: 12pt; color:red; font-weight:bold;">
				Informar no campo de 'observa��es' se pretende manter ou fazer ajustes na meta do indicador, e justificar essa decis�o.
			</span>
		</div>

	<? endif; ?>
	

	<?= form_open('indicador_plugin/exp_adesao_projetado_consolidado/salvar')?>
		<?= form_start_box('indicador_box', 'Indicador') ?>
			<?= form_default_hidden('cd_indicador_tabela', 'C�digo indicador tabela', $tabela[0]['cd_indicador_tabela'])?>
			<?= form_default_hidden('cd_exp_adesao_projetado_consolidado', 'C�digo da tabela', intval($row['cd_exp_adesao_projetado_consolidado']))?>
			<?= form_default_row('', 'Indicador:', '<span class="label label-inverse">'.$tabela[0]['ds_indicador'].'</span>')?>
			<?= form_default_row('', 'Per�odo aberto:', '<span class="label label-important">'.$tabela[0]['ds_periodo'].'</span>')?> 
			<?= form_default_row('','','')?>
		<?= form_end_box("default_box")?>

		<?= form_start_box('default_box', 'Cadastro') ?>
			<?= form_default_hidden('dt_referencia', $label_0.': (*)', $row)?> 
			<?= form_default_mes_ano('mes_referencia', 'ano_referencia', $label_0.': (*)', $row['dt_referencia'])?>
			<?= form_default_textarea('ds_observacao', $label_4.': ', $row['ds_observacao'])?>
		<?= form_end_box('default_box')?>
		<?= form_command_bar_detail_start()?>
			<?= button_save()?>

			<? if(intval($row['cd_exp_adesao_projetado_consolidado']) > 0) : ?>
				<?= button_save('Excluir', 'excluir();', 'botao_vermelho')?>
			<? endif; ?>

		<?= form_command_bar_detail_end()?>
	<?= form_close()?>
	<?= br()?>
<?= aba_end()?>
<?=$this->load->view('footer_interna')?>