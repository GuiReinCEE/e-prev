<?php
set_title( 'Resposta a Contato' );
$this->load->view('header');
?>
<script type="text/javascript">
    function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/operacional_contato"); ?>';
	}

    function salvar(form)
	{
        if($("#fl_envia_email").val() == "")
        {
           alert("Informe o campo Enviar Email.");
           document.getElementById('fl_envia_email').focus();
        }
        else
        {
            if(confirm('Salvar?'))
            {
                form.submit();
            }
        }
	}
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_jogo', 'Resposta', TRUE, 'location.reload();');
echo aba_start( $abas );

    $arrEmail[] = array('value' => 'S', 'text' => 'SIM');
    $arrEmail[] = array('value' => 'N', 'text' => 'NÃO');

    $mensagem =
        "\n".
        "Gerência de Atendimento ao Participante\n".
        "Rua dos Andradas, 702 Porto Alegre- RS CEP 90020-004\n".
        "Ligue grátis: 0800 51 2596\n".
        "Atendimento de segunda a sexta, das 08 às 17 horas.\n";

    $mensagem = $mensagem ."\n\n--- Em " . $row['data'] .' às '. $row['hora'].', ' . $row['nome'] . " escreveu: ---\n\n" . $row['comentario'] . "\n\n\nMensagem nº " . $row['codigo'];

    echo form_open('ecrm/operacional_contato/salvar');
        echo form_start_box( "default_box", "Resposta" );
            echo form_default_hidden("codigo", "", $row['codigo']);
            echo form_default_text("ds_nome", "Nome: ", $row['nome'], "style='width:200%;border: 0px;' readonly" );
            echo form_default_text("ds_endereco", "Endereço: ", $row['endereco'], "style='width:200%;border: 0px;' readonly" );
            echo form_default_text("ds_bairro", "Bairro: ", $row['bairro'], "style='width:200%;border: 0px;' readonly" );
            echo form_default_text("ds_cep", "CEP: ", $row['cep'], "style='width:100%;border: 0px;' readonly" );
            echo form_default_text("ds_cidade", "Cidade: ", $row['cidade'], "style='width:200%;border: 0px;' readonly" );
            echo form_default_text("ds_estado", "Estado: ", $row['estado'], "style='width:200%;border: 0px;' readonly" );
            echo form_default_text("ds_telefone", "Telefone: ", $row['ddd'].$row['telefone'], "style='width:100%;border: 0px;' readonly" );
            echo form_default_text("ds_fax", "Fax: ", $row['fax'], "style='width:100%;border: 0px;' readonly" );
            echo form_default_text("ds_email", "Email: ", $row['email'], "style='width:200%;border: 0px;' readonly" );
            echo form_default_text("dt_data", "Data: ", $row['data'], "style='width:100%;border: 0px;' readonly" );
            echo form_default_text("hr_hora", "Hora: ", $row['hora'], "style='width:100%;border: 0px;' readonly" );
            echo form_default_text("ds_empresa", "Empresa: ", $row['empresa'], "style='width:200%;border: 0px;' readonly" );
            echo form_default_text("ds_re", "RE: ", $row['re'], "style='width:100%;border: 0px;' readonly" );
            echo form_default_dropdown('cd_tipo_atendimento', 'Tipo do Contato:', $tipo_atendimento, array($row['cd_atendimento']));
            echo form_default_dropdown('fl_envia_email', 'Enviar email:',  $arrEmail);
            echo form_default_textarea('resposta', 'Resposta:', $row['respondido'] == 'N' ? $mensagem : "Resposta: \n". $row['resposta'] . "\nPergunta\n ".$mensagem);
        echo form_end_box("default_box");
        echo form_command_bar_detail_start();
            echo button_save("Salvar");
        echo form_command_bar_detail_end();
    echo form_close();

echo aba_end();

?>