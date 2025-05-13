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

	function apresentacao(opcao, mes)
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

		window.open("<?= site_url("gestao/caderno_cci_relatorio/apresentacao/".$row["cd_caderno_cci"]) ?>/"+gerar+"/"+mes);
	}

	function pdf(opcao, mes)
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

		window.open("<?= site_url("gestao/caderno_cci_relatorio/pdf/".$row["cd_caderno_cci"]) ?>/"+gerar+"/"+mes);
	}

	function fechar(mes)
	{
		location.href = "<?= site_url("gestao/caderno_cci_relatorio/fechar/".$row["cd_caderno_cci"]) ?>/"+mes;
	}

</script>
<?php
$abas[] = array("aba_lista", "Lista", FALSE, "ir_lista();");
$abas[] = array("aba_projetado", "Projetado", FALSE, "ir_projetado();");
$abas[] = array("aba_estrutura", "Estrutura", FALSE, "ir_estrutura();");
$abas[] = array("aba_indice", "Índices", FALSE, "ir_indice();");
$abas[] = array("aba_grafico", "Configuração de Apresentação", FALSE, "ir_grafico();");
$abas[] = array("aba_relatorio", "Relatório", TRUE, "location.reload();");
$abas[] = array("aba_rentabiliade", "Rentabilidade - Planos e Segmentos", FALSE, "ir_rentabilidade();");
$abas[] = array("aba_rentabiliade_planos", "Rentabilidade - Planos", FALSE, "ir_rentabilidade_planos();");
$abas[] = array("aba_rentabiliade_planos_aberto", "Rentabilidade - Planos (Aberto)", FALSE, "ir_rentabilidade_aberto();");

$body = array();
$head = array( 
	"Mês",
	"Usuário",
	"Dt. Fechamento",
	""
);

foreach($collection as $item)
{
	$body[] = array(
		array($item["text"], "text-align:left"),
		array($item["nome"], "text-align:left"),
		$item["dt_inclusao"],
		'<a href="javascript:void(0);" onclick="apresentacao('.(trim($item["dt_inclusao"]) == '' ? '0' : '1').', '.$item["value"].')">[apresentação]</a> '.
		/*'<a href="javascript:void(0);" onclick="pdf('.(trim($item["dt_inclusao"]) == '' ? '0' : '1').', '.$item["value"].')">[pdf]</a> '.*/
		(trim($item["dt_inclusao"]) == '' ? '<a href="javascript:void(0);" onclick="fechar('.$item["value"].')">[fechar]</a> ' : '')
	);
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