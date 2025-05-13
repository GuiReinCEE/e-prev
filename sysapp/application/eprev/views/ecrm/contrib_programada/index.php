<?php
	set_title('Contribuição Programada');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?php echo loader_html(); ?>");

		$.post("<?= site_url('ecrm/contrib_programada/listar') ?>",
		$("#filter_bar_form").serialize(),
		function(data)
		{
			$("#result_div").html(data);
			configure_result_table();
		});
	}

	function ir_lista_percent()
	{
		location.href = "<?= site_url('ecrm/contrib_percentual_programada') ?>";
	}

	function excluir(cd_contribuicao_programada)
	{
		var confirmacao = 'Deseja Confirmar?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = '<?= site_url('ecrm/contrib_programada/confirmar') ?>/' + cd_contribuicao_programada;
		}
	}

	function confirmar()
	{
		var ipts = $("#table-1>tbody").find("input:checkbox");

        var contribuicao_programada = [];

        ipts.each(function(i, item){
            if($(this).is(":checked"))
            {
                contribuicao_programada.push($(item).val());
            }
        });

        if(contribuicao_programada.length > 0)
        {
			var confirmacao = "Deseja Confirmar (Total Selecionado: "+contribuicao_programada.length+")?\n\n"+
				"Clique [Ok] para Sim\n\n"+
				"Clique [Cancelar] para Não\n\n";

			if(confirm(confirmacao))
			{
				$('#result_div').html("<?= loader_html() ?>");
				
				$.post("<?= site_url('ecrm/contrib_programada/confirmar_itens') ?>",
				{
					"contribuicao_programada[]" : contribuicao_programada
				},
				function(data)
				{
					filtrar();
				});
			}
        }
        else
        {
        	alert("Selecione algum item.");
        }
	}

	function cancelar()
	{
		var ipts = $("#table-1>tbody").find("input:checkbox");

        var contribuicao_programada = [];

        ipts.each(function(i, item){
            if($(this).is(":checked"))
            {
                contribuicao_programada.push($(item).val());
            }
        });

        if(contribuicao_programada.length > 0)
        {
			var confirmacao = "Deseja cancelar (Total Selecionado: "+contribuicao_programada.length+")?\n\n"+
				"Clique [Ok] para Sim\n\n"+
				"Clique [Cancelar] para Não\n\n";

			if(confirm(confirmacao))
			{
				$('#result_div').html("<?= loader_html() ?>");
				
				$.post("<?= site_url('ecrm/contrib_programada/cancelar') ?>",
				{
					"contribuicao_programada[]" : contribuicao_programada
				},
				function(data)
				{
					filtrar();
				});
			}
        }
        else
        {
        	alert("Selecione algum item.");
        }
	}

	function check_all()
    {
        var ipts = $("#table-1>tbody").find("input:checkbox");
        var check = document.getElementById("checkboxCheckAll");
     
        check.checked ?
            jQuery.each(ipts, function(){
            this.checked = true;
        }) :
            jQuery.each(ipts, function(){
            this.checked = false;
        });
    }   

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			null,
			"RE",
			"CaseInsensitiveString",
			"NumberFloatBR",
			"NumberFloatBR",
			"DateTimeBR", 
			"DateBR",
			"DateTimeBR", 
			"DateTimeBR"
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
		ob_resul.sort(4, true);
	}

	$(function(){

		if($("#dt_solicitacao_ini").val() == '' || $("#dt_solicitacao_fim").val() == '')
		{
			$("#dt_solicitacao_ini_dt_solicitacao_fim_shortcut").val("lastMonth");
			$("#dt_solicitacao_ini_dt_solicitacao_fim_shortcut").change();
		}

		filtrar();
	})

</script>

<?php
	$abas[0] = array('aba_lista', 'Valor Instituidor', true, 'location.reload();');  
	$abas[1] = array('aba_lista_percent', '% Patroc', false, 'ir_lista_percent();');

	echo aba_start($abas);
		echo form_list_command_bar();
		echo form_start_box_filter(); 
			echo filter_participante(array('cd_empresa','cd_registro_empregado','seq_dependencia', 'nome'), 'Participante:', array(), TRUE, TRUE);
			echo filter_text('nome', 'Nome:', '', 'style="width:400px;"');
			echo filter_date_interval('dt_solicitacao_ini', 'dt_solicitacao_fim', 'Dt. Solicitação:');
			echo filter_date_interval('dt_confirmacao_ini', 'dt_confirmacao_fim', 'Dt. Confirmação:');
			echo filter_date_interval('dt_inicio_ini', 'dt_inicio_fim', 'Dt. Início:');
			echo filter_dropdown('dt_cancelado', 'Cancelado:', array(array('value' => 'N', 'text' => 'Não'), array('value' => 'S', 'text' => 'Sim')), array('N'));
			echo filter_dropdown('fl_confirmado', 'Confirmado:', array(array('value' => 'N', 'text' => 'Não'),array('value' => 'S', 'text' => 'Sim')), array('N'));
		echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br();
	echo aba_end();

	$this->load->view('footer_interna');
?>