<?php
	set_title('Documentos Encaminhados');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('ds_justificativa', 'fl_enviar_email')); ?>

	function ir_lista()
    {
        location.href = "<?= site_url('ecrm/doc_encaminhado') ?>";
    }

	function enviar()
	{
		if($("#fl_enviar_email").val() == 'S')
		{
			var confirmacao = 
			 	'Deseja enviar E-mail?\n\n'+
	            'Clique [Ok] para Sim\n\n'+
	            'Clique [Cancelar] para Não\n\n';
        }
        else
        {
        	var confirmacao = 
			 	'Deseja SEM enviar E-mail?\n\n'+
	            'Clique [Ok] para Sim\n\n'+
	            'Clique [Cancelar] para Não\n\n';
        }

        if(confirm(confirmacao))
        { 
			location.href = "<?= site_url('ecrm/doc_encaminhado/enviar/'.intval($row['cd_doc_encaminhado'])) ?>/"+$("#fl_enviar_email").val();
		}
	}

	function confirmar()
	{
		var confirmacao = 
		 	'Deseja confirmar a documentação do participante?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        { 
			location.href = "<?= site_url('ecrm/doc_encaminhado/confirmar/'.intval($row['cd_doc_encaminhado'])) ?>";
		}
	}

	function cancelar()
	{
		location.href = "<?= site_url('ecrm/doc_encaminhado/cancelamento/'.intval($row['cd_doc_encaminhado'])) ?>";
	}

	function andamento()
	{
		location.href = "<?= site_url('ecrm/doc_encaminhado/acompanhamento/'.intval($row['cd_doc_encaminhado'])) ?>/S";
	}

	function ir_acompanhamento()
	{
		location.href = "<?= site_url('ecrm/doc_encaminhado/acompanhamento/'.intval($row['cd_doc_encaminhado'])) ?>";
	}

	function validado()
	{
		location.href = "<?= site_url('ecrm/doc_encaminhado/validado/'.intval($row['cd_doc_encaminhado'])) ?>";
	}

	function checkAll()
    {
        var ipts = $("#table-1>tbody").find("input:checkbox");
        var check = document.getElementById("checkboxCheckAll");
	 
        check.checked ?
            jQuery.each(ipts, function(){
            this.checked = true;
        }) :
            jQuery.each(ipts, function(){
            this.checked = false;
        });
    }

    /*
    function aprovar()
    {
        var arr = new Array();
	
		$("input[name='reuniao_cci_pauta[]']:checked").each(function(){
		   arr.push($(this).val());
		});
        
        var confirmacao = 'Deseja aprovar as pautas da reunião?\n\n'+
					'Clique [Ok] para Sim\n\n'+
					'Clique [Cancelar] para Não\n\n';
        
        if(arr.length > 0)
        {
            if(confirm(confirmacao))
            {
                $.post( '<?php echo site_url('gestao/reuniao_cci/aprovar') ?>',
                {
                    'reuniao_cci_pauta[]' : arr,
                    cd_reuniao_cci        : $('#cd_reuniao_cci').val()
                },
                function(data)
                {
                    ir_lista();
                });
            }
        }
        else
        {
            alert('Selecione os aprovados.');
        }
    }
    */

    function valida_doc()
    {
    	var doc = true;

    	$("input[name='doc_encaminhado_arquivo[]']:checked").each(function(){
		  // arr.push($(this).val());

		   	if($("#doc_encaminhado_"+$(this).val()).val() == '')
		   	{
		   		doc = false;
		   	}
		});

		return doc;
    }

    function gerar_protocolo_interno()
    {
    	var doc = valida_doc();

		if(!doc)
		{
			alert("Informe os Cód. de Documento.");
		}
		else
		{
			if($("#cd_documento_recebido_tipo_solic").val() == '')
			{
				alert("Informe o tipo de solicitação.");
			}
			else
			{

				var confirmacao = 
				 	'Deseja criar um PROTOCOLO INTERNO?\n\n'+
		            'Clique [Ok] para Sim\n\n'+
		            'Clique [Cancelar] para Não\n\n';
				
				if(confirm(confirmacao))
        		{
					$("#form_documento").attr("action", "<?= site_url('ecrm/doc_encaminhado/gerar_protocolo_interno') ?>");
					$("#form_documento").attr("target", "_blank");
					$("#form_documento").submit();

					location.reload();
				}
			}
		}
    }

    function enviar_documento_liquid()
    {
    	var doc = valida_doc();

		if(!doc)
		{
			alert("Informe os Cód. de Documento.");
		}
		else
		{
			var confirmacao = 
			 	'Deseja enviar o(s) documento(s) para o LIQUID?\n\n'+
	            'Clique [Ok] para Sim\n\n'+
	            'Clique [Cancelar] para Não\n\n';
			
			if(confirm(confirmacao))
    		{
				$("#form_documento").attr("action", "<?= site_url('ecrm/doc_encaminhado/enviar_documento_liquid') ?>");
				$("#form_documento").submit();

				//location.reload();
			}
		}
    }
</script>
<style>
    #justificativa_item {
        white-space:normal !important;
    }

    #validado {
        white-space:normal !important;
    }
</style>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cancelar', 'Cadastro', TRUE, 'location.reload();');
	$abas[] = array('aba_acompanhamento', 'Acompanhamento', FALSE, 'ir_acompanhamento();');

	$enviar_email = array(
		array('value' => 'S', 'text' => 'Sim'),
		array('value' => 'N', 'text' => 'Não')
	);

	$head = array( 
		'<input type="checkbox"  id="checkboxCheckAll" onclick="checkAll();" title="Clique para Marcar ou Desmarcar Todos">',
		'Cód de Documento',
		'Documento',
		'Observação',
		'Protocolo Interno',
		'ID Liquid'
	);

	$body = array();

	foreach($collection as $item)
	{
		$campo_check = array(
			'name'  => 'doc_encaminhado_arquivo[]',
			'id'    => 'doc_encaminhado_arquivo[]',
			'value' => $item['cd_doc_encaminhado_arquivo']
		);

		$body[] = array(
			form_checkbox($campo_check),
			//$item['cd_tipo_doc'],
			form_input(array('type' => 'text', 'name' => 'doc_encaminhado_'.$item['cd_doc_encaminhado_arquivo'], 'id' => 'doc_encaminhado_'.$item['cd_doc_encaminhado_arquivo'], 'value'=> $item['cd_tipo_doc'])).
			'<script>
			jQuery(function($){
			   $("#doc_encaminhado_'.$item['cd_doc_encaminhado_arquivo'].'").numeric();
			});
			</script>',
			array(anchor(base_url().'up/doc_encaminhado/'.$item['ds_documento'], $item['ds_documento'], array('target' => '_blank')), 'text-align:left'),
			array($item['ds_observacao'], 'text-align:justify'),
			(intval($item['cd_documento_recebido']) > 0 ?
				anchor(
					'ecrm/cadastro_protocolo_interno/detalhe/'.intval($item['cd_documento_recebido']), $item['nr_documento_recebido'], 
					array('target' => '_blank')
				) : ''),
			$item['id_liquid']
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	$head = array( 
		'Cód.',
		'RE',
		'Nome Participante',
		'Tipo de Documento',
		'Qt. Documento',
		'Dt. Encaminhamento',
		'Status',
		'Obs',
		'Dt. Envio Part.',
		'Dt. Confirmação',
		'Usuário',
		'Dt. Cancelamento',
		'Usuário',
		'Justificativa'
	);

	$body = array();

	foreach($encaminhamento as $item)
	{
		$body[] = array(
			anchor('ecrm/doc_encaminhado/cadastro/'.$item['cd_doc_encaminhado'], $item['cd_doc_encaminhado']),
			anchor('ecrm/doc_encaminhado/cadastro/'.$item['cd_doc_encaminhado'], $item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia']),
			anchor('ecrm/doc_encaminhado/cadastro/'.$item['cd_doc_encaminhado'], $item['nome']),
			$item['ds_doc_encaminhado_tipo_doc'],
			$item['qt_documento'],
			$item['dt_encaminhamento'],
			'<label class="'.$item['ds_class_status'].'">'.$item['ds_status'].'</label>',
			array((trim($item['fl_andamento']) == 'S' ? nl2br($item['ds_andamento']) : nl2br($item['ds_acompanhamento'])), 'text-align:justify;'),
			$item['dt_envio_participante'],
			$item['dt_confirmacao'],
			$item['cd_usuario_confirmacao'],
			$item['dt_cancelamento'],
			$item['cd_usuario_cancelamento'],
			array($item['ds_justificativa'], 'text-align:justify')
		);
	}

	$grid_encaminhamento = new grid();
	$grid_encaminhamento->head = $head;
	$grid_encaminhamento->body = $body;

	echo aba_start($abas);
		
		echo form_start_box('default_box', 'Documento Encaminhado');
			echo form_default_row('', 'Cód:', '<label class="label label-inverse">'.$row['cd_doc_encaminhado'].'</label>');
			echo form_default_row('', 'Status:', '<label class="'.$row['ds_class_status'].'">'.$row['ds_status'].'</label>');
			echo form_default_row('', 'RE:', $row['cd_empresa'].'/'.$row['cd_registro_empregado'].'/'.$row['seq_dependencia']);
			echo form_default_row('', 'Nome:', $row['nome']);
			echo form_default_row('', 'Dt. Encaminhamento:', $row['dt_encaminhamento']);
			echo form_default_row('', 'Tipo Documento:', $row['ds_doc_encaminhado_tipo_doc']);
			if(trim($row['dt_cancelamento']) != '')
			{
				echo form_default_row('', 'Dt. Cancelamento:', $row['dt_cancelamento']);
				echo form_default_row('justificativa', 'Justificativa:', nl2br($row['ds_justificativa']));
			}

			if(trim($row['dt_confirmacao']) != '')
			{
				echo form_default_row('', 'Dt. Confirmação:', $row['dt_confirmacao']);
			}

			if(trim($row['dt_andamento']) != '')
			{
				echo form_default_row('', 'Dt. Andamento:', $row['dt_andamento']);
			}

			if(trim($row['dt_validado']) != '')
			{
				echo form_default_row('', 'Dt. Validado:', $row['dt_validado']);
				echo form_default_row('validado', 'Descrição:', $row['ds_validado']);
			}
			//echo form_default_row('', 'Documento:',(anchor(base_url().'up/doc_encaminhado/'.$row['ds_documento'], $row['ds_documento'], array('target' => '_blank'))));
		echo form_end_box('default_box');
		echo form_open('ecrm/doc_encaminhado/gerar', 'id="form_documento"');
			
			echo $grid->render();
			
			echo form_start_box('protocolo_interno_box', 'Protocolo Interno');
				echo form_default_hidden('cd_doc_encaminhado', '', $row['cd_doc_encaminhado']);
				echo form_default_dropdown('cd_documento_recebido_tipo_solic', 'Tipo de Solicitação GCM: (*)', $tipo_protocolo_interno);
			echo form_end_box('protocolo_interno_box');
		echo form_close();
		echo form_command_bar_detail_start();
			if(trim($row['dt_envio_participante']) == '')
			{
				if(trim($row['dt_cancelamento']) == '')
				{
					if(trim($row['dt_andamento']) == '')
					{
						echo button_save('Em Andamento', 'andamento();');
					}

					if(trim($row['dt_validado']) == '')
					{
						echo button_save('Validado', 'validado();', 'botao_amarelo');
					}

					echo button_save('Confirmar', 'confirmar();', 'botao_verde');
					echo button_save('Cancelar', 'cancelar();', 'botao_vermelho');
				}
			}

			if(trim($row['dt_confirmacao']) != '')
			{
				echo button_save('Gerar Protocolo Interno', 'gerar_protocolo_interno();');
				echo button_save('Enviar Documentos Liquid', 'enviar_documento_liquid();');
			}
		echo form_command_bar_detail_end();
			/*
			echo form_start_box('default_box', 'Cancelamento');
				echo form_default_hidden('cd_doc_encaminhado', '', $row['cd_doc_encaminhado']);
				if(trim($row['dt_cancelamento']) != '')
				{
					echo form_default_dropdown('fl_enviar_email', 'Enviar E-mail: (*)', $enviar_email, 'S');
				}
				echo form_default_textarea('ds_justificativa', 'Justificativa: (*)', $row['ds_justificativa']);
			echo form_end_box('default_box');
			echo form_command_bar_detail_start();

				if(trim($row['dt_envio_participante']) == '')
				{
					echo button_save('Salvar');

					if(trim($row['dt_cancelamento']) != '')
					{
						echo button_save('Enviar', 'enviar();', 'botao_verde');
					}
				}
            echo form_command_bar_detail_end();
            */

        echo form_start_box('encaminhamento_box', 'Outros Encaminhamentos');
			echo $grid_encaminhamento->render();
		echo form_end_box('encaminhamento_box');
		
	echo aba_end();

	$this->load->view('footer');
?>