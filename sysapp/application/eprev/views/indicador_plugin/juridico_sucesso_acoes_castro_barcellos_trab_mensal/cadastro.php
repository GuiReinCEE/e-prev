<?php
	set_title($tabela[0]['ds_indicador']);
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array(
		'mes_referencia', 
        'ano_referencia', 
                
		'nr_inicial', 

		'nr_improcede_1', 
		'nr_parcial_1', 
        'nr_procede_1',

		'nr_improcede_2', 
		'nr_parcial_2', 
        'nr_procede_2',

		'nr_improcede_3', 
		'nr_parcial_3', 
		'nr_procede_3'
		),'_salvar(form)')	?> 

    function ir_lista()
    {
        location.href = '<?= site_url("indicador/manutencao") ?>';
    }

    function ir_lancamento()
    {
        location.href = '<?= site_url("indicador_plugin/juridico_sucesso_acoes_castro_barcellos_trab_mensal/index") ?>';
    }

    function _salvar(form)
	{
		$("#dt_referencia").val("01/"+$("#mes_referencia").val()+"/"+$("#ano_referencia").val());
		$("#dt_referencia_db").val($("#ano_referencia").val()+"-"+$("#mes_referencia").val()+"-01");

		if(confirm("Salvar?"))
		{
            form.submit();
		}
	}
    function excluir()
    {
        location.href = '<?= site_url("indicador_plugin/juridico_sucesso_acoes_castro_barcellos_trab_mensal/excluir/".$row['cd_juridico_sucesso_acoes_castro_barcellos_trab_mensal']) ?>';
    }
</script>
<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista()');
    $abas[] = array('aba_lancamento', 'Lançamento', FALSE, 'ir_lancamento()');
    $abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload()');

    echo aba_start($abas);
?>
    <? if(count($tabela) == 0): ?>

    <div style="width:100%; text-align:center;">
        <span style="font-size: 12pt; color:red; font-weight:bold;">
            Nenhum período aberto para criar a tabela do indicador.
        </span>
    </div>

    <? elseif(count($tabela) > 1): ?>

    <div style="width:100%; text-align:center;">
        <span style="font-size: 12pt; color:red; font-weight:bold;">
            Existe mais de um período aberto, no entanto só será possível incluir valores para o novo período depois de fechar o mais antigo.
        </span>
    </div>

<?php
    else: 
        echo form_open('indicador_plugin/juridico_sucesso_acoes_castro_barcellos_trab_mensal/salvar');
        echo form_start_box('cadastro_box', 'Cadastro');
            echo form_default_hidden('cd_juridico_sucesso_acoes_castro_barcellos_trab_mensal', '', $row);
            echo form_default_hidden('cd_indicador_tabela', '', $tabela[0]['cd_indicador_tabela']);
            echo form_default_hidden('dt_referencia', '', $row);
            echo form_default_hidden('dt_referencia_db', '', '');
            echo form_default_row('', 'Indicador:', '<span class="label label-inverse">'.$tabela[0]['ds_indicador'].'</span>');
            echo form_default_row('', 'Período aberto:', '<span class="label label-important">'.$tabela[0]['ds_periodo'].'</span>');
            echo form_default_mes_ano('mes_referencia', 'ano_referencia', $label_0.': (*)', $row['dt_referencia']);
            echo form_default_textarea('ds_observacao', $label_10.':', $row);
            echo form_default_numeric('nr_improc_min', $label_11.': (*)', number_format($row['nr_improc_min'], 2, '.', ','), 'class="indicador_text"');
            echo form_default_numeric('nr_improc_max', $label_12.': (*)', number_format($row['nr_improc_max'], 2, '.', ','), 'class="indicador_text"');
            echo form_default_numeric('nr_parcial_min', $label_13.': (*)', number_format($row['nr_parcial_min'], 2, '.', ','), 'class="indicador_text"');
            echo form_default_numeric('nr_parcial_max', $label_14.': (*)', number_format($row['nr_parcial_max'], 2, '.', ','), 'class="indicador_text"');
            echo form_default_numeric('nr_proc_min', $label_15.': (*)', number_format($row['nr_proc_min'], 2, '.', ','), 'class="indicador_text"');
            echo form_default_numeric('nr_proc_max', $label_16.': (*)', number_format($row['nr_proc_max'], 2, '.', ','), 'class="indicador_text"');
        echo form_end_box('cadastro_box');
        echo form_start_box('fase_inicial_box', 'Fase Inicial');
            echo form_default_integer('nr_inicial', $label_1.': (*)', $row, 'class="indicador_text"'); 	
        echo form_end_box('fase_inicial_box');
        echo form_start_box('primeira_instancia_box', '1° Instância');
            echo form_default_integer('nr_improcede_1', $label_2.': (*)', $row, 'class="indicador_text"');
            echo form_default_integer('nr_parcial_1', $label_4.': (*)', $row, 'class="indicador_text"');
            echo form_default_integer('nr_procede_1', $label_6.': (*)', $row, 'class="indicador_text"');
        echo form_end_box('primeira_instancia_box');
        echo form_start_box('segunda_instancia_box', '2° Instância');
            echo form_default_integer('nr_improcede_2', $label_2.': (*)', $row, 'class="indicador_text"');
            echo form_default_integer('nr_parcial_2', $label_4.': (*)', $row, 'class="indicador_text"');
            echo form_default_integer('nr_procede_2', $label_6.': (*)', $row, 'class="indicador_text"');
        echo form_end_box('segunda_instancia_box');
        echo form_start_box('terceira_instancia_box', '3° Instância');
            echo form_default_integer('nr_improcede_3', $label_2.': (*)', $row, 'class="indicador_text"');
            echo form_default_integer('nr_parcial_3', $label_4.': (*)', $row, 'class="indicador_text"');
            echo form_default_integer('nr_procede_3', $label_6.': (*)', $row, 'class="indicador_text"');
        echo form_end_box('terceira_instancia_box');
        echo form_command_bar_detail_start();
        echo button_save();
        if(intval($row['cd_juridico_sucesso_acoes_castro_barcellos_trab_mensal']) > 0) : 
            echo button_save('Excluir', 'excluir();', 'botao_vermelho');
        endif; 
            echo form_command_bar_detail_end();
        echo form_close();
   endif; 
        echo br(2);
        echo aba_end();

   $this->load->view('footer_interna')
?>