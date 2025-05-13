<?php
set_title('Relacionamento - Empresas');
$this->load->view('header');
?>
<script>
	<?php echo form_default_js_submit(array("ds_empresa" ));?>
	
	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/relacionamento_empresa"); ?>';
	}

	function ir_pessoa()
	{
		location.href='<?php echo site_url("ecrm/relacionamento_empresa/pessoas/".intval($row["cd_empresa"])); ?>';
	}
	
	function ir_contato()
	{
		location.href='<?php echo site_url("ecrm/relacionamento_empresa/contato/".intval($row["cd_empresa"])); ?>';
	}
	
	function ir_anexo()
	{
		location.href='<?php echo site_url("ecrm/relacionamento_empresa/anexo/".intval($row["cd_empresa"])); ?>';
	}
	
	function ir_agenda()
	{
		location.href='<?php echo site_url(  "ecrm/relacionamento_empresa/agenda/".intval($row['cd_empresa'])); ?>';
	}
	
	function filtrar_cidade(uf, cidade)
	{
		var select = $('#cidade');
		
		if(select.prop) 
		{
		   var options = select.prop('options');
		}
		else 
		{
		   var options = select.attr('options');
		}
		
		$('option', select).remove();
		
		options[options.length] = new Option('Selecione', '');

		if(uf != '')
		{
			$.post("<?php echo site_url('ecrm/relacionamento_empresa/cidades'); ?>", 
			{
				uf        : uf,
				fl_filtro : 'N'
			}, 
			function(data)
			{ 
				$.each(data, function(val, text) {
					options[options.length] = new Option(text.cidade, text.cidade);
				});
				
				if(cidade != "")
				{ 
					$("#cidade").val(cidade); 
				}
			}, 'json');
		}
	}
	
	function carregar_dados_geograficos(data)
	{
		cidade     = 0;
		uf         = 1;
		logradouro = 2;
		bairro     = 3;

		dados = data.toString().split('|');

		$('#uf').val(dados[uf]);
		filtrar_cidade(dados[uf], dados[cidade]);
		$('#logradouro').val(dados[logradouro]);
		$('#bairro').val(dados[bairro]);

		$('#numero').focus();
	}
	
	function excluir()
	{
		if(confirm("ATENÇÃO\n\nDeseja excluir a empresa?\n\nClique [Ok] para Sim\nClique [Cancelar] para Não\n\n"))
		{
			location.href='<?php echo site_url("ecrm/relacionamento_empresa/excluir/".intval($row["cd_empresa"])); ?>';
		}
	}
	
	function listar_emails()
	{
		$('#result_email').html("<?php echo loader_html(); ?>");
	
		$.post( '<?php echo site_url('/ecrm/relacionamento_empresa/listar_emails'); ?>',
		{
			cd_empresa : $('#cd_empresa').val()
		},
		function(data)
		{
			$('#result_email').html(data);
		});
	}
	
	function salvar_email()
	{
		if($('#ds_email').val() == '')
		{
			if( $("#ds_email").val()=="" )
			{
				alert( "Informe os campos obrigatórios! \n\n(os campos obrigatórios tem um * logo após a identificação).\n\n[ds_email]" );
				$("#ds_email").focus();
			}
		}
		else
		{	
			$.post("<?php echo site_url('ecrm/relacionamento_empresa/salvar_email'); ?>", 
			{
				cd_empresa : $('#cd_empresa').val(),
				ds_email   : $('#ds_email').val()
			}, 
			function(data)
			{ 
				$('#ds_email').val('');
				listar_emails();
			});
		}
	}
	
	function excluir_email(cd_empresa_email)
	{	
		if(confirm("ATENÇÃO\n\nDeseja excluir o email?\n\nClique [Ok] para Sim\nClique [Cancelar] para Não\n\n"))
		{
			$.post("<?php echo site_url('ecrm/relacionamento_empresa/excluir_email'); ?>", 
			{
				cd_empresa_email : cd_empresa_email
			}, 
			function(data)
			{ 
				listar_emails();
			});
		}
	}
	
	function listar_grupos()
	{
		$('#result_grupo').html("<?php echo loader_html(); ?>");
	
		$.post( '<?php echo site_url('/ecrm/relacionamento_empresa/listar_grupos'); ?>',
		{
			cd_empresa : $('#cd_empresa').val()
		},
		function(data)
		{
			$('#result_grupo').html(data);
		});
	}
	
	function salvar_grupo()
	{
		if($('#cd_grupo').val() == '')
		{
			if( $("#cd_grupo").val()=="" )
			{
				alert( "Informe os campos obrigatórios! \n\n(os campos obrigatórios tem um * logo após a identificação).\n\n[cd_grupo]" );
				$("#cd_grupo").focus();
			}
		}
		else
		{	
			$.post("<?php echo site_url('ecrm/relacionamento_empresa/salvar_grupo'); ?>", 
			{
				cd_empresa : $('#cd_empresa').val(),
				cd_grupo   : $('#cd_grupo').val()
			}, 
			function(data)
			{ 
				$('#cd_grupo').val('');
				listar_grupos();
			});
		}
	}
	
	function excluir_grupo(cd_empresa_grupo_relaciona)
	{	
		if(confirm("ATENÇÃO\n\nDeseja excluir o grupo?\n\nClique [Ok] para Sim\nClique [Cancelar] para Não\n\n"))
		{
			$.post("<?php echo site_url('ecrm/relacionamento_empresa/excluir_grupo'); ?>", 
			{
				cd_empresa_grupo_relaciona : cd_empresa_grupo_relaciona
			}, 
			function(data)
			{ 
				listar_grupos();
			});
		}
	}
	
	function listar_segmentos()
	{
		$('#result_segmento').html("<?php echo loader_html(); ?>");
	
		$.post( '<?php echo site_url('/ecrm/relacionamento_empresa/listar_segmentos'); ?>',
		{
			cd_empresa : $('#cd_empresa').val()
		},
		function(data)
		{
			$('#result_segmento').html(data);
		});
	}
	
	function salvar_segmento()
	{
		if($('#cd_segmento').val() == '')
		{
			if( $("#cd_segmento").val()=="" )
			{
				alert( "Informe os campos obrigatórios! \n\n(os campos obrigatórios tem um * logo após a identificação).\n\n[cd_segmento]" );
				$("#cd_segmento").focus();
			}
		}
		else
		{	
			$.post("<?php echo site_url('ecrm/relacionamento_empresa/salvar_segmento'); ?>", 
			{
				cd_empresa  : $('#cd_empresa').val(),
				cd_segmento : $('#cd_segmento').val()
			}, 
			function(data)
			{ 
				$('#cd_segmento').val('');
				listar_segmentos();
			});
		}
	}
	
	function salvar_evento()
	{
		if($('#cd_empresa_evento').val() == '')
		{
			if( $("#cd_empresa_evento").val()=="" )
			{
				alert( "Informe os campos obrigatórios! \n\n(os campos obrigatórios tem um * logo após a identificação).\n\n[cd_empresa_evento]" );
				$("#cd_empresa_evento").focus();
			}
		}
		else
		{	
			$.post("<?php echo site_url('ecrm/relacionamento_empresa/salvar_evento'); ?>", 
			{
				cd_empresa        : $('#cd_empresa').val(),
				cd_empresa_evento : $('#cd_empresa_evento').val()
			}, 
			function(data)
			{ 
				$('#cd_empresa_evento').val('');
				listar_evento();
			});
		}
	}
	
	function listar_evento()
	{
		$('#result_evento').html("<?php echo loader_html(); ?>");
	
		$.post( '<?php echo site_url('/ecrm/relacionamento_empresa/listar_evento'); ?>',
		{
			cd_empresa : $('#cd_empresa').val()
		},
		function(data)
		{
			$('#result_avento').html(data);
		});
	}
	
	function excluir_evento(cd_empresa_evento_relaciona)
	{	
		if(confirm("ATENÇÃO\n\nDeseja excluir o evento?\n\nClique [Ok] para Sim\nClique [Cancelar] para Não\n\n"))
		{
			$.post("<?php echo site_url('ecrm/relacionamento_empresa/excluir_evento'); ?>", 
			{
				cd_empresa_evento_relaciona : cd_empresa_evento_relaciona
			}, 
			function(data)
			{ 
				listar_evento();
			});
		}
	}
	
	function excluir_segmento(cd_empresa_segmento_relaciona)
	{	
		if(confirm("ATENÇÃO\n\nDeseja excluir o segmento?\n\nClique [Ok] para Sim\nClique [Cancelar] para Não\n\n"))
		{
			$.post("<?php echo site_url('ecrm/relacionamento_empresa/excluir_segmento'); ?>", 
			{
				cd_empresa_segmento_relaciona : cd_empresa_segmento_relaciona
			}, 
			function(data)
			{ 
				listar_segmentos();
			});
		}
	}
	
	function listarPessoas()
	{
		$('#result_pessoa').html("<?php echo loader_html(); ?>");
	
		$.post( '<?php echo site_url('/ecrm/relacionamento_empresa/pessoasListar'); ?>',
		{
			cd_empresa : $('#cd_empresa').val()
		},
		function(data)
		{
			$('#result_pessoa').html(data);
		});
	}	
	
	$(function(){
		filtrar_cidade("<?php echo $row['uf']; ?>","<?php echo $row['cidade']; ?>");
		
		if($('#cd_empresa').val() > 0)
		{	
			listarPessoas();
			listar_emails();
			listar_grupos();
			listar_segmentos();
			listar_evento();
		}
	})

</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_empresa', 'Empresa', true, 'location.reload();');

if( intval($row['cd_empresa'])>0 )
{
	$abas[] = array('aba_contato', 'Contato', FALSE, 'ir_contato();');
	$abas[] = array('aba_agenda', 'Agenda', FALSE, 'ir_agenda();');
	$abas[] = array('aba_pessoa', 'Pessoa', false, 'ir_pessoa();');
	$abas[] = array('aba_pessoa', 'Anexo', FALSE, 'ir_anexo();');
}

echo aba_start($abas);
	echo form_open('ecrm/relacionamento_empresa/salvar');
		echo form_start_box("default_box", "Cadastro de Empresas");
			echo form_hidden('cd_empresa', intval($row['cd_empresa']));
			echo form_default_text("ds_empresa", "Nome: *", $row, "style='width:400px;'");
			echo form_default_integer("nr_colaborador", "Qt de Colaboradores/Associados:", $row, "style='width:50px;'");
			echo form_default_telefone("fax", "Fax:", $row, "style='width:100px;'");
			echo form_default_integer("fax_ramal", "Ramal do fax:", $row, "style='width:100px;'");
			echo form_default_text("telefone", "Telefone:", $row);
			echo form_default_integer("telefone_ramal", "Ramal do telefone:", $row, "style='width:100px;'");
			echo form_default_text("celular", "Celular:", $row);
			echo form_default_cep("cep", "CEP:", $row, array('db' => TRUE, 'callback_function' => 'carregar_dados_geograficos'));
			echo form_default_dropdown("uf", "UF", $arr_uf, array($row["uf"]), "onchange='filtrar_cidade(this.value);'");
			echo form_default_dropdown("cidade", "Cidade:", array());
			echo form_default_text("logradouro", "Logradouro:", $row, "style='width:300px;'");
			echo form_default_integer("numero", "Número:", $row, "style='width:50px;'");
			echo form_default_text("complemento", "Complemento:", $row, "style='width:100px;'");
			echo form_default_text("bairro", "Bairro:", $row, "style='width:300px;'");
			echo form_default_text("site", "Site:", $row, "style='width:300px;'");
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();
			echo button_save();

			if(intval($row['cd_empresa']) > 0)
			{
				echo button_save('Excluir', 'excluir()', 'botao_vermelho');
			}
		echo form_command_bar_detail_end();
	echo form_close();
	if(intval($row['cd_empresa']) > 0)
	{
		echo form_start_box("info_box", "Informações" );
			echo form_default_row("","Pessoa(s):",'<div id="result_pessoa" style="width:600px;"></div>');		
		
			echo form_default_text("ds_email", "Email:", "", "style='width:300px;'");
			echo form_default_row("","",button_save('Adicionar Email', 'salvar_email()', 'botao_disabled'));
			echo form_default_row("","",'<div id="result_email" style="width:600px;"></div>');

			echo form_default_dropdown_db("cd_grupo", "Grupo:", array("expansao.empresa_grupo","cd_empresa_grupo","ds_empresa_grupo"), "", "", "", TRUE);
			echo form_default_row("","",button_save('Adicionar Grupo', 'salvar_grupo()', 'botao_disabled'));
			echo form_default_row("","",'<div id="result_grupo" style="width:600px;"></div>');

			echo form_default_dropdown_db("cd_segmento", "Segmento:", array("expansao.empresa_segmento","cd_empresa_segmento","ds_empresa_segmento"), "", "", "", TRUE);
			echo form_default_row("","",button_save('Adicionar Segmento', 'salvar_segmento()', 'botao_disabled'));
			echo form_default_row("","",'<div id="result_segmento" style="width:600px;"></div>');

			echo form_default_dropdown_db("cd_empresa_evento", "Evento:", array("expansao.empresa_evento","cd_empresa_evento","ds_empresa_evento"), "", "", "", TRUE);
			echo form_default_row("","",button_save('Adicionar Evento', 'salvar_evento()', 'botao_disabled'));
			echo form_default_row("","",'<div id="result_avento" style="width:600px;"></div>');
		echo form_end_box("info_box");	
	}
	echo br(10);
echo aba_end();

$this->load->view('footer_interna');
?>