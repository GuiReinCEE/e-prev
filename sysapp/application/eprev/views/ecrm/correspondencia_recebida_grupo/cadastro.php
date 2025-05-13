<?php
set_title('Protocolo Correspondência Recebida');
$this->load->view('header');
?>
<script>

<?php echo form_default_js_submit(array('ds_nome'));?>

function ir_lista()
{
    location.href='<?php echo site_url('ecrm/correspondencia_recebida_grupo');?>';
}

function ir_usuario()
{
    location.href='<?php echo site_url('ecrm/correspondencia_recebida_grupo/usuario/'.intval($row['cd_correspondencia_recebida_grupo']));?>';
}

</script>
<?php
$abas[] = array( 'aba_lista', 'Lista', false, 'ir_lista();' );
$abas[] = array('aba_nc', 'Cadastro', TRUE, 'location.reload();');

if(intval($row['cd_correspondencia_recebida_grupo']) > 0)
{
	$abas[] = array('aba_lista', 'Usuários', false, 'ir_usuario();');
}

echo aba_start( $abas );
	echo form_open('ecrm/correspondencia_recebida_grupo/salvar');
		echo form_start_box( "default_box", "Cadastro" );
			echo form_hidden('cd_correspondencia_recebida_grupo', $row['cd_correspondencia_recebida_grupo']);
			echo form_default_text('ds_nome', "Descrição :*", $row['ds_nome'], 'style="width: 300px;"');	
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();     
			echo button_save("Salvar");
        echo form_command_bar_detail_end();
	echo form_close();
	echo br();
echo aba_end();

$this->load->view('footer_interna');
?>