<?php
set_title('Reunião Sistema de Gestão Tipo - Configuração');
$this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array()) ?>
   
    function ir_lista()
    {
        location.href = "<?= site_url('gestao/reuniao_sistema_gestao_tipo') ?>";
    }

    function ir_cadastro()
    {
        location.href = "<?= site_url('gestao/reuniao_sistema_gestao_tipo/cadastro/'.$row['cd_reuniao_sistema_gestao_tipo']) ?>";
    }

    function ir_indicador()
    {
        location.href = '<?= site_url('gestao/reuniao_sistema_gestao_tipo/indicador/'.$row['cd_reuniao_sistema_gestao_tipo']) ?>';
    }
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
$abas[] = array('aba_indicador', 'Indicador', FALSE, 'ir_indicador();');
$abas[] = array('aba_cadastro_ordem', 'Ordenação dos Processos', TRUE, 'location.reload();');

echo aba_start($abas);
    echo form_open('gestao/reuniao_sistema_gestao_tipo/salvar_cadastro_ordem');
        echo form_start_box('default_box', 'Ordenação dos Processos');
			echo form_default_hidden('cd_reuniao_sistema_gestao_tipo', '', $row['cd_reuniao_sistema_gestao_tipo']);
			
			foreach($processo as $item)
			{
				echo form_default_hidden('processo_'.$item['cd_processo'], '', $item['cd_processo']);
				echo form_default_integer($item['cd_processo'], $item['processo'].': ', $item['nr_ordem']);
			}
			
        echo form_end_box('default_box');
        echo form_command_bar_detail_start();   
			echo button_save('Salvar');
        echo form_command_bar_detail_end();
    echo form_close();
    echo br(2);
echo aba_end();
$this->load->view('footer_interna');
?>