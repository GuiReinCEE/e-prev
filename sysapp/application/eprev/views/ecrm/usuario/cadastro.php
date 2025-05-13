<?php
set_title('Extranet - Usuários');
$this->load->view('header');
?>
<script>
<?php
    echo form_default_js_submit(Array('nome', 'usuario', 'senha', 'fl_troca_senha', 'cd_empresa', 'cpf', 'email'));
?>
    
    function ir_lista()
    {
        location.href='<?php echo site_url("ecrm/usuario"); ?>';
    }   

	function excluir()
	{
		var confirmacao = 'Deseja excluir?\n\n'+
                'Clique [Ok] para Sim\n\n'+
                'Clique [Cancelar] para Não\n\n';
        
        if(confirm(confirmacao))
        {
           location.href='<?php echo site_url("ecrm/usuario/excluir/".intval($row['cd_usuario'])); ?>';
        }
		
	}
	
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_nc', 'Cadastro', TRUE, 'location.reload();');

$arr_dropdown[] = array('value' => 'S', 'text' => 'Sim');
$arr_dropdown[] = array('value' => 'N', 'text' => 'Não');

echo aba_start( $abas );
    echo form_open('ecrm/usuario/salvar', 'name="filter_bar_form"');
        echo form_start_box( "default_box", "Cadastro" );
			echo form_default_text('cd_usuario', "Código: ", $row, "style='width:100%;border: 0px;' readonly" );
			echo form_default_hidden('senha_old', "", $row['senha']);
			echo form_default_text('nome', "Nome:* ", $row, "style='width:500px;'");
			echo form_default_text('usuario', "Usuário:* ", $row, "style='width: 100%;'");
			echo form_default_password('senha', "Senha:* ", $row, "style='width: 100%;'");
			echo form_default_dropdown('fl_troca_senha', 'Trocar senha 1º acesso:*', $arr_dropdown, Array($row['fl_troca_senha']));		
			echo form_default_empresa('cd_empresa', $row['cd_empresa']);
			echo form_default_cpf('cpf', 'CPF:* ', $row['cpf']);
			echo form_default_text('email', "Email:* ", $row, "style='width: 100%;'");
			echo form_default_telefone('telefone_1', "Telefone 1: ", $row);
			echo form_default_telefone('telefone_2', "Telefone 2: ", $row);			
        echo form_end_box("default_box");
        echo form_command_bar_detail_start();     
            echo button_save("Salvar");
			if(intval($row['cd_usuario']) > 0)
			{
				echo button_save("Excluir", "excluir()", "botao_vermelho");
			}
        echo form_command_bar_detail_end();
    
    echo form_close();

    echo br();	

echo aba_end();

$this->load->view('footer_interna');
?>