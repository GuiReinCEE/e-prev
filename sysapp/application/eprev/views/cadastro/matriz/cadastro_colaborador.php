<?php
set_title('Avaliação - Matriz Salarial ');
$this->load->view('header');
?>
<script>
<?php
	echo form_default_js_submit(Array('cd_escolaridade', 'cd_matriz_salarial', 'dt_admissao', 'dt_promocao', 'tipo_promocao'));
?>

function ir_lista()
{
    location.href='<?php echo site_url('/cadastro/matriz/matriz_salarial');?>';
}

function ir_colaboradores()
{
    location.href='<?php echo site_url('/cadastro/matriz');?>';
}
</script>
<?php
$abas[] = array( 'aba_lista', 'Colaboradores', false, 'ir_colaboradores();' );
$abas[] = array('aba_nc', 'Cadastro', TRUE, 'location.reload();');
$abas[] = array('aba_lista', 'Matriz Salarial', FALSE, 'ir_lista();');

$arr_promocao[] = array('text' => 'Horizontal', 'value' => 'H');
$arr_promocao[] = array('text' => 'Vertical', 'value' => 'V');

echo aba_start( $abas );
    echo form_open('cadastro/matriz/salvar_colaborador', 'name="filter_bar_form"');
        echo form_start_box( "default_box", "Cadastro" );
            echo form_default_hidden('cd_usuario_matriz', "Código:", $row, "style='width:100%;border: 0px;' readonly" );
            echo form_default_hidden('cd_usuario', "Código:", $cd_usuario, "style='width:100%;border: 0px;' readonly" );
            echo form_default_text('nome', "Nome:", $nome, "style='width:100%;border: 0px;' readonly" );
            echo form_default_text('divisao', "Gerência:", $divisao, "style='width:100%;border: 0px;' readonly" );
            echo form_default_dropdown('cd_escolaridade', 'Escolaridade:*', $arr_escolaridade, array($row['cd_escolaridade']));
            echo form_default_dropdown('cd_matriz_salarial', 'Classe - Faixa:*', $arr_matriz_salarial, array($row['cd_matriz_salarial']));
            echo form_default_date('dt_admissao', "Admissão:*", $row);
            echo form_default_date('dt_promocao', "Promoção:*", $row);
            echo form_default_dropdown('tipo_promocao', 'Tipo:*', $arr_promocao, array($row['tipo_promocao']));
        echo form_end_box("default_box");
        echo form_command_bar_detail_start(); 
            echo button_save("Salvar");
        echo form_command_bar_detail_end();
    echo form_close();
	echo br();	
echo aba_end();

$this->load->view('footer_interna');
?>