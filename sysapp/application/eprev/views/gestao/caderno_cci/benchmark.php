<?php
set_title("Caderno CCI - Benchmark");
$this->load->view("header");
?>
<script>
	<?php
		echo form_default_js_submit(array("ds_caderno_cci_benchmark"));
	?>

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			"Number",
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
		ob_resul.sort(0, false);
	}

	function ir_lista()
	{
		location.href = "<?= site_url("gestao/caderno_cci") ?>";
	}

	function ir_projetado()
	{
		location.href = "<?= site_url("gestao/caderno_cci/projetado/".$row["cd_caderno_cci"]) ?>";
	}

	function ir_estrutura()
	{
		location.href = "<?= site_url("gestao/caderno_cci/estrutura/".$row["cd_caderno_cci"]) ?>";
	}

	function ir_indice()
	{
		location.href = "<?= site_url("gestao/caderno_cci/indice/".$row["cd_caderno_cci"]) ?>";
	}

	function ir_relatorio()
	{
		location.href = "<?= site_url("gestao/caderno_cci_relatorio/index/".$row["cd_caderno_cci"]) ?>";
	}

	function cancelar()
	{
		location.href = "<?= site_url("gestao/caderno_cci/benchmark/".$row["cd_caderno_cci"]) ?>";
	}

	function valores()
	{
		location.href = "<?= site_url("gestao/caderno_cci/benchmark_valor/".$row["cd_caderno_cci"]) ?>";
	}

	function ir_grafico()
	{
		location.href = "<?= site_url("gestao/caderno_cci/grafico/".$row["cd_caderno_cci"]) ?>";
	}

	function ir_rentabilidade()
	{
		location.href = "<?= site_url("gestao/caderno_cci/rentabilidade_planos_segmentos/".$row["cd_caderno_cci"]) ?>";
	}

	function ir_rentabilidade_planos()
	{
		location.href = "<?= site_url("gestao/caderno_cci/rentabilidade_planos/".$row["cd_caderno_cci"]) ?>";
	}

	function ir_rentabilidade_aberto()
	{
		location.href = "<?= site_url("gestao/caderno_cci/rentabilidade_planos_aberto/".$row["cd_caderno_cci"]) ?>";
	}

	function excluir(cd_caderno_cci_benchmark)
	{	
		var confirmacao = "Deseja excluir o benchmark?\n\n"+
			"Clique [Ok] para Sim\n\n"+
			"Clique [Cancelar] para N�o\n\n";

		if(confirm(confirmacao))
		{ 
			location.href = "<?= site_url("gestao/caderno_cci/benchmark_excluir/".$row["cd_caderno_cci"]) ?>/" + cd_caderno_cci_benchmark;
		}
	}

	$(function(){
		$("#result_div").html("<?= loader_html() ?>");
				
		$.post("<?= site_url("gestao/caderno_cci/benchmark_listar") ?>",
		{
			cd_caderno_cci : "<?= $row["cd_caderno_cci"] ?>"
		},
		function(data)
		{
			$("#result_div").html(data);
			configure_result_table();
		});	
	});
</script>
<?php
$abas[] = array("aba_lista", "Lista", FALSE, "ir_lista();");
$abas[] = array("aba_projetado", "Projetado", FALSE, "ir_projetado();");
$abas[] = array("aba_estrutura", "Estrutura", FALSE, "ir_estrutura();");
$abas[] = array("aba_indice", "�ndices", TRUE, "location.reload();");
$abas[] = array("aba_grafico", "Configura��o de Apresenta��o", FALSE, "ir_grafico();");
$abas[] = array("aba_relatorio", "Relat�rio", FALSE, "ir_relatorio();");
$abas[] = array("aba_rentabiliade", "Rentabilidade - Planos e Segmentos", FALSE, "ir_rentabilidade();");

$abas[] = array("aba_rentabiliade_planos", "Rentabilidade - Planos", FALSE, "ir_rentabilidade_planos();");
$abas[] = array("aba_rentabiliade_planos_aberto", "Rentabilidade - Planos (Aberto)", FALSE, "ir_rentabilidade_aberto();");

$abas2[] = array("aba_indice2", "�ndice", FALSE, "ir_indice();");
$abas2[] = array("aba_benchmark", "Benchmark", TRUE, "location.reload();");

echo aba_start($abas);
echo aba_start($abas2);
	echo form_open("gestao/caderno_cci/benchmark_salvar");
		echo form_start_box("default_box", "Cadastro");
			echo form_default_hidden("cd_caderno_cci_benchmark", "", $benchmark);	
			echo form_default_hidden("cd_caderno_cci", "", $row);	

			echo form_default_row("nr_ano", "Ano :", '<label class="label label-inverse">'.$row["nr_ano"]."</label>");
			echo form_default_integer("nr_ordem", "Ordem :", $benchmark);
			echo form_default_text("ds_caderno_cci_benchmark", "Benchmark :*", $benchmark, 'style="width:300px;"');
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();
			echo button_save("Salvar");	
			
			if(intval($benchmark["cd_caderno_cci_benchmark"]) > 0)
			{
				echo button_save("Cancelar", "cancelar();", "botao_disabled");	
			}

			echo button_save("Valores", "valores()", "botao_verde");
		echo form_command_bar_detail_end();
	echo form_close();
	echo '<div id="result_div"></div>';
	echo br(5);
echo aba_end();
$this->load->view("footer_interna");
?>