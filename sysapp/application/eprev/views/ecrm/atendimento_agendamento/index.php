<?php
set_title('Agenda Atendimento');
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
				
		$.post("<?= site_url('ecrm/atendimento_agendamento/listar') ?>",
		$("#filter_bar_form").serialize(),
		function(data)
		{
			$("#result_div").html(data);
			configure_result_table();
		});	
	}


	function setcompareceuCor(cd_atendimento_agendamento)
	{
		$("#compareceu_valor_" + cd_atendimento_agendamento).removeClass();
		$("#compareceu_valor_" + cd_atendimento_agendamento).addClass(($("#fl_compareceu_" + cd_atendimento_agendamento).val() == "N" ? "label label-important" : "label label-info"));
	}
	
    function setcompareceu(cd_atendimento_agendamento)
    {
		$("#ajax_compareceu_valor_" + cd_atendimento_agendamento).html("<?= loader_html('P') ?>");

        $.post("<?= site_url('ecrm/atendimento_agendamento/salvar_compareceu') ?>",
        {
            cd_atendimento_agendamento : cd_atendimento_agendamento,
            fl_compareceu              : $("#fl_compareceu_" + cd_atendimento_agendamento).val()	
        },
        function(data)
        {
			$("#ajax_compareceu_valor_" + cd_atendimento_agendamento).empty();
			
			setcompareceuCor(cd_atendimento_agendamento);
			
			$("#fl_compareceu_" + cd_atendimento_agendamento).hide();
			$("#compareceu_salvar_" + cd_atendimento_agendamento).hide(); 
			
            $("#compareceu_valor_" + cd_atendimento_agendamento).html($("#fl_compareceu_" + cd_atendimento_agendamento +" option:selected").text()); 
			$("#compareceu_valor_" + cd_atendimento_agendamento).show(); 
			$("#compareceu_editar_" + cd_atendimento_agendamento).show(); 
			
        });
    }
	
	function editarcompareceu(cd_atendimento_agendamento)
	{
		$("#compareceu_valor_" + cd_atendimento_agendamento).hide(); 
		$("#compareceu_editar_" + cd_atendimento_agendamento).hide(); 

		$("#compareceu_salvar_" + cd_atendimento_agendamento).show(); 
		$("#fl_compareceu_" + cd_atendimento_agendamento).show(); 
		$("#fl_compareceu_" + cd_atendimento_agendamento).focus();	
	}	

	function cancelar(cd_atendimento_agendamento)
	{
		location.href = "<?= site_url('ecrm/atendimento_agendamento/justificar_cancelamento')?>"+"/"+cd_atendimento_agendamento;
	}

	function cadastro()
	{
		location.href = "<?= site_url('ecrm/atendimento_agendamento/cadastro')?>";
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
		    "DateTimeBR",
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "Number",
		    "RE",
		    "CaseInsensitiveString", 
		    "CaseInsensitiveString", 
		    null,
		    "DateTimeBR", 
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

		if($("#fl_cancelado").val() == "")
		{
			$("#fl_cancelado").val("N");
		}

		if($("#dt_agenda_ini").val() == "" && $("#dt_agenda_fim").val() == "")
		{
			$("#dt_agenda_ini_dt_agenda_fim_shortcut").val("currentMonth");
			$("#dt_agenda_ini_dt_agenda_fim_shortcut").change();
		}

		filtrar();
	});

</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	$config['button'][] = array('Novo Agendamento', 'cadastro();');

	echo aba_start($abas);
		echo form_list_command_bar($config);
		echo form_start_box_filter();
			echo filter_date_interval('dt_agenda_ini', 'dt_agenda_fim', 'Dt. Agendamento:');
			echo filter_participante(array('cd_empresa','cd_registro_empregado','seq_dependencia', 'nome'), 'Participante:', array(), TRUE, TRUE); 
			echo filter_text('nome', 'Nome:', '', 'style="width:400px;"');
			echo filter_date_interval('dt_inclusao_ini', 'dt_inclusao_fim', 'Dt. Solicitação:');
			echo filter_date_interval('dt_cancelamento_ini', 'dt_cancelamento_fim', 'Dt. Cancelamento:');
			echo filter_dropdown('fl_cancelado', 'Cancelado :', array(array('value' => 'N', 'text' => 'Não'), array('value' => 'S', 'text' => 'Sim')));
			echo filter_dropdown('fl_compareceu', 'Compareceu:', array(array('value' => 'N', 'text' => 'Não'), array('value' => 'S', 'text' => 'Sim')));
	    echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();

	$this->load->view('footer');
?>
