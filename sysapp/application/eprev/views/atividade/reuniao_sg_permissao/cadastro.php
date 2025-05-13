<?php
set_title('Reunião SG - Controle');
$this->load->view('header');
?>
<script>
<?php
echo form_default_js_submit(Array('usuario_gerencia', 'usuario'));
?>
	
    function ir_lista()
    {
        location.href='<?php echo site_url("atividade/reuniao_sg_permissao"); ?>';
    }
	
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

echo aba_start($abas);

echo form_open('atividade/reuniao_sg_permissao/salvar');

	echo form_start_box("default_box", "Cadastro");
		echo form_default_hidden("cd_reuniao_sg_permissao", "", $row['cd_reuniao_sg_permissao']);
		echo form_default_usuario_ajax('usuario', $row['divisao'], $row['cd_usuario'], "Usuário :* ", "Gerência :* ");
	echo form_end_box("default_box");

echo form_command_bar_detail_start();
	 echo button_save("Salvar");
echo form_command_bar_detail_end();
echo form_close();

echo br(2);

echo aba_end();
$this->load->view('footer_interna');
?>