<?php
	set_title('Manual');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('nr_versao', 'cd_manual', 'ds_manual_tipo', 'dt_referencia'), 'valida_arquivo(form)') ?>

	function ir_lista()
    {
        location.href = "<?= site_url('gestao/manual/index') ?>";
    }

    function valida_arquivo(form)
    {
        if($("#arquivo").val() == "" && $("#arquivo_nome").val() == "")
        {
            alert("Nenhum arquivo foi anexado.");
            return false;
        }
        else
        {
            if(confirm("Salvar?"))
            {
                form.submit();
            }
        }
    }

    function get_versao(cd_manual_tipo)
    {
        $.post("<?= site_url('gestao/manual/get_versao') ?>",
        {
            cd_manual_tipo : cd_manual_tipo
        },
        function(data)
        {
            $("#nr_versao").val(data);
        });
    }

    function enviar()
    {
        var confirmacao = 'Deseja enviar e-mail do manual?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        { 
            location.href = "<?= site_url('gestao/manual/enviar/'.$row['cd_manual']) ?>";
        }
    }
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

    $head = array(
        'Versão',
        'Manual',
        'Dt. Aprovação',
        'Arquivo'
    );

    $body = array();

    foreach ($collection as $item)
    {
        $body[] = array(
            $item['nr_versao'],
            $item['ds_manual_tipo'],
            $item['dt_referencia'],
            anchor(base_url().'up/manual/'.$item['arquivo'], '[arquivo]', array('target' => '_blank'))
        );
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;

	echo aba_start($abas);
		echo form_open('gestao/manual/salvar');
			echo form_start_box('default_box', 'Cadastro');
                echo form_default_hidden('cd_manual', '', $row['cd_manual']); 
                echo form_default_dropdown('cd_manual_tipo', 'Manual: (*)', $manual, $row['cd_manual_tipo'], 'onchange="get_versao($(this).val())"');
                echo form_default_text('nr_versao', 'Versão: (*)', $row['nr_versao']);  
				echo form_default_date('dt_referencia', 'Dt. Aprovação: (*)', $row['dt_referencia']);
		        echo form_default_upload_iframe('arquivo', 'manual', 'Arquivo: (*)', array($row['arquivo'], $row['arquivo_nome']), 'manual', (gerencia_in(array('GRC')) ? true : false));
                if(trim($row['dt_envio']) != '')
                {
                    echo form_default_row('', 'Dt. Envio:', $row['dt_envio']);
                    echo form_default_row('', 'Usuário:', $row['ds_usuario_envio']);
                }
			echo form_end_box('default_box');
			echo form_command_bar_detail_start();
                if(gerencia_in(array('GC')))
                {
                    echo button_save('Salvar'); 

                    if(intval($row['cd_manual']) > 0 AND trim($row['dt_envio']) == '')
                    {
                        echo button_save('Enviar E-mail', 'enviar()', 'botao_verde'); 
                    }
                }      	            
		    echo form_command_bar_detail_end();
		echo form_close();        
        if(count($collection) > 0)
        {
            echo $grid->render();
        }
		echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?>