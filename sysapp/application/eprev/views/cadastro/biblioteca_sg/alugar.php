<?php
set_title("Biblioteca SG - Empréstimo");
$this->load->view("header");
?>
<script>
	<?php
		echo form_default_js_submit(array("cpf"));
	?>

	function ir_lista()
	{
		location.href = "<?= site_url("cadastro/biblioteca_sg") ?>";
	}

	function busca_participante($cpf)
	{
		
		$.post('<?= site_url("cadastro/biblioteca_sg/busca_participante") ?>',
		{
			cpf : $cpf.val()
		},
		function(data)
		{
			$("#nome_item").html(data.nome);
		}, "json");	
	}
</script>
<?php
$abas[] = array("aba_lista", "Lista", FALSE, "ir_lista();");
$abas[] = array("aba_cadastro", "Cadastro", TRUE, "location.reload();");

echo aba_start( $abas );
	echo form_open("cadastro/biblioteca_sg/locacao_salvar");
		echo form_start_box("default_box", "Livro");
			echo form_default_hidden("cd_biblioteca_livro", "", $row);	
			echo form_default_row("nr_biblioteca_livro", "Cód :", $row["nr_biblioteca_livro"]);
			echo form_default_row("ds_biblioteca_livro", "Título :", $row["ds_biblioteca_livro"]);
			echo form_default_row("autor", "Autor :", $row["autor"]);
			echo form_default_cpf("cpf", "CPF :*", "", 'onblur="busca_participante($(this));"');
			echo form_default_row("nome", "Nome :", "");
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();
			echo button_save("Salvar");	
		echo form_command_bar_detail_end();
	echo form_close();
echo aba_end();
$this->load->view("footer_interna");
?>