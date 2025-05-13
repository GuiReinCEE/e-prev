<?php
set_title('Certificados Participantes - Documentos');
$this->load->view('header');
?>
<script>
<?php
    echo form_default_js_submit(Array('cd_tipo_doc', 'cd_empresa', 'fl_verificar'));
?>
    function ir_lista()
    {
        location.href='<?php echo site_url("ecrm/certificado_participante_documento"); ?>';
    }
    
    function excluir()
    {
        var confirmacao = 'Deseja excluir o documento?\n\n'+
                'Clique [Ok] para Sim\n\n'+
                'Clique [Cancelar] para Não\n\n';
        
        if(confirm(confirmacao))
        {
            location.href='<?php echo site_url("ecrm/certificado_participante_documento/excluir/".$row['cd_certificado_participante_documento']); ?>';
        }
    }

    function callback_buscar_tipo_documento()
    {
        $('#cd_empresa').focus();
    }
    
    <?php
    if(intval($row['cd_certificado_participante_documento']) > 0)
    {
    ?>
    $(function(){
        consultar_tipo_documentos__cd_tipo_doc();
    });
    <?php
    }
    ?>
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_nc', 'Cadastro', TRUE, 'location.reload();');

$fl_verificar[] = array('value' => 'S', 'text' => 'Sim');
$fl_verificar[] = array('value' => 'N', 'text' => 'Não');

echo aba_start( $abas );
    echo form_open('ecrm/certificado_participante_documento/salvar', 'name="filter_bar_form_cadastro"');
        echo form_start_box( "default_box", "Cadastro" );
             echo form_default_hidden('cd_certificado_participante_documento', '', $row['cd_certificado_participante_documento']);
             echo form_default_hidden('cd_documento', 'Item', $row);
             echo form_default_tipo_documento(array('caption' => 'Documento: *', 'callback_buscar' => 'callback_buscar_tipo_documento();', 'value'=> $row['cd_documento']));
             echo form_default_dropdown('cd_empresa', 'Empresa: *', $arr_patrocinadoras, array($row['cd_empresa']));
             echo form_default_dropdown('fl_verificar', 'Verificar Eletro : *', $fl_verificar, array($row['fl_verificar']));
        echo form_end_box("default_box");
        echo form_command_bar_detail_start();     
            if(intval($row['cd_certificado_participante_documento']) > 0)
            {
                echo button_save("Excluir", "excluir()", "botao_vermelho");
            }
            else
            {
                echo button_save("Salvar");
            }
        echo form_command_bar_detail_end();
    
    echo form_close();
    echo br(3);	

echo aba_end();

$this->load->view('footer_interna');
?>