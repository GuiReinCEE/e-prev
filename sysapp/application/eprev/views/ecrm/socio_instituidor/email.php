<?php
    set_title('Sócio Instituidor');
    $this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");

        $.post("<?= site_url('ecrm/socio_instituidor/listar_email') ?>",
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
            "DateTimeBR",
            "DateTimeBR", 
			"CaseInsensitiveString",
            "CaseInsensitiveString",
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
		ob_resul.sort(8, true);
	}

	function ir_lista()
	{
		location.href = "<?= site_url('ecrm/socio_instituidor') ?>";
	}	

	$(function(){
		filtrar();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_email', 'Email', TRUE, 'location.reload();');

	echo aba_start($abas);
    	echo form_list_command_bar(array());
    	echo form_start_box_filter();
    		echo filter_date_interval('dt_email_ini', 'dt_email_fim', 'Período do email:',calcular_data('','2 month'), date('d/m/Y'));
    	echo form_end_box_filter();
    	echo br();
    	echo '
            <div id="result_div">
                <span class="label label-success">Realize um filtro para exibir a listar</span>
            </div>';
    	echo br(2);
    echo aba_end();
    $this->load->view('footer');
?>