<?php
$this->load->view('header_sem_menu');
?>
<script type="text/javascript">
<!--
	function documento_redirect()
	{
		location.href = "<?php echo base_url().index_page(); ?>/documento/recebido/documento";
	}
//-->
</script>
<?

echo form_open("documento/recebido/criar");
echo form_start_box("box", "Protocolo");

echo form_default_text("nr_ano", "Ano:", "");
echo form_default_text("nr_contador", "Sequência:", "");
echo form_default_dropdown("cd_documento_recebido_tipo", "Tipo de documento:", $tipo_documentos_collection);

echo form_end_box("box");
echo "<center>";
echo form_submit(array('class'=>'botao'), "Salvar");
echo form_input(array('type'=>'button', 'class'=>'botao', 'onclick'=>'documento_redirect();'), "Voltar");
echo "</center>";

$this->load->view('footer_sem_menu');
?>