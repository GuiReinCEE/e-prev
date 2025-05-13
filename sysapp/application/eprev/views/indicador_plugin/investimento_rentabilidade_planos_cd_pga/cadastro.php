<?= set_title($tabela[0]['ds_indicador']) ?>
<?= $this->load->view('header') ?>

<script>
	<?= 
		form_default_js_submit(
			array(
				'cd_indicador_tabela', 
				'mes_referencia', 
				'ano_referencia'
			), 'validacao(form);') 
	?>
	
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
		location.href = "<?= site_url('indicador_plugin/investimento_rentabilidade_planos_cd_pga') ?>";
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
			location.href = "<?= site_url('indicador_plugin/investimento_rentabilidade_planos_cd_pga/excluir/'.$row['cd_investimento_rentabilidade_planos_cd_pga']) ?>";
		}
	}
	
	function get_valores()
	{
		if(($("#mes_referencia").val() != "") && ($("#ano_referencia").val() != ""))
		{
			$("#msg_importar").show();	
			
			$("#command_bar").hide();
			
			$.post("<?= site_url('indicador_plugin/investimento_rentabilidade_planos_cd_pga/get_valores') ?>", 
			{
				nr_ano : $("#ano_referencia").val(),
				nr_mes : $("#mes_referencia").val()
			},
			function(data)
			{
				if(data)
				{
					$.each(data, function (name, value) {
						$("#"+value.ds_caderno_cci_integracao_indicador_campo).val(value.nr_valor);
					});
				}
				
				$("#msg_importar").hide();	
				$("#command_bar").show();
			},
			'json');
		}
		else
		{
			alert("Informe o Mês e Ano");
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

	<?= form_open('indicador_plugin/investimento_rentabilidade_planos_cd_pga/salvar') ?>
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
			<?= form_default_hidden('cd_investimento_rentabilidade_planos_cd_pga', '', intval($row['cd_investimento_rentabilidade_planos_cd_pga'])) ?>
			<?= form_default_hidden('dt_referencia', '', $row['dt_referencia']) ?>
			<?= form_default_mes_ano('mes_referencia', 'ano_referencia', $label_0.' (*) : ', $row['dt_referencia']) ?>
			<?= form_default_textarea('observacao', $label_7.' :', $row['observacao']) ?>
			<?= form_default_row("", "",'<span style="font-size: 130%; font-weight: bold; color:red; display:none;" id="msg_importar">Aguarde, importando os dados.</span>') ?>
		<?= form_end_box('default_box') ?>

		<?= form_start_box('default_ceee_box', 'CEEE PREV') ?>
			<?= form_default_numeric('nr_realizado_ceee_mes', $label_1.' :', number_format($row['nr_realizado_ceee_mes'], 4, ',','.'), 'class="indicador_text"', array('centsLimit' => 4)) ?>
			<?= form_default_numeric('nr_projetado_ceee_ano', $label_5.' :', number_format($row['nr_projetado_ceee_ano'], 4, ',','.'), 'class="indicador_text"', array('centsLimit' => 4)) ?>
			<?= form_default_numeric('nr_bechmark_ceee_mes', $label_3.' :', number_format($row['nr_bechmark_ceee_mes'], 4, ',','.'), 'class="indicador_text"', array('centsLimit' => 4)) ?>
		<?= form_end_box('default_ceee_box') ?>

		<?= form_start_box('default_crm_box', 'CRM PREV') ?>
			<?= form_default_numeric('nr_realizado_crm_mes', $label_1.' :', number_format($row['nr_realizado_crm_mes'], 4, ',','.'), 'class="indicador_text"', array('centsLimit' => 4)) ?>
			<?= form_default_numeric('nr_projetado_crm_ano', $label_5.' :', number_format($row['nr_projetado_crm_ano'], 4, ',','.'), 'class="indicador_text"', array('centsLimit' => 4)) ?>
			<?= form_default_numeric('nr_bechmark_crm_mes', $label_3.' :', number_format($row['nr_bechmark_crm_mes'], 4, ',','.'), 'class="indicador_text"', array('centsLimit' => 4)) ?>
		<?= form_end_box('default_crm_box') ?>

		<?= form_start_box('default_senge_box', 'SENGE PREV') ?>
			<?= form_default_numeric('nr_realizado_senge_mes', $label_1.' :', number_format($row['nr_realizado_senge_mes'], 4, ',','.'), 'class="indicador_text"', array('centsLimit' => 4)) ?>
			<?= form_default_numeric('nr_projetado_senge_ano', $label_5.' :', number_format($row['nr_projetado_senge_ano'], 4, ',','.'), 'class="indicador_text"', array('centsLimit' => 4)) ?>
			<?= form_default_numeric('nr_bechmark_senge_mes', $label_3.' :', number_format($row['nr_bechmark_senge_mes'], 4, ',','.'), 'class="indicador_text"', array('centsLimit' => 4)) ?>
		<?= form_end_box('default_senge_box') ?>

		<?= form_start_box('default_sinpro_box', 'SINPRO PREV') ?>
			<?= form_default_numeric('nr_realizado_sinpro_mes', $label_1.' :', number_format($row['nr_realizado_sinpro_mes'], 4, ',','.'), 'class="indicador_text"', array('centsLimit' => 4)) ?>
			<?= form_default_numeric('nr_projetado_sinpro_ano', $label_5.' :', number_format($row['nr_projetado_sinpro_ano'], 4, ',','.'), 'class="indicador_text"', array('centsLimit' => 4)) ?>
			<?= form_default_numeric('nr_bechmark_sinpro_mes', $label_3.' :', number_format($row['nr_bechmark_sinpro_mes'], 4, ',','.'), 'class="indicador_text"', array('centsLimit' => 4)) ?>
		<?= form_end_box('default_sinpro_box') ?>

		<?= form_start_box('default_familia_box', 'FAMÍLIA') ?>
			<?= form_default_numeric('nr_realizado_familia_mes', $label_1.' :', number_format($row['nr_realizado_familia_mes'], 4, ',','.'), 'class="indicador_text"', array('centsLimit' => 4)) ?>
			<?= form_default_numeric('nr_projetado_familia_ano', $label_5.' :', number_format($row['nr_projetado_familia_ano'], 4, ',','.'), 'class="indicador_text"', array('centsLimit' => 4)) ?>
			<?= form_default_numeric('nr_bechmark_familia_mes', $label_3.' :', number_format($row['nr_bechmark_familia_mes'], 4, ',','.'), 'class="indicador_text"', array('centsLimit' => 4)) ?>
		<?= form_end_box('default_familia_box') ?>

		<?= form_start_box('default_inpel_box', 'INPELPREV') ?>
			<?= form_default_numeric('nr_realizado_inpel_mes', $label_1.' :', number_format($row['nr_realizado_inpel_mes'], 4, ',','.'), 'class="indicador_text"', array('centsLimit' => 4)) ?>
			<?= form_default_numeric('nr_projetado_inpel_ano', $label_5.' :', number_format($row['nr_projetado_inpel_ano'], 4, ',','.'), 'class="indicador_text"', array('centsLimit' => 4)) ?>
			<?= form_default_numeric('nr_bechmark_inpel_mes', $label_3.' :', number_format($row['nr_bechmark_inpel_mes'], 4, ',','.'), 'class="indicador_text"', array('centsLimit' => 4)) ?>
		<?= form_end_box('default_inpel_box') ?>

		<?= form_start_box('default_pga_box', 'PGA') ?>
			<?= form_default_numeric('nr_realizado_pga_mes', $label_1.' :', number_format($row['nr_realizado_pga_mes'], 4, ',','.'), 'class="indicador_text"', array('centsLimit' => 4)) ?>
			<?= form_default_numeric('nr_projetado_pga_ano', $label_5.' :', number_format($row['nr_projetado_pga_ano'], 4, ',','.'), 'class="indicador_text"', array('centsLimit' => 4)) ?>
			<?= form_default_numeric('nr_bechmark_pga_mes', $label_3.' :', number_format($row['nr_bechmark_pga_mes'], 4, ',','.'), 'class="indicador_text"', array('centsLimit' => 4)) ?>
		<?= form_end_box('default_pga_box') ?>

		<?= form_command_bar_detail_start() ?>

			<?= button_save() ?>
			<?= button_save('Importar Valores', 'get_valores();', 'botao_verde') ?>
			
			<? if(intval($row['cd_investimento_rentabilidade_planos_cd_pga']) > 0) :?>	
				<?= button_save('Excluir', 'excluir()', 'botao_vermelho') ?>
			<? endif; ?>
		<?= form_command_bar_detail_end() ?>
	<?= form_close() ?>

<? endif; ?>
</br></br>
<?= aba_end() ?>

<?= $this->load->view('footer') ?>