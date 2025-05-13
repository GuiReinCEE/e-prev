<?php
set_title('Protocolo Digitalização - Descarte de Documentos');
$this->load->view('header');
?>
<script>
<?php
    echo form_default_js_submit(Array('cd_tipo_doc', 'fl_descarte'));
?>
    
    function ir_lista()
    {
        location.href='<?php echo site_url("ecrm/documento_protocolo_descarte"); ?>';
    }
	
	function excluir()
	{
		if(confirm('Deseja excluir documento do descarte?'))
        {
			location.href='<?php echo site_url("ecrm/documento_protocolo_descarte/excluir/".intval($row['cd_documento'])); ?>';
		}
	}
	
	function callback_buscar_verifica_documento()
	{
		$.post( '<?php echo site_url('/ecrm/documento_protocolo_descarte/verifica_documento'); ?>',
		{
			cd_documento : $('#cd_tipo_doc').val()
		},
		function(restultado)
		{
			if(restultado == 'erro')
			{
				location.href='<?php echo site_url("ecrm/documento_protocolo_descarte/cadastro/"); ?>/'+$('#cd_tipo_doc').val()+'/'+'<?php echo $this->session->userdata('divisao') ?>';
			}
		});
	}
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_nc', 'Cadastro', TRUE, 'location.reload();');

$arr[] = array('value' => 'S', 'text' => 'Sim'); 
$arr[] = array('value' => 'N', 'text' => 'Não'); 

echo aba_start( $abas );
    echo form_open('ecrm/documento_protocolo_descarte/salvar', 'name="filter_bar_form"');
        echo form_start_box( "default_box", "Cadastro" );
			echo form_hidden('acao', (intval($row['cd_documento']) > 0 ? 'e' : 's'));
			if(($row['cd_documento']) > 0)
			{
				echo form_hidden('cd_tipo_doc', intval($row['cd_documento']));
				echo form_default_text('documento', "Documento:", $row['documento'], "style='width:500px;border: 0px;' readonly" );	
				echo form_default_text('gerencia', "Gerência:", $row['gerencia'], "style='width:500px;border: 0px;' readonly" );	
			}
			else
			{
				echo form_default_tipo_documento(array('caption' => 'Documento:* ', 'callback_buscar' => 'callback_buscar_verifica_documento();'));
			}
			echo form_default_dropdown('fl_descarte', 'Descartar:* ', $arr, array($row['fl_descarte']));
        echo form_end_box("default_box");
        echo form_command_bar_detail_start();   
			if((intval($row['cd_documento']) == 0) OR ((intval($row['cd_documento']) > 0) AND $this->session->userdata('divisao') == trim($row['cd_divisao'])))
			{
				echo button_save("Salvar");
			}
			if((intval($row['cd_documento']) > 0) AND ((intval($row['cd_documento']) > 0) AND $this->session->userdata('divisao') == trim($row['cd_divisao'])))
			{
				echo button_save("Excluir", 'excluir();', 'botao_vermelho');
			}
        echo form_command_bar_detail_end();
    echo form_close();
    echo br();	
echo aba_end();

$this->load->view('footer_interna');
?>