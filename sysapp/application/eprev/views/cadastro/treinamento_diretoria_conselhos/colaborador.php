<?php
    set_title('Treinamento - Diretoria e Conselhos');
    $this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('ds_nome')) ?> 

	function ir_lista()
    {
        location.href = "<?= site_url('cadastro/treinamento_diretoria_conselhos') ?>";
    }
	
    function ir_cadastro()
    {
        location.href = "<?= site_url('cadastro/treinamento_diretoria_conselhos/cadastro/'.$cd_treinamento_diretoria_conselhos) ?>";
    }
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
$abas[] = array('aba_colaborador', 'Colaborador', TRUE, 'location.reload();');

echo aba_start($abas);
    echo form_open('cadastro/treinamento_diretoria_conselhos/salvar_colaborador');
    echo form_start_box('default_box', 'Cadastro');
	    echo form_default_hidden('cd_treinamento_diretoria_conselhos', '', $cd_treinamento_diretoria_conselhos);
	    echo form_default_hidden('cd_treinamento_diretoria_conselhos_item', '', $row['cd_treinamento_diretoria_conselhos_item']);
        echo form_default_participante(array('cd_empresa','cd_registro_empregado','seq_dependencia', 'ds_nome'), 'RE:', array('cd_empresa' => $row['cd_empresa'], 'cd_registro_empregado' => $row['cd_registro_empregado'], 'seq_dependencia' => $row['seq_dependencia']), TRUE, TRUE);	
        echo form_default_text('ds_nome', 'Nome: (*)', $row['ds_nome'], 'style="width:300px;"');
        echo form_default_dropdown('cd_gerencia', 'Gerência:', $gerencias, $row['cd_gerencia']);
        echo form_default_text('ds_centro_custo', 'Centro de Custo:', $row['ds_centro_custo'], 'style="width:300px;"');
        echo form_default_upload_iframe('arquivo', 'treinamento_diretoria_conselhos', 'Certificado:', array($row['arquivo'], $row['arquivo_nome']), 'treinamento_diretoria_conselhos');
    echo form_end_box('default_box');
    echo form_command_bar_detail_start();
        echo button_save('Salvar');
    echo form_command_bar_detail_end();
    echo br(2);
echo aba_end();

$this->load->view('footer_interna');

?>