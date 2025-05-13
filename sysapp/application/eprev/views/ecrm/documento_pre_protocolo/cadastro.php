<?php
set_title('Doc. Pré Cadastro');
$this->load->view('header');
?>
<script>
<?php
    echo form_default_js_submit(Array('cd_tipo_doc', 'nome'));
?>
    
    function ir_lista()
    {
        location.href='<?php echo site_url("ecrm/documento_pre_protocolo"); ?>';
    }
	
	function callback_documento_descartar()
    {
        if( $('#cd_tipo_doc_fica_marcado').is(':checked'))
        {
            if($('#cd_empresa').val() == '')
            {
                $('#cd_empresa').focus();
            }
            else
            {
                $('#ds_observacao').focus();
            }
        }
        else if( $('#participante_fica_marcado').is(':checked') )
        {
            $('#ds_observacao').focus();
        }
        else
        {
            $('#cd_empresa').focus();
        }
    
        if($('#nome_documento').val() != '')
        {
            $.post("<?php echo site_url('ecrm/protocolo_digitalizacao/descartar'); ?>",
            {
                cd_tipo_doc : $('#cd_tipo_doc').val()
            },
            function(data)
            { 
                $("#fl_descartar option[value='"+data+"']").attr('selected', 'selected');
            });
            
        }
    }
	
	function callback_participante()
    {
        if( $('#cd_tipo_doc_fica_marcado').is(':checked') )
        {
            $('#ds_observacao').focus();
        }
        else if( $('#participante_fica_marcado').is(':checked') )
        {
            if( $('#cd_tipo_doc').val()=='')
            {
                $('#cd_tipo_doc').focus();
            }
            else
            {
                $('#ds_observacao').focus();
            }
        }
        else
        {
            $('#ds_observacao').focus();
        }
    }
	
	$(function(){
		if($('#cd_documento_pre_protocolo').val() > 0)
		{
			consultar_tipo_documentos__cd_tipo_doc();
			consultar_participante_focus__cd_empresa();
		}
		
		$('#cd_tipo_doc').focus();
		
	});
	
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_nc', 'Cadastro', TRUE, 'location.reload();');

$arr_documento = array (
	'caption'         => 'Documento :* ', 
	'callback_buscar' => 'callback_documento_descartar();',
	'value'           => $row['cd_tipo_doc']
);

$arr_participante = array('cd_empresa', 'cd_registro_empregado', 'seq_dependencia', 'nome');

$arr_participante_value = array(
	'cd_empresa'            => $row['cd_empresa'], 
	'cd_registro_empregado' => $row['cd_registro_empregado'], 
	'seq_dependencia'       => $row['seq_dependencia']
);

$arr_yer_or_no[] = array('text' => 'Não', 'value' => 'N');
$arr_yer_or_no[] = array('text' => 'Sim', 'value' => 'S');

echo aba_start( $abas );
    echo form_open('ecrm/documento_pre_protocolo/salvar', 'name="filter_bar_form"');
        echo form_start_box( "default_box", "Cadastro" );
			echo form_default_hidden('cd_documento_pre_protocolo', '', $row['cd_documento_pre_protocolo']);
			echo form_default_tipo_documento($arr_documento);
			echo form_default_participante($arr_participante, 'Participante (Emp/RE/Seq) :', $arr_participante_value, true, true, 'callback_participante();');
			echo form_default_text('nome','Nome: *', $row, 'style="width:500px;"');
			echo form_default_text('ds_observacao', 'Observações :', $row, 'style="width:500px;"');
			echo form_default_dropdown('fl_descartar', 'Descartar:', $arr_yer_or_no, array($row['fl_descartar']));
			echo form_default_integer('nr_folha', 'Nr folhas:', $row);
			echo form_default_upload_iframe('arquivo', 'documento_pre_documento', 'Arquivo:', $row['arquivo'], '');
			echo form_default_dropdown('fl_manter', 'Manter :', $arr_yer_or_no, array($row['fl_manter']));
        echo form_end_box("default_box");
        echo form_command_bar_detail_start();     
            echo button_save("Salvar");
        echo form_command_bar_detail_end();
    echo form_close();
    echo br(2);	
echo aba_end();

$this->load->view('footer_interna');
?>