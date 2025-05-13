<?php
	set_title('Pós-Venda');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>")
		
        $.post("<?= site_url('ecrm/posvenda/listar') ?>",
		$("#filter_bar_form").serialize(),
        function(data)
        {
			$("#result_div").html(data)
            configure_result_table();
        });
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
        [
            null, 
            "RE",
            "CaseInsensitiveString",
            "DateTimeBR", 
            "DateBR",
            "DateBR",
            "DateBR",
            "DateBR",
            "CaseInsensitiveString", 
            "DateBR", 
            "CaseInsensitiveString", 
            "Number",
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
		ob_resul.sort(5, true);
	}
    
    function ir_relatorio_email()
	{
		location.href = "<?= site_url('ecrm/posvenda/relatorio_email') ?>";
	}
    
    function ir_relatorio()
	{
		location.href = "<?= site_url('ecrm/posvenda/relatorio') ?>";
	}
    
    function excluir(cd_pos_venda_participante)
    {
    	var confirmacao = "Deseja EXCLUIR o Pós-Venda do Participante?\n\n"+
                          "Clique [Ok] para Sim\n\n"+
                          "Clique [Cancelar] para Não\n\n"; 

        if(confirm(confirmacao))
        {
            location.href = "<?= site_url('ecrm/posvenda/excluir') ?>/" + cd_pos_venda_participante;
        }
    }
    
    $(function(){
    	if($("#dt_digita_ingresso_ini").val() == '')
    	{
    		$("#dt_digita_ingresso_ini").val("<?= date('01/m/Y') ?>");
    	}

    	if($("#dt_digita_ingresso_fim").val() == '')
    	{
    		$("#dt_digita_ingresso_fim").val("<?= date('d/m/Y') ?>");
    	}

        filtrar();
    });
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
	$abas[] = array('aba_envia_email', 'Emails', FALSE, 'ir_relatorio_email();');
	$abas[] = array('aba_relatorio', 'Relatório', FALSE, 'ir_relatorio();');

	$participante['cd_empresa']            = '';
	$participante['cd_registro_empregado'] = '';
	$participante['seq_dependencia']       = '';

	echo aba_start($abas);
		echo form_list_command_bar(array());
		echo form_start_box_filter();
			echo filter_participante(array('cd_empresa', 'cd_registro_empregado', 'seq_dependencia', 'nome'), 'Participante:', $participante, TRUE, TRUE);	
	        echo filter_date_interval('dt_ingresso_ini', 'dt_ingresso_fim', 'Dt Ingresso:');
	        echo filter_date_interval('dt_digita_ingresso_ini', 'dt_digita_ingresso_fim', 'Dt. Cadastro Ingresso:');
	        echo filter_date_interval('dt_boas_vindas_ini', 'dt_boas_vindas_fim', 'Dt. Envio Boas Vindas:');
	        echo filter_date_interval('dt_inicio_ini', 'dt_inicio_fim', 'Dt. Envio Pós-Venda:');
	        echo filter_dropdown('cd_usuario_inicio', 'Usuário Envio Pós-Venda:', $usuario_cadastro);
	        echo filter_date_interval('dt_final_ini', 'dt_final_fim', 'Dt. Encerramento Pós-Venda:');
	        echo filter_dropdown('cd_usuario_final', 'Usuário do Encerramento Pós-Venda:', $usuario_encerramento);
	        echo filter_text('cd_atendimento', 'Protocolo:');
	        echo filter_dropdown('cd_usuario_vendedor', 'Usuário Vendedor:', $usuario_vendedor);
		echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br();
	echo aba_end();

	$this->load->view('footer'); 
?>