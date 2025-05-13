<?php
set_title('Reunião Sistema de Gestão - Configuração');
$this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array('dt_reuniao_sistema_gestao', 'cd_reuniao_sistema_gestao_tipo')) ?>
   
    function ir_lista()
    {
        location.href = "<?= site_url('gestao/reuniao_sistema_gestao') ?>";
    }

    function ir_anexo()
    {
        location.href = "<?= site_url('gestao/reuniao_sistema_gestao/anexo/'.$row['cd_reuniao_sistema_gestao']) ?>";
    }

    function ir_cadastro()
    {
        location.href = "<?= site_url('gestao/reuniao_sistema_gestao/cadastro/'.$row['cd_reuniao_sistema_gestao']) ?>";
    }

    function ir_indicador()
    {
        location.href = "<?= site_url('gestao/reuniao_sistema_gestao/indicador/'.$row['cd_reuniao_sistema_gestao']) ?>";
    }

    function ir_processo()
    {
        location.href = "<?= site_url('gestao/reuniao_sistema_gestao/processo/'.$row['cd_reuniao_sistema_gestao']) ?>";
    }

    $(function(){

	});
</script>

<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
    $abas[] = array('aba_processo', 'Processo', FALSE, 'ir_processo();');
	$abas[] = array('aba_indicador', 'Indicador', FALSE, 'ir_indicador();');
	$abas[] = array('aba_cadastro_ordem', 'Ordenação dos Processos', TRUE, 'location.reload();');
	$abas[] = array('aba_anexo', 'Anexo', FALSE, 'ir_anexo();');

    echo aba_start($abas);
        echo form_open('gestao/reuniao_sistema_gestao/salvar_cadastro_ordem');
            echo form_start_box('default_box', 'Ordenação dos Processos');
    			echo form_default_hidden('cd_reuniao_sistema_gestao', '', $row['cd_reuniao_sistema_gestao']);
				
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