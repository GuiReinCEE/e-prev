<?php
set_title('Treinamento Colaborador');
$this->load->view('header');
?>
<script>
<?php
		echo form_default_js_submit(Array('nome'));
?>
    function irLista()
	{
		location.href='<?php echo site_url("cadastro/treinamento_colaborador"); ?>';
	}
    
    function irCadastro()
    {
        location.href='<?php echo site_url("cadastro/treinamento_colaborador/cadastro/".$ano."/".$numero); ?>';
    }
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'irLista();');
$abas[] = array('aba_nc', 'Cadastro', FALSE, 'irCadastro();');
$abas[] = array('aba_aa', 'Colaborador', TRUE, 'location.reload();');
echo aba_start( $abas );
    echo form_open('cadastro/treinamento_colaborador/salvarColaborador', 'name="filter_bar_form"');
    echo form_start_box( "default_box", "Cadastro" );
        echo form_default_hidden('numero', "", $numero);
        echo form_default_hidden('ano', "", $ano);
        echo form_default_hidden('cd_treinamento_colaborador_item', "", $row['cd_treinamento_colaborador_item']);
        echo form_default_participante(array('cd_empresa','cd_registro_empregado','seq_dependencia', 'nome'), "Colaborador (RE):", array('cd_empresa' => $row['cd_empresa'], 'cd_registro_empregado' => $row['cd_registro_empregado'], 'seq_dependencia' => $row['seq_dependencia']), TRUE, TRUE );	
        echo form_default_text('nome', "Nome:*", $row['nome'], "style='width:100%;'" );
        echo form_default_dropdown('area', 'Gerência:', $gerencias, $row['area']);
        echo form_default_text('centro_custo', "Centro de Custo:", $row['centro_custo'], "style='width:100%;'" );
        echo form_default_upload_iframe('arquivo', 'certificado_treinamento', 'Certificado:', array($row['arquivo'], $row['arquivo_nome']), 'certificado_treinamento');
    echo form_end_box("default_box");
    
    echo form_command_bar_detail_start();
        echo button_save("Salvar");
    echo form_command_bar_detail_end();

	echo "<BR><BR><BR>";	

echo aba_end();

$this->load->view('footer_interna');

?>