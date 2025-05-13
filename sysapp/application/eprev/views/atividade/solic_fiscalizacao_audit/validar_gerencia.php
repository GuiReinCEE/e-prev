<?php
	set_title('Registro de Solicitações, Fiscalizações e Auditorias');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('fl_competencia'), 'valida_formulario(form_validar)') ?>

	function ir_lista()
    {
        location.href = "<?= site_url('atividade/solic_fiscalizacao_audit/minhas') ?>";
    }

    function valida_formulario(form)
	{
		if($("#fl_competencia").val() == "S" && $("#fl_apoio").val() == "")
		{
			alert("Informe os campos obrigatórios!\n\n");
			$("#ds_origem").focus();
			return false;
		}

		if($("#fl_competencia").val() == "N" && $("#cd_gerencia").val() == "")
		{
			alert("Informe os campos obrigatórios!\n\n");
			$("#ds_origem").focus();
			return false;
		}

		var fl_marcado = false;
		$("input[type='checkbox'][id='gerencia_apoio']").each( 
			function() 
			{ 
				if (this.checked) 
				{ 
					fl_marcado = true;
				} 
			}
		);	

		if($("#fl_competencia").val() == "S" && $("#fl_apoio").val() == "S" && !fl_marcado)
		{
			alert("Informe uma área área de apoio!\n\n");
			return false;
		}

		if(confirm("Salvar?"))
		{
			form.submit();
		}
	}


    function set_competencia(fl_competencia)
    {
    	if(fl_competencia == 'S')
    	{
    		$("#fl_apoio_row").show();
    		$("#cd_gerencia_row").hide();
	    	$("#gerencia_apoio_row").hide();
    	}
    	else if(fl_competencia == 'N')
    	{
    		$("#cd_gerencia_row").show();
	    	$("#fl_apoio_row").hide();
	    	$("#gerencia_apoio_row").hide();
    	}
    	else
    	{
    		$("#cd_gerencia_row").hide();
	    	$("#fl_apoio_row").hide();
	    	$("#gerencia_apoio_row").hide();
    	}
    }

    function set_apoio(fl_apoio)
    {
    	if(fl_apoio == 'S')
    	{
	    	$("#gerencia_apoio_row").show();
    	}
    	else if(fl_competencia == 'N')
    	{
	    	$("#gerencia_apoio_row").hide();
    	}
    	else
    	{
	    	$("#gerencia_apoio_row").hide();
    	}
    }

    $(function(){ 
    	$("#cd_gerencia_row").hide();
    	$("#fl_apoio_row").hide();
    	$("#gerencia_apoio_row").hide();
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

		echo form_open('atividade/solic_fiscalizacao_audit/validar_gerencia', 'id="form_validar"');
            echo form_start_box('default_retorno_documentacao_box', 'Competência da Gerência Responsável');
                echo form_default_hidden('cd_solic_fiscalizacao_audit', '', $row['cd_solic_fiscalizacao_audit']);
                echo form_default_hidden('cd_solic_fiscalizacao_audit_documentacao', '', $documentacao['cd_solic_fiscalizacao_audit_documentacao']);
                echo form_default_dropdown('fl_competencia', 'Confirma que este item é de competência da [Gerência Responsável]?:', array(array('value' => 'N', 'text' => 'Não'), array('value' => 'S', 'text' => 'Sim')), '', 'onchange="set_competencia($(this).val())"');
                echo form_default_dropdown('cd_gerencia', 'Gerência:', $gerencia);
                echo form_default_dropdown('fl_apoio', 'Precisará de apoio de outra Gerência para responder este item?:', array(array('value' => 'N', 'text' => 'Não'), array('value' => 'S', 'text' => 'Sim')), '', 'onchange="set_apoio($(this).val())"');
                echo form_default_checkbox_group('gerencia_apoio', 'Gerência de Apoio:', $gerencia, array(), 150, 350);
            echo form_end_box('default_retorno_documentacao_box');
            echo form_command_bar_detail_start();
				echo button_save('Salvar'); 
			echo form_command_bar_detail_end();	
	    echo form_close();

    

		echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?>