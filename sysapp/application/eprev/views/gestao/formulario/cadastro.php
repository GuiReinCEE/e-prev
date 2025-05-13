<?php
set_title('Cadastro de Formulários');
$this->load->view('header');
?>
<script>
	<?php
		echo form_default_js_submit(Array('nr_formulario','ds_formulario', 'fl_tipo'),'_salvar(form)');
	?>
	function _salvar(form)
	{
		if($('#fl_tipo').val() == 'D')
		{
			if($('#arquivo').val() == '' && $('#arquivo_nome').val() == '')
			{
				alert('Nenhum arquivo foi anexado.');
				return false;
			}
			else
			{
				if( confirm('Salvar?') )
				{
					form.submit();
				}
			}
		}
		else
		{
			if( confirm('Salvar?') )
			{
				form.submit();
			}
		}
	}	
	
	function ir_lista()
	{
		location.href='<?php echo site_url("gestao/formulario"); ?>';
	}
	
	function tipo()
	{
		var fl_tipo = $('#fl_tipo').val();
		
		if(fl_tipo == 'D')
		{
			$('#arquivo_row').show();
		}
		else
		{
			$('#arquivo_row').hide();
		}
	}
	
	$(function(){
		tipo();
	});	
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

$arr_tipo[] = array('value' => 'I', 'text' => 'Impresso');
$arr_tipo[] = array('value' => 'D', 'text' => 'Digital');

echo aba_start( $abas );
	echo form_open('gestao/formulario/salvar');
		echo form_start_box( "default_box", "Cadastro" );
			echo form_default_hidden('cd_formulario', "", $row);	
			echo form_default_integer('nr_formulario', 'Código :*', $row);
			echo form_default_text('ds_formulario', 'Descrição :*', $row, 'style="width:450px;"');
			echo form_default_dropdown('fl_tipo', 'Tipo :*', $arr_tipo, array($row['fl_tipo']), 'onclick="tipo()"');
			echo form_default_upload_iframe('arquivo', 'cadastro_formulario', 'Anexo :*', $row['arquivo'], 'cadastro_formulario');
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();
			echo button_save("Salvar");	
		echo form_command_bar_detail_end();
	echo form_close();
echo aba_end();
$this->load->view('footer_interna');
?>