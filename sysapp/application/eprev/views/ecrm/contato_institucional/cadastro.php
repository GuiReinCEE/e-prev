<?php
set_title('Contato Institucional');
$this->load->view('header');
?>

<script>
<?php
    echo form_default_js_submit(Array('cd_contato_institucional_tipo', 'cd_contato_institucional_empresa', 'cd_contato_institucional_cargo', 'nome'));
?>
    
    function ir_lista()
    {
        location.href='<?php echo site_url("ecrm/contato_institucional"); ?>';
    }
    
    function preenche_cep(data)
    {
        $('#logradouro').val(data.endereco);
        $('#bairro').val(data.bairro);
        $('#cidade').val(data.cidade);
        $('#uf').val(data.uf);
    }
    
    function excluir()
    {
        var confirmacao = 'Deseja excluir o item?\n\n'+
                'Clique [Ok] para Sim\n\n'+
                'Clique [Cancelar] para Não\n\n';
            
        if( confirm(confirmacao) )
        {
            location.href='<?php echo site_url("ecrm/contato_institucional/excluir/".$row['cd_contato_institucional']); ?>';
        }
    }
    
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_nc', 'Cadastro', TRUE, 'location.reload();');

$config['db'] = true;
$config['callback_function'] = "preenche_cep";
$config['return_type'] = "json";

echo aba_start( $abas );
    echo form_open('ecrm/contato_institucional/salvar', 'name="filter_bar_form"');
        echo form_start_box( "default_box", "Cadastro" );
            echo form_default_hidden('cd_contato_institucional', '', $row['cd_contato_institucional']);
            echo form_default_dropdown_db('cd_contato_institucional_tipo', 'Tipo:*', array('projetos.contato_institucional_tipo', 'cd_contato_institucional_tipo', 'ds_contato_institucional_tipo'), array($row['cd_contato_institucional_tipo']), '', '', TRUE, '', 'ds_contato_institucional_tipo');
            echo form_default_dropdown_db('cd_contato_institucional_empresa', 'Empresa:*', array('projetos.contato_institucional_empresa', 'cd_contato_institucional_empresa', 'ds_contato_institucional_empresa'), array($row['cd_contato_institucional_empresa']), '', '', TRUE, '', 'ds_contato_institucional_empresa');
            echo form_default_dropdown_db('cd_contato_institucional_cargo', 'Cargo:*', array('projetos.contato_institucional_cargo', 'cd_contato_institucional_cargo', 'ds_contato_institucional_cargo'), array($row['cd_contato_institucional_cargo']), '', '', TRUE, '', 'ds_contato_institucional_cargo');
            echo form_default_text('nome', 'Nome:*', $row['nome'], 'style="width:400px"');
            echo form_default_telefone('telefone_1', 'Telefone 1:', $row['telefone_1']);
            echo form_default_telefone('telefone_2', 'Telefone 2:', $row['telefone_2']);
            echo form_default_text('email_1', 'Email 1:', $row['email_1'], 'style="width:300px"');
            echo form_default_text('email_2', 'Email 2:', $row['email_2'], 'style="width:300px"');
            echo form_default_cep('cep', 'CEP:', $row['cep'], $config);
            echo form_default_text('logradouro', 'Logradouro:', $row['logradouro'], 'style="width:300px"');
            echo form_default_text('numero', 'Número:', $row['numero']);
            echo form_default_text('complemento', 'Complemento:', $row['complemento'], 'style="width:300px"');
            echo form_default_text('bairro', 'Bairro:', $row['bairro'], 'style="width:300px"');
            echo form_default_text('cidade', 'Cidade:', $row['cidade'], 'style="width:300px"');
            echo form_default_dropdown('uf', 'UF:', $arr_uf, array($row['uf']));
        echo form_end_box("default_box");
        
        echo form_start_box( "default_box", "Secretária" );
            echo form_default_text('sec_nome', 'Nome:', $row['sec_nome'], 'style="width:400px"');
            echo form_default_telefone('sec_telefone_1', 'Telefone 1:', $row['sec_telefone_1']);
            echo form_default_telefone('sec_telefone_2', 'Telefone 2:', $row['sec_telefone_2']);
            echo form_default_text('sec_email_1', 'Email 1:', $row['sec_email_1'], 'style="width:300px"');
            echo form_default_text('sec_email_2', 'Email 2:', $row['sec_email_2'], 'style="width:300px"');
        echo form_end_box("default_box");
        
        echo form_command_bar_detail_start();     
            echo button_save("Salvar");
            if(intval($row['cd_contato_institucional']) > 0)
            {
                echo button_save("Excluir", "excluir()", "botao_vermelho");
            }
        echo form_command_bar_detail_end();
    echo form_close();

    echo br(3);
echo aba_end();

$this->load->view('footer_interna');
?>