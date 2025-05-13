<?php
	set_title('Política');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('nr_versao', 'cd_politica', 'ds_politica_tipo', 'dt_referencia'), 'valida_arquivo(form)') ?>

	function ir_lista()
    {
        location.href = "<?= site_url('gestao/politica/index') ?>";
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

    function get_versao(cd_politica_tipo)
    {
        $.post("<?= site_url('gestao/politica/get_versao') ?>",
        {
            cd_politica_tipo : cd_politica_tipo
        },
        function(data)
        {
            $("#nr_versao").val(data);
        });
    }

    function enviar()
    {
        var confirmacao = 'Deseja enviar e-mail com do regimento interno?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        { 
            location.href = "<?= site_url('gestao/politica/enviar/'.$row['cd_politica']) ?>";
        }
    }
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

    $head = array(
        'Versão',
        'Regimento interno',
        'Dt. Aprovação',
        'Arquivo'
    );

    $body = array();

    foreach ($collection as $item)
    {
        $body[] = array(
            $item['nr_versao'],
            $item['ds_politica_tipo'],
            $item['dt_referencia'],
            anchor(base_url().'up/politica/'.$item['arquivo'], '[arquivo]', array('target' => '_blank'))
        );
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;

	echo aba_start($abas);
		echo form_open('gestao/politica/salvar');
			echo form_start_box('default_box', 'Cadastro');
                echo form_default_hidden('cd_politica', '', $row['cd_politica']); 
                echo form_default_dropdown('cd_politica_tipo', 'Política: (*)', $politica, $row['cd_politica_tipo'], 'onchange="get_versao($(this).val())"');
                echo form_default_text('nr_versao', 'Versão: (*)', $row['nr_versao']);  
				echo form_default_date('dt_referencia', 'Dt. Aprovação: (*)', $row['dt_referencia']);
		        echo form_default_upload_iframe('arquivo', 'politica', 'Arquivo: (*)', array($row['arquivo'], $row['arquivo_nome']), 'politica', (gerencia_in(array('GRC')) ? true : false));
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

                    if(intval($row['cd_politica']) > 0 AND trim($row['dt_envio']) == '')
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