<?php
set_title('Área Restrita - Validar Login');
$this->load->view('header');
?>
<script>
	<?php 
		echo form_default_js_submit(array("validar_login_senha"),'formValida(form)');	
	?>
	
	function formValida(form)
	{
		form.submit();
	}
	
	$(function(){
		$("#validar_login_senha").focus();
		$("#validar_login_senha").removeAttr("onkeypress");
		$("#validar_login_senha").keydown(function(e) {
			if(e.which == 13) {
				$("#validar_login_form").submit();
				e.preventDefault();
				return false;
			}
		});
	});
</script>
<?php
$abas[] = array('aba_lista', 'Área Restrita', TRUE, 'location.reload();');

echo aba_start($abas);
	echo form_open('login/validar_login',array("id"=>"validar_login_form","method"=>"post"));
		echo form_start_box("default_box", "Login" );
			echo form_default_hidden('validar_login_ir_para', "Ir para:", $validar_login_ir_para);
			echo form_default_text('validar_login_usuario', "Usuário:", $this->session->userdata('usuario'), 'style="font-size: 150%;" readonly');
			echo form_default_password('validar_login_senha', "Senha:(*)","",'style="font-size: 150%;"');
			echo form_default_row("","", "<i>Para acessar, informe sua senha da rede<BR>
A senha da rede é aquela que você utiliza quando liga seu computador<BR>
Clique no botão enviar<BR>
Em caso de dúvida entre em contato com o Suporte da Informática</i>");
		echo form_end_box("default_box");

		echo form_command_bar_detail_start();
			echo button_save("Enviar");
			echo button_save("Cancelar","location.reload();","botao_disabled");
		echo form_command_bar_detail_end();
	echo form_close();
	echo br(5);	
echo aba_end();

$this->load->view('footer_interna');
?>