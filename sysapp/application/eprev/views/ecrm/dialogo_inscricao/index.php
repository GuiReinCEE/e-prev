<?php
set_title('Diálogo Inscrições');
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		load();
	}
	
	function load()
	{
		if($('#cd_dialogo').val() > 0)
		{
			$("#result_div").html("<?php echo loader_html(); ?>");
			$.post( '<?php echo base_url() . index_page(); ?>/ecrm/dialogo_inscricao/inscricaoListar'
				,{
					cd_dialogo  : $('#cd_dialogo').val(),				
					fl_presente : $('#fl_presente').val()					
				}
				,
			function(data)
				{
					$("#result_div").html(data);
					configure_result_table();
				}
			);
		}
		else
		{
			alert("Informe a edição.");
			$('#cd_dialogo').focus();
		}
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),[
					"Number",
					"RE",
					"CaseInsensitiveString",
					"CaseInsensitiveString",
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
		ob_resul.sort(4, true);
	}
	
	
	function setPresente(fl_presente, cd_dialogo_inscricao)
	{
		$.post( '<?php echo base_url() . index_page(); ?>/ecrm/dialogo_inscricao/setPresente'
			,{
				cd_dialogo_inscricao : cd_dialogo_inscricao,					
				fl_presente          : fl_presente					
			}
			,
		function(data)
			{
				if(data != "1")
				{
					alert("ERRO");
				}
			}
		);
	}	
	
	function enviaCertificado(cd_certificado)
	{
		if(confirm("ATENÇÃO\n\nEsta ação é IRREVERSÍVEL.\n\nDeseja enviar o email?"))
		{
			$.post( '<?php echo base_url() . index_page(); ?>/ecrm/dialogo_inscricao/enviaCertificado'
				,{
					cd_certificado : cd_certificado		
				}
				,
			function(data)
				{
					if(data != "1")
					{
						alert("ERRO");
					}
					else
					{
						alert("Certificado enviado");
						
					}
				}
			);
		}
	}	


	function enviaCertificadoLista()
	{
		if(confirm("ATENÇÃO\n\nSerá enviado EMAIL para todos os PRESENTES.\n\nEsta ação é IRREVERSÍVEL.\n\nDeseja enviar o email?"))
		{
			$.post( '<?php echo base_url() . index_page(); ?>/ecrm/dialogo_inscricao/enviaCertificadoLista'
				,{}
				,
			function(data)
				{
					if(data != "1")
					{
						alert("ERRO");
					}
					else
					{
						alert("Certificados enviados");
						filtrar();
					}
				}
			);
		}
	}	
</script>
<?php
	$abas[0] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
	echo aba_start( $abas );

	#$config['button'][]=array('Enviar Email Certificado', 'enviaCertificadoLista();');
	#echo form_list_command_bar($config);	
	Echo form_list_command_bar();	
	echo form_start_box_filter('filter_bar', 'Filtros', TRUE);
		echo filter_dropdown('cd_dialogo', 'Edição: *', $ar_edicao);

		$ar_tipo = Array(Array('text' => 'Sim', 'value' => 'S'),Array('text' => 'Não', 'value' => 'N')) ;
		echo filter_dropdown('fl_presente', 'Presente:', $ar_tipo);
		
		
	echo form_end_box_filter();

?>
<div id="result_div"><br><br><span style='color:green;'><b>Realize um filtro para exibir a lista</b></span></div>
<br />
<?php echo aba_end(''); ?>
<script>
	$(document).ready(function() {
		filtrar();
	});	
</script>
<?php
$this->load->view('footer');
?>