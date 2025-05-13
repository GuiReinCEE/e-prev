<?php
	set_title('Registro de Pendência de Documento');
	$this->load->view('header');
?>
<script>
	function ir_lista()
    {
        location.href = "<?= site_url('ecrm/registro_pendencia_documento') ?>";
    }
    
	function confirmar()
	{
		var confirmacao = 
		 	'Deseja confirmar a documentação do participante?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        { 
			location.href = "<?= site_url('ecrm/registro_pendencia_documento/confirmar/'.intval($row['cd_registro_pendencia_documento'])) ?>";
		}
	}

	function cancelar()
	{
		location.href = "<?= site_url('ecrm/registro_pendencia_documento/cancelamento/'.intval($row['cd_registro_pendencia_documento'])) ?>";
	}

	function andamento()
	{
		location.href = "<?= site_url('ecrm/registro_pendencia_documento/andamento/'.intval($row['cd_registro_pendencia_documento'])) ?>";
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


    function valida_doc()
    {
    	var doc = true;

    	$("input[name='registro_pendencia_documento[]']:checked").each(function(){
		  // arr.push($(this).val());

		   	if($("#registro_pendencia_"+$(this).val()).val() == '')
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
					$("#form_documento").attr("action", "<?= site_url('ecrm/registro_pendencia_documento/gerar_protocolo_interno') ?>");
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
				$("#form_documento").attr("action", "<?= site_url('ecrm/registro_pendencia_documento/enviar_documento_liquid') ?>");
				$("#form_documento").submit();

				//location.reload();
			}
		}
    }

</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cancelar', 'Cadastro', TRUE, 'location.reload();');

	$enviar_email = array(
		array('value' => 'S', 'text' => 'Sim'),
		array('value' => 'N', 'text' => 'Não')
	);

	$head = array( 
		'<input type="checkbox"  id="checkboxCheckAll" onclick="checkAll();" title="Clique para Marcar ou Desmarcar Todos">',
		'Cód de Documento',
		'Documento',
		'Protocolo Interno',
		'ID Liquid'
	);

	$body = array();

	foreach($collection as $item)
	{
		$campo_check = array(
			'name'  => 'registro_pendencia_documento[]',
			'id'    => 'registro_pendencia_documento[]',
			'value' => $item['cd_registro_pendencia_documento_arquivo']
		);

		$body[] = array(
			form_checkbox($campo_check),
			//$item['cd_tipo_doc'],
			form_input(array('type' => 'text', 'name' => 'registro_pendencia_'.$item['cd_registro_pendencia_documento_arquivo'], 'id' => 'registro_pendencia_'.$item['cd_registro_pendencia_documento_arquivo'], 'value'=> $item['cd_tipo_doc'])).
			'<script>
			jQuery(function($){
			   $("#registro_pendencia_'.$item['cd_registro_pendencia_documento_arquivo'].'").numeric();
			});
			</script>',
			array(anchor(base_url().'up/registro_pendencia_documento/'.$item['ds_arquivo'], $item['ds_arquivo'], array('target' => '_blank')), 'text-align:left'),
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

	echo aba_start($abas);
		echo form_start_box('default_box', 'Registro de Pendência de Documento');
			echo form_default_row('', 'Cód:', '<label class="label label-inverse">'.$row['cd_registro_pendencia_documento'].'</label>');
			echo form_default_row('', 'Status:', '<label class="'.$row['ds_class_status'].'">'.$row['ds_status'].'</label>');
			echo form_default_row('', 'RE:', $row['cd_empresa'].'/'.$row['cd_registro_empregado'].'/'.$row['seq_dependencia']);
			echo form_default_row('', 'Nome:', $row['nome']);
			echo form_default_row('', 'Dt. Encaminhamento:', $row['dt_encaminhamento']);
			//echo form_default_row('', 'Tipo Documento:', $row['ds_doc_encaminhado_tipo_doc']);
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
			//echo form_default_row('', 'Documento:',(anchor(base_url().'up/doc_encaminhado/'.$row['ds_documento'], $row['ds_documento'], array('target' => '_blank'))));
		echo form_end_box('default_box');
		echo form_open('ecrm/registro_pendencia_documento/gerar', 'id="form_documento"');
			
			echo $grid->render();
			
			echo form_start_box('protocolo_interno_box', 'Protocolo Interno');
				echo form_default_hidden('cd_registro_pendencia_documento', '', $row['cd_registro_pendencia_documento']);
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

					echo button_save('Confirmar', 'confirmar();', 'botao_verde');
				}
	
				echo button_save('Cancelar', 'cancelar();', 'botao_vermelho');	
			}

			if(trim($row['dt_confirmacao']) != '')
			{
				echo button_save('Gerar Protocolo Interno', 'gerar_protocolo_interno();');
				echo button_save('Enviar Documentos Liquid', 'enviar_documento_liquid();');
			}
		echo form_command_bar_detail_end();
	echo aba_end();

	$this->load->view('footer');
?>