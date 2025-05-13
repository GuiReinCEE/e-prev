<?php
set_title("Caderno CCI - Configuração de Barra Agrupada");
$this->load->view("header");
?>
<script>
	<?php
		echo form_default_js_submit(array("ds_caderno_cci_grafico_agrupamento", "nr_ordem"));
	?>

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			"Number",
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

	function ir_grafico()
	{
		location.href = "<?= site_url("gestao/caderno_cci/grafico/".$row["cd_caderno_cci"]) ?>";
	}

	function ir_rotulo()
	{
		location.href = "<?= site_url("gestao/caderno_cci/grafico_configurar/".$row["cd_caderno_cci"]."/".$grafico["cd_caderno_cci_grafico"]) ?>";
	}

	function excluir(cd_caderno_cci_grafico_agrupamento)
	{
		var confirmacao = "Deseja excluir o agrupamento?\n\n"+
			"Clique [Ok] para Sim\n\n"+
			"Clique [Cancelar] para Não\n\n";

		if(confirm(confirmacao))
		{ 
			location.href = "<?= site_url("gestao/caderno_cci/grafico_configura_agrupamento_excluir/".$row["cd_caderno_cci"]."/".$grafico["cd_caderno_cci_grafico"]) ?>/"+cd_caderno_cci_grafico_agrupamento;
		}
	}

	$(function(){
		configure_result_table();
	});

</script>
<?php
$abas[] = array("aba_lista", "Lista", FALSE, "ir_lista();");
$abas[] = array("aba_projetado", "Projetado", FALSE, "ir_projetado();");
$abas[] = array("aba_estrutura", "Estrutura", FALSE, "ir_estrutura();");
$abas[] = array("aba_indice", "Índices", FALSE, "ir_indice();");
$abas[] = array("aba_apresentacao", "Configuração de Apresentação", FALSE, "ir_grafico();");
$abas[] = array("aba_grafico", "Configuração de Gráfico", TRUE, "location.reload();");
$abas[] = array("aba_relatorio", "Relatório", FALSE, "ir_relatorio();");

$abas2[] = array("aba_rotulo", "Rótulo", FALSE, "ir_rotulo();");
$abas2[] = array("aba_grupo", "Agrupamento", TRUE, "location.reload();");

$body = array();
$head = array( 
	"Ordem",
	"Nome do Agrupamento"
);

foreach ($rotulo as $key => $value) 
{
	$head[count($head)] = $value["ds_caderno_cci_grafico_rotulo"];
}

$head[count($head)] = "";

$i = 0;

foreach( $collection as $item )
{
	$body[$i] = array(
		$item["nr_ordem"],
		array(anchor("gestao/caderno_cci/grafico_configura_agrupamento/".$row["cd_caderno_cci"]."/".$item["cd_caderno_cci_grafico"]."/".$item["cd_caderno_cci_grafico_agrupamento"], $item["ds_caderno_cci_grafico_agrupamento"]), "text-align:left;")
	);

	foreach ($rotulo as $key => $value) 
	{
		$body[$i][count($body[$i])] = array($item[$value["cd_caderno_cci_grafico_rotulo"]], "text-align:left;");
	}

	$body[$i][count($body[$i])] = '<a href="javascript:void(0);" onclick="excluir('.$item["cd_caderno_cci_grafico_agrupamento"].')">[excluir]</a>';

	$i++;
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo aba_start($abas);
	echo aba_start($abas2);
		echo form_open("gestao/caderno_cci/grafico_configura_agrupamento_salvar");
			echo form_default_hidden("cd_caderno_cci_grafico", "", $grafico);	
			echo form_default_hidden("cd_caderno_cci", "", $row);	
			echo form_default_hidden("cd_caderno_cci_grafico_agrupamento", "", $grafico_agrupamento);	

			echo form_start_box("default_box", "Gráfico");	
				echo form_default_row("", "Nome do Gráfico :", $grafico["ds_caderno_cci_grafico"]);
			echo form_end_box("default_box");

			echo form_start_box("default_box", "Cadastro");	
				
				echo form_default_text("ds_caderno_cci_grafico_agrupamento", "Nome do Agrupamento :*", $grafico_agrupamento, 'style="width:300px;"');
				echo form_default_integer("nr_ordem", "Ordem :*", $grafico_agrupamento);
				
				foreach ($rotulo as $key => $value) 
				{
					$value_agrupamento = "";

					if(isset($grafico_agrupamento[$value["cd_caderno_cci_grafico_rotulo"]]))
					{
						$value_agrupamento = $grafico_agrupamento[$value["cd_caderno_cci_grafico_rotulo"]];
					}

					echo form_default_dropdown("arr_agrupamento[".$value["cd_caderno_cci_grafico_rotulo"]."]", $value["ds_caderno_cci_grafico_rotulo"]." :", $arr_rotulo, array($value_agrupamento));
				}
				
			echo form_end_box("default_box");

			echo form_command_bar_detail_start();
				echo button_save("Salvar");	
			echo form_command_bar_detail_end();
		echo $grid->render();
		echo br(5);
	echo aba_end();
echo aba_end();

$this->load->view("footer_interna");
?>