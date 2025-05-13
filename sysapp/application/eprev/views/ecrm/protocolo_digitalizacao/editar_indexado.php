<?php
set_title('Protocolo de Digitalização');
$this->load->view('header');
?>
<script>
    function ir_lista()
    {
        location.href = "<?= site_url('ecrm/protocolo_digitalizacao') ?>";
    }

    function ir_relatorio()
    {
        location.href = "<?= site_url('ecrm/protocolo_digitalizacao/relatorio') ?>";
    }

    function excluir_protocolo()
    {
        var confirmacao = 'Deseja excluir o protocolo?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        { 
            location.href = "<?= site_url('ecrm/protocolo_digitalizacao/excluir_indexado/'.$row['cd_documento_protocolo']) ?>";
        }
    }

    function excluir_protocolo_item(cd_documento_protocolo_item)
    {
        var confirmacao = 'Deseja excluir o protocolo?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        { 
            location.href = "<?= site_url('ecrm/protocolo_digitalizacao/excluir_item_indexado/'.$row['cd_documento_protocolo']) ?>/"+cd_documento_protocolo_item;
        }
    }

    function editar_obs(cd_documento_protocolo_item)
	{
		$("#obs_valor_" + cd_documento_protocolo_item).hide(); 
		$("#obs_editar_" + cd_documento_protocolo_item).hide(); 

		$("#obs_salvar_" + cd_documento_protocolo_item).show(); 
		$("#ds_observacao_indexacao_" + cd_documento_protocolo_item).show(); 
		$("#ds_observacao_indexacao_" + cd_documento_protocolo_item).focus();	
	}

	function alterar_obs(cd_documento_protocolo_item)
    {
        $("#ajax_obs_valor_" + cd_documento_protocolo_item).html("<?= loader_html('P') ?>");

        $.post("<?= site_url('ecrm/protocolo_digitalizacao/alterar_observacao_indexacao') ?>",
        {
            cd_documento_protocolo_item : cd_documento_protocolo_item,
            ds_observacao_indexacao : $("#ds_observacao_indexacao_" + cd_documento_protocolo_item).val()	
        },
        function(data)
        {
			$("#ajax_obs_valor_" + cd_documento_protocolo_item).empty();
			
			$("#ds_observacao_indexacao_" + cd_documento_protocolo_item).hide();
			$("#obs_salvar_" + cd_documento_protocolo_item).hide(); 
			
            $("#obs_valor_" + cd_documento_protocolo_item).html($("#ds_observacao_indexacao_" + cd_documento_protocolo_item).val()); 
			$("#obs_valor_" + cd_documento_protocolo_item).show(); 
			$("#obs_editar_" + cd_documento_protocolo_item).show();
			
			//filtrar();
        }, 'html',true);
    }	
    
 </script>
<?php
    $abas[] = array('aba_lista', 'Lista', false, 'ir_lista();');
    $abas[] = array('aba_relatorio', 'Relatório', false, 'ir_relatorio();');
    $abas[] = array('aba_detalhe', 'Protocolo '.$row['ano'].'/'.$row['contador'].'/'.$row['tipo'], true, 'location.reload();');

    $body = array();
    $head = array(
      'Obs Digitalização',
      '',
      'Observação',
      'Cadastro',
      'Exclusão',
      'Usuário',
      'Indexação',
      'Devolução',
      'Motivo devolução',
      'RE',
      'Participante',
      'Doc',
      'Tipo de documento',
      'Caminho',
      'Páginas',
      'Arquivo',
      ''
    );

    foreach ($collection as $item)
    {
        $caminho = $item['caminho'];

    	if(trim($item['ds_caminho_liquid']) != '')
    	{
    		$caminho = $item['ds_caminho_liquid'];
    	}

    	$excluir = false;

    	if(trim($row['dt_exclusao']) == '' AND trim($item['dt_exclusao']) == '')
    	{
    		$excluir = true;
    	}

    	$config = array(
			'name'   => 'ds_observacao_indexacao_'.$item['cd_documento_protocolo_item'], 
			'id'     => 'ds_observacao_indexacao_'.$item['cd_documento_protocolo_item'],
			'onblur' => 'alterar_obs('.$item['cd_documento_protocolo_item'].');',
			'style'  => 'display:none;'
		);
    	
        $body[] = array(
    		array(
    			'<span id="ajax_obs_valor_'.$item['cd_documento_protocolo_item'].'"></span> '.
				'<span id="obs_valor_'.$item['cd_documento_protocolo_item'].'" style="color:red; font-weight:bold;text-align:left;">'.$item['ds_observacao_indexacao'].'</span>'.
				form_textarea($config, $item['ds_observacao_indexacao']), 'text-align:justify;'
			),

            ($excluir ? '<a id="obs_editar_'.$item['cd_documento_protocolo_item'].'" href="javascript:void(0);" onclick="editar_obs('.$item['cd_documento_protocolo_item'].');" title="Editar">[Editar]</a>'.
			'<a id="obs_salvar_'.$item['cd_documento_protocolo_item'].'" href="javascript:void(0);" style="display:none;" title="Salvar">[Salvar]</a>' : ''),

    		array($item['observacao'], 'text-align:left;'),
            $item['dt_cadastro'],
            $item['dt_exclusao'],
    		$item['ds_usuario_exclusao'],
    		$item['dt_indexacao'],
    		$item['dt_devolucao'],
    		$item['motivo_devolucao'],
    		$item['cd_empresa'] . '/' . $item['cd_registro_empregado'] . '/' . $item['seq_dependencia'],
    		array($item["nome_participante"], "text-align:left;"),
    		(($item['cd_tipo_doc']) ? $item['cd_tipo_doc'] : $item['cd_doc_juridico']),
    		array((($item['nome_documento']) ? $item['nome_documento'] : $item['nome_documento_juridico']), "text-align:left;"),
    		array($caminho, 'text-align:left;'),
    		$item['nr_folha'],
    		(trim($item['arquivo']) != "" ? '<a href="' . base_url() . 'up/protocolo_digitalizacao_' . $item['cd_documento_protocolo'] . '/' . $item['arquivo'] . '" target="_blank">' . $item['arquivo'] . '</a>' : ""),
            ($excluir ? '<a href="javascript:void(0);" onclick="excluir_protocolo_item('.$item['cd_documento_protocolo_item'].')">[excluir]</a>' : '')
        );
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;

    echo aba_start($abas);
    	echo form_start_box('default_box', 'Protocolo');
    		echo form_default_row('', 'Protocolo:', '<span class="label label-inverse">'.$row['ano'].'/'.$row['contador'].'</span>');
    		echo form_default_row('', 'Tipo:', '<span class="label label-important">'.($row['tipo'] == 'D' ? 'DIGITAL' : 'PAPEL').'</span>');	
    	    echo form_default_row('', 'Dt cadastro:', $row['dt_cadastro']);
    	    echo form_default_row('', 'Usuário:', $row['nome_usuario_cadastro']);

    	    echo form_default_row('', 'Dt Envio:', $row['dt_envio']);
            echo form_default_row('', 'Usuário:', $row['nome_usuario_envio']);

            echo form_default_row('', 'Dt Recebido:', $row['dt_ok']);
            echo form_default_row('', 'Usuário:', $row['nome_usuario_ok']);

            echo form_default_row('', 'Dt Indexação:', $row['dt_indexacao']);

            if(trim($row['dt_exclusao']) != '')
            {
    	        echo form_default_row('', 'Dt Exclusão:', $row['dt_exclusao']);
    	        echo form_default_row('', 'Usuário:', $row['ds_usuario_exclusao']);
        	}
    	echo form_end_box('default_box');
    	echo form_command_bar_detail_start();
            if(trim($row['dt_exclusao']) == '')
            {
    			echo button_save('Excluir Protocolo', 'excluir_protocolo()', 'botao_vermelho');
            }
        echo form_command_bar_detail_end();

        echo br();
    	echo $grid->render();
    echo aba_end();
    $this->load->view('footer');
?>