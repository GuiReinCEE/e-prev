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
        location.href = '<?= site_url("indicador_plugin/administrativo_correspondencias_devolvidas/index") ?>';
    }

    function _salvar(form)
	{
		$("#dt_referencia").val("01/"+$("#mes_referencia").val()+"/"+$("#ano_referencia").val());

		if(confirm("Salvar?"))
		{
            form.submit();
		}
	}
    function excluir()
    {
        location.href = '<?= site_url("indicador_plugin/administrativo_correspondencias_devolvidas/excluir/".$row['cd_administrativo_correspondencias_devolvidas']) ?>';
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
	?>
			<? if(intval($row['qt_ano']) == 7): ?>

				<div style="width:100%; text-align:center;">
					<span style="font-size: 12pt; color:red; font-weight:bold;">
						Informar no campo de 'observações' se pretende manter ou fazer ajustes na meta do indicador, e justificar essa decisão.
					</span>
				</div>

			<? endif; ?>
	<?
	
        echo form_open('indicador_plugin/administrativo_correspondencias_devolvidas/salvar');
        echo form_start_box('cadastro_box', 'Cadastro');
            echo form_default_hidden('cd_administrativo_correspondencias_devolvidas', '', $row);
            echo form_default_hidden('cd_indicador_tabela', '', $tabela[0]['cd_indicador_tabela']);
            echo form_default_hidden('dt_referencia', '', $row);
            echo form_default_row('', 'Indicador:', '<span class="label label-inverse">'.$tabela[0]['ds_indicador'].'</span>');
            echo form_default_row('', 'Período aberto:', '<span class="label label-important">'.$tabela[0]['ds_periodo'].'</span>');
            echo form_default_mes_ano('mes_referencia', 'ano_referencia', $label_0.': (*)', $row['dt_referencia']);
            echo form_default_integer('nr_expedidas', $label_1.': (*)', $row['nr_expedidas'], 'class="indicador_text"');
            echo form_default_integer('nr_devolvidas', $label_2.': (*)', $row['nr_devolvidas'], 'class="indicador_text"');
            echo form_default_numeric('nr_meta', $label_3.': (*)', number_format($row['nr_meta'], 2, ',', '.'), 'class="indicador_text"');
            echo form_default_textarea('ds_observacao', $label_5.':', $row);
        echo form_end_box('cadastro_box');
        echo form_command_bar_detail_start();
        echo button_save();
        if(intval($row['cd_administrativo_correspondencias_devolvidas']) > 0) : 
            echo button_save('Excluir', 'excluir();', 'botao_vermelho');
        endif; 
            echo form_command_bar_detail_end();
        echo form_close();
   endif; 
        echo br(2);
        echo aba_end();

   $this->load->view('footer_interna')
?>