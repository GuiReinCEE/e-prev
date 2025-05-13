<?php $this->load->view('header', array('topo_titulo'=>'Envio de FAX')); ?>
<script>
	function salvar( form )
	{
		/*
		if( $("#cd_empresa").val()=="" ) 
		{
			alert( "Informe a Empresa" );
			$("#cd_empresa").focus();
			return false;
		}
		
		if( $("#cd_registro_empregado").val()=="" ) 
		{
			alert( "Informe o RE" );
			$("#cd_registro_empregado").focus();
			return false;
		}	

		if( $("#seq_dependencia").val()=="" ) 
		{
			alert( "Informe a Sequência" );
			$("#seq_dependencia").focus();
			return false;
		}
		*/
		
		if( $("#nr_telefone").val()=="" )
		{
			alert( "Informe o Número do FAX " );
			$("#nr_telefone").focus();
			return false;
		}
		
		if( $("#userfile").val()=="" )
		{
			alert( "Informe o arquivo para envio do FAX" );
			$("#userfile").focus();
			return false;
		}
		
		var arquivo = $("#userfile").val();
		if(((arquivo.substr(arquivo.lastIndexOf('.'),arquivo.length)).toLowerCase()!='.pdf') && 
		   ((arquivo.substr(arquivo.lastIndexOf('.'),arquivo.length)).toLowerCase()!='.txt') && 
		   ((arquivo.substr(arquivo.lastIndexOf('.'),arquivo.length)).toLowerCase()!='.tif'))
		{
			alert('Tipo de arquivo inválido.\n\nArquivos permitidos: PDF, TXT e TIF');
			return false;
		} 
		

		if( confirm('ATENÇÃO: O envio não poderá ser desfeito\n\nConfirma o envio?') )
		{
			form.submit();
		}
		
	}

	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/envio_fax"); ?>';
	}
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_detalhe', 'Enviar FAX', true, 'location.reload();');
	echo aba_start( $abas );

	echo form_open_multipart('ecrm/envio_fax/salvar');

	// Registros da tabela principal ...
	echo form_start_box( "default_box", "Envio de FAX" );
	echo form_default_participante(); 
	echo form_default_integer("nr_telefone", "Nr FAX*"); 
	echo form_default_row("","Arquivo (pdf,txt,tif)*:","<input type='file' name='userfile' id='userfile'>");
	echo form_end_box("default_box");

	// Barra de comandos ...
	echo form_command_bar_detail_start();
	echo button_save("Enviar");

	echo form_command_bar_detail_button("Voltar para lista", "location.href='".site_url('ecrm/envio_fax')."'");
	echo form_command_bar_detail_end();
?>
<script>
	 $('#cd_empresa').focus();
	jQuery(function($){
	   $("#nr_telefone").mask("(99) 99999999");
	});	

	<?php
		if($fl_retorno == "OK")
		{
			echo "alert('Um e-mail será enviado para você quando o fax for enviado ou em caso de erro.');";
			echo "location.href='".site_url("ecrm/envio_fax/detalhe")."';";
		}
	?>
</script>

<?php
echo aba_end();
// FECHAR FORM
echo form_close();

$this->load->view('footer_interna');
?>