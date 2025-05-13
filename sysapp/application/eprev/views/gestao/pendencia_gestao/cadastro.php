<?php
	set_title('Pendências Gestão');
	$this->load->view('header');
?>
<script>
<?php
	if((intval($row['cd_pendencia_gestao']) > 0) AND (trim($row['dt_prazo_prorroga']) != ''))
	{	
		echo form_default_js_submit(array('cd_reuniao_sistema_gestao_tipo', 'ds_item', 'cd_superior', 'dt_prazo_prorroga'), 'valida_responsavel(form);') ;
	}	
	else
	{
		echo form_default_js_submit(array('cd_reuniao_sistema_gestao_tipo','ds_item', 'cd_superior'), 'valida_responsavel(form);') ;
	}
?>

	function valida_responsavel(form)
	{
		var fl_marcado = false;
		
		$("input[type='checkbox'][id='responsavel']").each( 
			function() 
			{ 
				if (this.checked) 
				{ 
					fl_marcado = true;
				} 
			}
		);

		if(($('#cd_usuario_responsavel').val() == '') && (!fl_marcado))
		{
			
			alert("Informe ao menos um Responsável!");
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

	function get_usuarios(cd_gerencia, campo)
	{
		$.post("<?= site_url('gestao/pendencia_gestao/get_usuarios') ?>",
		{
			cd_gerencia : cd_gerencia
		},
		function(data)
		{
			if(campo == 0)
			{
				var select = $('#cd_usuario_responsavel'); 
			}
			
			if(select.prop) 
			{
				var options = select.prop('options');
			}
			else
			{
				var options = select.attr('options');
			}
			
			$('option', select).remove();
			
			options[options.length] = new Option('Selecione', '');
			
			$.each(data, function(val, text) {
				options[options.length] = new Option(text.text, text.value);
			});
			
		}, 'json', true);
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			'DateTimeBR',
			'DateTime',
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

	function ir_lista()
    {
        location.href = "<?= site_url('gestao/pendencia_gestao') ?>";
    }

    function ir_acompanhamento()
    {
        location.href = "<?= site_url('gestao/pendencia_gestao/acompanhamento/'.intval($row['cd_pendencia_gestao'])) ?>";
    }
    
    function ir_anexo()
    {
        location.href = "<?= site_url('gestao/pendencia_gestao/anexo/'.intval($row['cd_pendencia_gestao'])) ?>";
    }
	
    function ir_cronograma()
    {
        location.href = "<?= site_url('gestao/pendencia_gestao/cronograma/'.intval($row['cd_pendencia_gestao'])) ?>";
    }	

    function encerrar()
    {
    	var confirmacao = "Deseja ENCERRAR a Pendência?\n\n"+
                          "Clique [Ok] para Sim\n\n"+
                          "Clique [Cancelar] para Não\n\n"; 

        if(confirm(confirmacao))
        {
            location.href = "<?= site_url("gestao/pendencia_gestao/encerrar/".intval($row['cd_pendencia_gestao'])) ?>";
        }
    }

    <?php
    	if(count($historico) > 0)
	    {
	    	echo '
	    		$(function(){ 
	    			configure_result_table();
				});';
	    }
    ?>
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_pendencia', 'Cadastro', TRUE, 'location.reload();');

	if(intval($row['cd_pendencia_gestao']) > 0)
	{
	    if($row['fl_cronograma'] == "S")
		{
			$abas[] = array('aba_cronograma', 'Cronograma', FALSE, 'ir_cronograma();');
		}
		
		$abas[] = array('aba_acompanhamento', 'Acompanhamento', FALSE, 'ir_acompanhamento();');
	    $abas[] = array('aba_anexo', 'Anexo', FALSE, 'ir_anexo();');

	    if(count($historico) > 0)
	    {
		    $head = array(
			  	'Dt. Inclusão',
				'Dt. Prorrogação',
			  	'Usuário',
			);

			$body = array();

			foreach($historico as $item)
			{
			    $body[] = array(
			    	$item['dt_inclusao'],
			    	$item['dt_pendencia_gestao_prorrogacao'],
			      	array($item['ds_usuario'], 'text-align:left')
			    );
			}

			$this->load->helper('grid');
			$grid = new grid();
			$grid->head = $head;
			$grid->body = $body;
		}
	}

	echo aba_start($abas);
		echo form_open('gestao/pendencia_gestao/salvar');
			echo form_start_box('default_box', 'Cadastro');
				echo form_default_hidden('cd_pendencia_gestao', '', $row['cd_pendencia_gestao']);
				echo form_default_dropdown('cd_reuniao_sistema_gestao_tipo', 'Reunião: (*)', $reuniao, array($row['cd_reuniao_sistema_gestao_tipo']));
				echo form_default_date('dt_reuniao', 'Dt. Reunião:', $row);
				echo form_default_textarea('ds_item', 'Item: (*)', $row['ds_item'], 'style="height:80px; width:500px;"');
				echo form_default_dropdown('cd_superior', 'Superior: (*)', $superior, $row['cd_superior']);
				echo form_default_checkbox_group('responsavel', 'Ger. Responsáveis: ', $responsavel, $responsavel_checked, 150);
				echo form_default_gerencia('cd_gerencia_responsavel', 'Gerência: ', $row['cd_gerencia'],'onchange="get_usuarios(this.value, 0)"');	
				echo form_default_dropdown('cd_usuario_responsavel', 'Usuário Responsável: ', $usuarios, $row['cd_usuario_responsavel'] );
				echo form_default_row('', '', 'Responsável pela execução das ações propostas');

				if((intval($row['cd_pendencia_gestao']) == 0) OR (trim($row['dt_prazo']) == ''))
				{
					echo form_default_date('dt_prazo', 'Dt. Prazo:', $row['dt_prazo']);
				}
				else
				{
					echo form_default_hidden('dt_prazo', '', $row['dt_prazo']);
					echo form_default_row('dt_prazo_row', 'Dt. Prazo:', $row['dt_prazo']);
					echo form_default_hidden('dt_prazo_prorroga_old', '', $row['dt_prazo_prorroga']);
					echo form_default_date('dt_prazo_prorroga', 'Dt. Prorrogação Prazo:', $row['dt_prazo_prorroga']);
				}
				
                if(trim($row['dt_inicio']) != '')
                {	
					echo form_default_row('', 'Em Andamento: ', trim($row['dt_executando']).' - '.trim($row['ds_usuario_executando']));		
				}
				if(trim($row['dt_implementado']) != '')
                {		
					echo form_default_row('', 'Implementado: ', trim($row['ds_implementado']).' - '.trim($row['ds_usuario_implementado']));
				}

				if(trim($row['dt_verificacao_eficacia']) != '')
                {
                	echo form_default_row('', 'Verificação Eficácia: ', trim($row['dt_verificacao_eficacia']).' - '.trim($row['ds_usuario_verificacao_eficacia']));
                	echo form_default_row('', 'Dt. Verificação a Eficácia: ', $row['dt_verificado_eficacia']);
                }
		

				if(trim($row['dt_encerrada']) != '')
				{
					echo form_default_row('dt_encerrada', 'Dt. Encerramento:', $row['dt_encerrada']);
					echo form_default_row('ds_usuario_encerramento', 'Usuário:', $row['ds_usuario_encerramento']);
					
					if(trim($row['arquivo']) != '')
					{
						echo form_default_row('arquivo', 'Plano de Ação:', anchor(base_url().'up/plano_acao/'.$row['arquivo'], $row['arquivo_nome'], array('target' => '_blank')));
					}
				}
				else
				{
					echo form_default_upload_iframe('arquivo', 'pendencia_gestao', 'Plano de Ação:', $row['arquivo'], 'pendencia_gestao', true);
				}
				
			echo form_end_box('default_box');
			echo form_command_bar_detail_start();

		        if(((intval($row['cd_pendencia_gestao']) == 0) OR (trim($fl_permissao) == 'S')) AND (trim($row['dt_encerrada']) == ''))
		        {
		            echo button_save('Salvar');         	            
		        }

		        if((intval($row['cd_pendencia_gestao']) > 0) AND ($fl_permissao_encerrar == 'S')) 
	            { 
	            	echo button_save('Encerrar', 'encerrar()', 'botao_vermelho');
	            }			        

	        echo form_command_bar_detail_end();
		echo form_close();
		if(count($historico) > 0)
	    {
	    	echo $grid->render();
	    }
 		echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?>