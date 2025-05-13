<?php
	set_title('Extrato Institutos');
	$this->load->view('header');
?>
<script>	
	function filtrar()
    {
		if(($("#dt_emissao_extrato_ini").val() != "") && ($("#dt_emissao_extrato_fim").val() != ""))
		{
			$("#result_div").html("<?= loader_html() ?>");

			$.post("<?= site_url('atividade/extrato_institutos/listar') ?>", 
			$("#filter_bar_form").serialize(),
			function(data)
			{
				$("#result_div").html(data);
				configure_result_table();
			});
		}
		else
		{
			alert("Informe o Período da Emissão do Extrato.");
			$("#result_div").html('<br/><br/><span class="label label-success">Informe o Período da Emissão do Extrato</span>');
			$("#dt_inicio").focus();
		}
    }
	
    function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("tabela_demonstrativo"),
        [
			null,
            'CaseInsensitiveString', 
            'RE', 
            'CaseInsensitiveString',
            'CaseInsensitiveString', 
            'CaseInsensitiveString', 
            'CaseInsensitiveString', 
            'CaseInsensitiveString',
            'CaseInsensitiveString',
            'DateTimeBR',
            'DateBR', 
            'DateBR', 
            'DateTimeBR',
            'DateTimeBR'
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
        ob_resul.sort(1, false);
    }	
	
	function ir_emails()
	{
		location.href = "<?= site_url('atividade/extrato_institutos/emails') ?>";
	}
	
	function check_all()
    {
        var ipts = $("#tabela_demonstrativo > tbody").find("input:checkbox");
        var check = document.getElementById("checkboxCheckAll");
	 
        check.checked ?
            jQuery.each(ipts, function(){
            this.checked = true;
        }) :
            jQuery.each(ipts, function(){
            this.checked = false;
        });
    }
	
	function get_check()
    {
        var ipts = $("#tabela_demonstrativo > tbody").find("input:checkbox:checked");
		
        $("#part_selecionado").val("");
		
        ipts.each(function(){
            if($.trim($("#part_selecionado").val()) == "")
            {
                $("#part_selecionado").val("'"+$(this).val()+"'");
            }
            else
            {
                $("#part_selecionado").val($("#part_selecionado").val()+",'"+$(this).val()+"'");
            }
        })		
    }	
	
	function enviar()
    {
		get_check();
		
		if($("#part_selecionado").val() != "")
		{
			var confirmacao = 'Confirma o envio do Extrato Institutos (E-MAIL)?\n\n'+
							  'Clique [Ok] para Sim\n\n'+
							  'Clique [Cancelar] para Não\n\n';			

			if(confirm(confirmacao))
			{
				$('#result_div').html("<?= loader_html() ?>");
				
				$.post("<?= site_url('atividade/extrato_institutos/enviar') ?>",
				{
					arr_re_cripto          : $("#part_selecionado").val(),
					dt_emissao_extrato_ini : $("#dt_emissao_extrato_ini").val(),
					dt_emissao_extrato_fim : $("#dt_emissao_extrato_fim").val()
				},
				function(data)
				{
					filtrar();
				});
			}
		}
		else
		{
			alert("Selecione o(s) Participante(s) para enviar o Extrato Institutos.");
		}
    }	

    function enviar_manual()
    {
		get_check();
		
		if($("#part_selecionado").val() != "")
		{
			var confirmacao = 'Confirma o envio do Extrato Institutos (MANUAL)?\n\n'+
							  'Clique [Ok] para Sim\n\n'+
							  'Clique [Cancelar] para Não\n\n';			

			if(confirm(confirmacao))
			{
				$('#result_div').html("<?= loader_html() ?>");
				
				$.post("<?= site_url('atividade/extrato_institutos/enviar_manual') ?>",
				{
					arr_re_cripto          : $("#part_selecionado").val(),
					dt_emissao_extrato_ini : $("#dt_emissao_extrato_ini").val(),
					dt_emissao_extrato_fim : $("#dt_emissao_extrato_fim").val()
				},
				function(data)
				{
					filtrar();
				});
			}
		}
		else
		{
			alert("Selecione o(s) Participante(s) para enviar o Extrato Institutos.");
		}
    }	

    function enviar_correio()
    {
		get_check();
		
		if($("#part_selecionado").val() != "")
		{
			var confirmacao = 'Confirma o envio do Extrato Institutos (CORREIO)?\n\n'+
							  'Clique [Ok] para Sim\n\n'+
							  'Clique [Cancelar] para Não\n\n';			

			if(confirm(confirmacao))
			{
				$('#result_div').html("<?= loader_html() ?>");
				
				$.post("<?= site_url('atividade/extrato_institutos/enviar_correio') ?>",
				{
					arr_re_cripto          : $("#part_selecionado").val(),
					dt_emissao_extrato_ini : $("#dt_emissao_extrato_ini").val(),
					dt_emissao_extrato_fim : $("#dt_emissao_extrato_fim").val()
				},
				function(data)
				{
					filtrar();
				});
			}
		}
		else
		{
			alert("Selecione o(s) Participante(s) para enviar o Extrato Institutos.");
		}
    }	
	
	$(function(){
		if(($("#dt_emissao_extrato_ini").val() == "") || ($("#dt_emissao_extrato_fim").val() == ""))
		{
			$("#dt_emissao_extrato_ini_dt_emissao_extrato_fim_shortcut").val("currentMonth");
			$("#dt_emissao_extrato_ini_dt_emissao_extrato_fim_shortcut").change();
		}
		
		filtrar();
	});
		
</script>
<?php
	$abas[] = array('aba_lista', 'Enviar', TRUE, 'location.reload();');
	$abas[] = array('aba_emails', 'Emails', FALSE, 'ir_emails();');

	$arr = array(
		array('value' => 'N', 'text' => 'Não'),
		array('value' => 'S', 'text' => 'Sim')
	);

	$ar_eletronico = array(
		array('text' => 'Sim', 'value' => 'I'),
		array('text' => 'Não', 'value' => 'C')
	);

	echo aba_start($abas);
		echo form_list_command_bar();
		echo form_start_box_filter('filter_bar', 'Filtros', TRUE);
			echo filter_date_interval('dt_emissao_extrato_ini', 'dt_emissao_extrato_fim', 'Dt. Emissão Extrato: (*)');
			echo filter_dropdown('fl_recebido_extrato', 'Receb. Extrato:', $arr);
			echo filter_dropdown('fl_enviado', 'Enviado:', $arr);
			echo filter_dropdown('fl_email', 'Com email:', $arr);
			echo filter_dropdown('fl_eletronico', 'Eletrônico:', $ar_eletronico);
			echo form_default_hidden('part_selecionado');
		echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end(); 
	 
	$this->load->view('footer_interna');
?>