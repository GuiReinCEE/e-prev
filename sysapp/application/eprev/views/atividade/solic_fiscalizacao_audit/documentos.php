<?php
	set_title('Registro de Solicitações, Fiscalizações e Auditorias');
	$this->load->view('header');
?>
<script>
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

    function remover_documento(cd_solic_fiscalizacao_audit_documentacao_anexo)
    {
        var confirmacao = 'Desejar remover este documento?\n\n'+
                          'Clique [Ok] para Sim\n\n'+
                          'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        { 
            location.href = "<?= site_url('atividade/solic_fiscalizacao_audit/remover_documento/'.intval($row['cd_solic_fiscalizacao_audit'])).'/'.$documentacao['cd_solic_fiscalizacao_audit_documentacao'] ?>/"+cd_solic_fiscalizacao_audit_documentacao_anexo;
        }
    }

	function atendeu(cd_solic_fiscalizacao_audit_documentacao)
	{
		var confirmacao = 'Solicitação foi atendida?\n\n'+
                          'Deseja concluir item?\n\n'+
                          'Clique [Ok] para Sim\n\n'+
                          'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        { 
            location.href = "<?= site_url('atividade/solic_fiscalizacao_audit/atendeu/'.$row['cd_solic_fiscalizacao_audit']) ?>/"+cd_solic_fiscalizacao_audit_documentacao;
        }
	}

    function nao_atendeu(cd_solic_fiscalizacao_audit_documentacao)
    {
        location.href = "<?= site_url('atividade/solic_fiscalizacao_audit/atendimento/'.$row['cd_solic_fiscalizacao_audit']) ?>/"+cd_solic_fiscalizacao_audit_documentacao;
    }

    function check_item(t)
    {
        salvar_item(t.val(), t.is(':checked') ? 'S' : 'N');
    }

    function salvar_item(cd_solic_fiscalizacao_audit_documentacao_anexo, fl_checked)
    {
        $.post("<?= site_url('atividade/solic_fiscalizacao_audit/salvar_encaminhar_documento') ?>",
        {
            cd_solic_fiscalizacao_audit_documentacao_anexo : cd_solic_fiscalizacao_audit_documentacao_anexo,
            fl_salvar                                      : fl_checked
        },
        function(data){
            
        });
    }
</script>
<style>
    #ds_solic_fiscalizacao_audit_documentacao_item {
        white-space:normal !important;
    }
</style>
<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
    $abas[] = array('aba_prorrogacao', 'Prorrogação de Prazo', FALSE, 'ir_prorrogacao();');
    $abas[] = array('aba_acompanhamento', 'Acompanhamento', FALSE, 'ir_acompanhamento();');
    $abas[] = array('aba_documentacao', 'Documentação/Informação', FALSE, 'ir_documentacao();');
    $abas[] = array('aba_documentos', 'Documentos', TRUE, 'location.reload();');

	$head = array(
        'Encaminhar Doc.',
        'Documento',
        'Dt. Removido',
        'Usuário',
        ''
	);

    $body = array();

    foreach ($collection as $key => $item)
    {
        $remover = '';

        if(trim($item['dt_removido']) == '' AND trim($row['cd_gerencia']) == trim($this->session->userdata('divisao')))
        {
            $remover = '<a href="javascript:void(0);" onclick="remover_documento('.$item['cd_solic_fiscalizacao_audit_documentacao_anexo'].')">[remover]</a>';
        }

        $campo_check = array(
            'name'     => 'cd_solic_fiscalizacao_audit_documentacao_anexo_'.$item['cd_solic_fiscalizacao_audit_documentacao_anexo'],
            'id'       => 'cd_solic_fiscalizacao_audit_documentacao_anexo_'.$item['cd_solic_fiscalizacao_audit_documentacao_anexo'],
            'value'    => $item['cd_solic_fiscalizacao_audit_documentacao_anexo'],
            'checked'  => ($item['fl_encaminhar_documento'] == 'S' ? TRUE : FALSE),
            'onchange' => 'check_item($(this))'   
        ); 

        if(trim($documentacao['dt_encerramento']) != '' AND intval($item['cd_liquid']) > 0)
        {
            $ext = pathinfo($item['arquivo'], PATHINFO_EXTENSION);

            if(in_array($ext, array('tif', 'pdf', 'png', 'jpg', 'jpeg', 'bmp', 'svg')))
            {
                $link = 'atividade/solic_fiscalizacao_audit/abrir_documento_liquid/'.$item['cd_liquid'];
            }
            else
            {
                $link = 'atividade/solic_fiscalizacao_audit/abrir_documento/'.$item['cd_liquid'].'/'.$ext;
            }
        }
        else
        {
            $link = 'atividade/solic_fiscalizacao_audit/abrir_documento_web/'.$item['cd_solic_fiscalizacao_audit_documentacao_anexo'];
        }

        $body[] = array(
            (trim($documentacao['dt_encerramento']) == '' ? form_checkbox($campo_check) : $item['ds_encaminhar_documento']),
            array(anchor($link, $item['arquivo_nome'] , array('target' => "_blank")), "text-align:left;"),
            $item['dt_removido'],
            $item['ds_usuario'],
            $remover
        );
    }
    
	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
    $grid->body = $body;
    
    if($documentacao['dt_encerramento'] != '')
    {
        $grid->col_oculta = array(3);
    }

    echo aba_start($abas);
    echo form_start_box('default_solicitacao_box', 'Solicitação');
        echo form_default_row('', 'Ano/Nº:', '<span class="label label-inverse">'.$row['ds_ano_numero'].'</i>');
        echo form_default_row('', 'Origem:', $row['ds_solic_fiscalizacao_audit_origem'].(trim($row['ds_origem']) != '' ? ' ('.$row['ds_origem'].')' : ''));
        echo form_default_row('', 'Data Recebimento:', $row['dt_recebimento']);
        echo form_default_row('', 'Tipo:', $row['ds_solic_fiscalizacao_audit_tipo'].(trim($row['ds_tipo']) != '' ? ' ('.$row['ds_tipo'].')' : ''));

        echo form_default_row('', 'Documento:', $row['ds_documento']);
        echo form_default_row('', 'Teor:', $row['ds_teor']);
        echo form_default_row('', 'Dt. Prazo:', $row['dt_prazo']);

        echo form_default_row('', 'Prazo para Retorno:', $documentacao['dt_prazo_retorno']);

    echo form_end_box('default_solicitacao_box');

    echo form_start_box('default_documentacao_solicitacao_box', 'Documentação/Solicitação');
        echo form_default_row('ds_solic_fiscalizacao_audit_documentacao',  'Item:', $documentacao['nr_item'].' - '.nl2br($documentacao['ds_solic_fiscalizacao_audit_documentacao']));
        if(trim($row['dt_envio_solicitacao_documento']) != '')
        {
            echo form_default_row('', 'Dt. Envio Solicitação:', $row['dt_envio_solicitacao_documento']);
            echo form_default_row('', 'Usuário:', $row['ds_usuario_envio_solicitacao_documento']);
        }
    echo form_end_box('default_documentacao_solicitacao_box');

    if(trim($documentacao['dt_atendimento_responsavel']) != '' AND trim($documentacao['dt_atendimento']) == '' AND trim($row['cd_gerencia']) == trim($this->session->userdata('divisao')))
    {
        echo form_command_bar_detail_start();
            echo button_save('Atendeu', 'atendeu('.$documentacao['cd_solic_fiscalizacao_audit_documentacao'].')', 'botao_verde');
            echo button_save('Não Atendeu', 'nao_atendeu('.$documentacao['cd_solic_fiscalizacao_audit_documentacao'].')', 'botao_vermelho');
        echo form_command_bar_detail_end();
    }
    
    echo br();
    echo $grid->render();    
	echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?>