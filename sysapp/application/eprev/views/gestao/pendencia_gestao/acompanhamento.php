<?php
	set_title('Pendências Gestão');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('fl_implementado', 'fl_executando', 'fl_verificado', 'ds_pendencia_gestao_acompanhamento'), '_valida_formulario(form)') ?>

	function _valida_formulario(form)
	{
		var confirmacao = "Salvar?";

		if($("#cd_reuniao_sistema_gestao_tipo").val() != 24)
		{
			if(confirm(confirmacao))
			{
				form.submit();
			}
		}
		else
		{	<? if(trim($row['dt_implementado']) == ''): ?>
			if($("#dt_verificado_eficacia").val() == "" && $("#fl_implementado").val() == 'S' || $("#dt_verificado_eficacia").val() != "" && $("#fl_implementado").val() == 'N')
			{
				alert("Informe a data que foi Verificação da Eficácia.");
			}
			else if($('#dt_verificado_eficacia').val() != "")
            {
                var dt_verificado_eficacia = Date.fromString($('#dt_verificado_eficacia').val());
                dt_verificado_eficacia.zeroTime();
                var dt_minima = new Date();
                dt_minima.addDays(+7);
                dt_minima.zeroTime();
					
                if(dt_verificado_eficacia < dt_minima)
                {
                    alert('A data mímima de verificação é a data de hoje +7 dias ('+dt_minima.asString()+')');
                    $('#dt_verificado_eficacia').focus()
                    return false;
                }
                else 
                {
                	if(confirm(confirmacao))
					{
						form.submit();
					}
                }
            }
			else
			{
				if(confirm(confirmacao))
				{
					form.submit();
				}
			}
			<? else: ?>
				if(confirm(confirmacao))
				{
					form.submit();
				}
			<? endif;?>
		}
	}

	function ir_lista()
    {
        location.href = "<?= site_url('gestao/pendencia_gestao') ?>";
    }

    function ir_cadastro()
    {
        location.href = "<?= site_url('gestao/pendencia_gestao/cadastro/'.intval($row['cd_pendencia_gestao'])) ?>";
    }
    
    function ir_anexo()
    {
        location.href = "<?= site_url('gestao/pendencia_gestao/anexo/'.intval($row['cd_pendencia_gestao'])) ?>";
    }
	
    function ir_cronograma()
    {
        location.href = "<?= site_url('gestao/pendencia_gestao/cronograma/'.intval($row['cd_pendencia_gestao'])) ?>";
    }	

    function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			'DateTimeBR',
			'CaseInsensitiveString',
			'CaseInsensitiveString'
		]);
		ob_resul.onsort = function ()
		{
			var rows = ob_resul.tBody.rows;
			var l = rows.length;
			for (var i = 0; i < l; i++)
			{
				removeClassName( rows[i], i % 2 ? "sort-par" : "sort-impar" );
				addClassName( rows[i], i % 2 ? "sort-impar" : "sort-par" );
			}
		};
		ob_resul.sort(0, true);
	}

	function excluir_acompanhamento(cd_pendencia_gestao_acompanhamento)
    {
        var confirmacao = 'Deseja excluir o Item?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        { 
            location.href = '<?= site_url('gestao/pendencia_gestao/excluir_acompanhamento/'.$row['cd_pendencia_gestao']) ?>/'+cd_pendencia_gestao_acompanhamento;
        }
    }
	
	function checkImplementado()
	{
		if($('#fl_implementado').val() == "N")
		{
			$('#fl_executando_row').show();
		}
		else
		{
			$('#fl_executando_row').hide();
			$('#fl_executando').val("N");
		}
	}

	function checkVerificacao()
	{
		<? if(trim($row['dt_implementado']) == ''): ?>
			if($('#fl_implementado').val() == "S")
			{
				$('#dt_verificado_eficacia_row').show();
			}
			else
			{
				$('#dt_verificado_eficacia_row').hide();
				$('#dt_verificado_eficacia').val("");
			}
		<? else: ?>
			$('#dt_verificado_eficacia_row').show();
		<? endif;?>
	}		

	$(function(){
		configure_result_table();
		
		checkImplementado();
		checkVerificacao();
		
		$('#fl_implementado').change(function(){ checkImplementado() });
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_pendencia', 'Cadastro', FALSE, 'ir_cadastro();');
	if($row['fl_cronograma'] == "S")
	{
		$abas[] = array('aba_cronograma', 'Cronograma', FALSE, 'ir_cronograma();');
	}	
	$abas[] = array('aba_acompanhamento', 'Acompanhamento', TRUE, 'location.reload();');
	$abas[] = array('aba_anexo', 'Anexo', FALSE, 'ir_anexo();');

    $head = array(
      'Dt. Inclusão',
      'Acompanhamento',
      'Usuário',
      ''
    );

    $body = array();

    foreach ($collection as $item)
    {
        $body[] = array(
          $item['dt_inclusao'],
          array(nl2br($item['ds_pendencia_gestao_acompanhamento']), 'text-align:justify;'),
          array($item['ds_usuario'], 'text-align:left;'),
          ((intval($item['cd_usuario_inclusao']) == $this->session->userdata('codigo') AND (trim($item['dt_encerramento']) == '')) ? '<a href="javascript:void(0);" onclick="excluir_acompanhamento('.$item['cd_pendencia_gestao_acompanhamento'].' )">[excluir]</a>': '')
        );
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;    

	echo aba_start($abas);
		echo form_open('gestao/pendencia_gestao/salva_acompanhamento');
			echo form_start_box('default_box_pendencia', 'Pêndencia');
				echo form_default_hidden('cd_pendencia_gestao', '', $row['cd_pendencia_gestao']);
				echo form_default_hidden('cd_reuniao_sistema_gestao_tipo', '', $row['cd_reuniao_sistema_gestao_tipo']);
				echo form_default_row('ds_reuniao_sistema_gestao_tipo', 'Reunião:', $row['ds_reuniao_sistema_gestao_tipo']);

				if(trim($row['dt_reuniao']) != '')
				{
					echo form_default_row('dt_reuniao', 'Dt. Reunião:', $row['dt_reuniao']);
                }

                echo form_default_row('dt_prazo', 'Dt. Prazo:', $row['dt_prazo']);
				echo form_default_textarea('ds_item', 'Item:', $row['ds_item'], 'style="height:80px; width:500px;" readonly=""');
				
			echo form_end_box('default_box_pendencia');
			echo form_start_box('default_box', 'Acompanhamento');
                if(trim($row['dt_implementado']) == '')
                {
					if(trim($row['dt_inicio']) == '')
					{
						echo form_default_dropdown('fl_executando', 'Em Andamento: ', $executando, 'N');
					}
					else
					{
						echo form_default_row('', 'Em Andamento: ', trim($row['dt_executando']).' - '.trim($row['ds_usuario_executando']));
						echo form_default_hidden('fl_executando', '', 'N');
					}

					echo form_default_dropdown('fl_implementado', 'Implementado: ', $implementado, 'N', 'onchange="checkVerificacao();"');
                }
                else
                {
                    if(trim($row['dt_inicio']) != '')
					{
                    	echo form_default_row('', 'Em Andamento: ', trim($row['dt_executando']).' - '.trim($row['ds_usuario_executando']));
                	}

                	echo form_default_row('', 'Implementado: ', trim($row['ds_implementado']).' - '.trim($row['ds_usuario_implementado']));

					echo form_default_hidden('fl_implementado', '', 'N');
                    echo form_default_hidden('fl_executando', '', 'N');
                }

                if(intval($row['cd_reuniao_sistema_gestao_tipo']) == 24)
                {
                	echo form_default_hidden('fl_verificado', '', 'S');
                	if($row['dt_verificado_eficacia'] != '')
                	{
                		echo form_default_row('', 'Dt. Verificação a Eficácia:', $row['dt_verificado_eficacia']);
                	}

                	echo form_default_date('dt_verificado_eficacia', 'Dt. Verificação a Eficácia:');
                	
                }
            	else
            	{
                    echo form_default_hidden('fl_verificado', '', 'N');
            	}
				
				echo form_default_textarea('ds_pendencia_gestao_acompanhamento', 'Descrição: (*)', '', 'style="height:80px; width:500px;"');
			echo form_end_box('default_box');
            echo form_command_bar_detail_start();
            if(trim($row['dt_prazo']) != '')
            { 
                echo button_save('Salvar');
            }
            else
            {
                echo '<span class="label label-important">Para registrar o Acompanhamento antes é necessário informar a Data do Prazo na aba Cadastro</span>';
            }
	        echo form_command_bar_detail_end();
		echo form_close();
		echo $grid->render();
 		echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?>