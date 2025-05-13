<?php
    set_title($tabela[0]['ds_indicador']);
    $this->load->view('header'); 
?>
<script>
	<?php 
    echo form_default_js_submit(array(
            'mes_referencia', 
            'ano_referencia', 
            'cd_indicador_tabela', 
            
            'nr_carga_ceee',
            'nr_inadimplencia_ceee',
            'nr_meta_ceee',
        
            'nr_carga_cgtee',
            'nr_inadimplencia_cgtee',
            'nr_meta_cgtee',
        
            'nr_carga_rge',
            'nr_inadimplencia_rge',
            'nr_meta_rge',
        
            'nr_carga_rgesul',
            'nr_inadimplencia_rgesul',
            'nr_meta_rgesul',
        
            'nr_carga_ceeemigrado',
            'nr_inadimplencia_ceeemigrado',
            'nr_meta_ceeemigrado',
        
            'nr_carga_fundacaomigrado',
            'nr_inadimplencia_fundacaomigrado',
            'nr_meta_fundacaomigrado',
        
            'nr_carga_ceeenovos',
            'nr_inadimplencia_ceeenovos',
            'nr_meta_ceeenovos',
        
            'nr_carga_fundacaonovos',
            'nr_inadimplencia_fundacaonovos',
            'nr_meta_fundacaonovos',
        
            'nr_carga_crm',
            'nr_inadimplencia_crm',
            'nr_meta_crm',
        
            'nr_carga_inpel',
            'nr_inadimplencia_inpel',
            'nr_meta_inpel',
        
            'nr_carga_foz',
            'nr_inadimplencia_foz',
            'nr_meta_foz',
        
            'nr_carga_ceran',
            'nr_inadimplencia_ceran',
            'nr_meta_ceran'  
        ),'_salvar(form)');	?>

	function _salvar(form)
	{
		$('#dt_referencia').val('01/'+$('#mes_referencia').val()+'/'+$('#ano_referencia').val());

		if(confirm('Salvar?'))
		{
			form.submit();
		}
	}

	function ir_lista()
	{
		location.href = '<?= site_url("indicador_plugin/financeiro_inadimplencia_previdenciaria") ?>';
	}
	
    function manutencao()
    {
        location.href = '<?= site_url("indicador/manutencao") ?>';
    }
	
	function excluir()
	{
		location.href = '<?= site_url("indicador_plugin/financeiro_inadimplencia_previdenciaria/excluir/".$row["cd_financeiro_inadimplencia_previdenciaria"]) ?>';
	}
		
	$(function() {
		$("#mes_referencia").focus();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', false, 'manutencao();');
	$abas[] = array('aba_lancamento','Lançamento', false, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', true, 'location.reload();');
    
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
        echo form_open('indicador_plugin/financeiro_inadimplencia_previdenciaria/salvar');
        echo form_start_box('cadastro_box', 'Cadastro');
            echo form_default_hidden('cd_financeiro_inadimplencia_previdenciaria', '', $row);
            echo form_default_hidden('cd_indicador_tabela', '', $tabela[0]['cd_indicador_tabela']);
            echo form_default_hidden('dt_referencia', '');
            echo form_default_row('', 'Indicador:', '<span class="label label-inverse">'.$tabela[0]['ds_indicador'].'</span>');
            echo form_default_row('', 'Período aberto:', '<span class="label label-important">'.$tabela[0]['ds_periodo'].'</span>');
            echo form_default_mes_ano('mes_referencia', 'ano_referencia', $label_0.': (*)', $row['dt_referencia']);
            echo form_default_textarea('ds_observacao', $label_5.':', $row);
        echo form_end_box('cadastro_box');

        echo form_start_box('ceee_box', 'CEEE');
            echo form_default_numeric('nr_carga_ceee', $label_1.': (*)', number_format($row['nr_carga_ceee'], 2, ',', '.'), 'class="indicador_text"');
            echo form_default_numeric('nr_inadimplencia_ceee', $label_2.': (*)', number_format($row['nr_inadimplencia_ceee'], 2, ',', '.'), 'class="indicador_text"');
            echo form_default_numeric('nr_meta_ceee', $label_3.': (*)', number_format($row['nr_meta_ceee'], 2, ',', '.'), 'class="indicador_text"');
        echo form_end_box('ceee_box');
        
        echo form_start_box('cgtee_box', 'CGTEE');
            echo form_default_numeric('nr_carga_cgtee', $label_1.': (*)', number_format($row['nr_carga_cgtee'], 2, ',', '.'), 'class="indicador_text"');
            echo form_default_numeric('nr_inadimplencia_cgtee', $label_2.': (*)', number_format($row['nr_inadimplencia_cgtee'], 2, ',', '.'), 'class="indicador_text"');
            echo form_default_numeric('nr_meta_cgtee', $label_3.': (*)', number_format($row['nr_meta_cgtee'], 2, ',', '.'), 'class="indicador_text"');
        echo form_end_box('cgtee_box');
        
        echo form_start_box('rge_box', 'RGE');
            echo form_default_numeric('nr_carga_rge', $label_1.': (*)', number_format($row['nr_carga_rge'], 2, ',', '.'), 'class="indicador_text"');
            echo form_default_numeric('nr_inadimplencia_rge', $label_2.': (*)', number_format($row['nr_inadimplencia_rge'], 2, ',', '.'), 'class="indicador_text"');
            echo form_default_numeric('nr_meta_rge', $label_3.': (*)', number_format($row['nr_meta_rge'], 2, ',', '.'), 'class="indicador_text"');
        echo form_end_box('rge_box');
        
        echo form_start_box('rgesul_box', 'RGE SUL');
            echo form_default_numeric('nr_carga_rgesul', $label_1.': (*)', number_format($row['nr_carga_rgesul'], 2, ',', '.'), 'class="indicador_text"');
            echo form_default_numeric('nr_inadimplencia_rgesul', $label_2.': (*)', number_format($row['nr_inadimplencia_rgesul'], 2, ',', '.'), 'class="indicador_text"');
            echo form_default_numeric('nr_meta_rgesul', $label_3.': (*)', number_format($row['nr_meta_rgesul'], 2, ',', '.'), 'class="indicador_text"');
        echo form_end_box('rgesul_box');
        
        echo form_start_box('ceee_migrado_box', 'CEEE MIGRADO');
            echo form_default_numeric('nr_carga_ceeemigrado', $label_1.': (*)', number_format($row['nr_carga_ceeemigrado'], 2, ',', '.'), 'class="indicador_text"');
            echo form_default_numeric('nr_inadimplencia_ceeemigrado', $label_2.': (*)', number_format($row['nr_inadimplencia_ceeemigrado'], 2, ',', '.'), 'class="indicador_text"');
            echo form_default_numeric('nr_meta_ceeemigrado', $label_3.': (*)', number_format($row['nr_meta_ceeemigrado'], 2, ',', '.'), 'class="indicador_text"');
        echo form_end_box('ceee_migrado_box');
        
        echo form_start_box('fund_migrado_box', 'FUND. MIGRADO');
            echo form_default_numeric('nr_carga_fundacaomigrado', $label_1.': (*)', number_format($row['nr_carga_fundacaomigrado'], 2, ',', '.'), 'class="indicador_text"');
            echo form_default_numeric('nr_inadimplencia_fundacaomigrado', $label_2.': (*)', number_format($row['nr_inadimplencia_fundacaomigrado'], 2, ',', '.'), 'class="indicador_text"');
            echo form_default_numeric('nr_meta_fundacaomigrado', $label_3.': (*)', number_format($row['nr_meta_fundacaomigrado'], 2, ',', '.'), 'class="indicador_text"');
        echo form_end_box('fund_migrado_box');
        
        echo form_start_box('ceee_novos_box', 'CEEE NOVOS');
            echo form_default_numeric('nr_carga_ceeenovos', $label_1.': (*)', number_format($row['nr_carga_ceeenovos'], 2, ',', '.'), 'class="indicador_text"');
            echo form_default_numeric('nr_inadimplencia_ceeenovos', $label_2.': (*)', number_format($row['nr_inadimplencia_ceeenovos'], 2, ',', '.'), 'class="indicador_text"');
            echo form_default_numeric('nr_meta_ceeenovos', $label_3.': (*)', number_format($row['nr_meta_ceeenovos'], 2, ',', '.'), 'class="indicador_text"');
        echo form_end_box('ceee_novos_box');

        echo form_start_box('fund_novos_box', 'FUND. NOVOS');
            echo form_default_numeric('nr_carga_fundacaonovos', $label_1.': (*)', number_format($row['nr_carga_fundacaonovos'], 2, ',', '.'), 'class="indicador_text"');
            echo form_default_numeric('nr_inadimplencia_fundacaonovos', $label_2.': (*)', number_format($row['nr_inadimplencia_fundacaonovos'], 2, ',', '.'), 'class="indicador_text"');
            echo form_default_numeric('nr_meta_fundacaonovos', $label_3.': (*)', number_format($row['nr_meta_fundacaonovos'], 2, ',', '.'), 'class="indicador_text"');
        echo form_end_box('fund_novos_box');
        
        echo form_start_box('crm_box', 'CRM');
            echo form_default_numeric('nr_carga_crm', $label_1.': (*)', number_format($row['nr_carga_crm'], 2, ',', '.'), 'class="indicador_text"');
            echo form_default_numeric('nr_inadimplencia_crm', $label_2.': (*)', number_format($row['nr_inadimplencia_crm'], 2, ',', '.'), 'class="indicador_text"');
            echo form_default_numeric('nr_meta_crm', $label_3.': (*)', number_format($row['nr_meta_crm'], 2, ',', '.'), 'class="indicador_text"');
        echo form_end_box('crm_box');
        
        echo form_start_box('inpel_box', 'INPEL');
            echo form_default_numeric('nr_carga_inpel', $label_1.': (*)', number_format($row['nr_carga_inpel'], 2, ',', '.'), 'class="indicador_text"');
            echo form_default_numeric('nr_inadimplencia_inpel', $label_2.': (*)',number_format( $row['nr_inadimplencia_inpel'], 2, ',', '.'), 'class="indicador_text"');
            echo form_default_numeric('nr_meta_inpel', $label_3.': (*)', number_format($row['nr_meta_inpel'], 2, ',', '.'), 'class="indicador_text"');
        echo form_end_box('inpel_box');
        
        echo form_start_box('foz_box', 'FOZ');
            echo form_default_numeric('nr_carga_foz', $label_1.': (*)', number_format($row['nr_carga_foz'], 2, ',', '.'), 'class="indicador_text"');
            echo form_default_numeric('nr_inadimplencia_foz', $label_2.': (*)', number_format($row['nr_inadimplencia_foz'], 2, ',', '.'), 'class="indicador_text"');
            echo form_default_numeric('nr_meta_foz', $label_3.': (*)', number_format($row['nr_meta_foz'], 2, ',', '.'), 'class="indicador_text"');
        echo form_end_box('foz_box');
        
        echo form_start_box('ceran_box', 'CERAN');
            echo form_default_numeric('nr_carga_ceran', $label_1.': (*)', number_format($row['nr_carga_ceran'], 2, ',', '.'), 'class="indicador_text"');
            echo form_default_numeric('nr_inadimplencia_ceran', $label_2.': (*)', number_format($row['nr_inadimplencia_ceran'], 2, ',', '.'), 'class="indicador_text"');
            echo form_default_numeric('nr_meta_ceran', $label_3.': (*)', number_format($row['nr_meta_ceran'], 2, ',', '.'), 'class="indicador_text"');
        echo form_end_box('ceran_box');
        
        echo form_command_bar_detail_start();
        echo button_save();
        if(intval($row['cd_financeiro_inadimplencia_previdenciaria']) > 0) : 
            echo button_save('Excluir', 'excluir();', 'botao_vermelho');
        endif; 
            echo form_command_bar_detail_end();
        echo form_close();
   endif; 
        echo br(2);
        echo aba_end();

   $this->load->view('footer_interna')
?>