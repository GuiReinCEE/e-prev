<?php
	set_title('Menu Autoatendimento');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
				
		$.post("<?= site_url('servico/autoatendimento_menu/listar') ?>",
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
		location.href = "<?= site_url('servico/autoatendimento_menu/cadastro') ?>";
	}

	function alterar_ordem(cd_menu)
    {
        $("#ajax_ordem_valor_" + cd_menu).html("<?= loader_html('P') ?>");

        $.post("<?= site_url('servico/autoatendimento_menu/altera_ordem') ?>",
        {
            cd_menu : cd_menu,
            nr_ordem : $("#nr_ordem_" + cd_menu).val()	
        },
        function(data)
        {
			$("#ajax_ordem_valor_" + cd_menu).empty();
			
			$("#nr_ordem_" + cd_menu).hide();
			$("#ordem_salvar_" + cd_menu).hide(); 
			
            $("#ordem_valor_" + cd_menu).html($("#nr_ordem_" + cd_menu).val()); 
			$("#ordem_valor_" + cd_menu).show(); 
			$("#ordem_editar_" + cd_menu).show();
        });
    }	
	
	function editar_ordem(cd_menu)
	{
		$("#ordem_valor_" + cd_menu).hide(); 
		$("#ordem_editar_" + cd_menu).hide(); 

		$("#ordem_salvar_" + cd_menu).show(); 
		$("#nr_ordem_" + cd_menu).show(); 
		$("#nr_ordem_" + cd_menu).focus();	
	}
	
	$(function(){
		filtrar();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	$config['button'][] = array('Novo Menu', 'novo();');
	
	echo aba_start($abas);
		echo form_list_command_bar($config);
		echo form_start_box_filter(); 
			echo filter_text('ds_menu', 'Menu:', '', 'style="width:300px;"');
			echo filter_dropdown('fl_status', 'Status:', $fl_status, 'A');
	    echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();

	$this->load->view('footer');
?>