<?php 
	set_title($tabela[0]['ds_indicador']);
	$this->load->view('header'); 
?>
<script>
	<?= form_default_js_submit(array(
			'nr_etapas_previstas', 
			'nr_etapas_cumpridas', 
			'nr_percentual_cumpridas', 
            'nr_meta',
            'cd_indicador_tabela', 
		),'_salvar(form)')	
	?>

	function _salvar(form)
	{
		$("#dt_referencia").val("01/"+$("#mes_referencia").val()+"/"+$("#ano_referencia").val());

		if(confirm("Salvar?"))
		{
			form.submit();
		}
	}

	function ir_lista()
	{
		location.href = "<?= site_url('indicador_plugin/controladoria_cumprimento_projeto_modernidade') ?>";
	}
	
    function manutencao()
    {
        location.href = "<?= site_url('indicador/manutencao') ?>";
    }
	
	function excluir()
	{
		location.href = "<?= site_url('indicador_plugin/controladoria_cumprimento_projeto_modernidade/excluir/'.$row['cd_controladoria_cumprimento_projeto_modernidade']) ?>";
	}
		
	$(function() {
		$("#mes_referencia").focus();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', false, 'manutencao();');
	$abas[] = array('aba_lista', 'Lançamento', false, 'ir_lista();');
    $abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');
    
    echo aba_start($abas)
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
        echo form_open('indicador_plugin/controladoria_cumprimento_projeto_modernidade/salvar');
            echo form_start_box("default_box", 'Cadastro');
                echo form_default_hidden('cd_indicador_tabela', '', $tabela[0]['cd_indicador_tabela']);
                echo form_default_hidden('cd_controladoria_cumprimento_projeto_modernidade', '', $row);
                echo form_default_hidden('dt_referencia', $label_0.': (*)', $row); 
                echo form_default_row('', 'Indicador:', '<span class="label label-inverse">'.$tabela[0]['ds_indicador'].'</span>');
                echo form_default_row('', 'Período aberto:', '<span class="label label-important">'.$tabela[0]['ds_periodo'].'</span>'); 
                echo form_default_integer('ano_referencia', 'Ano: (*)', $row['ano_referencia'], 'class="indicador_text"') ;
                echo form_default_dropdown('mes_referencia', 'Trimestre: (*)', $drop, $row['mes_referencia'], 'class="indicador_text"') ;
                echo form_default_integer('nr_etapas_previstas', $label_1.': (*)', $row['nr_etapas_previstas'], 'class="indicador_text"'); 
                echo form_default_integer('nr_etapas_cumpridas', $label_2.': (*)', $row['nr_etapas_cumpridas'], 'class="indicador_text"');
                echo form_default_numeric('nr_meta', $label_4.': (*)', number_format($row['nr_meta'], 2, ',', '.'), 'class="indicador_text"');
                echo form_default_textarea('ds_observacao', $label_5.':', $row['ds_observacao']);
            echo form_end_box("default_box");
            echo form_command_bar_detail_start();
                echo button_save();
                if(intval($row['cd_controladoria_cumprimento_projeto_modernidade']) > 0)
                {
                    echo button_save('Excluir', 'excluir();', 'botao_vermelho');
                } 
            echo form_command_bar_detail_end();
        echo form_close();
    endif; 
    echo br(2);
    echo aba_end();
    $this->load->view('footer_interna');
?>
