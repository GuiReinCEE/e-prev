<?php 
set_title($tabela[0]['ds_indicador']);
$this->load->view('header'); 
?>
<script>
	<?php 
	echo form_default_js_submit(array("mes_referencia", "ano_referencia", "cd_indicador_tabela", "nr_tarefas", "nr_realizadas", "nr_meta"),'_salvar(form)');	?>

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
		location.href = '<?= site_url("indicador_plugin/controladoria_implementacao_projetos") ?>';
	}
	
    function manutencao()
    {
        location.href = '<?= site_url("indicador/manutencao") ?>';
    }
	
	function excluir()
	{
		location.href = '<?= site_url("indicador_plugin/controladoria_implementacao_projetos/excluir/".$row["cd_controladoria_implementacao_projetos"]) ?>';
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

	echo aba_start($abas);
	?>
	<? if(intval($row['qt_ano']) == 0): ?>

		<div style="width:100%; text-align:center;">
			<span style="font-size: 12pt; color:red; font-weight:bold;">
				Informar no campo de 'observa��es' se pretende manter ou fazer ajustes na meta do indicador, e justificar essa decis�o.
			</span>
		</div>

	<? endif; ?>
	
	<?
		echo form_open('indicador_plugin/controladoria_implementacao_projetos/salvar');
			echo form_start_box("default_box", 'Cadastro');
				echo form_default_hidden('cd_indicador_tabela', '', $tabela[0]['cd_indicador_tabela']);
				echo form_default_hidden('cd_controladoria_implementacao_projetos', '', $row['cd_controladoria_implementacao_projetos']);
				echo form_default_hidden('dt_referencia', '', $row); 
				echo form_default_row("", "Indicador:", '<span class="label label-inverse">'.$tabela[0]['ds_indicador'].'</span>'); 
				echo form_default_row("", "Per�odo aberto:", '<span class="label label-important">'.$tabela[0]['ds_periodo'].'</span>'); 
				echo form_default_integer('ano_referencia', 'Ano: (*)', $row['ano_referencia'], 'class="indicador_text"');
	            echo form_default_dropdown('mes_referencia', $label_0.': (*)', $drop, $row['mes_referencia'], 'class="indicador_text"');
				echo form_default_integer("nr_tarefas", $label_2.' :', ($row['nr_tarefas']), "class='indicador_text'");
				echo form_default_integer("nr_realizadas", $label_1.' :', ($row['nr_realizadas']), "class='indicador_text'"); 
				echo form_default_numeric("nr_meta", $label_4.' :', number_format($row['nr_meta'],2,",","."), "class='indicador_text'");
				echo form_default_textarea("ds_observacao", $label_6.":", $row['ds_observacao']);
			echo form_end_box("default_box");
			echo form_command_bar_detail_start();
				echo button_save();
				if(intval($row['cd_controladoria_implementacao_projetos']) > 0)
				{
					echo button_save('Excluir', 'excluir();', 'botao_vermelho');
				}
			echo form_command_bar_detail_end();
		echo form_close();
		echo br();
	echo aba_end();
	$this->load->view('footer_interna');
?>