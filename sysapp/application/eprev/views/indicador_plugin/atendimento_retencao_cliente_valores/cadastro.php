<?php
	set_title($tabela[0]['ds_indicador']);
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array(
    		'mes_referencia', 
            'ano_referencia',
            'nr_interesse',
            'nr_efetivas',
            'nr_nao_retido'
	),'_salvar(form)')	?> 

    function ir_lista()
    {
        location.href = '<?= site_url("indicador/manutencao") ?>';
    }

    function ir_lancamento()
    {
        location.href = '<?= site_url("indicador_plugin/atendimento_retencao_cliente_valores/index") ?>';
    }

        function excluir()
    {
        location.href = '<?= site_url("indicador_plugin/atendimento_retencao_cliente_valores/excluir/".$row['cd_atendimento_retencao_cliente_valores']) ?>';
    }

    function _salvar(form)
	{
		$("#dt_referencia").val("01/"+$("#mes_referencia").val()+"/"+$("#ano_referencia").val());

		if(confirm("Salvar?"))
		{
            form.submit();
		}
	}

    function getValores()
	{
		if(($("#mes_referencia").val() != "") && ($("#ano_referencia").val() != ""))
		{
			$("#msg_importar").show();	
			
			$("#command_bar").hide();
			
			$.post("<?= site_url('indicador_plugin/atendimento_retencao_cliente_valores/get_valores') ?>", 
			{
				nr_ano : $("#ano_referencia").val(),
				nr_mes : $("#mes_referencia").val()
			},
			function(data)
			{
                if(data)
                {
                    if(data.error.status == 0)
                    {
                        var result = data.result[0];

                        $("#msg_importar").hide();  
            
                        $("#command_bar").show();

                        $("#nr_interesse").val(result.total_retencoes);                    
                        $("#nr_cliente").val(result.total_clientes_vlr);                    
                        $("#nr_efetivas").val(result.total_retidos_vlr);                    
                        $("#nr_nao_retido").val(result.total_nao_retidos_vlr);
                        $("#nr_negociacao").val(result.total_em_negociacao_vlr);
                    }
                    else
                    {
                        $("#msg_importar").html(data.error.mensagem);  
                    }
                }
                else
                {
                    $("#msg_importar").html("Erro de comunica��o com o Oracle");  
                }			
				
			},
			'json');
		}
		else
		{
			alert("Informe o M�s e Ano");
		}
	}
</script>
<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista()');
    $abas[] = array('aba_lancamento', 'Lan�amento', FALSE, 'ir_lancamento()');
    $abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload()');

    echo aba_start($abas);
?>
    <? if(count($tabela) == 0): ?>

    <div style="width:100%; text-align:center;">
        <span style="font-size: 12pt; color:red; font-weight:bold;">
            Nenhum per�odo aberto para criar a tabela do indicador.
        </span>
    </div>

    <? elseif(count($tabela) > 1): ?>

    <div style="width:100%; text-align:center;">
        <span style="font-size: 12pt; color:red; font-weight:bold;">
            Existe mais de um per�odo aberto, no entanto s� ser� poss�vel incluir valores para o novo per�odo depois de fechar o mais antigo.
        </span>
    </div>

<?php
    else: 
        echo form_open('indicador_plugin/atendimento_retencao_cliente_valores/salvar');
        echo form_start_box('cadastro_box', 'Cadastro');
            echo form_default_hidden('cd_atendimento_retencao_cliente_valores', '', $row);
            echo form_default_hidden('cd_indicador_tabela', '', $tabela[0]['cd_indicador_tabela']);
            echo form_default_hidden('dt_referencia', '', $row);
            echo form_default_row('', 'Indicador:', '<span class="label label-inverse">'.$tabela[0]['ds_indicador'].'</span>');
            echo form_default_row('', 'Per�odo aberto:', '<span class="label label-important">'.$tabela[0]['ds_periodo'].'</span>');
            echo form_default_mes_ano('mes_referencia', 'ano_referencia', $label_0.': (*)', $row['dt_referencia']);
            //echo form_default_integer('nr_interesse', $label_1.': (*)', intval($row['nr_interesse']), 'class="indicador_text"'); 	
            echo form_default_integer('nr_interesse', $label_1.': (*)', intval($row['nr_interesse']), 'class="indicador_text"');    
            echo form_default_numeric('nr_cliente', $label_8.': (*)', number_format($row['nr_cliente'], 2, '.', ','), 'class="indicador_text"'); 	
            echo form_default_numeric('nr_efetivas', $label_2.': (*)', number_format($row['nr_efetivas'], 2, '.', ','), 'class="indicador_text"'); 	
            echo form_default_numeric('nr_nao_retido', $label_5.': (*)', number_format($row['nr_nao_retido'], 2, '.', ','), 'class="indicador_text"'); 	
            echo form_default_numeric('nr_negociacao', $label_4.': (*)', number_format($row['nr_negociacao'], 2, '.', ','), 'class="indicador_text"'); 	
            echo form_default_textarea('ds_observacao', $label_7.':', $row);
        echo form_end_box('cadastro_box');
        echo form_default_row("", "",'<span style="font-size: 130%; font-weight: bold; color:red; display:none;" id="msg_importar">Aguarde, importando os dados.</span>');
        echo form_command_bar_detail_start();
        echo button_save();
        echo button_save('Importar Valores', 'getValores();', 'botao_verde');
        if(intval($row['cd_atendimento_retencao_cliente_valores']) > 0) : 
            echo button_save('Excluir', 'excluir();', 'botao_vermelho');
        endif; 
            echo form_command_bar_detail_end();
        echo form_close();
   endif; 
        echo br(2);
        echo aba_end();

   $this->load->view('footer_interna')
?>
