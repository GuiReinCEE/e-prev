<?php
set_title("Biblioteca SG");
$this->load->view("header");
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
				
		$.post('<?= site_url("cadastro/biblioteca_sg/listar") ?>',
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
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
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
		ob_resul.sort(0, true);
	}
					
	function novo()
	{
		location.href = '<?= site_url("cadastro/biblioteca_sg/cadastro") ?>';
	}
	
	function excluir(cd_biblioteca_livro)
	{	
		var confirmacao = 'Deseja excluir o livro?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = '<?= site_url("cadastro/biblioteca_sg/excluir") ?>/' + cd_biblioteca_livro;
		}
	}

	function devolver(cd_biblioteca_livro_movimento)
	{	
		var confirmacao = 'Deseja devolver o livro?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = '<?= site_url("cadastro/biblioteca_sg/devolver") ?>/' + cd_biblioteca_livro_movimento;
		}
	}

	function confirmar(cd_biblioteca_livro_movimento)
	{	
		var confirmacao = 'Deseja confirmar o recebimento do livro?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = '<?= site_url("cadastro/biblioteca_sg/confirmar") ?>/' + cd_biblioteca_livro_movimento;
		}
	}

	function alugar(cd_biblioteca_livro)
	{
		location.href = '<?= site_url("cadastro/biblioteca_sg/alugar/") ?>/'+cd_biblioteca_livro;
	}


	$(function(){
		filtrar();
	});

</script>
<?php
$abas[] = array("aba_lista", "Lista", TRUE, "location.reload();");

$config["button"][] = array("Novo livro", "novo();");

echo aba_start( $abas );
	echo form_list_command_bar((gerencia_in(array("SG")) ? $config : array()));
	echo form_start_box_filter(); 
		echo filter_integer("nr_biblioteca_livro", "Cód. :");
		echo filter_text("ds_biblioteca_livro", "Título :", "", 'style="width:400px;"');
		echo filter_text("autor", "Autor :", "", 'style="width:400px;"');
    echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br(5);
echo aba_end();
$this->load->view('footer');
?>