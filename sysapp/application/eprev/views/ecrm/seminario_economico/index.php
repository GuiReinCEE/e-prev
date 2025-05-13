<?php
set_title('Seminário Econômico - Lista');
$this->load->view('header');
?>
<script>
	function enviarCertificado(cd_inscricao)
	{
		if($('#cd_seminario_edicao').val() != "")
		{
			var msg_confirma = "";
			if(parseInt(cd_inscricao) == 0)
			{
				msg_confirma = "ATENÇÃO\n\nEsta ação é IRREVERSÍVEL.\n\nDeseja enviar o certificado para TODOS presentes no seminário?\n\n[OK] para Sim\n[Cancelar] para Não\n\n";
			}
			else
			{
				msg_confirma = "ATENÇÃO\n\nEsta ação é IRREVERSÍVEL.\n\nDeseja enviar o certificado para:\n\n" + $('#seminario_nome_'+cd_inscricao).html()+"\n\n[OK] para Sim\n[Cancelar] para Não\n\n";
			}
			
			if(confirm(msg_confirma))
			{
				$('#enviarCertificado').attr('disabled', true);
				$('#result_div').html("<br><br><span style='color:blue; font-family: calibri, arial; font-size: 20pt;'><b>Aguarde.<BR>Enviando email(s)...<BR>"+"<?php echo loader_html(); ?>"+"</b></span>")

				$.post( '<?php echo base_url() . index_page(); ?>/ecrm/seminario_economico/certificado',
				{
					cd_seminario_edicao : $('#cd_seminario_edicao').val(),
					cd_inscricao        : cd_inscricao
				}
				,
				function(data)
				{
					if(parseInt(data) > -1)
					{
						alert("Foram enviado "+data+" email(s).");
					}
					else
					{
						alert(data);
					}
					$('#enviarCertificado').attr('disabled', false);
					filtrar()
				}
				);
			}
		}
		else
		{
			alert("Informe a edição do Seminário");
		}
	
	}

	function filtrar()
	{
		if($('#cd_seminario_edicao').val() != "")
		{
			document.getElementById("result_div").innerHTML = "<?php echo loader_html(); ?>";
			$.post( '<?php echo base_url() . index_page(); ?>/ecrm/seminario_economico/listar',
			{
				cd_seminario_edicao : $('#cd_seminario_edicao').val(),
				dt_inclusao_ini     : $('#dt_inclusao_ini').val(),
				dt_inclusao_fim     : $('#dt_inclusao_fim').val(),
				fl_presente         : $('#fl_presente').val(),
				fl_email            : $('#fl_email').val()
				
			}
			,
			function(data)
			{
				document.getElementById("result_div").innerHTML = data;
				configure_result_table();
			}
			);
		}
		else
		{
			alert("Informe a edição do Seminário");
			$('#cd_seminario_edicao').focus();
		}
	
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),[
					"Number",
					"RE",
					"CaseInsensitiveString",
					"CaseInsensitiveString",
					"CaseInsensitiveString",
					"DateTimeBR",
					"CaseInsensitiveString",
					"CaseInsensitiveString",
					"DateTimeBR"
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
		ob_resul.sort(2, false);
	}
</script>
<?php
	$abas[] = Array('aba_lista', 'Lista', TRUE, 'location.reload();');
	echo aba_start($abas);

	$config['button'][] = Array('Enviar certificado', 'enviarCertificado(0);','enviarCertificado');
	echo form_list_command_bar($config);	
	echo form_start_box_filter('filter_bar', 'Filtros');
		echo filter_dropdown('cd_seminario_edicao', 'Edição:', $ar_seminario_edicao);		
		echo filter_date_interval('dt_inclusao_ini', 'dt_inclusao_fim', 'Data de inscrição:');
		
		$ar_presente = Array(Array('text' => 'Todos', 'value' => ''),Array('text' => 'Sim', 'value' => 'S'),Array('text' => 'Não', 'value' => 'N')) ;
		echo filter_dropdown('fl_presente', 'Presente:', $ar_presente);	
		
		$ar_email = Array(Array('text' => 'Todos', 'value' => ''),Array('text' => 'Sim', 'value' => 'S'),Array('text' => 'Não', 'value' => 'N')) ;
		echo filter_dropdown('fl_email', 'Email:', $ar_email);	
	echo form_end_box_filter();
?>
<div id="result_div"><br><br><span style='color:green;'><b>Realize um filtro para exibir a lista</b></span></div>
<BR>
<BR>
<script>
	filtrar();
</script>
<?php
	echo aba_end(''); 
	$this->load->view('footer');
?>