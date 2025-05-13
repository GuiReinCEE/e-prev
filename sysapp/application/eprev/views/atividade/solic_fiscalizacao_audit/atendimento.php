<?php
	set_title('Registro de Solicitações, Fiscalizações e Auditorias');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('ds_motivo_atendeu')) ?>

	function ir_lista()
    {
        location.href = "<?= site_url('atividade/solic_fiscalizacao_audit') ?>";
    }

    function ir_cadastro()
    {
    	location.href = "<?= site_url('atividade/solic_fiscalizacao_audit/cadastro/'.intval($row['cd_solic_fiscalizacao_audit'])) ?>";
    }

    function ir_prorrogacao()
    {
    	location.href = "<?= site_url('atividade/solic_fiscalizacao_audit/prorrogacao/'.intval($row['cd_solic_fiscalizacao_audit'])) ?>";
    }

    function ir_acompanhamento()
    {
    	location.href = "<?= site_url('atividade/solic_fiscalizacao_audit/acompanhamento/'.intval($row['cd_solic_fiscalizacao_audit'])) ?>";
    }

    function ir_documentacao()
    {
    	location.href = "<?= site_url('atividade/solic_fiscalizacao_audit/documentacao/'.intval($row['cd_solic_fiscalizacao_audit'])) ?>";
    }
</script>
<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
    $abas[] = array('aba_prorrogacao', 'Prorrogação de Prazo', FALSE, 'ir_prorrogacao();');
    $abas[] = array('aba_acompanhamento', 'Acompanhamento', FALSE, 'ir_acompanhamento();');
    $abas[] = array('aba_documentacao', 'Documentação/Informação', FALSE, 'ir_documentacao();');
    $abas[] = array('aba_atendimento', 'Atendimento', TRUE, 'location.reload();');    

    echo aba_start($abas);
    echo form_start_box('default_solicitacao_box', 'Solicitação');
        echo form_default_row('', 'Ano/Nº:', '<span class="label label-inverse">'.$row['ds_ano_numero'].'</i>');
        echo form_default_row('', 'Origem:', $row['ds_solic_fiscalizacao_audit_origem'].(trim($row['ds_origem']) != '' ? ' ('.$row['ds_origem'].')' : ''));
        echo form_default_row('', 'Data Recebimento:', $row['dt_recebimento']);
        echo form_default_row('', 'Tipo:', $row['ds_solic_fiscalizacao_audit_tipo'].(trim($row['ds_tipo']) != '' ? ' ('.$row['ds_tipo'].')' : ''));
        echo form_default_row('', 'Área Consolidadora:', $row['cd_gerencia']);
        
        if(count($row['gestao']) > 0)
        {
            echo form_default_row('', 'Gestão:', implode(', ', $row['gestao']));
        }

        echo form_default_row('', 'Documento:', $row['ds_documento']);
        echo form_default_row('', 'Teor:', $row['ds_teor']);
        echo form_default_row('', 'Dt. Prazo:', $row['dt_prazo']);
        echo form_default_row('', 'Dt. Inclusão:', $row['dt_inclusao']);
        echo form_default_row('', 'Usuário:', $row['ds_usuario_inclusao']);
        echo form_default_row('', 'Dt. Envio:', $row['dt_envio']);
        echo form_default_row('', 'Usuário:', $row['ds_usuario_envio']);

        if(trim($row['dt_envio_solicitacao_documento']) != '')
        {
            echo form_default_row('', 'Dt. Envio Solicitação:', $row['dt_envio_solicitacao_documento']);
            echo form_default_row('', 'Usuário:', $row['ds_usuario_envio_solicitacao_documento']);
        }

        echo form_default_row('', 'Prazo para Retorno:', $documentacao['dt_prazo_retorno']);
            
    echo form_end_box('default_solicitacao_box');
    echo form_open('atividade/solic_fiscalizacao_audit/salvar_atendimento');
        echo form_start_box('default_atendimento_box', 'Atendimento');
            echo form_default_hidden('cd_solic_fiscalizacao_audit', '', $row['cd_solic_fiscalizacao_audit']);
            echo form_default_hidden('cd_solic_fiscalizacao_audit_documentacao', '', $documentacao['cd_solic_fiscalizacao_audit_documentacao']);
            echo form_default_textarea('ds_motivo_atendeu', 'Motivo: (*)', '', 'style="height:80px;"');
            echo form_default_date('dt_prorrogacao_prazo_retorno', 'Novo Prazo:');
        echo form_end_box('default_atendimento_box'); 
        echo form_command_bar_detail_start();
            echo button_save('Salvar');
        echo form_command_bar_detail_end();
    echo form_close();
	echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?>