<?php
set_title("Caderno CCI");
$this->load->view("header");
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
				
		$.post('<?= site_url("gestao/caderno_cci/listar") ?>',
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
		location.href = '<?= site_url("gestao/caderno_cci/cadastro") ?>';
	}
	
	function excluir(cd_caderno_cci)
	{	
		var confirmacao = 'Deseja excluir o ano?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = '<?= site_url("gestao/caderno_cci/excluir") ?>/' + cd_caderno_cci;
		}
	}

	function apresentacao(cd ,opcao, mes)
	{
		var gerar;

		if(opcao == 0)
		{
			gerar = 'N';
		}
		else
		{
			gerar = 'S';
		}

		window.open("<?= site_url("gestao/caderno_cci_relatorio/apresentacao")?>/"+ cd +"/"+gerar+"/"+mes);
	}

	function csv(cd)
	{
		window.open("<?= site_url("gestao/caderno_cci_csv/index")?>/"+ cd);
	}


	$(function(){
		filtrar();
	});

</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	$config['button'][] = array('Novo Ano', 'novo();');

	echo aba_start($abas);
		echo form_list_command_bar($config);
		echo form_start_box_filter(); 
			echo filter_integer('nr_ano', 'Ano:');
	    echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br();
	echo aba_end();
	$this->load->view('footer');
?>