<?php
set_title('Atividades - Benefícios - Atendimento');
$this->load->view('header');
?>
<script>
	<?php
		echo form_default_js_submit(Array('status_atual'));
	?>
	
	function ir_lista()
    {
        location.href='<?php echo site_url("atividade/minhas"); ?>';
    }
	
	function ir_solicitacao()
    {
		location.href='<?php echo site_url('atividade/atividade_solicitacao/index/'.$ar_atividade['cd_gerencia_destino'].'/'.$ar_atividade['numero']);?>';
    }
	
	function ir_historico()
    {
        location.href='<?php echo site_url('atividade/atividade_historico/index/'.$ar_atividade['numero'].'/'.$ar_atividade['cd_gerencia_destino']);?>';
    }
	
	function ir_acompanhamento()
    {
        location.href='<?php echo site_url('atividade/atividade_acompanhamento/index/'.$ar_atividade['numero'].'/'.$ar_atividade['cd_gerencia_destino']);?>';
    }
	
	function ir_anexo()
    {
        location.href='<?php echo site_url('atividade/atividade_anexo/index/'.$ar_atividade['numero'].'/'.$ar_atividade['cd_gerencia_destino']);?>';
    }

    function imprimir()
    {
    	window.open('<?php echo site_url('atividade/atividade_solicitacao/imprimir/'.$ar_atividade['numero'].'/'.$ar_atividade['cd_gerencia_destino']);?>');
    }
	
	$(function(){
	});
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_lista', 'Solicitação', FALSE, 'ir_solicitacao();');
$abas[] = array('aba_lista', 'Atendimento', TRUE, 'location.reload();');
$abas[] = array('aba_lista', 'Anexo', FALSE, 'ir_anexo();');
$abas[] = array('aba_lista', 'Acompanhamento', FALSE, 'ir_acompanhamento();');
$abas[] = array('aba_lista', 'Histórico', FALSE, 'ir_historico();');

$ar_relevante[] = array('text' => 'Não', 'value' => 'N');
$ar_relevante[] = array('text' => 'Sim', 'value' => 'S');
$ar_balanco_info[] = array('text' => 'Não', 'value' => 'N');
$ar_balanco_info[] = array('text' => 'Sim', 'value' => 'S');

echo aba_start( $abas );
    echo form_open('atividade/atividade_atendimento/salvar');

        echo form_start_box("default_box", "Cadastro");
			echo form_default_hidden('numero', '', intval($ar_atividade['numero']));
			echo form_default_hidden('tipo_ativ', '', trim($ar_atividade['tipo_ativ']));
			echo form_default_hidden('cod_atendente', '', intval($ar_atividade['cod_atendente']));
			echo form_default_hidden('dt_cadastro', '', trim($ar_atividade['dt_cadastro']));
			echo form_default_hidden('dt_env_teste', '', $ar_atividade['dt_env_teste']);
			echo form_default_hidden('cd_gerencia_destino', '', trim($ar_atividade['cd_gerencia_destino']));
			echo form_default_hidden('fl_salvar', '', ($fl_salvar ? "S" : "N"));
			echo form_default_hidden('fl_teste', '', ($fl_teste ? "S" : "N"));
			
			echo form_default_row('numero_1', 'Número:', '<span class="label">'.trim($ar_atividade['numero']).'</span>');
			echo form_default_row('dt_cad', 'Dt Solicitação:', $ar_atividade['dt_cad']);
			echo form_default_row('dt_fim_real_1', 'Dt Fim:', $ar_atividade['dt_fim_real']);
			echo form_default_row('status', 'Status Atual:', '<span class="'.trim($ar_atividade['class_status']).'">'.trim($ar_atividade['status_atividade']).'</span>');
			if(intval($ar_atividade['qt_anexo']) > 0)
			{
				echo form_default_row('', '', '<i>Esta atividade possui anexo(s).</i>');
			}
		echo form_end_box("default_box");	
			
		echo form_start_box("default_encaminhamento", "Encaminhamento");	
			echo form_default_hidden('sistema', '', 211); #Atendimento de atividade de Benefícios
			echo form_default_hidden('status_anterior', '', trim($ar_atividade['status_atual']));
			echo form_default_dropdown('status_atual', 'Status:(*)', $ar_status_atual, $ar_atividade['status_atual']);
			echo form_default_textarea('solucao', 'Descrição da Manutenção:', $ar_atividade['solucao'], 'style="width:500px; height:100px;"');	
		echo form_end_box("default_encaminhamento");	
				
		echo form_command_bar_detail_start();  
			if($fl_salvar)
			{		
				echo button_save("Salvar");
			}
			echo button_save("Imprimir", 'imprimir()', 'botao_disabled');
		echo form_command_bar_detail_end();
    echo form_close();
	
    echo br(10);	
echo aba_end();

$this->load->view('footer_interna');
?>