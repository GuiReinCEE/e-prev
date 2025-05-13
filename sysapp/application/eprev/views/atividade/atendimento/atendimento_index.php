<?php $this->load->view('header_interna', array('topo_titulo'=>'Atividades') ); ?>
<script>

	<?php echo form_default_js_submit( array( 'status_atual') ); ?>

	function aba_solicitacao_click(ob)
	{
		if(confirm('Ir para aba Solicitação?'))
		{
			location.href = '<?php echo site_url('atividade/atendimento/index/'.$atividade['numero']); ?>';
		}
	}
</script>
<?
echo form_open('atividade/atendimento/salvar_atendimento');

	// monta abas
	$abas[0] = array('aba_solicitacao', 'Solicitação', false, 'aba_solicitacao_click(this)');
	$abas[1] = array('aba_atendimento', 'Atendimento', true);
	$abas[2] = array('aba_anexos', 'Anexos');
	$abas[3] = array('aba_historico', 'Histórico');

	echo aba_start( $abas );
	echo aba_end( 'solicitacao');

	echo form_start_box("atendimento", "Atendimento");
	echo form_default_text( 'numero', "Número", $atividade, "readonly style='border:none;'" );
	echo form_default_text( 'dt_cad', "Data de solicitação", $atividade, "readonly style='border:none;'" );
	echo form_default_dropdown('sistema', "Projeto", $projeto_dd, array($atividade['sistema']));
	echo form_default_dropdown('status_atual', "Status: *", $status_dd, array($atividade['status_atual']));
	echo form_end_box("atendimento");

	echo form_start_box("analise", "Análise do Solicitante");
	echo form_default_text( 'dt_env_teste', "Data de envio para análise:", $atividade, "readonly style='border:none;'" );
	echo form_default_date('dt_limite_testes', 'Data limite para análise:', $atividade);

	echo form_default_usuario_ajax("cod_testador", '', $atividade['cod_testador'], 'Responsável pela análise', 'Gerência do responsável:');
	echo form_end_box("analise");

	echo form_start_box("encaminhamento", "Encaminhamento");
	echo form_default_date("dt_inicio_real", "Data de início real", $atividade);
	echo form_default_date( 'dt_fim_real', "Data de fim real", $atividade );
	echo form_default_textarea("solucao", "Descrição da Manutenção:", $atividade);
	echo form_end_box("encaminhamento");

	echo form_start_box("cronograma", "Cronograma");
	echo form_default_dropdown("complexidade", "Complexidade", $complexidade_dd, array($atividade['complexidade']));
	echo form_default_text('numero_dias', 'Dias previstos', $atividade);
	echo form_default_text('periodicidade', 'Periodicidades', $atividade);
	echo form_end_box("cronograma");

?>
<div class="aba_conteudo">

	<!-- BARRA DE COMANDOS -->
	<div id="command_bar" class="command-bar">

		<input type="button" value="Salvar" class="botao" style="width:100px;" onclick="salvar(this.form);" />

	</div>

</div>
<script>
	$('#tipo_solicitacao').focus();
</script>
<?
echo form_close();
$this->load->view('footer_interna');
?>