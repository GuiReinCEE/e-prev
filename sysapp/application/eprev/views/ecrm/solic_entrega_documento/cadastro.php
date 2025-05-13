<?php
	set_title('Solicitação Entrega Documento');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('cd_solic_entrega_documento_tipo', 'data_ini', 'hr_ini', 'hr_limite', 'ds_endereco', 'fl_prioridade', 'ds_contato', 'fl_destinatario'), 'valida_destinario(form)') ?>

	function ir_lista()
    {
        location.href = "<?= site_url('ecrm/solic_entrega_documento') ?>";
    }

    function ir_acompanhamento()
    {
        location.href = "<?= site_url('ecrm/solic_entrega_documento/acompanhamento/'.$row['cd_solic_entrega_documento']) ?>";
    }

    function valida_destinario(form)
    {
    	if(($("#fl_destinatario").val() == "E" || $("#fl_destinatario").val() == "O") && ($("#ds_destinatario").val() == '') )
    	{
    		var descricao = "";

    		if($("#fl_destinatario").val() == "E")
    		{
    			descricao = "Nome do Contato";
    		}
    		else
    		{	
    			descricao = "Outro";
    		}

    		alert("Informe os campos obrigatórios!\n\n("+descricao+")");
			$("#ds_tipo").focus();
			return false;
    	}

    	if(confirm("Salvar?"))
		{
			form.submit();
		}
    }

    function set_contato(fl_destinatario, fl_apaga)
    {
        if(fl_apaga == 1)
        {
            $("#ds_destinatario").val("");
        }

    	if(fl_destinatario == "E")
    	{
    		$("#ds_destinatario_row label").html("Nome do Contato: (*)");

    		$("#ds_destinatario_row").show();
    	}
    	else if(fl_destinatario == "O")
    	{
    		$("#ds_destinatario_row label").html("Outro: (*)");

    		$("#ds_destinatario_row").show();
    	}
    	else
    	{
    		$("#ds_destinatario_row").hide();
    		$("#ds_destinatario").val("");
    	}
    }

    $(function(){
    	set_contato($("#fl_destinatario").val(), 0);
    });

</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

    if(intval($row['cd_solic_entrega_documento']) > 0)
    {
        $abas[] = array('aba_acompanhamento', 'Acompanhamento', FALSE, 'ir_acompanhamento();');
    }

    $destinatario = array(
        array('value' => 'A', 'text' => 'Aguardar'),
        array('value' => 'E', 'text' => 'Entrar em contato com'),
        array('value' => 'O', 'text' => 'Outro')
    );

    $prioridade = array(
        array('value' => 'U', 'text' => 'Urgente'),
        array('value' => 'M', 'text' => 'Moderada'),
        array('value' => 'B', 'text' => 'Baixa')
    );

	echo aba_start($abas);
		echo form_open('ecrm/solic_entrega_documento/salvar');
			echo form_start_box('default_box', 'Cadastro');
				echo form_default_hidden('cd_solic_entrega_documento', '', $row['cd_solic_entrega_documento']);
				echo form_default_dropdown_db('cd_solic_entrega_documento_tipo', 'Tipo de Documento: (*)', array('projetos.solic_entrega_documento_tipo', 'cd_solic_entrega_documento_tipo', 'ds_solic_entrega_documento_tipo'), array($row['cd_solic_entrega_documento_tipo']), '', '', TRUE);
				echo form_default_date('data_ini', 'Data: (*)', $row['data_ini']);              
                echo form_default_time('hr_ini', 'Hora Inicial: (*)', $row['hr_ini']);	
                echo form_default_time('hr_limite', 'Horário Limite: (*)', $row['hr_limite']);
                echo form_default_textarea('ds_endereco', 'Endereço/local: (*)', $row, 'style="height:100px;"');
				echo form_default_dropdown('fl_prioridade', 'Prioridade: (*)', $prioridade, $row['fl_prioridade']);	
                echo form_default_text('ds_contato', 'Destinatário: (*)', $row, 'style="width:350px;"');
				echo form_default_dropdown('fl_destinatario', 'Destinatário Ausente: (*)', $destinatario, $row['fl_destinatario'], 'onchange="set_contato($(this).val(), 1)"');	
				echo form_default_text('ds_destinatario', 'Nome do Contato: (*)', $row, 'style="width:350px;"');
                echo form_default_textarea('ds_observacao', 'Observações:', $row['ds_observacao'], 'style="height:100px;"');			
			echo form_end_box('default_box');
			echo form_command_bar_detail_start();
                if($fl_cadastro)
                {
                    echo button_save('Salvar');
                }
		    echo form_command_bar_detail_end();
		echo form_close();
		echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?>