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

	function ir_historico()
	{
		location.href = "<?= site_url("cadastro/biblioteca_sg/historico/".$row["cd_biblioteca_livro"]) ?>";
	}
</script>
<?php
$abas[] = array("aba_lista", "Lista", FALSE, "ir_lista();");
$abas[] = array("aba_cadastro", "Cadastro", TRUE, "location.reload();");

if(intval($row["cd_biblioteca_livro"]) > 0)
{
	$abas[] = array("aba_lista", "Histórico", FALSE, "ir_historico();");
}

echo aba_start( $abas );
	echo form_open("cadastro/biblioteca_sg/salvar");
		echo form_start_box("default_box", "Cadastro");
			echo form_default_hidden("cd_biblioteca_livro", "", $row);	
			echo form_default_integer("nr_biblioteca_livro", "Cód :*", $row);
			echo form_default_text("ds_biblioteca_livro", "Título :*", $row);
			echo form_default_text("autor", "Autor :", $row);
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();
			echo button_save("Salvar");	
		echo form_command_bar_detail_end();
	echo form_close();
echo aba_end();
$this->load->view("footer_interna");
?>