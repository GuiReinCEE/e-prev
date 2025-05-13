<?php
	set_title('Convênios de adesão - Cadastro');
	$this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array('cd_plano', 'cd_plano_empresa', 'arquivo')) ?>

    function ir_lista()
    {
    	location.href = "<?= site_url('gestao/convenio_adesao') ?>";
    }	

    function enviar()
    {
    	var confirmacao = 'Deseja enviar e-mail com o convênio de adesão?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        { 
            location.href = "<?= site_url('gestao/convenio_adesao/enviar/'.$row['cd_convenio_adesao']) ?>";
        }
    }

    function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
            "CaseInsensitiveString",
		    "CaseInsensitiveString"
		]);
		ob_resul.onsort = function ()
		{
			var rows = ob_resul.tBody.rows;
			var l = rows.length;
			for (var i = 0; i < l; i++)
			{
				removeClassName(rows[i], i % 2 ? "sort-par" : "sort-impar");
				addClassName(rows[i], i % 2 ? "sort-impar" : "sort-par");
			}
		};
		ob_resul.sort(0, false);
	}

    $(function(){
		configure_result_table();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

    $head = array( 
        'Empresa',
        'Plano',
        'Arquivo',
        'Documento',
        'Doc. Aprovação',
        'Termo Aditivo',
        'Portaria de Aprovação Termo Aditivo',
        'Termo de Adesão',
        'Portaria de Aprovação Termo Adesão'
    );

	$body = array();

	foreach ($collection as $item)
	{
        $body[] = array(
            array($item['empresa'], 'text-align:left;'),
            array($item['plano'], 'text-align:left;'),
            array(anchor(base_url().'up/convenio_adesao/'.$item['arquivo'], $item['arquivo_nome'], array('target' => '_blank')), 'text-align:left;'),
            array($item['ds_convenio_adesao'], 'text-align:left;'),
            array(anchor(base_url().'up/convenio_adesao/'.$item['arquivo_aprovacao'], $item['arquivo_aprovacao_nome'], array('target' => '_blank')), 'text-align:left;'),
            array(anchor(base_url().'up/convenio_adesao/'.$item['arquivo_termo_aditivo'], $item['arquivo_termo_aditivo_nome'], array('target' => '_blank')), 'text-align:left;'),
            array(anchor(base_url().'up/convenio_adesao/'.$item['arquivo_portaria_aprovacao'], $item['arquivo_portaria_aprovacao_nome'], array('target' => '_blank')), 'text-align:left;'),
            array(anchor(base_url().'up/convenio_adesao/'.$item['arquivo_termo_adesao'], $item['arquivo_termo_adesao_nome'], array('target' => '_blank')), 'text-align:left;'),
            array(anchor(base_url().'up/convenio_adesao/'.$item['arquivo_portaria_aprovacao_adesao'], $item['arquivo_portaria_aprovacao_adesao_nome'], array('target' => '_blank')), 'text-align:left;')
        );
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo aba_start($abas);
        echo form_open('gestao/convenio_adesao/salvar');
            echo form_start_box('cadastro_box', 'Cadastro');
            	echo form_default_hidden('cd_convenio_adesao', '', $row);
            	echo form_default_plano_empresa_ajax('cd_plano', $row['cd_plano'], $row['cd_empresa'], 'Plano: (*)', 'Empresa: (*)');
				echo form_default_upload_iframe('arquivo', 'convenio_adesao', 'Arquivo: (*)', array($row['arquivo'], $row['arquivo_nome']), 'convenio_adesao', TRUE);
            	echo form_default_text('ds_convenio_adesao', 'Documento:', $row, 'style="width:500px;"');
				
				$ar_lgpd = Array(Array('text' => 'Selecione', 'value' => ''),Array('text' => 'Sim', 'value' => 'S'),Array('text' => 'Não', 'value' => 'N')) ;
				echo form_default_dropdown('fl_lgpd', 'Cláusulas LGPD:*', $ar_lgpd, Array($row['fl_lgpd']));						
				
				echo form_default_upload_iframe('arquivo_aprovacao', 'convenio_adesao', 'Doc. Aprovação:', array($row['arquivo_aprovacao'], $row['arquivo_aprovacao_nome']), 'convenio_adesao', TRUE);
				echo form_default_upload_iframe('arquivo_termo_aditivo', 'convenio_adesao', 'Termo Aditivo:', array($row['arquivo_termo_aditivo'], $row['arquivo_termo_aditivo_nome']), 'convenio_adesao', TRUE);
				echo form_default_upload_iframe('arquivo_portaria_aprovacao', 'convenio_adesao', 'Portaria de Aprovação Termo Aditivo:', array($row['arquivo_portaria_aprovacao'], $row['arquivo_portaria_aprovacao_nome']), 'convenio_adesao', TRUE);
                echo form_default_upload_iframe('arquivo_termo_adesao', 'convenio_adesao', 'Termo de Adesão:', array($row['arquivo_termo_adesao'], $row['arquivo_termo_adesao_nome']), 'convenio_adesao', TRUE);
				echo form_default_upload_iframe('arquivo_portaria_aprovacao_adesao', 'convenio_adesao', 'Portaria de Aprovação Termo Adesão:', array($row['arquivo_portaria_aprovacao_adesao'], $row['arquivo_portaria_aprovacao_adesao_nome']), 'convenio_adesao', TRUE);
            
                if(trim($row['dt_envio']) != '')
                {
                    echo form_default_row('', 'Dt. Envio:', $row['dt_envio']);
                    echo form_default_row('', 'Usuário:', $row['ds_usuario_envio']);
                }
                
            echo form_end_box('cadastro_box');
            echo form_command_bar_detail_start();
                if($fl_editar)
                {
                	echo button_save('Salvar'); 

                	if(intval($row['cd_convenio_adesao']) > 0)
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
	$this->load->view('footer');
?>