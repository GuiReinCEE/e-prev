<?php
set_title("Biblioteca SG");
$this->load->view("header");
?>
<script>
	<?php
		echo form_default_js_submit(array("nr_biblioteca_livro", "ds_biblioteca_livro"));
	?>

	function ir_lista()
	{
		location.href = "<?= site_url("cadastro/biblioteca_sg") ?>";
	}

	function ir_cadastro()
	{
		location.href = "<?= site_url("cadastro/biblioteca_sg/cadastro/".$row["cd_biblioteca_livro"]) ?>";
	}
</script>
<?php
$abas[] = array("aba_lista", "Lista", FALSE, "ir_lista();");
$abas[] = array("aba_cadastro", "Cadastro", FALSE, "ir_cadastro();");
$abas[] = array("aba_lista", "Histórico", TRUE, "location.reload();");

$body = array();
$head = array( 
	"CPF",
	"Nome",
	"Dt. Retirada",
	"Dt. Recebimento",
	"Dt. Devolução"
);

foreach( $collection as $item )
{
	$body[] = array(
		$item["cpf"],
		array($item["nome"], "text-align:left;"),
		$item["dt_retirada"],
		$item["dt_recebido"],
		$item["dt_devolvido"]
	);
}

$this->load->helper("grid");
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo aba_start( $abas );
	echo form_start_box("default_box", "Cadastro");
		echo form_default_row("nr_biblioteca_livro", "Cód :", $row["nr_biblioteca_livro"]);
		echo form_default_row("ds_biblioteca_livro", "Título :", $row["ds_biblioteca_livro"]);
		echo form_default_row("autor", "Autor :", $row["autor"]);
	echo form_end_box("default_box");
	echo $grid->render();
echo aba_end();
$this->load->view("footer_interna");
?>