<?= set_title($tabela[0]['ds_indicador']) ?>
<?= $this->load->view('header') ?>

<script>
	<?= form_default_js_submit(array(
					'cd_indicador_tabela', 
					'mes_referencia', 
					'ano_referencia', 
					'nr_valor_1', 
					'nr_meta', 
					'nr_inpc', 
					'nr_atuarial_projetado', 
					'nr_poder_meta',
					'nr_acumulado_ceee',
				    'nr_benchmark_ceee',

				    'nr_acumulado_rge',
				    'nr_benchmark_rge',

				    'nr_acumulado_aes',
				    'nr_benchmark_aes',

				    'nr_acumulado_ceeeprev',
				    'nr_benchmark_ceeeprev',

				    'nr_acumulado_senge',
				    'nr_benchmark_senge',

				    'nr_acumulado_fam_corp',
				    'nr_benchmark_fam_corp',

				    'nr_acumulado_fam_assoc',
				    'nr_benchmark_fam_assoc',

				    'nr_acumulado_ceranprev',
				    'nr_benchmark_ceranprev',

				    'nr_acumulado_fozprev',
				    'nr_benchmark_fozprev',

				    'nr_acumulado_crmprev',
				    'nr_benchmark_cremprev',

				    'nr_acumulado_municipio',
				    'nr_benchmark_municipio',

				    'nr_acumulado_ieab',
				    'nr_benchmark_ieab',

				    'nr_acumulado_pga',
				    'nr_benchmark_pga'

				 ), 'validacao(form);') ?>
	
	function validacao(form)
	{
		if($('#nr_ano_periodo').val() != $('#ano_referencia').val())
		{
			alert("ERRO\n\nANO ("+$('#ano_referencia').val()+") do lançamento diferente do ANO ("+$('#nr_ano_periodo').val()+") do período\n\n");

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
		location.href = "<?= site_url('indicador_plugin/investimento_rentabilidade_carteira') ?>";
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
			location.href = "<?= site_url('indicador_plugin/investimento_rentabilidade_carteira/excluir/'.$row['cd_investimento_rentabilidade_carteira']) ?>";
		}
	}
	function get_valores()
{
	if(($("#mes_referencia").val() != "") && ($("#ano_referencia").val() != ""))
	{
		$("#msg_importar").show();	
		
		$("#command_bar").hide();
		
		$.post("<?= site_url('indicador_plugin/investimento_rentabilidade_carteira/get_valores') ?>", 
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
	<? if(intval($row['qt_ano']) == 0): ?>

		<div style="width:100%; text-align:center;">
			<span style="font-size: 12pt; color:red; font-weight:bold;">
				Informar no campo de 'observações' se pretende manter ou fazer ajustes na meta do indicador, e justificar essa decisão.
			</span>
		</div>

	<? endif; ?>
	<?= form_open('indicador_plugin/investimento_rentabilidade_carteira/salvar') ?>
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

		<?= form_end_box('indicador_box')?> 
<?php
			echo form_start_box('default_box', 'Cadastro');
				echo form_default_hidden('cd_indicador_tabela', '', $tabela[0]['cd_indicador_tabela']);
				echo form_default_hidden('nr_ano_periodo', '', $tabela[0]['nr_ano_referencia']);
				echo form_default_hidden('cd_investimento_rentabilidade_carteira', '', intval($row['cd_investimento_rentabilidade_carteira']));
				echo form_default_hidden('dt_referencia', '', $row['dt_referencia']);

				echo form_default_mes_ano('mes_referencia', 'ano_referencia', $label_0.' (*) : ', $row['dt_referencia']);

				echo form_default_numeric('nr_valor_1', $label_1.' (*) :', number_format($row['nr_valor_1'], 4, ',', '.'), 'class="indicador_text"', array("centsLimit" => 4));
				echo form_default_numeric('nr_meta', $label_2.' (*) :', number_format($row['nr_meta'], 4, ',', '.'), 'class="indicador_text"', array("centsLimit" => 4));
				echo form_default_numeric('nr_inpc', $label_3.' (*) :', number_format($row['nr_inpc'], 4, ',', '.'), 'class="indicador_text"', array("centsLimit" => 4)) ;
				echo form_default_numeric('nr_atuarial_projetado', $label_8.' (*) :', number_format($row['nr_atuarial_projetado'], 4, ',', '.'), 'class="indicador_text"', array("centsLimit" => 4));
				echo form_default_textarea('observacao', $label_13.' :', $row['observacao']);
				echo form_default_row("", "",'<span style="font-size: 130%; font-weight: bold; color:red; display:none;" id="msg_importar">Aguarde, importando os dados.</span>');
			echo form_end_box('default_box');

			echo form_start_box('default_box_PUGC', 'Plano Único Grupo CEEE');
				echo form_default_numeric('nr_acumulado_ceee', $label_11.' (*) :', number_format($row['nr_acumulado_ceee'], 4, ',', '.'), 'class="indicador_text"', array("centsLimit" => 4));
				echo form_default_numeric('nr_benchmark_ceee', $label_12.' (*) :', number_format($row['nr_benchmark_ceee'], 4, ',', '.'), 'class="indicador_text"', array("centsLimit" => 4));
			echo form_end_box('default_box_PUGC');

			echo form_start_box('default_box_RGE', 'Plano Único RGE I');
				echo form_default_numeric('nr_acumulado_rge', $label_11.' (*) :', number_format($row['nr_acumulado_rge'], 4, ',', '.'), 'class="indicador_text"', array("centsLimit" => 4));
				echo form_default_numeric('nr_benchmark_rge', $label_12.' (*) :', number_format($row['nr_benchmark_rge'], 4, ',', '.'), 'class="indicador_text"', array("centsLimit" => 4));
			echo form_end_box('default_box_RGE');

			echo form_start_box('default_box_AESS', 'Plano Único RGE II');
				echo form_default_numeric('nr_acumulado_aes', $label_11.' (*) :', number_format($row['nr_acumulado_aes'], 4, ',', '.'), 'class="indicador_text"', array("centsLimit" => 4));
				echo form_default_numeric('nr_benchmark_aes', $label_12.' (*) :', number_format($row['nr_benchmark_aes'], 4, ',', '.'), 'class="indicador_text"', array("centsLimit" => 4));
			echo form_end_box('default_box_AESS');

			echo form_start_box('default_box_CP', 'CeeePrev');
				echo form_default_numeric('nr_acumulado_ceeeprev', $label_11.' (*) :', number_format($row['nr_acumulado_ceeeprev'], 4, ',', '.'), 'class="indicador_text"', array("centsLimit" => 4));
				echo form_default_numeric('nr_benchmark_ceeeprev', $label_12.' (*) :', number_format($row['nr_benchmark_ceeeprev'], 4, ',', '.'), 'class="indicador_text"', array("centsLimit" => 4));
			echo form_end_box('default_box_CP');

			echo form_start_box('default_box_SEN', 'SENGE');
				echo form_default_numeric('nr_acumulado_senge', $label_11.' (*) :', number_format($row['nr_acumulado_senge'], 4, ',', '.'), 'class="indicador_text"', array("centsLimit" => 4));
				echo form_default_numeric('nr_benchmark_senge', $label_12.' (*) :', number_format($row['nr_benchmark_senge'], 4, ',', '.'), 'class="indicador_text"', array("centsLimit" => 4));
			echo form_end_box('default_box_SEN');


			echo form_start_box('default_box_FC', 'Família Corporativo');
				echo form_default_numeric('nr_acumulado_fam_corp', $label_11.' (*) :', number_format($row['nr_acumulado_fam_corp'], 4, ',', '.'), 'class="indicador_text"', array("centsLimit" => 4));
				echo form_default_numeric('nr_benchmark_fam_corp', $label_12.' (*) :', number_format($row['nr_benchmark_fam_corp'], 4, ',', '.'), 'class="indicador_text"', array("centsLimit" => 4));
			echo form_end_box('default_box_FC');

			echo form_start_box('default_box_FA', 'Família Associativo');
				echo form_default_numeric('nr_acumulado_fam_assoc', $label_11.' (*) :', number_format($row['nr_acumulado_fam_assoc'], 4, ',', '.'), 'class="indicador_text"', array("centsLimit" => 4));
				echo form_default_numeric('nr_benchmark_fam_assoc', $label_12.' (*) :', number_format($row['nr_benchmark_fam_assoc'], 4, ',', '.'), 'class="indicador_text"', array("centsLimit" => 4));
			echo form_end_box('default_box_FA');

			echo form_start_box('default_box_CP', 'CeranPrev');
				echo form_default_numeric('nr_acumulado_ceranprev', $label_11.' (*) :', number_format($row['nr_acumulado_ceranprev'], 4, ',', '.'), 'class="indicador_text"', array("centsLimit" => 4));
				echo form_default_numeric('nr_benchmark_ceranprev', $label_12.' (*) :', number_format($row['nr_benchmark_ceranprev'], 4, ',', '.'), 'class="indicador_text"', array("centsLimit" => 4));
			echo form_end_box('default_box_CP');

			echo form_start_box('default_box_FP', 'FozPrev');
				echo form_default_numeric('nr_acumulado_fozprev', $label_11.' (*) :', number_format($row['nr_acumulado_fozprev'], 4, ',', '.'), 'class="indicador_text"', array("centsLimit" => 4));
				echo form_default_numeric('nr_benchmark_fozprev', $label_12.' (*) :', number_format($row['nr_benchmark_fozprev'], 4, ',', '.'), 'class="indicador_text"', array("centsLimit" => 4));
			echo form_end_box('default_box_FP');

			echo form_start_box('default_box_CRMP', 'CRMPrev');
				echo form_default_numeric('nr_acumulado_crmprev', $label_11.' (*) :', number_format($row['nr_acumulado_crmprev'], 4, ',', '.'), 'class="indicador_text"', array("centsLimit" => 4));
				echo form_default_numeric('nr_benchmark_cremprev', $label_12.' (*) :', number_format($row['nr_benchmark_cremprev'], 4, ',', '.'), 'class="indicador_text"', array("centsLimit" => 4));
			echo form_end_box('default_box_CRMP');

			echo form_start_box('default_box_municipio', 'Família Municípios');
				echo form_default_numeric('nr_acumulado_municipio', $label_11.' (*) :', number_format($row['nr_acumulado_municipio'], 4, ',', '.'), 'class="indicador_text"', array("centsLimit" => 4));
				echo form_default_numeric('nr_benchmark_municipio', $label_12.' (*) :', number_format($row['nr_benchmark_municipio'], 4, ',', '.'), 'class="indicador_text"', array("centsLimit" => 4));
			echo form_end_box('default_box_municipio');

			echo form_start_box('default_box_ieab', 'IEAB Prev');
				echo form_default_numeric('nr_acumulado_ieab', $label_11.' (*) :', number_format($row['nr_acumulado_ieab'], 4, ',', '.'), 'class="indicador_text"', array("centsLimit" => 4));
				echo form_default_numeric('nr_benchmark_ieab', $label_12.' (*) :', number_format($row['nr_benchmark_ieab'], 4, ',', '.'), 'class="indicador_text"', array("centsLimit" => 4));
			echo form_end_box('default_box_ieab');

			echo form_start_box('default_box_PGA', 'PGA');
				echo form_default_numeric('nr_acumulado_pga', $label_11.' (*) :', number_format($row['nr_acumulado_pga'], 4, ',', '.'), 'class="indicador_text"', array("centsLimit" => 4));
				echo form_default_numeric('nr_benchmark_pga', $label_12.' (*) :', number_format($row['nr_benchmark_pga'], 4, ',', '.'), 'class="indicador_text"', array("centsLimit" => 4));
			echo form_end_box('default_box_PGA');

			echo form_command_bar_detail_start();
				echo button_save();
				echo button_save('Importar Valores', 'get_valores();', 'botao_verde');
				if(intval($row['cd_investimento_rentabilidade_carteira']) > 0)
				{
					echo button_save('Excluir', 'excluir()', 'botao_vermelho');
				}
			echo form_command_bar_detail_end();
		echo form_close();
?>
<? endif; ?>
</br></br>
<?= aba_end() ?>

<?= $this->load->view('footer') ?>