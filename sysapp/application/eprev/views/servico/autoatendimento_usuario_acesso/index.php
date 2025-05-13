<?php
	set_title('Autoatendimento Usu�rio Acesso - Lista');
	$this->load->view('header');
?>
<script>
	function novo()
	{
		location.href = "<?= site_url('servico/autoatendimento_usuario_acesso/cadastro/') ?>";
	}

	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");

		$.post("<?= site_url('servico/autoatendimento_usuario_acesso/listar/') ?>",
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
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
			"DateTimeBR",
		    "CaseInsensitiveString",
			"DateTimeBR",
		    "CaseInsensitiveString",
			null,
			null
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

	function excluirUsuario(cd_usuario)
	{
		var confirmacao = 'Deseja EXCLUIR?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para N�o\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = '<?= site_url('servico/autoatendimento_usuario_acesso/excluir') ?>/'+cd_usuario;
		}		
	}
	
	function reativarUsuario(cd_usuario)
	{
		var confirmacao = 'Deseja REATIVAR?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para N�o\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = '<?= site_url('servico/autoatendimento_usuario_acesso/reativar') ?>/'+cd_usuario;
		}		
	}
	
	$(function(){
		filtrar();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	$config['button'][] = array('Liberar Usu�rio', 'novo();');

	echo aba_start($abas);
		echo form_list_command_bar($config);
		echo form_start_box_filter(); 
			echo filter_dropdown('fl_situacao', 'Situa��o:', $situacao, 'A');
	    echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();

	$this->load->view('footer');
?>