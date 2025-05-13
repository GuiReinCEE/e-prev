<?= set_title($tabela[0]['ds_indicador']) ?>
<?= $this->load->view('header') ?>

<script>
	<?= form_default_js_submit(array('cd_indicador_tabela', 'mes_referencia', 'ano_referencia', 'nr_pessoal_cad', 'nr_pessoal_emp', 'nr_pessoal_inv', 'nr_pessoal_pre', 'nr_pessoal_seg', 'nr_telefonico_cad', 'nr_telefonico_emp', 'nr_telefonico_inv', 'nr_telefonico_pre', 'nr_telefonico_seg', 'nr_email_cad', 'nr_email_emp', 'nr_email_inv', 'nr_email_pre', 'nr_email_seg'), 'validacao(form);') ?>
	
	function validacao(form)
	{
		if($('#nr_ano_periodo').val() != $('#ano_referencia').val())
		{
			var alert = "ERRO\n\n"+
                        "ANO ("+$('#ano_referencia').val()+") do lançamento diferente do ANO ("+$('#nr_ano_periodo').val()+") do período."; 

			alert(alert);

			$("#ano_referencia").focus();
		}
		else
		{
			$("#dt_referencia").val("01/"+$("#mes_referencia").val()+"/"+$("#ano_referencia").val());

			if(confirm('Salvar?'))
			{
				form.submit();
			}
		}
	}	

    function ir_lista()
	{
		location.href = "<?= site_url('indicador_plugin/atend_atendimentos_programa') ?>";
	}

    function manutencao()
    {
        location.href = "<?= site_url('indicador/manutencao') ?>";
    } 	
	
	function excluir()
	{
		var confirmacao = "Deseja Excluir?\n\n"+
                          "Clique [Ok] para Sim\n\n"+
                          "Clique [Cancelar] para Não\n\n"; 

		if(confirm(confirmacao))
		{
			location.href = "<?= site_url('indicador_plugin/atend_atendimentos_programa/excluir/'.$row['cd_atend_atendimentos_programa']) ?>";
		}
	}
	
	$(function() {
		$("#mes_referencia").focus();
	});	
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', false, 'manutencao();');
	$abas[] = array('aba_lancamento','Lançamento', false, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', true, 'location.reload();');
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

	<?= form_open('indicador_plugin/atend_atendimentos_programa/salvar') ?>
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
			<?= form_default_hidden('cd_atend_atendimentos_programa', '', intval($row['cd_atend_atendimentos_programa'])) ?>
			<?= form_default_hidden('dt_referencia', '', $row['dt_referencia']) ?>

			<?= form_default_mes_ano('mes_referencia', 'ano_referencia', $label_0." (*) : ", $row['dt_referencia']) ?>
			<?= form_default_textarea('observacao', $label_5.' :', $row['observacao']) ?>
		<?= form_end_box('default_box') ?>

		<?= form_start_box('default_box', 'Pessoal') ?>
			<?= form_default_integer('nr_pessoal_cad', 'Nº Cadastro (*) :', $row['nr_pessoal_cad'], 'class="indicador_text"') ?>
			<?= form_default_integer('nr_pessoal_emp', 'Nº Empréstimo (*) :', $row['nr_pessoal_emp'], 'class="indicador_text"') ?>
			<?= form_default_integer('nr_pessoal_inv', 'Nº Investimento (*) :', $row['nr_pessoal_inv'], 'class="indicador_text"') ?>
			<?= form_default_integer('nr_pessoal_pre', 'Nº Previdenciário (*) :', $row['nr_pessoal_pre'], 'class="indicador_text"') ?>
			<?= form_default_integer('nr_pessoal_seg', 'Nº Seguro (*) :', $row['nr_pessoal_seg'], 'class="indicador_text"') ?>
		<?= form_end_box('default_box') ?>
		
		<?= form_start_box('default_box', 'Telefônico') ?>
			<?= form_default_integer('nr_telefonico_cad', 'Nº Cadastro (*) :', $row['nr_telefonico_cad'], 'class="indicador_text"') ?>
			<?= form_default_integer('nr_telefonico_emp', 'Nº Empréstimo (*) :', $row['nr_telefonico_emp'], 'class="indicador_text"') ?>
			<?= form_default_integer('nr_telefonico_inv', 'Nº Investimento (*) :', $row['nr_telefonico_inv'], 'class="indicador_text"') ?>
			<?= form_default_integer('nr_telefonico_pre', 'Nº Previdenciário (*) :', $row['nr_telefonico_pre'], 'class="indicador_text"') ?>
			<?= form_default_integer('nr_telefonico_seg', 'Nº Seguro (*) :', $row['nr_telefonico_seg'], 'class="indicador_text"') ?>
		<?= form_end_box('default_box') ?>
		
		<?= form_start_box('default_box', 'E-mail') ?>
			<?= form_default_integer('nr_email_cad', 'Nº Cadastro (*) :', $row['nr_email_cad'], 'class="indicador_text"') ?>
			<?= form_default_integer('nr_email_emp', 'Nº Empréstimo (*) :', $row['nr_email_emp'], 'class="indicador_text"') ?>
			<?= form_default_integer('nr_email_inv', 'Nº Investimento (*) :', $row['nr_email_inv'], 'class="indicador_text"') ?>
			<?= form_default_integer('nr_email_pre', 'Nº Previdenciário (*) :', $row['nr_email_pre'], 'class="indicador_text"') ?>
			<?= form_default_integer('nr_email_seg', 'Nº Seguro (*) :', $row['nr_email_seg'], 'class="indicador_text"') ?>
		<?= form_end_box('default_box') ?>


<?= form_start_box('default_box', 'Whatsapp') ?>
			<?= form_default_integer('nr_whatsapp_cad', 'Nº Cadastro (*) :', $row['nr_whatsapp_cad'], 'class="indicador_text"') ?>
			<?= form_default_integer('nr_whatsapp_emp', 'Nº Empréstimo (*) :', $row['nr_whatsapp_emp'], 'class="indicador_text"') ?>
			<?= form_default_integer('nr_whatsapp_inv', 'Nº Investimento (*) :', $row['nr_whatsapp_inv'], 'class="indicador_text"') ?>
			<?= form_default_integer('nr_whatsapp_pre', 'Nº Previdenciário (*) :', $row['nr_whatsapp_pre'], 'class="indicador_text"') ?>
			<?= form_default_integer('nr_whatsapp_seg', 'Nº Seguro (*) :', $row['nr_whatsapp_seg'], 'class="indicador_text"') ?>
		<?= form_end_box('default_box') ?>

		<?= form_start_box('default_box', 'Virtual') ?>
			<?= form_default_integer('nr_virtual_cad', 'Nº Cadastro (*) :', $row['nr_virtual_cad'], 'class="indicador_text"') ?>
			<?= form_default_integer('nr_virtual_emp', 'Nº Empréstimo (*) :', $row['nr_virtual_emp'], 'class="indicador_text"') ?>
			<?= form_default_integer('nr_virtual_inv', 'Nº Investimento (*) :', $row['nr_virtual_inv'], 'class="indicador_text"') ?>
			<?= form_default_integer('nr_virtual_pre', 'Nº Previdenciário (*) :', $row['nr_virtual_pre'], 'class="indicador_text"') ?>
			<?= form_default_integer('nr_virtual_seg', 'Nº Seguro (*) :', $row['nr_virtual_seg'], 'class="indicador_text"') ?>
		<?= form_end_box('default_box') ?>

		<?= form_start_box('default_box', 'Consulta') ?>
			<?= form_default_integer('nr_consulta_cad', 'Nº Cadastro (*) :', $row['nr_consulta_cad'], 'class="indicador_text"') ?>
			<?= form_default_integer('nr_consulta_emp', 'Nº Empréstimo (*) :', $row['nr_consulta_emp'], 'class="indicador_text"') ?>
			<?= form_default_integer('nr_consulta_inv', 'Nº Investimento (*) :', $row['nr_consulta_inv'], 'class="indicador_text"') ?>
			<?= form_default_integer('nr_consulta_pre', 'Nº Previdenciário (*) :', $row['nr_consulta_pre'], 'class="indicador_text"') ?>
			<?= form_default_integer('nr_consulta_seg', 'Nº Seguro (*) :', $row['nr_consulta_seg'], 'class="indicador_text"') ?>
		<?= form_end_box('default_box') ?>
		
		<?= form_command_bar_detail_start() ?>

			<?= button_save() ?>

			<? if(intval($row['cd_atend_atendimentos_programa']) > 0) :?>	
				<?= button_save('Excluir', 'excluir()', 'botao_vermelho') ?>
			<? endif; ?>
		<?= form_command_bar_detail_end() ?>
	<?= form_close() ?>

<? endif; ?>
</br></br>
<?= aba_end() ?>

<?= $this->load->view('footer') ?>