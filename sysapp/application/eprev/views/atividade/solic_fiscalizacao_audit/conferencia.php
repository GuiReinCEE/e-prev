<?php
	set_title('Registro de Solicitações, Fiscalizações e Auditorias');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array(), 'valida_arquivo(form)') ?>

    function valida_arquivo(form)
    {
        if($("#arquivo").val() == "" && $("#arquivo_nome").val() == "")
        {
            alert("Nenhum arquivo foi anexado.");
            return false;
        }
        else
        {
            if(confirm("Salvar?"))
            {
                form.submit();
            }
        }
    }

    function validaArq(enviado, nao_enviado, arquivo)
    {
        $("form").submit();
    }

	function ir_lista()
    {
        location.href = "<?= site_url('atividade/solic_fiscalizacao_audit/minhas_conferencia') ?>";
    }

    function encerrar_solicitacao_conferencia()
    {
    	var confirmacao = 'Deseja encerrar a solicitação?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        { 
            location.href = "<?= site_url('atividade/solic_fiscalizacao_audit/encerrar_solicitacao_conferencia/'.$documentacao['cd_solic_fiscalizacao_audit_documentacao']) ?>";
        }
    }

    function nao_atendeu()
    {
        location.href = "<?= site_url('atividade/solic_fiscalizacao_audit/atendimento_conferencia/'.$row['cd_solic_fiscalizacao_audit'].'/'.$documentacao['cd_solic_fiscalizacao_audit_documentacao']) ?>";
    }

    function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			"DateTimeBR",
			"CaseInsensitiveString",
			"CaseInsensitiveString"
	    ]);
		ob_resul.onsort = function ()
		{
			var rows = ob_resul.tBody.rows;
			var l = rows.length;
			for (var i = 0; i < l; i++)
			{
				removeClassName( rows[i], i % 2 ? "sort-par" : "sort-impar" );
				addClassName( rows[i], i % 2 ? "sort-impar" : "sort-par" );
			}
		};
		ob_resul.sort(0, true);
	}

    $(function(){
		configure_result_table();
	});
</script>
<style>
    #ds_solic_fiscalizacao_audit_documentacao_item {
        white-space:normal !important;
    }
</style>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_responder', 'Conferência', TRUE, 'location.reload();');

	$head = array(
		'Dt. Inclusao',
		'Documento',
		'Usuário'
	);

	$body = array();

	foreach ($collection as $item)
	{
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

		$check = array(
			'name'    => 'cd_solic_fiscalizacao_audit_documentacao_anexo_'.$item['cd_solic_fiscalizacao_audit_documentacao_anexo'],
			'id'      => 'cd_solic_fiscalizacao_audit_documentacao_anexo_'.$item['cd_solic_fiscalizacao_audit_documentacao_anexo'],
			'value'   => $item['cd_solic_fiscalizacao_audit_documentacao_anexo'],
			'checked' => (trim($item['fl_envio_conferencia']) == 'S' ? TRUE : FALSE)
		);

		$body[] = array(
		    $item['dt_inclusao'],
		    array(anchor($link, $item['arquivo_nome'], array('target' => '_blank')), 'text-align:left'),
		    array($item['ds_usuario'], 'text-align:left')
		);
    }

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
    $grid->view_count = false;

	$link_documento = base_url().'up/solic_fiscalizacao_audit/'.$row['arquivo'];

	if(intval($row['cd_liquid']) > 0)
	{
		$ext = pathinfo($row['arquivo'], PATHINFO_EXTENSION);

		if(in_array($ext, array('tif', 'pdf', 'png', 'jpg', 'jpeg', 'bmp', 'svg')))
		{
			$link_documento = 'atividade/solic_fiscalizacao_audit/abrir_documento_liquid/'.$row['cd_liquid'];
		}
		else
		{
			$link_documento = 'atividade/solic_fiscalizacao_audit/abrir_documento/'.$row['cd_liquid'].'/'.$ext;
		}
	}

	echo aba_start($abas);
		echo form_start_box('default_solicitacao_box', 'Registro de Solicitação');
			echo form_default_row('', 'Ano/Nº:', '<span class="label label-inverse">'.$row['ds_ano_numero'].'</i>');
			echo form_default_row('', 'Origem:', $row['ds_solic_fiscalizacao_audit_origem'].(trim($row['ds_origem']) != '' ? ' ('.$row['ds_origem'].')' : ''));
			echo form_default_row('', 'Tipo:', $row['ds_solic_fiscalizacao_audit_tipo'].(trim($row['ds_tipo']) != '' ? ' ('.$row['ds_tipo'].')' : ''));
			echo form_default_row('', 'Documento:', $row['ds_documento']);
			echo form_default_row('', 'Teor:', $row['ds_teor']);
			echo form_default_row('', 'Área Consolidadora:', $row['cd_gerencia']);
			echo form_default_row('', 'Arquivo:', anchor($link_documento, $row['arquivo_nome'], array('target' => '_blank')));
			echo form_default_row('', 'Dt. Envio Solicitação:', $row['dt_envio_solicitacao_documento']);
            echo form_default_row('', 'Usuário:', $row['ds_usuario_envio_solicitacao_documento']);
		echo form_end_box('default_solicitacao_box');

		echo form_start_box('default_documentacao_box', 'Solicitação de Documentação');
			echo form_default_row('nr_item', 'Nº Item:', $documentacao['nr_item']);
			echo form_default_row('ds_solic_fiscalizacao_audit_documentacao', 'Descrição Resumida:', nl2br($documentacao['ds_solic_fiscalizacao_audit_documentacao']));
			echo form_default_row('dt_prazo_retorno', 'Prazo Retorno:', $documentacao['dt_prazo_retorno']);
            echo form_default_row('dt_prazo', 'Prazo Final para Atendimento:', $row['dt_prazo']);
            if($documentacao['dt_atendimento_responsavel'] != '')
            {
                echo form_default_row('dt_atendimento_responsavel', 'Dt. Encerramento:', $documentacao['dt_atendimento_responsavel']);
            }
            if($documentacao['fl_atendeu'] == 'S')
            {
                echo form_default_row('', 'Atendeu:', '<span class="label label-success">Sim</span>');
            }
            else if($documentacao['fl_atendeu'] == 'N')
            {
                echo form_default_row('', 'Atendeu:', '<span class="label label-important">Não</span>');
                echo form_default_textarea('', 'Motivo:', $documentacao['ds_motivo_atendeu'], 'style="height:80px;" readonly=""');
            }

            if($documentacao['dt_envio_conferencia'])
            {
            	echo form_default_row('dt_envio_conferencia', 'Dt. Enc. Conferência:', $documentacao['dt_envio_conferencia']);
            }

            if($documentacao['fl_atendeu_conferencia'] == 'S')
            {
                echo form_default_row('', 'Atendeu:', '<span class="label label-success">Sim</span>');
            }
            else if($documentacao['fl_atendeu_conferencia'] == 'N')
            {
                echo form_default_row('', 'Atendeu:', '<span class="label label-important">Não</span>');
                echo form_default_textarea('', 'Motivo:', $documentacao['ds_motivo_atendeu_conferencia'], 'style="height:80px;" readonly=""');
            }

		echo form_end_box('default_documentacao_box');

		if(trim($documentacao['dt_envio_conferencia']) != '')
		{
			echo form_command_bar_detail_start();
				echo button_save('Atendeu/Encerrar', 'encerrar_solicitacao_conferencia()', 'botao_verde');
				echo button_save('Não Atendeu', 'nao_atendeu()', 'botao_vermelho');
			echo form_command_bar_detail_end();
		}
		echo br();
		echo $grid->render();
		echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?>