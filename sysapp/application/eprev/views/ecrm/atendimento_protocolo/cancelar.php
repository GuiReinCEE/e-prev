<?php
set_title( 'Protocolo Correspondência' );
$this->load->view('header');
?>
<script type="text/javascript">
<?php
	echo form_default_js_submit(Array('ds_motivo'));
?>

    function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/atendimento_protocolo"); ?>';
	}
</script>

<?php
$abas[] = array( 'aba_lista', 'Lista', false, 'ir_lista();' );
$abas[] = array( 'aba_lista', 'Cancelar', true, 'location.reload();' );

echo aba_start( $abas );

    echo form_open('ecrm/atendimento_protocolo/cancelarSalvar');
	echo form_start_box( "default_box", "Cancelamento" );
    echo form_default_integer('cd_atendimento_protocolo', 'Código: ', $cd_atendimento_protocolo, "style='width:500px;border: 0px;' readonly" );
        echo form_default_textarea('ds_motivo', 'Motivo: (*) ');

    echo form_end_box("default_box");

        echo form_command_bar_detail_start();
            echo button_save("Salvar");
        echo form_command_bar_detail_end();
	echo form_close();

echo aba_end();
$this->load->view('footer_interna');
?>