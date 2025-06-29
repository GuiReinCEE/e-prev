<?php
	set_title('Reclama��es e Sugest�es - Retorno');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('dt_retorno', 'cd_reclamacao_retorno')) ?>
	
	function ir_lista()
	{
		location.href = "<?= site_url('ecrm/reclamacao') ?>";
	}
	
	function ir_cadastro()
	{
		location.href = "<?= site_url('ecrm/reclamacao/cadastro/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
	}

	function ir_anexo()
	{
		location.href = "<?= site_url('ecrm/reclamacao/anexo/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
	}

	function ir_atendimento()
	{
		location.href = "<?= site_url('ecrm/reclamacao/atendimento/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
	}	

	function ir_prorrogacao()
	{
		location.href = "<?= site_url('ecrm/reclamacao/prorrogacao/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
	}

	function ir_reencaminhamento()
	{
		location.href = "<?= site_url('ecrm/reclamacao/reencaminhamento/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
	}
	
	function ir_acompanhamento()
	{
		location.href = "<?= site_url('ecrm/reclamacao/acompanhamento/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
	}

	function ir_acao()
	{
		location.href = "<?= site_url('ecrm/reclamacao/acao/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
	}

	function ir_validacao()
	{
		location.href = "<?= site_url('ecrm/reclamacao/validacao_comite/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
	}

	function ir_parecer_final()
	{
		location.href = "<?= site_url('ecrm/reclamacao/parecer_comite_avaliacao/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
	}

	$(function() 
	{
		default_conceito_box_box_recolher();
	});
</script>
<style>
    #conceito_item {
        white-space:normal !important;
    }
</style>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_reclamacao', 'Cadastro', FALSE, 'ir_cadastro();');

	if($permissao['fl_aba_atendimento'])
	{	
	//	$abas[] = array('aba_atendimento', 'Atendimento', FALSE, 'ir_atendimento();');
	}

	if($permissao['fl_aba_prorrogacao'])
	{	
		$abas[] = array('aba_reencaminahemnto', 'Reencaminhamento', FALSE, 'ir_reencaminhamento();');
		$abas[] = array('aba_prorrogacao', 'Prorroga��o', FALSE, 'ir_prorrogacao();');
	}

	$abas[] = array('aba_acompanhamento', 'Acompanhamento', FALSE, 'ir_acompanhamento();');
	$abas[] = array('aba_anexo', 'Anexo', FALSE, 'ir_anexo();');

	if($permissao['fl_aba_acao'])
	{
		$abas[] = array('aba_acao', 'A��o', FALSE, 'ir_acao();');
	}
	
	$abas[] = array('aba_retorno', 'Retorno', TRUE, 'location.reload();');
	
	if($permissao['fl_aba_comite'])
	{
		$abas[] = array('aba_validacao_comite', 'Valida��o Comit�', FALSE, 'ir_validacao();');
	}	

	if($permissao['fl_aba_parecer_final'])
	{
		$abas[] = array('aba_parecer_final', 'Parecer Final', FALSE, 'ir_parecer_final();');
	}	

	echo aba_start($abas);
		echo form_start_box('default_conceito_box', 'Coneceito da Tela');
			echo form_default_row('conceito', 'Retorno:', 'Registro da data, da forma  de retorno ao Participante, assim como do retorno que foi dado.');
		echo form_end_box('default_conceito_box');
		echo form_start_box('default_reclamacao_box', 'Reclama��o');
			echo form_default_row('numero', 'N�mero:', $reclamacao['cd_reclamacao']);

			if(intval($reclamacao['cd_usuario_responsavel']) > 0)
			{
				echo form_default_row('dt_prazo_acao', 'Dt. Prazo A��o:', '<span class="label label-inverse">'.$reclamacao['dt_prazo_acao'].'</span>');
				
				if(trim($reclamacao['dt_prorrogacao_acao']) != '')
				{
					echo form_default_row('dt_prorrogacao_acao', 'Dt. Prorroga��o A��o:', '<span class="label label-info">'.$reclamacao['dt_prorrogacao_acao'].'</span>');
				}

				echo form_default_row('dt_prazo', 'Dt. Prazo Classifica��o:', '<span class="label label-inverse">'.$reclamacao['dt_prazo'].'</span>');
				
				if(trim($reclamacao['dt_prorrogacao']) != '')
				{
					echo form_default_row('dt_prorrogacao', 'Dt. Prorroga��o Classifica��o:', '<span class="label label-info">'.$reclamacao['dt_prorrogacao'].'</span>');
				}
			}
			
		echo form_end_box('default_reclamacao_box');
		echo form_open('ecrm/reclamacao/salvar_retorno');
			echo form_start_box('default_box', 'Cadastro');
				echo form_default_hidden('numero', '', $row['numero']);
				echo form_default_hidden('ano', '', $row['ano']);
				echo form_default_hidden('tipo', '', $row['tipo']);

				echo form_default_date('dt_retorno', 'Dt. Retorno: (*)', $retorno_atendimento);
				echo form_default_dropdown('cd_reclamacao_retorno', 'Forma: (*)', $reclamacao_retorno, $retorno_atendimento['cd_reclamacao_retorno']);
				echo form_default_textarea('ds_observacao_retorno', 'Descri��o do Retorno:', $retorno_atendimento['ds_observacao_retorno'], 'style="width:500px; height: 100px;"');
			echo form_end_box('default_box');

			echo form_command_bar_detail_start();
				if(trim($retorno_atendimento['dt_retorno']) == '' AND $permissao['fl_acao_retorno'])
				{		
					echo button_save('Salvar');
				}
			echo form_command_bar_detail_end();
		echo form_close();

		echo br(2);
	echo aba_end();

	$this->load->view('footer_interna');
?>