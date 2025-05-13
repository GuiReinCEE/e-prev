<?php
	set_title('Regulamento');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('cd_regulamento_tipo', 'dt_aprovacao_cd', 'nr_ata_cd', 'fl_envio_email'), 'valida_arquivo(form)') ?>

	function ir_lista()
    {
        location.href = "<?= site_url('gestao/regulamento/index') ?>";
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

    function enviar()
    {
    	var confirmacao = 'Deseja enviar e-mail com a alteração do regulamento?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        { 
            location.href = "<?= site_url('gestao/regulamento/enviar/'.$row['cd_regulamento']) ?>";
        }
    }

    function set_email(cd_regulamento_tipo)
    {
    	if(cd_regulamento_tipo == 13)
    	{
    		$("#fl_envio_email").val("<?= $row['fl_envio_email'] ?>");
    		$("#fl_envio_email_row").show();
    	}
    	else
    	{
    		$("#fl_envio_email").val("R");
    		$("#fl_envio_email_row").hide();
    	}
    }

    $(function(){
    	set_email($("#cd_regulamento_tipo").val());
    });	
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

	$head = array(
		'Regulamento',
		'Arquivo',
		'Dt. Aprovação CD',
		'Nr. Ata Aprovação CD',
		'Dt. Envio PREVIC',
		'Dt. Aprovação PREVIC',
		'Descrição Doc. PREVIC',
		'Doc. Aprovação',
		'Quadros Comparativos'
	);

	$body = array();

	foreach ($collection as $item)
	{
	  	$body[] = array(
			array($item['ds_regulamento_tipo'], 'text-align:left;'),
			anchor(base_url().'up/regulamento/'.$item['arquivo'], '[regulamento]', array('target' => '_blank')),
		    $item['dt_aprovacao_cd'],
		    $item['nr_ata_cd'],
		    $item['dt_envio_previc'],
		    $item['dt_aprovacao_previc'], 
		    array($item['ds_aprovacao_previc'], 'text-align:left;'),
			(trim($item['arquivo_aprovacao_previc']) != '' ? anchor(base_url().'up/regulamento/'.$item['arquivo_aprovacao_previc'], '[arquivo]', array('target' => '_blank')) : ''),
			(trim($item['arquivo_comparativo']) != '' ? anchor(base_url().'up/regulamento/'.$item['arquivo_comparativo'], '[arquivo]', array('target' => '_blank')) : ''),
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	$fl_envio_email = array(
		array('value' => 'R', 'text' => 'Regulamento'),
		array('value' => 'A', 'text' => 'Anexos')
	);

	$publicado_site = array(
		array('value' => 'N', 'text' => 'Não'),
		array('value' => 'S', 'text' => 'Sim')
	);

    echo aba_start($abas);
    	echo form_open('gestao/regulamento/salvar');
    		echo form_start_box('default_box', 'Cadastro'); 
	    		echo form_default_hidden('cd_regulamento', '', $row['cd_regulamento']);
	    		if(intval($row['cd_regulamento']) == 0)
	    		{
	    			echo form_default_dropdown('cd_regulamento_tipo', 'Regulamento: (*)', $regulamento, $row['cd_regulamento_tipo'], 'onchange="set_email($(this).val())"');
	    		}
	    		else
	    		{
	    			echo form_default_hidden('cd_regulamento_tipo', '', $row['cd_regulamento_tipo']);
	    			echo form_default_row('', 'Regulamento:', $row['ds_regulamento_tipo']);
	    		}
                
                echo form_default_gerencia('cd_gerencia_responsavel', 'Responsável: (*)', $row['cd_gerencia_responsavel']);
                echo form_default_dropdown('fl_publicado_site', 'Publicado no Site: (*)', $publicado_site, $row['fl_publicado_site']);
		        echo form_default_upload_iframe('arquivo', 'regulamento', 'Arquivo Regulamento: (*)', array($row['arquivo'], $row['arquivo_nome']), 'regulamento', $fl_editar);
	         	echo form_default_date('dt_aprovacao_cd', 'Dt. Aprovação CD: (*)', $row['dt_aprovacao_cd']);
	         	echo form_default_integer('nr_ata_cd', 'Nº Ata Aprovação CD: (*)', $row['nr_ata_cd']);
	    		echo form_default_date('dt_envio_previc', 'Dt. Envio PREVIC:', $row['dt_envio_previc']);
	         	echo form_default_date('dt_aprovacao_previc', 'Dt. Aprovação PREVIC:', $row['dt_aprovacao_previc']);	    		
                echo form_default_text('ds_aprovacao_previc', 'Descrição Doc. PREVIC:', $row['ds_aprovacao_previc'], 'style="width:350px;"');		        
		        echo form_default_upload_iframe('arquivo_aprovacao_previc', 'regulamento', 'Doc. Aprovação:', array($row['arquivo_aprovacao_previc'], $row['arquivo_aprovacao_previc_nome']), 'regulamento', $fl_editar);
		        echo form_default_upload_iframe('arquivo_comparativo', 'regulamento', 'Quadros Comparativos:', array($row['arquivo_comparativo'], $row['arquivo_comparativo_nome']), 'regulamento', $fl_editar);

		        echo form_default_dropdown('fl_envio_email', 'Alteração e-mail: (*)', $fl_envio_email, $row['fl_envio_email']);

		        if(trim($row['dt_envio']) != '')
		        {
		        	echo form_default_row('', 'Dt. Envio:', $row['dt_envio']);
		        	echo form_default_row('', 'Usuário:', $row['ds_usuario_envio']);
		        }

	    	echo form_end_box('default_box');
	    	echo form_command_bar_detail_start();
                if($fl_editar)
                {
                	echo button_save('Salvar'); 

                	if(intval($row['cd_regulamento']) > 0)
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
