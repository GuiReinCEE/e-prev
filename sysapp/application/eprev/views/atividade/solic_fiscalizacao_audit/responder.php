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
        $("#form_anexo").submit();
    }

	function ir_lista()
    {
        location.href = "<?= site_url('atividade/solic_fiscalizacao_audit/minhas') ?>";
    }

    function excluir_documento(cd_solic_fiscalizacao_audit_documentacao_anexo)
    {
    	var confirmacao = 'Deseja excluir o documento?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        { 
            location.href = "<?= site_url('atividade/solic_fiscalizacao_audit/excluir_documento/'.$documentacao['cd_solic_fiscalizacao_audit_documentacao']) ?>/"+cd_solic_fiscalizacao_audit_documentacao_anexo;
        }
    }

    function encerrar_solicitacao()
    {
    	var confirmacao = 'Deseja encerrar a solicitação?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        { 
            location.href = "<?= site_url('atividade/solic_fiscalizacao_audit/encerrar_solicitacao/'.$documentacao['cd_solic_fiscalizacao_audit_documentacao']) ?>";
        }
    }

    function encaminhar_conferencia()
    {
    	var ipts  = $("#table-1 > tbody").find("input:checkbox:checked");
		
		var solic_fiscalizacao_audit_documentacao_anexo = [];
	
		ipts.each(function() {
			solic_fiscalizacao_audit_documentacao_anexo.push($(this).val());
		});

		validation = true;

		if(solic_fiscalizacao_audit_documentacao_anexo.length == 0)
		{
			alert('Selecione no mínimo uma solicitação');
			validation = false;
			return false;
		}

		if($("#cd_usuario_conferente").val() == '')
		{
			alert('Informe o conferente.');
			validation = false;
			return false;
		}

		if($("#cd_usuario_sub_conferente").val() == '')
		{
			alert('Informe o segundo conferente conferente.');
			validation = false;
			return false;
		}

		if(validation)
		{
			var confirmacao = 'Deseja encaminhar a documentação para conferência?\n\n'+
	            'Clique [Ok] para Sim\n\n'+
	            'Clique [Cancelar] para Não\n\n';

	        if(confirm(confirmacao))
	        { 
	        	$("#solic_fiscalizacao_audit_documentacao_anexo").val(solic_fiscalizacao_audit_documentacao_anexo);

	            form_documentacao.method = "post";
		        form_documentacao.action = "<?= site_url('atividade/solic_fiscalizacao_audit/encaminhar_conferencia') ?>";
		        form_documentacao.target = "_self";
		        form_documentacao.submit();
	        }
	    }
    }

    function checkAll()
    {
        var ipts = $("#table-1 > tbody").find("input:checkbox");
        var check = document.getElementById("checkboxCheckAll");

        jQuery.each(ipts, function(){
            this.checked = check.checked ? true : false;
        });
    }

    function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			null,
			"DateTimeBR",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			null
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
		ob_resul.sort(1, true);
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
    $abas[] = array('aba_responder', 'Responder', TRUE, 'location.reload();');

	$head = array(
		'<input type="checkbox" id="checkboxCheckAll" onclick="checkAll();" title="Clique para Marcar ou Desmarcar Todos">',
		'Dt. Inclusao',
		'Documento',
		'Usuário',
		'Enc. Conferência',
		''
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
			(trim($documentacao['dt_envio_conferencia']) == '' ? form_checkbox($check) : ''),
		    $item['dt_inclusao'],
		    array(anchor($link, $item['arquivo_nome'], array('target' => '_blank')), 'text-align:left'),
		    array($item['ds_usuario'], 'text-align:left'),
		    $item['ds_envio_conferencia'],
		    (trim($documentacao['dt_envio_conferencia']) == '' ? '<a href="javascript:void(0)" onclick="excluir_documento('.$item['cd_solic_fiscalizacao_audit_documentacao_anexo'].')">[excluir]</a>' : ''),
		);
    }

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
    $grid->view_count = false;

    if($documentacao['dt_atendimento_responsavel'] != '')
    {
        $grid->col_oculta = array(4);
    }

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

            if(trim($documentacao['dt_envio_conferencia']) != '')
            {
            	echo form_default_row('dt_envio_conferencia', 'Dt. Enc. Conferência:', $documentacao['dt_envio_conferencia']);
            }

            if($documentacao['fl_atendeu_conferencia'] == 'S')
            {
                echo form_default_row('', 'Atendeu Conferência:', '<span class="label label-success">Sim</span>');
            }
            else if($documentacao['fl_atendeu_conferencia'] == 'N')
            {
                echo form_default_row('', 'Atendeu Conferência:', '<span class="label label-important">Não</span>');
                echo form_default_textarea('', 'Motivo:', $documentacao['ds_motivo_atendeu_conferencia'], 'style="height:80px;" readonly=""');
            }

		echo form_end_box('default_documentacao_box');

		if(trim($documentacao['dt_envio_conferencia']) == '')
		{
	        if($documentacao['dt_atendimento_responsavel'] == '')
	        {
	            echo form_open('atividade/solic_fiscalizacao_audit/anexar_documento', 'id="form_anexo"');
	                echo form_start_box('default_retorno_documentacao_box', 'Documentação');
	                    echo form_default_hidden('cd_solic_fiscalizacao_audit', '', $row['cd_solic_fiscalizacao_audit']);
	                    echo form_default_hidden('cd_solic_fiscalizacao_audit_documentacao', '', $documentacao['cd_solic_fiscalizacao_audit_documentacao']);
	                    echo form_default_upload_multiplo('arquivo_m', 'Documentos: (*)', 'solic_fiscalizacao_audit', 'validaArq');
	                echo form_end_box('default_retorno_documentacao_box');
					if($this->session->userdata('divisao') == 'GC' AND count($collection) > 0)
					{
						echo form_command_bar_detail_start();
							echo button_save('Encerrar Solicitação', 'encerrar_solicitacao()', 'botao_verde');
						echo form_command_bar_detail_end();
					}
			    echo form_close();
	        }

	        if(count($collection) > 0 AND $documentacao['dt_atendimento_responsavel'] == '' AND trim($documentacao['dt_envio_conferencia']) == '')
			{
		        echo form_open('atividade/solic_fiscalizacao_audit/encaminhar_conferencia', 'id="form_documentacao"');
		            echo form_start_box('default_retorno_documentacao_conferencia_box', 'Conferência');
						echo form_default_hidden('solic_fiscalizacao_audit_documentacao_anexo', '', '');
		                echo form_default_hidden('cd_solic_fiscalizacao_audit', '', $row['cd_solic_fiscalizacao_audit']);
		                echo form_default_hidden('cd_solic_fiscalizacao_audit_documentacao', '', $documentacao['cd_solic_fiscalizacao_audit_documentacao']);
		                echo form_default_dropdown('cd_usuario_conferente', 'Conferente: (*)', $usuario_conferente, $cd_usuario_conferente);
		                echo form_default_dropdown('cd_usuario_sub_conferente', 'Substituto: (*)', $usuario_sub_conferente, $cd_usuario_sub_conferente);
		            echo form_end_box('default_retorno_documentacao_conferencia_box');
		            echo form_command_bar_detail_start();
						echo button_save('Enviar para Conferência', 'encaminhar_conferencia()');
						//echo button_save('Encerrar Solicitação', 'encerrar_solicitacao()', 'botao_verde');
					echo form_command_bar_detail_end();
			    echo form_close();
			}
		}
		echo br();
		echo $grid->render();
		echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?>