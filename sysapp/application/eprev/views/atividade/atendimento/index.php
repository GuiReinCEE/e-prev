<?php
	$this->load->view('header_interna', array('topo_titulo'=>'Atividades') );
?>
<script>
	<?php echo form_default_js_submit( array( 'cod_solicitante', 'area', 'tipo_solicitacao', 'tipo', 'titulo', 'descricao', 'problema', 'cod_atendente') ); ?>
	/**
	 * Controla o clique na aba Solicitação
	 * 
	 * @param object 	ob	Objeto que invocou o evento
	 */
	function aba_solicitacao_click(ob)
	{
		return true;
	}

	<?php if(intval($atividade['numero'])>0): ?>
	function aba_atendimento_click(ob)
	{
		if(confirm('Ir para atendimento?'))
		{
			location.href='<?php echo site_url('atividade/atendimento/aba_atendimento_index/'.$atividade['numero']); ?>';
		}
		else
		{
			return false;
		}
	}
	<?php endif; ?>
</script>
<?
echo form_open('atividade/atendimento/salvar_solicitacao');

	// monta abas

	$abas[0] = array('aba_solicitacao', 'Solicitação', true, 'aba_solicitacao_click(this)');

	if(intval($atividade['numero'])>0)
	{
		$abas[1] = array('aba_atendimento', 'Atendimento', false, 'aba_atendimento_click(this)');
		$abas[2] = array('aba_anexos', 'Anexos');
		$abas[3] = array('aba_historico', 'Histórico');
	}

	echo aba_start( $abas );
	echo aba_end( 'solicitacao');

	// monta formulário

	if(intval($atividade['numero'])>0)
	{
		echo form_start_box('atividade', 'Atividade');
		echo form_default_text( 'numero', "Número", $atividade, "readonly style='border:none;'" );
		echo form_default_text( 'dt_cad', "Data de solicitação", $atividade, "readonly style='border:none;'" );
		echo form_end_box('atividade');
	}

	echo form_start_box('solicitante', 'Solicitante');
		if($atividade['numero']>0)
		{
			$divisao=$atividade['divisao']; 
			$cod_solicitante=$atividade['cod_solicitante'];
		}
		else
		{
			$divisao=$this->session->userdata('divisao');
			$cod_solicitante=$this->session->userdata('codigo');
		}

		echo form_default_usuario_ajax( array("divisao", "cod_solicitante"), $divisao, $cod_solicitante, "Usuário: *" );
	echo form_end_box('solicitante');

	echo form_start_box("atendente", "Atendente");
		echo form_default_text("area", "Gerência de destino", "GAP", "readonly style='border:none;'" );
		echo form_default_usuario_dropdown("cod_atendente", "Atendente da atividade *", "GAP", $atividade['cod_atendente']);
		echo form_default_date("dt_limite", "Data Limite:", $atividade);
	echo form_end_box("atendente");

	echo form_start_box("detalhes", "Detalhes");

		echo form_default_dropdown("tipo_solicitacao", "Tipo de manutenção *", $tipo_manutencao_dd, array($atividade['tipo_solicitacao']));
		echo form_default_dropdown("tipo", "Tipo da atividade *", $tipo_atividade_dd, array($atividade['tipo']));
		echo form_default_text("titulo", "Título *", $atividade, "maxlenght=200 size=50");
		echo form_default_textarea("descricao", "Descrição *", $atividade, "style='width:500px;height:150px;'");
		echo form_default_textarea("problema", "Justificativa *", $atividade, "style='width:500px;height:100px;'");

	echo form_end_box("detalhes");

	echo form_start_box('participante', "Atendimento ao Participante");

		echo form_default_participante(array('cd_empresa','cd_registro_empregado','cd_sequencia', 'nome_participante'),'Participante',$atividade);
		echo form_default_dropdown('cd_plano', 'Plano', $plano_dd, array($atividade['cd_plano']));
		echo form_default_dropdown('solicitante', 'Perfil de solicitante', $perfil_solicitante_dd, array($atividade['solicitante']));
		echo form_default_dropdown('forma', 'Forma de solicitação', $forma_solicitacao_dd, array($atividade['forma']));
		echo form_default_dropdown('tp_envio', 'Forma de envio', $forma_envio_dd, array($atividade['tp_envio']));
		echo form_default_text("cd_atendimento", "Protocolo de atendimento", $atividade);

	echo form_end_box('participante');
?>
<div class="aba_conteudo">

	<!-- BARRA DE COMANDOS -->
	<div id="command_bar" class="command-bar">

		<?php if( usuario_id()==$atividade['cod_solicitante'] ): ?>
		<input type="button" value="Salvar" class="botao" style="width:100px;" onclick="salvar(this.form);" />
		<?php endif; ?>

	</div>

</div>
<script>
	$('#divisao').focus();
</script>
<?
echo form_close();
$this->load->view('footer_interna');
?>