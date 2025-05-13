<?php
$this->load->view('header_interna');
?>
<script type="text/javascript">
<!--
	function save_form(f)
	{
		if( confirm('Salvar?') )
		{
			f.submit();
		}
	}

	function protocolo_redirect()
	{
		window.location = '<?php echo base_url().index_page(); ?>/documento/recebido/protocolo';
	}

	function lista_redirect()
	{
		window.location = '<?php echo base_url().index_page(); ?>/documento/recebido';
	}
//-->
</script>
<?
echo form_open("documento/recebido/adicionar");

echo form_start_box("protocolo", "Protocolo");

echo form_default_row(
	""
	, ""
	, form_input(
		array(
			'type'=>'button'
			, 'value'=>'Incluir Protocolo'
			, 'onclick'=>'protocolo_redirect();'
			, 'class'=>'botao'
		)
	) 
);

echo form_default_dropdown("cd_documento_recebido", "Protocolo:", $documento_recebido_collection);
echo form_end_box("protocolo");

echo form_start_box("participante", "Participante");
echo form_default_participante(array("cd_empresa", "cd_registro_empregado", "seq_dependencia", "nome_participante"), "Participante:");
echo form_end_box("participante");

echo form_start_box("documento", "Documento");
echo form_default_dropdown("cd_tipo_doc", "Tipo de documento:", $tipo_documentos_collection);
echo form_default_text("ds_observacao", "Observação:", "");
echo form_default_text("nr_folha", "Nº Folhas:", "");
echo form_end_box("documento");

echo "<center>";
echo form_input(array('id'=>'salvar', 'name'=>'salvar', 'type'=>'button', 'class'=>'botao', 'onclick'=>'save_form(this.form)'), "Salvar");
echo form_input(array('id'=>'voltar', 'name'=>'voltar', 'type'=>'button', 'class'=>'botao', 'onclick'=>'lista_redirect();'), "Voltar");
echo "</center>";

$this->load->view('footer_interna');
?>