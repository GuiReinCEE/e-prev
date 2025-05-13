<?php
set_title("Caderno CCI");
$this->load->view("header");
?>
<script>
	<?php
		echo form_default_js_submit(array("nr_ano"));
	?>

	function ir_lista()
	{
		location.href = "<?= site_url("gestao/caderno_cci") ?>";
	}
	
</script>
<?php
$abas[] = array("aba_lista", "Lista", FALSE, "ir_lista();");
$abas[] = array("aba_cadastro", "Cadastro", TRUE, "location.reload();");

echo aba_start($abas);
	echo form_open("gestao/caderno_cci/salvar");
		echo form_start_box("default_box", "Cadastro");
			echo form_default_hidden("cd_caderno_cci", "", $row);	
			echo form_default_hidden("cd_caderno_cci_referencia", "", $row["cd_caderno_cci_referencia"]);	

			if(intval($row["cd_caderno_cci"]) == 0)
			{
				echo form_default_integer("nr_ano", "Ano :*", $row);
			}
			else
			{
				echo form_default_row("nr_ano", "Ano :", '<label class="label label-inverse">'.$row["nr_ano"]."</label>");
			}
			
		echo form_end_box("default_box");

		echo form_command_bar_detail_start();
			if(intval($row["cd_caderno_cci"]) == 0)
			{
				if(intval($row["cd_caderno_cci_referencia"]) > 0)
				{

					echo button_save("Salvar e Replicar Ano Anterior");	
				}
				else
				{
					echo button_save("Salvar");	
				}
			}
		echo form_command_bar_detail_end();
	echo form_close();
	echo br(5);
echo aba_end();
$this->load->view("footer_interna");
?>