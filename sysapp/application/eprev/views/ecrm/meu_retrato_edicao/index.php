<?php
	set_title('Meu Retrato Edição');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
				
		$.post("<?= site_url('ecrm/meu_retrato_edicao/listar') ?>",
		$("#filter_bar_form").serialize(),
		function(data)
		{
			$("#result_div").html(data);
			configure_result_table();
		});	
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("tabela_mr_lista"),
		[
			null,
			"Number",
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "Number",
		    "DateBR",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
		    null,
		    "DateTimeBR",
		    "DateTimeBR",
		    "CaseInsensitiveString",
		    "DateTimeBR",
		    "CaseInsensitiveString",
		    "DateTimeBR",
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
		ob_resul.sort(6, true);
	}
	
    function cadastro(cd_edicao)
    {
    	location.href = "<?= site_url('ecrm/meu_retrato_edicao/cadastro') ?>/"+cd_edicao;
    }
	
	function checkAll()
	{
		var ipts = $("#tabela_mr_lista>tbody").find("input:checkbox");
		var check = document.getElementById("checkboxCheckAll");
	 
		check.checked ?
			jQuery.each(ipts, function(){
				this.checked = true;
			}) :
			jQuery.each(ipts, function(){
				this.checked = false;
			});
	}	
	
	function getCheck()
	{
		var ipts = $("#tabela_mr_lista>tbody").find("input:checkbox:checked");
		
		$("#edicao_selecionada").val("");
		
		jQuery.each(ipts, function(){
			//alert(this.name + " => " + this.value);
			if(jQuery.trim($("#edicao_selecionada").val()) == "")
			{
				$("#edicao_selecionada").val(this.value);
			}
			else
			{
				$("#edicao_selecionada").val($("#edicao_selecionada").val() + "," + this.value);
			}
		});
	}	

	function setGerar()
	{
		getCheck();
		if(jQuery.trim($("#edicao_selecionada").val()) != "")
		{		
			if(confirm("ATENÇÃO\n\nAs edições selecionados serão marcadas para GERAR.\n\nDeseja GERAR?"))
			{
				document.getElementById('filter_bar_form').action = '<?php echo site_url('/ecrm/meu_retrato_edicao/setGerar/');?>';
				document.getElementById('filter_bar_form').method = "post";
				document.getElementById('filter_bar_form').target = "_self";
				$("#filter_bar_form").submit();		
			}
		}
		else
		{
			alert("Selecione pelo menos uma edição");
		}
	}		
	
				
	$(function(){
		filtrar();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	$config['button'][] = array('Nova Edição', 'cadastro(0)');

	echo aba_start($abas);
		echo form_list_command_bar((gerencia_in(array('GGS')) ? $config : array()));
		echo form_start_box_filter(); 
			echo filter_plano_empresa_ajax('cd_plano', '', '', 'Plano:', 'Empresa:');

			echo filter_integer('nr_extrato', 'Nº Extrato:');
			echo filter_dropdown('tp_participante', 'Tipo Participante:', $tipo_participante);
			echo filter_dropdown('dt_base_extrato', 'Dt. Base Extrato:', $data_base);
			
			echo form_hidden('edicao_selecionada',"Ed Sel:");
	    echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();

	$this->load->view('footer');
?>