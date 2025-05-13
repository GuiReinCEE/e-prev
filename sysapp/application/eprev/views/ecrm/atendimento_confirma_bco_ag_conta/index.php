<?php
	set_title('Atendimento - Alteração de Conta');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
				
		$.post("<?= site_url('ecrm/atendimento_confirma_bco_ag_conta/listar') ?>",
		$("#filter_bar_form").serialize(),
		function(data)
		{
			$("#result_div").html(data);
			configure_result_table();
		});	
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			'Number',
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			'RE',
			'CaseInsensitiveString',
			'DateTimeBR',
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
		ob_resul.sort(0, true);
	}

	function confirmar(cd_atendimento, cd_atendimento_confirma_bco_ag_conta)
	{
		var confirmacao = "Deseja Confirmar a Alteração da Conta?\n\n"+
						  "Clique [Ok] para Sim\n\n"+
						  "Clique [Cancelar] para Não\n\n";

		if(confirm(confirmacao))
		{			
			location.href = "<?= site_url('ecrm/atendimento_confirma_bco_ag_conta/confirmar') ?>/"+cd_atendimento+"/"+cd_atendimento_confirma_bco_ag_conta;
		}
	}
	
	function alterar_motivo(cd_atendimento, cd_atendimento_confirma_bco_ag_conta)
    {
        $("#ajax_motivo_valor_" + cd_atendimento).html("<?= loader_html('P') ?>");

        $.post("<?= site_url('ecrm/atendimento_confirma_bco_ag_conta/alterar_motivo') ?>",
        {
            cd_atendimento : cd_atendimento,
			cd_atendimento_confirma_bco_ag_conta : cd_atendimento_confirma_bco_ag_conta,
            ds_observacao : $("#nr_motivo_" + cd_atendimento).val()	
        },
        function(data)
        {
			$("#ajax_motivo_valor_" + cd_atendimento).empty();
			
			$("#nr_motivo_" + cd_atendimento).hide();
			$("#motivo_salvar_" + cd_atendimento).hide(); 
			
            $("#motivo_valor_" + cd_atendimento).html($("#nr_motivo_" + cd_atendimento).val()); 
			$("#motivo_valor_" + cd_atendimento).show(); 
			$("#motivo_editar_" + cd_atendimento).show();
			
			filtrar();
        }, 'html',true);
    }	
	
	function editar_motivo(cd_atendimento)
	{
		$("#motivo_valor_" + cd_atendimento).hide(); 
		$("#motivo_editar_" + cd_atendimento).hide(); 

		$("#motivo_salvar_" + cd_atendimento).show(); 
		$("#nr_motivo_" + cd_atendimento).show(); 
		$("#nr_motivo_" + cd_atendimento).focus();	
	}
	
	$(function(){
		$("#dt_atendimento_ini_dt_atendimento_fim_shortcut").val("last7days");
		$("#dt_atendimento_ini_dt_atendimento_fim_shortcut").change();

		filtrar();
	});
</script>

<?php
$abas[] = array('aba_lista', 'Lista', true, 'location.reload();');

$config = array();

echo aba_start($abas);
	echo form_list_command_bar($config);
	echo form_start_box_filter();
        echo filter_date_interval('dt_atendimento_ini', 'dt_atendimento_fim', 'Dt. Atendimento:');
        echo filter_dropdown('fl_confirmado', 'Confirmado: ', $confirmacao, 'N');
		echo filter_dropdown('fl_atendente', 'Atendente: ', $atendente);
    echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br(2);	
echo aba_end();

$this->load->view('footer');
?>