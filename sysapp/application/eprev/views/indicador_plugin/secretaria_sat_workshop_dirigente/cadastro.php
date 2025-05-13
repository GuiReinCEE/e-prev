<?php 
	set_title($tabela[0]['ds_indicador']);
	$this->load->view('header'); 
?>
<script>
	<?=form_default_js_submit(array(
			"mes_referencia", 
			"ano_referencia", 
			"cd_indicador_tabela", 
			"nr_respondentes", 
			"nr_satisfeitos", 
			"nr_meta"),
			"_salvar(form)"
		);?>

	function _salvar(form)
	{
		$("#dt_referencia").val("01/01/"+$("#ano_referencia").val());

		if(confirm("Salvar?"))
		{
			form.submit();
		}
	}

	function ir_lancamento()
	{
		location.href = "<?= site_url("indicador_plugin/secretaria_sat_workshop_dirigente") ?>";
	}
	
    function manutencao()
    {
        location.href = "<?= site_url("indicador/manutencao") ?>";
    }
	
	function excluir()
	{
		var text = "Deseja excluir este item?\n\n"+
				   "[OK] para Sim\n\n"+
				   "[Cancelar] para N�o\n\n";

		if(confirm(text))
		{
			location.href = "<?= site_url("indicador_plugin/secretaria_sat_workshop_dirigente/excluir/".$row["cd_secretaria_sat_workshop_dirigente"]) ?>";
		}
	}
		
	$(function() {
		$("#mes_referencia").focus();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'manutencao();');
	$abas[] = array('aba_lancamento', 'Lan�amento', FALSE, 'ir_lancamento();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

	echo aba_start($abas);

	if(count($tabela) == 0):
?>
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
<?php
 	else:

	echo form_start_box('indicador_box', 'Indicador') 
?>
		<tr>
			<td class="coluna-padrao-form">
				<label class="label-padrao-form">Indicador:</label>
			</td>
			<td>
				<span class="label label-inverse"><?= $tabela[0]['ds_indicador'] ?></span>
			</td>
		</tr>
<?php
	echo form_end_box('indicador_box');
	echo form_open('indicador_plugin/secretaria_sat_workshop_dirigente/salvar');
		echo form_start_box('default_box', 'Cadastro');
			echo form_default_hidden('cd_indicador_tabela', 'C�digo indicador tabela', $tabela[0]['cd_indicador_tabela']);
			echo form_default_hidden('cd_secretaria_sat_workshop_dirigente', 'C�digo da tabela', intval($row['cd_secretaria_sat_workshop_dirigente']));
			echo br();
			echo form_default_hidden('dt_referencia', $label_0.': (*)', $row);
			echo form_default_integer('ano_referencia', $label_0.' : (*)', $row['ano_referencia']);
			echo form_default_integer('nr_respondentes', $label_1.' : (*)', $row['nr_respondentes'], "class='indicador_text'"); 
			echo form_default_integer('nr_satisfeitos', $label_2.' : (*)', $row['nr_satisfeitos'], "class='indicador_text'");
			echo form_default_numeric('nr_meta', $label_4.' : (*)', number_format($row['nr_meta'],2,',','.'), "class='indicador_text'");
			echo form_default_textarea('ds_observacao', $label_5.':', $row['ds_observacao']);
		echo form_end_box('default_box');
		echo form_command_bar_detail_start();
			echo button_save();
			if(intval($row['cd_secretaria_sat_workshop_dirigente']) > 0):
				echo button_save('Excluir', 'excluir();', 'botao_vermelho');
			endif;
		echo form_command_bar_detail_end();
	echo form_close();
	echo br();

echo aba_end();
echo$this->load->view('footer_interna');
endif; 
?>