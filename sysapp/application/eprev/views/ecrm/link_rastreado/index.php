<?php
	set_title('Link Rastreado (Log)');
	$this->load->view('header');
?>
<style>
	.coluna-padrao-form-objeto {
		font-size: 10pt;
	}
</style>
<script>
	function filtrar()
	{
		linkLog();
	}
	
	function ir_gerar()
	{
		location.href='<?php echo site_url("ecrm/link_rastreado/gerar_index"); ?>';
	}
	
	function linkLog()
	{
		if(jQuery.trim($("#ds_url").val()) != "")
		{
			$("#result_div_log").html("<?php echo loader_html(); ?>");

			$.post( '<?php echo base_url().index_page(); ?>/ecrm/link_rastreado/linkLog',
				{
					ds_url        : $("#ds_url").val(),
					dt_acesso_ini : $("#dt_acesso_ini").val(),
					dt_acesso_fim : $("#dt_acesso_fim").val()
				}
				,
				function(data)
				{
					$("#result_div_log").html(data);
					linkLog_result();
					linkLogHora_result();
				}
			);
		}
		else
		{
			alert("Informe o Link");
			$("#ds_url").focus();
		}
	}
	
	function linkLog_result()
	{
		var ob_resul = new SortableTable(document.getElementById("tabela_linkLog"),
		[
			'DateBR',  
			'Number',  
			'Number',  
			'Number'
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
	
	function linkLogDia(dt_acesso)
	{
		if(jQuery.trim($("#ds_url").val()) != "")
		{
			$("#result_div_log").html("<?php echo loader_html(); ?>");

			$.post( '<?php echo base_url().index_page(); ?>/ecrm/link_rastreado/linkLogDia',
				{
					ds_url    : $("#ds_url").val(),
					dt_acesso : dt_acesso
				}
				,
				function(data)
				{
					$("#result_div_log").html(data);
					linkLogDia_result();
				}
			);
		}
		else
		{
			alert("Informe o Link");
			$("#ds_url").focus();
		}
	}	

	function linkLogDia_result()
	{
		var ob_resul = new SortableTable(document.getElementById("tabela_linkLog"),
		[
			'DateTimeBR',  
			'DateTimeBR',  
			'Number',  
			'Number',  
			'Number'
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
	

	function linkLogHora_result()
	{
		var ob_resul = new SortableTable(document.getElementById("tabela_linkLogHora"),
		[
			'CaseInsensitiveString',  
			'Number',  
			'Number',  
			'Number'
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
	
	function ir_tecnologia()
	{
		location.href = '<?php echo site_url("ecrm/link_rastreado/tecnologia/"); ?>' + "/" + $("#cd_link").val();
	}

	$(function(){
		$("#ds_url").width(400);
		$("#aba_tecnologia").hide();
		filtrar();
	});	
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', False, 'ir_gerar();');
	$abas[] = array('aba_relatorio', 'Relatório', TRUE, 'location.reload();');
	$abas[] = array('aba_tecnologia', 'Tecnologia', FALSE, 'ir_tecnologia()');	
	
	echo aba_start( $abas );

		echo form_list_command_bar();	
		echo form_start_box_filter('filter_bar', 'Filtros');
			echo form_default_hidden('cd_link',"Cod Link:");
			
			echo (trim($ar_param['ds_url']) != "" ? filter_text('ds_url',"Link: (*)", $ar_param['ds_url']) : filter_text('ds_url',"Link: (*)"));
			echo filter_date_interval('dt_acesso_ini', 'dt_acesso_fim', 'Período do Acesso: ');
		echo form_end_box_filter();	

		echo '<div id="result_div_log" align="center"><BR><BR><span class="label label-success">Realize um filtro para exibir a lista</span></div>';
		echo br(5);
		
	
	echo aba_end(); 
	$this->load->view('footer');
?>