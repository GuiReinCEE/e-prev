<?php
set_title("Protocolo Interno - Grupo");
$this->load->view('header');
?>
<script>

<?php echo form_default_js_submit(array("ds_nome"));?>

function ir_lista()
{
    location.href="<?= site_url("ecrm/cadastro_protocolo_interno_grupo") ?>";
}

function ir_usuario()
{
    location.href="<?= site_url("ecrm/cadastro_protocolo_interno_grupo/usuario/".intval($row["cd_documento_recebido_grupo"])) ?>";
}

</script>
<?php
$abas[] = array("aba_lista", "Lista", FALSE, "ir_lista();");
$abas[] = array("aba_lista", "Cadastro", TRUE, "location.reload();");

if(intval($row["cd_documento_recebido_grupo"]) > 0)
{
	$abas[] = array("aba_lista", "Usuários", FALSE, "ir_usuario();");
}

echo aba_start($abas);
	echo form_open("ecrm/cadastro_protocolo_interno_grupo/salvar");
		echo form_start_box("default_box", "Cadastro");
			echo form_hidden("cd_documento_recebido_grupo", $row["cd_documento_recebido_grupo"]);
			echo form_default_text("ds_nome", "Descrição :*", $row["ds_nome"], 'style="width: 300px;"');	
			echo form_default_text("email_grupo", "Email :", $row["email_grupo"], 'style="width: 300px;"');	
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();     
			echo button_save("Salvar");
        echo form_command_bar_detail_end();
	echo form_close();
	echo br();
echo aba_end();

$this->load->view("footer_interna");
?>