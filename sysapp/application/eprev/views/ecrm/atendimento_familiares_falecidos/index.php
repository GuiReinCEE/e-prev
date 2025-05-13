<?php
	set_title('Contato Familiares Ex-autárquicos Falecidos');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
	
	    $.post("<?= site_url('ecrm/atendimento_familiares_falecidos/listar') ?>",
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
	    	'DateTimeBR',
			'RE',
			'CaseInsensitiveString',
			null,
			null,
			'CaseInsensitiveString',
			'Number',
			null,
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

	function novo()
	{
	    location.href = "<?= site_url('ecrm/atendimento_familiares_falecidos/cadastro') ?>";
	}

	function encerrar(cd_atendimento_familiares_falecidos)
	{
		var confirmacao = "Deseja ENCERRAR o Contato?\n\n"+
						  "Clique [Ok] para Sim\n\n"+
						  "Clique [Cancelar] para Não\n\n";	

		if(confirm(confirmacao))
		{
			location.href = "<?= site_url('ecrm/atendimento_familiares_falecidos/encerrar') ?>/"+cd_atendimento_familiares_falecidos;
		}
	}

	$(function(){
		filtrar();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	$config = array();
	$config['button'][] = array('Novo Contato', 'novo()');

	$encerrada[] = array('value' => 'N', 'text' => 'Não');
	$encerrada[] = array('value' => 'S', 'text' => 'Sim');

	echo aba_start($abas);
	    echo form_list_command_bar($config);
	    echo form_start_box_filter();
	    echo filter_participante(array('cd_empresa', 'cd_registro_empregado', 'seq_dependencia', 'nome'), 'Participante:', array(), TRUE, FALSE);	
		echo filter_date_interval('dt_ini', 'dt_fim', 'Período do Registro:');
		echo filter_dropdown('fl_encerrada', 'Encerrada:', $encerrada);
		echo filter_date_interval('dt_encerramento_ini', 'dt_encerramento_fim', 'Período do Encerramento:');
	    echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();

	$this->load->view('footer'); 
?>