<?php
set_title("Simulador de Benefício");
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?php echo loader_html(); ?>");
		
		$.post( '<?php echo site_url('/ecrm/simulador_beneficio/listar');?>',
		$("#filter_bar_form").serialize(),
		function(data)
		{
			$("#result_div").html(data);
			configure_result_table();
		});
	}

	function configure_result_table()
	{
		/*
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			'Number',
			'',
			'DateTimeBR',
			'DateTimeBR',
			'CaseInsensitiveString',
			'DateTimeBR', 
			'CaseInsensitiveString'

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
		*/
	}

	function formParticipante(id)
	{
		var retorno = true;
		var fl_participante = $("#fl_participante_"+id).val();
		
		if(fl_participante == "S")
		{
			$('#form_simulador_'+id+' :input').each(function() 
			{
				if($(this).attr('id') == "EMP")
				{
					$(this).val($("#cd_empresa_"+id).val());
				}
				else if($(this).attr('id') == "RE")
				{
					$(this).val($("#cd_registro_empregado_"+id).val());
				}
				else if($(this).attr('id') == "SEQ")
				{
					$(this).val($("#seq_dependencia_"+id).val());
				}			
			});			
			
			$('#form_simulador_'+id+' :input').each(function() 
			{
				if(($(this).attr('id') == "EMP") && (jQuery.trim($(this).val()) == ""))
				{
					retorno = false;
				}
				else if(($(this).attr('id') == "RE") && (jQuery.trim($(this).val()) == ""))
				{
					retorno = false;
				}
				else if(($(this).attr('id') == "SEQ") && (jQuery.trim($(this).val()) == ""))
				{
					retorno = false;
				}			
			});
		}		

		if(retorno)
		{
			$('#form_simulador_'+id).submit();
		}
		else
		{
			alert("Informe o RE do Participante");
		}
	}
	
	$(function(){
		filtrar();
	});

</script>
<?php
$abas[] = array('aba_lista', 'Simuladores', TRUE, 'location.reload();');
echo aba_start($abas);
	echo '<div id="result_div"><br><br><span style="color:green;"><b>Realize um filtro para exibir a lista</b></span></div>';
	

	
	echo br(3);
echo aba_end();
$this->load->view('footer'); 
?>
