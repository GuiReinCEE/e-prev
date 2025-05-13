<?php
	set_title($tabela[0]['ds_indicador']);
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array(
    		'mes_referencia', 
            'ano_referencia'
	),'_salvar(form)')	?> 

    function ir_lista()
    {
        location.href = '<?= site_url("indicador/manutencao") ?>';
    }

    function ir_lancamento()
    {
        location.href = '<?= site_url("indicador_plugin/cadastro_atividades/index") ?>';
    }

        function excluir()
    {
        location.href = '<?= site_url("indicador_plugin/cadastro_atividades/excluir/".$row['cd_cadastro_atividades']) ?>';
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
        echo form_open('indicador_plugin/cadastro_atividades/salvar');
        echo form_start_box('cadastro_box', 'Cadastro');
            echo form_default_hidden('cd_cadastro_atividades', '', $row);
            echo form_default_hidden('cd_indicador_tabela', '', $tabela[0]['cd_indicador_tabela']);
            echo form_default_hidden('dt_referencia', '', $row);
            echo form_default_row('', 'Indicador:', '<span class="label label-inverse">'.$tabela[0]['ds_indicador'].'</span>');
            echo form_default_row('', 'Per�odo aberto:', '<span class="label label-important">'.$tabela[0]['ds_periodo'].'</span>');
            echo form_default_mes_ano('mes_referencia', 'ano_referencia', $label_0.': (*)', $row['dt_referencia']);
            //echo form_default_integer('nr_interesse', $label_1.': (*)', intval($row['nr_interesse']), 'class="indicador_text"'); 	

            echo form_default_integer('nr_atividade_aberta', $label_1.': (*)', intval($row['nr_atividade_aberta']), 'class="indicador_text"');    
            echo form_default_integer('nr_atividade_andamento', $label_2.': (*)', intval($row['nr_atividade_andamento']), 'class="indicador_text"'); 	
            echo form_default_integer('nr_atividade_concluida', $label_3.': (*)', intval($row['nr_atividade_concluida']), 'class="indicador_text"'); 	
            echo form_default_integer('nr_atividade_cancelada', $label_4.': (*)', intval($row['nr_atividade_cancelada']), 'class="indicador_text"'); 	
            echo form_default_integer('nr_atividade_acumulada', $label_5.': (*)', intval($row['nr_atividade_acumulada']), 'class="indicador_text"'); 


            echo form_default_integer('nr_tempo_min', $label_8.': (*)', intval($row['nr_tempo_min']), 'class="indicador_text"');    
            echo form_default_integer('nr_tempo_hora', $label_9.': (*)', intval($row['nr_tempo_hora']), 'class="indicador_text"');   

            echo form_default_numeric('nr_meta', $label_7.': (*)', number_format($row['nr_meta'], 2, ',', '.'), 'class="indicador_text"'); 	

            echo form_default_textarea('ds_observacao', $label_10.':', $row);

        echo form_end_box('cadastro_box');
        echo form_default_row("", "",'<span style="font-size: 130%; font-weight: bold; color:red; display:none;" id="msg_importar">Aguarde, importando os dados.</span>');
        echo form_command_bar_detail_start();
        echo button_save();
        if(intval($row['cd_cadastro_atividades']) > 0) : 
            echo button_save('Excluir', 'excluir();', 'botao_vermelho');
        endif; 
            echo form_command_bar_detail_end();
        echo form_close();
   endif; 
        echo br(2);
        echo aba_end();

   $this->load->view('footer_interna')
?>
