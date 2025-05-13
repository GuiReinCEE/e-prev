<?php
set_title("Caderno CCI - Relatório");
$this->load->view("header");
?>
<script>
	<?php
		echo form_default_js_submit(array("mes"));
	?>

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

	function ir_grafico()
	{
		location.href = "<?= site_url("gestao/caderno_cci/grafico/".$row["cd_caderno_cci"]) ?>";
	}

	function ir_relatorio()
	{
		location.href = "<?= site_url("gestao/caderno_cci_relatorio/index/".$row["cd_caderno_cci"]) ?>";
	}

	function ir_rentabilidade()
	{
		location.href = "<?= site_url("gestao/caderno_cci/rentabilidade_planos_segmentos/".$row["cd_caderno_cci"]) ?>";
	}

	function ir_rentabilidade_planos()
	{
		location.href = "<?= site_url("gestao/caderno_cci/rentabilidade_planos/".$row["cd_caderno_cci"]) ?>";
	}

</script>
<?php
$abas[] = array("aba_lista", "Lista", FALSE, "ir_lista();");
$abas[] = array("aba_projetado", "Projetado", FALSE, "ir_projetado();");
$abas[] = array("aba_estrutura", "Estrutura", FALSE, "ir_estrutura();");
$abas[] = array("aba_indice", "Índices", FALSE, "ir_indice();");
$abas[] = array("aba_grafico", "Configuração de Apresentação", FALSE, "ir_grafico();");
$abas[] = array("aba_relatorio", "Relatório", FALSE, "ir_relatorio();");
$abas[] = array("aba_rentabiliade", "Rentabilidade - Planos e Segmentos", FALSE, "ir_rentabilidade();");
$abas[] = array("aba_rentabiliade_planos", "Rentabilidade - Planos", FALSE, "ir_rentabilidade_planos();");
$abas[] = array("aba_rentabiliade_planos_aberto", "Rentabilidade - Planos (Aberto)", TRUE, "location.reload();");


$body = array();
$head = array( 
	'Segmento'
);

foreach ($meses as $key => $item) 
{
	$head[] = mes_extenso($item).'/'.$row["nr_ano"];
}

$head[] = 'Acum.';
$head[] = 'Bench.';

$i = 0;

foreach($collection as $item)
{
	$body[$i][] = array('<b>'.$item["ds_caderno_cci_estrutura"].'</b>', "text-align:left");

	foreach ($item['rentabilidade'] as $rentabilidade)
	{
		$body[$i][] = (trim($rentabilidade) != '' ? number_format($rentabilidade, 2, ",", ".").' %' : '');
	}

	$i++;

	if(isset($item['sub']))
	{
		foreach ($item['sub'] as $item_sub) 
		{
			$body[$i][] = array('<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$item_sub["ds_caderno_cci_estrutura"].'</b>', "text-align:left");

			foreach ($item_sub['rentabilidade'] as $rentabilidade_sub)
			{
				$body[$i][] = (trim($rentabilidade_sub) != '' ? number_format($rentabilidade_sub, 2, ",", ".").' %' : '');
			}

			$i++;
		}
	}
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo aba_start($abas);
	echo form_start_box("default_box", "Cadastro");
		echo form_default_hidden("cd_caderno_cci", "", $row);	
		echo form_default_hidden("nr_ano", "", $row);	
		echo form_default_row("nr_ano", "Ano :", '<label class="label label-inverse">'.$row["nr_ano"]."</label>");
	echo form_end_box("default_box");
	echo $grid->render();
	echo br();
echo aba_end();
$this->load->view("footer_interna");
?>