<?php 
	set_title('Conferência de Documentos');
	$this->load->view('header'); 
?>
<script>
    function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
			
		$.post("<?= site_url('atividade/documento_protocolo_conferencia/listar') ?>",
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
	    	"CaseInsensitiveString",
	    	"DateTimeBR",
	    	"CaseInsensitiveString",
	    	"Date",
	    	"Number",
	    	"CaseInsensitiveString",
	    	"CaseInsensitiveString",
	    	"Number",
	    	"Number",
	    	"CaseInsensitiveString",
	    	"CaseInsensitiveString",
	    	"Number",
	    	"CaseInsensitiveString",
	    	"CaseInsensitiveString",
	    	null,
	    	"CaseInsensitiveString"
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

    function valida_status(cd_documento_protocolo_conf_gerencia_item)
    {
    	var fl_status = $("#cd_documento_conferencia_"+cd_documento_protocolo_conf_gerencia_item).val();

    	if(fl_status == 'C')
    	{
    		$("#cd_documento_conferencia_"+cd_documento_protocolo_conf_gerencia_item).hide();

    		$("#documento_conferencia_" + cd_documento_protocolo_conf_gerencia_item).html("<?= loader_html('P') ?>");
    		
    		$("#documento_conferencia_"+cd_documento_protocolo_conf_gerencia_item).show();

    		$.post("<?= site_url('atividade/documento_protocolo_conferencia/salvar_conferencia') ?>",
	        {
	            cd_documento_protocolo_conf_gerencia_item : cd_documento_protocolo_conf_gerencia_item,
	            fl_status 								  : 'C'
	        },
	        function(data){

	            $("#ocultar_ajuste_"+cd_documento_protocolo_conf_gerencia_item).empty();
	            
	            $("#documento_conferencia_"+cd_documento_protocolo_conf_gerencia_item).empty();

	            $("#documento_conferencia_" + cd_documento_protocolo_conf_gerencia_item).html(data.dt_conferencia);

	        },'json');
    	}
    	else if(fl_status == 'A')
    	{
    		location.href = "<?= site_url('atividade/documento_protocolo_conferencia/ajuste') ?>/" + cd_documento_protocolo_conf_gerencia_item;
    	}
    }

	$(function(){
		if($("#mes_referencia").val() == "")
		{
			$("#mes_referencia").val("<?= $nr_mes ?>");
		}

		if($("#ano_referencia").val() == "")
		{
			$("#ano_referencia").val("<?= $nr_ano ?>");	
		}

		if($("#fl_status").val() == "")
		{
			$("#fl_status").val("<?= $fl_status ?>");	
		}

		filtrar();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

    echo aba_start($abas);
        echo form_list_command_bar(array());
        echo form_start_box_filter('filter_bar', 'Filtros', TRUE);
			echo filter_mes('mes_referencia', 'Mês:');
			echo filter_dropdown('ano_referencia', 'Ano:', $ano);
			echo form_default_dropdown('fl_status', 'Status:', $drop_status);
        echo form_end_box_filter();
        echo '<div id="result_div"></div>';
		echo br(2);
    echo aba_end();
    $this->load->view('footer');
?>