<?php
set_title( 'Protocolo Correspondência Expedida' );
$this->load->view('header');
?>

<script>
<?php
		echo form_default_js_submit(Array('nome', 'ds_destino', 'cd_atendimento_protocolo_tipo', 'cd_atendimento_protocolo_discriminacao'));
?>
</script>

<script>
    function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/atendimento_protocolo"); ?>';
	}

    function carregar_dados_participante(data)
	{

		$('#nome').val(data.nome);

        if(data.complemento_endereco == null)
        {
            data.complemento_endereco = '';
        }
        else
        {
            data.complemento_endereco = ' / '+data.complemento_endereco;
        }

        $('#ds_destino').val(data.endereco+', '+data.nr_endereco+data.complemento_endereco+', '+data.bairro+', '+data.cidade+', '+data.cep+'-'+data.complemento_cep+' - '+data.unidade_federativa );
        montaComboEncaminhamento();
	}

    function montaComboEncaminhamento()
    {
        $.post( '<?php echo site_url("ecrm/atendimento_protocolo/encaminhamento"); ?>/',
		{
			cd_empresa            : $('#cd_empresa').val(),
			cd_registro_empregado : $('#cd_registro_empregado').val(),
			seq_dependencia       : $('#seq_dependencia').val(),
            cd_encaminhamento     : $('#cd_encaminhamento').val(),
            cd_atendimento        : $('#cd_atendimento').val()
		},
        function(data)
		{
			$('#comboEncaminhamento').html(data);
			 carregaTexto();
		});
    }

    function carregaTexto()
    {
        $.post( '<?php echo site_url("ecrm/atendimento_protocolo/carregaTextoEncaminhamento"); ?>/',
		{
			cd_protocolo_encaminhamento: $('#cd_protocolo_encaminhamento').val()
		},
        function(data)
		{
			$('#ds_protocolo').val(data);
		});
    }

    $(function (){
        if($('#cd_empresa').val() != '' && $('#cd_registro_empregado').val() != '' && $('#seq_dependencia').val() != '')
        {
            consultar_participante__cd_empresa();
        }
    });

	function qrcode_retorno(data)
	{
		if(data.result)
		{
			$("#cd_empresa").val(data.cd_empresa);
			$("#cd_registro_empregado").val(data.cd_registro_empregado);
			$("#seq_dependencia").val(data.seq_dependencia);
			consultar_participante_focus__cd_empresa();
			matriz_documento(data.cd_digitalizacao);
		}
	}
	
    function matriz_documento(cd_tipo_doc)
    {
        $.post('<?php echo site_url("ecrm/atendimento_protocolo/matriz_documento"); ?>/',
		{
			cd_tipo_doc : cd_tipo_doc
		},
        function(data)
		{
			var ret = "";
			if(data)
			{
				ret = data.cd_discriminacao;
			}
			
			$("#cd_atendimento_protocolo_discriminacao").val(ret);
			$("#cd_atendimento_protocolo_discriminacao").change();
		},
		'json');
    }	

    function valida_devolucao()
    {
        if($("#dt_devolucao").val() != '')
        {
            var confirmacao = "Salvar?";

            if(confirm(confirmacao))
            {
                $("form").submit();
            }
        }
        else
        {
            var confirmacao = "O campo Dt. Devolvido deve ser preenchido!";

            if(confirm(confirmacao))
            {
                $("#dt_devolucao").focus();
            }
        }
    }
	
	function descricao_tipo()
	{
		if($("#cd_atendimento_protocolo_tipo").val() == 8 || $("#cd_atendimento_protocolo_tipo").val() == 6)
		{
			$("#ds_descricao_tipo_row").show();
		}
		else
		{
			$("#ds_descricao_tipo_row").hide();
		}
	}
	
	$(function(){
		descricao_tipo();
	});
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_jogo', 'Cadastro', TRUE, 'location.reload();');

$arr_manter[] = array('value' => 'P', 'text' => 'Participante');
$arr_manter[] = array('value' => 'T', 'text' => 'Tipo');

$c['emp']['id']='cd_empresa';
$c['re']['id']='cd_registro_empregado';
$c['seq']['id']='seq_dependencia';
$c['emp']['value']=$row['cd_empresa'];
$c['re']['value']=$row['cd_registro_empregado'];
$c['seq']['value']=$row['seq_dependencia'];
$c['caption']='Participante:';
$c['callback']='carregar_dados_participante';

if(intval($row['cd_atendimento_protocolo']) == 0)
{
	$row['ds_descricao_tipo'] = 'TIPO DE DOCUMENTO:
ENDEREÇO DE COLETA:
ENDEREÇO DE ENTREGA:
BAIRRO DESTINO
OBSERVAÇÃO:
VOLUME DE OBJETOS:
TIPO DE VEÍCULO MOTO/CARRO:
TIPO DE SERVIÇO – NORMAL/NORMAL COM RETORNO:';
}

echo aba_start( $abas );

    echo form_open('ecrm/atendimento_protocolo/salvar');
    echo form_start_box( "default_box", "Nova" );
        #echo form_default_participante(array('cd_empresa', 'cd_registro_empregado', 'seq_dependencia', 'nome'), 'Participante :', false, true);
        echo form_default_qrcode(array('id'=>'qrcode', 'caption'=>'QR Code:','callback'=>'qrcode_retorno','value'=>''));
        echo form_default_participante_trigger($c);
        echo form_default_text("nome", "Nome:*", $row['nome'], "style='width:500px;'");
        echo form_default_textarea('ds_destino', 'Destino:*', $row['ds_destino'], "style=height:100px;'");
        echo form_default_row('', 'Protocolo/encaminhamento:', '<span id="comboEncaminhamento"></span>');
        echo form_default_text('ds_protocolo', "", '', "style='width:100%;border: 0px;' readonly" );
        echo form_default_dropdown('cd_atendimento_protocolo_tipo', 'Tipo:*', $tipo, array($row['cd_atendimento_protocolo_tipo']), 'onchange="descricao_tipo()"');
		echo form_default_textarea('ds_descricao_tipo', 'Descrição Tipo:*', $row['ds_descricao_tipo'], "style=height:150px;'");
        echo form_default_dropdown('cd_atendimento_protocolo_discriminacao', 'Discriminação:*', $discriminacao, array($row['cd_atendimento_protocolo_discriminacao']));
        echo form_default_text('ds_identificacao','', $row['ds_identificacao'], "style='width:500px;'");
        echo form_default_hidden('cd_encaminhamento', '', $row['cd_encaminhamento']);
        echo form_default_hidden('cd_atendimento', '', $row['cd_atendimento']);
        echo form_default_hidden('cd_atendimento_protocolo', '', $row['cd_atendimento_protocolo']);

        if(intval($cd_atendimento_protocolo) == 0 OR $manter == 'T')
		{
            echo form_default_dropdown('manter', 'Manter:', $arr_manter, array($manter));
		}

        echo form_default_text("dt_inclusao", "Dt Remessa: ", $row['dt_inclusao'], "style='width:100%;border: 0px;' readonly" );

        if(intval($cd_atendimento_protocolo) > 0 AND $manter != 'T')
        {
            echo form_default_hidden('cd_usuario_recebimento', '', $row['cd_usuario_recebimento']);
            echo form_default_text('dt_recebimento', 'Recebido em:', $row['nome_gad'], "style='width:100%;border: 0px;' readonly");
            echo form_default_text('dt_recebimento', 'Recebido em:', $row['dt_recebimento'], "style='width:100%;border: 0px;' readonly");
            echo form_default_text('dt_cancelamento', 'Cancelado em:', $row['dt_cancelamento'], "style='width:100%;border: 0px;' readonly");
            echo form_default_text('ds_motivo', 'Motivo Cancelamento:', $row['ds_motivo'], "style='width:100%;border: 0px;' readonly");
        }

        if($row['dt_devolvido'] != '')
        {
            echo form_default_row('', 'Dt. Devolvido:', $row['dt_devolvido']);
        }
        
        echo form_end_box("default_box");

        if($row['dt_recebimento'] == "" AND $row['dt_cancelamento'] == "")
        {
            echo form_command_bar_detail_start();
				if(($row['cd_atendimento_protocolo'] == 0) OR ($row['cd_atendimento_protocolo'] > 0 AND intval($row['cd_usuario_inclusao']) == $this->session->userdata('codigo')))
				{
					echo button_save("Salvar");
				}
            echo form_command_bar_detail_end();
        }
        
    echo form_close();

    if($cd_divisao == 'GFC' AND $row['dt_devolucao'] == '')
    {
        echo form_open('ecrm/atendimento_protocolo/salvar_devolucao');
            echo form_start_box( "default_box_devolucao", "Devolver" );
                echo form_default_hidden('cd_atendimento_protocolo', '', $row['cd_atendimento_protocolo']);
                echo form_default_date('dt_devolucao', 'Dt. Devolvido: (*)');
                echo form_default_textarea('ds_descricao_devolvido', 'Observações:');
            echo form_end_box("default_box_devolucao");
            echo form_command_bar_detail_start();
                echo button_save("Salvar", 'valida_devolucao()');
            echo form_command_bar_detail_end();
        echo form_close();
    }
    else if($row['dt_devolucao'] != '')
    {
        echo form_start_box( "default_box_devolucao", "Devolver" );
            echo form_default_row('', 'Dt. Devolvido:', '<span class="label label-info">'.$row['dt_devolucao'].'</span>');
            echo form_default_textarea('', 'Observações:', $row['ds_descricao_devolvido']);
        echo form_end_box("default_box_devolucao");
    }
echo br(3);
    echo aba_end();

    if(intval($cd_atendimento_protocolo) > 0)
    {
        echo '<script>montaComboEncaminhamento();</script>';
    }
    
    $this->load->view('footer');
?>