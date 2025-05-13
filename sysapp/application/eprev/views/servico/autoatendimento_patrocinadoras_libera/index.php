<?php
	set_title('Patrocinadoras Libera Autoatendimento');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
				
		$.post("<?= site_url('servico/autoatendimento_patrocinadoras_libera/listar') ?>",
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
			"Number",
			null,
		    "Number",
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
			null,
			null,
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
		ob_resul.sort(0, false);
	}
					
	function novo()
	{
		location.href = "<?= site_url('servico/autoatendimento_patrocinadoras_libera/cadastro') ?>";
	}

	function alterar_ordem(cd_patrocinadoras_libera)
    {
        $("#ajax_ordem_valor_" + cd_patrocinadoras_libera).html("<?= loader_html('P') ?>");

        $.post("<?= site_url('servico/autoatendimento_patrocinadoras_libera/altera_ordem') ?>",
        {
            cd_patrocinadoras_libera : cd_patrocinadoras_libera,
            nr_ordem : $("#nr_ordem_" + cd_patrocinadoras_libera).val()	
        },
        function(data)
        {
			$("#ajax_ordem_valor_" + cd_patrocinadoras_libera).empty();
			
			$("#nr_ordem_" + cd_patrocinadoras_libera).hide();
			$("#ordem_salvar_" + cd_patrocinadoras_libera).hide(); 
			
            $("#ordem_valor_" + cd_patrocinadoras_libera).html($("#nr_ordem_" + cd_patrocinadoras_libera).val()); 
			$("#ordem_valor_" + cd_patrocinadoras_libera).show(); 
			$("#ordem_editar_" + cd_patrocinadoras_libera).show();
        });
    }	
	
	function editar_ordem(cd_patrocinadoras_libera)
	{
		$("#ordem_valor_" + cd_patrocinadoras_libera).hide(); 
		$("#ordem_editar_" + cd_patrocinadoras_libera).hide(); 

		$("#ordem_salvar_" + cd_patrocinadoras_libera).show(); 
		$("#nr_ordem_" + cd_patrocinadoras_libera).show(); 
		$("#nr_ordem_" + cd_patrocinadoras_libera).focus();	
	}
	
	$(function(){
		filtrar();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	$config['button'][] = array('Nova Patrocinadora', 'novo();');
	
	echo aba_start($abas);
		echo form_list_command_bar($config);
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();

	$this->load->view('footer');
?>