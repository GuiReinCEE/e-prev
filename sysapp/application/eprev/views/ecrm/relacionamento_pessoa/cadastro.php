<?php
set_title('Cadastro de Pessoas');
$this->load->view('header');
?>
<script>
	<?php echo form_default_js_submit(array("ds_pessoa"));?>
	
	function ir_lista()
	{
		if($('#cd_empresa_relacionamento').val() > 0)
		{
			location.href='<?php echo site_url( "ecrm/relacionamento_empresa"); ?>';
		}
		else
		{
			location.href='<?php echo site_url("ecrm/relacionamento_pessoa"); ?>';
		}
	}
	
	function ir_empresa()
	{
		if($('#cd_empresa_relacionamento').val() > 0)
		{
			location.href='<?php echo site_url("ecrm/relacionamento_empresa/cadastro/".intval($cd_empresa_relacionamento)); ?>';
		}
		else
		{
			location.href='<?php echo site_url("ecrm/relacionamento_pessoa/cadastro/".intval($row["cd_pessoa"])); ?>';
		}
	}
	
	function ir_contato()
	{
		if($('#cd_empresa_relacionamento').val() > 0)
		{
			location.href='<?php echo site_url("ecrm/relacionamento_empresa/contato/".intval($cd_empresa_relacionamento)); ?>';
		}
		else
		{
			location.href='<?php echo site_url("ecrm/relacionamento_pessoa/contato/".intval($row["cd_pessoa"])); ?>';
		}
	}
	
	function ir_pessoa()
	{
		location.href='<?php echo site_url("ecrm/relacionamento_empresa/pessoas/".$cd_empresa_relacionamento); ?>';
	}
	
	function ir_anexo()
	{
		location.href='<?php echo site_url("ecrm/relacionamento_empresa/anexo/".$cd_empresa_relacionamento); ?>';
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
			$.post("<?php echo site_url('ecrm/relacionamento_pessoa/cidades'); ?>", 
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
			$.post("<?php echo site_url('ecrm/relacionamento_pessoa/salvar_email'); ?>", 
			{
				cd_pessoa : $('#cd_pessoa').val(),
				ds_email  : $('#ds_email').val()
			}, 
			function(data)
			{ 
				$('#ds_email').val('');
				listar_emails();
			});
		}
	}
	
	function listar_emails()
	{
		$('#result_div').html("<?php echo loader_html(); ?>");
	
		$.post( '<?php echo site_url('/ecrm/relacionamento_pessoa/listar_emails'); ?>',
		{
			cd_pessoa : $('#cd_pessoa').val()
		},
		function(data)
		{
			$('#result_div').html(data);
		});
	}
	
	function excluir_email(cd_pessoa_email)
	{	
		if(confirm("ATENÇÃO\n\nDeseja excluir o email?\n\nClique [Ok] para Sim\nClique [Cancelar] para Não\n\n"))
		{
			$.post("<?php echo site_url('ecrm/relacionamento_pessoa/excluir_email'); ?>", 
			{
				cd_pessoa_email : cd_pessoa_email
			}, 
			function(data)
			{ 
				listar_emails();
			});
		}
	}
	
	function excluir()
	{
		if(confirm("ATENÇÃO\n\nDeseja excluir o contato?\n\nClique [Ok] para Sim\nClique [Cancelar] para Não\n\n"))
		{
			location.href='<?php echo site_url("ecrm/relacionamento_pessoa/excluir/".intval($row["cd_pessoa"])); ?>';
		}
	}

	$(function(){
		filtrar_cidade("<?php echo $row['uf']; ?>","<?php echo $row['cidade']; ?>");
		
		if($('#cd_pessoa').val() > 0)
		{
			consultar_participante_focus__cd_empresa();
			
			listar_emails();
		}
	})
</script>

<?php
if(intval($cd_empresa_relacionamento) > 0)
{
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_lista', 'Empresa', FALSE, 'ir_empresa();');
	$abas[] = array('aba_lista', 'Contato', FALSE, 'ir_contato();');
	$abas[] = array('aba_lista', 'Pessoa', FALSE, 'ir_pessoa();');
	$abas[] = array('aba_lista', 'Cadastro Pessoa', TRUE, 'location.reload();');
	$abas[] = array('aba_lista', 'Anexo', FALSE, 'ir_anexo();');
}
else
{
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_lista', 'Pessoa', TRUE, 'location.reload();');
	
	if(intval($row['cd_pessoa']) > 0)
	{
		$abas[] = array('aba_contato', 'Contato', FALSE, 'ir_contato();');
	}
}
echo $row["cd_pessoa_departamento"];
echo aba_start( $abas );
	echo form_open('ecrm/relacionamento_pessoa/salvar');
		echo form_start_box("default_box", "Cadastro");
			echo form_hidden('cd_pessoa', intval($row['cd_pessoa']));
			echo form_hidden('cd_empresa_relacionamento', intval($cd_empresa_relacionamento));
			echo form_default_text("ds_pessoa", "Nome: *", $row, "style='width:300px;'");
			echo form_default_dropdown("cd_pessoa_empresa", "Empresa:", $arr_empresa, array($row["cd_pessoa_empresa"]));
			echo form_default_dropdown_db("cd_pessoa_departamento", "Departamento:", array("expansao.pessoa_departamento","cd_pessoa_departamento","ds_pessoa_departamento"), $row["cd_pessoa_departamento"], "", "", TRUE);
			echo form_default_dropdown_db("cd_pessoa_cargo", "Cargo:", array("expansao.pessoa_cargo","cd_pessoa_cargo","ds_pessoa_cargo"), $row["cd_pessoa_cargo"], "", "", TRUE);
			echo form_default_telefone("fax", "Fax:", $row);
			echo form_default_integer("fax_ramal", "Ramal do Fax:", $row, "style='width:100px;'");
			echo form_default_telefone("telefone", "Telefone:", $row);
			echo form_default_integer("telefone_ramal", "Ramal do Telefone:", $row, "style='width:100px;'");
			echo form_default_telefone("celular", "Celular:", $row);
			echo form_default_cep("cep", "CEP:", $row, array('db'=> TRUE, 'callback_function' => 'carregar_dados_geograficos'));
			echo form_default_dropdown("uf", "UF:", $arr_uf, array($row["uf"]), "onchange='filtrar_cidade(this.value);'");
			echo form_default_dropdown("cidade", "Cidade:", array());
			echo form_default_text("logradouro", "Logradouro:", $row, "style='width:300px;'");
			echo form_default_integer("numero", "Número:", $row, "style='width:50px;'");
			echo form_default_text("complemento", "Complemento:", $row, "style='width:100px;'");
			echo form_default_text("bairro", "Bairro:", $row, "style='width:300px;'");
			echo form_default_text("site", "Site:", $row, "style='width:300px;'");
			echo form_default_participante(array('cd_empresa','cd_registro_empregado','seq_dependencia', 'nome_participante'),'Participante:', array("cd_empresa" => $row["cd_empresa"], "cd_registro_empregado" => $row["cd_registro_empregado"], "seq_dependencia" => $row["seq_dependencia"] ), FALSE, TRUE);
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();
			echo button_save();

			if(intval($row['cd_pessoa']) > 0)
			{
				echo button_save('Excluir', 'excluir()', 'botao_vermelho');
			}
		echo form_command_bar_detail_end();
	echo form_close();
	if(intval($row['cd_pessoa']) > 0)
	{
		echo form_start_box( "emails_box", "Emails" );
			echo form_default_text("ds_email", "Email: ", "", "style='width:300px;'");
		echo form_end_box("emails_box");
		echo form_command_bar_detail_start();
			echo button_save('Adicionar Email', 'salvar_email()');
		echo form_command_bar_detail_end();
		echo '<div id="result_div"></div>';
	}
echo aba_end();

$this->load->view('footer_interna');
?>