<?php
	set_title($tabela[0]['ds_indicador']);
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array(
		'mes_referencia', 
        'ano_referencia', 
        'nr_resultado',
        'nr_meta'
		),'_salvar(form)')	?> 
    
    function ir_lista()
    {
        location.href = '<?= site_url("indicador/manutencao") ?>';
    }

    function ir_lancamento()
    {
        location.href = '<?= site_url("indicador_plugin/financeiro_divulgacoes_extratos_planos_prazo/index") ?>';
    }

    function excluir()
    {
        location.href = '<?= site_url("indicador_plugin/financeiro_divulgacoes_extratos_planos_prazo/excluir/".$row['cd_financeiro_divulgacoes_extratos_planos_prazo']) ?>';
    }

        function _salvar(form)
	{
		$("#dt_referencia").val("01/"+$("#mes_referencia").val()+"/"+$("#ano_referencia").val());

		if(confirm("Salvar?"))
		{
            form.submit();
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
        echo form_open('indicador_plugin/financeiro_divulgacoes_extratos_planos_prazo/salvar');
        echo form_start_box('cadastro_box', 'Cadastro');
            echo form_default_hidden('cd_financeiro_divulgacoes_extratos_planos_prazo', '', $row);
            echo form_default_hidden('cd_indicador_tabela', '', $tabela[0]['cd_indicador_tabela']);
            echo form_default_hidden('dt_referencia', '', $row);
            echo form_default_row('', 'Indicador:', '<span class="label label-inverse">'.$tabela[0]['ds_indicador'].'</span>');
            echo form_default_row('', 'Per�odo aberto:', '<span class="label label-important">'.$tabela[0]['ds_periodo'].'</span>');
            echo form_default_mes_ano('mes_referencia', 'ano_referencia', $label_0.': (*)', $row['dt_referencia']);
            echo form_default_integer('nr_resultado', $label_1.': (*)', $row, 'class="indicador_text"');
            echo form_default_integer('nr_meta', $label_2.': (*)', $row, 'class="indicador_text"');
            echo form_default_textarea('ds_observacao', $label_3.':', $row, 'class="indicador_text"');
        echo form_end_box('cadastro_box');
        echo form_command_bar_detail_start();
        echo button_save();
        if(intval($row['cd_financeiro_divulgacoes_extratos_planos_prazo']) > 0) : 
            echo button_save('Excluir', 'excluir();', 'botao_vermelho');
        endif; 
            echo form_command_bar_detail_end();
        echo form_close();
    endif; 
        echo br(2);
        echo aba_end();

   $this->load->view('footer_interna')
?>