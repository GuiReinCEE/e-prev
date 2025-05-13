<?php
	set_title('Pauta SG - Integrantes');
	$this->load->view('header');
?>
<script>
    function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
			
		$.post("<?= site_url('gestao/pauta_sg_integrante/listar') ?>",
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
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
            "DateTimeBR",
			null
		]);
		ob_resul.onsort = function ()
		{
			var rows = ob_resul.tBody.rows;
			var l = rows.length;
			for (var i = 0; i < l; i++)
			{
				removeClassName(rows[i], i % 2 ? "sort-par" : "sort-impar");
				addClassName(rows[i], i % 2 ? "sort-impar" : "sort-par");
			}
		};
		ob_resul.sort(0, false);
	}

	function ir_cadastro()
	{
		location.href = "<?= site_url('gestao/pauta_sg_integrante/cadastro') ?>";
	}

	function remover(cd_pauta_sg_integrante)
	{
		var confirmacao = "Deseja remover este item?\n\n"+
						  "[OK] para Sim\n\n"+
						  "[Cancelar] para Não\n\n";

		if(confirm(confirmacao))
		{
			location.href = "<?= site_url('gestao/pauta_sg_integrante/remover') ?>/" + cd_pauta_sg_integrante;
		}
	}

	function ativar(cd_pauta_sg_integrante)
	{
		var confirmacao = "Deseja ativar este item?\n\n"+
						  "[OK] para Sim\n\n"+
						  "[Cancelar] para Não\n\n";

		if(confirm(confirmacao))
		{
			location.href = "<?= site_url('gestao/pauta_sg_integrante/ativar') ?>/" + cd_pauta_sg_integrante;
		}
	}

    $(function(){
		filtrar();

		$("#fl_removido").val("N");
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

    $config['button'][] = array('Novo Integrante', 'ir_cadastro();');

    echo aba_start($abas);
        echo form_list_command_bar($config);
        echo form_start_box_filter(); 
        	echo filter_dropdown('fl_colegiado', 'Colegiado:', $colegiado);
        	echo filter_dropdown('fl_removido', 'Removido:', $removido);
        echo form_end_box_filter();
        echo '<div id="result_div"></div>';
		echo br(2);
    echo aba_end();

    $this->load->view('footer');

?>