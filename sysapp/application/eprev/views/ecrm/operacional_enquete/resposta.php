<?php
set_title('Pesquisa - Resposta');
$this->load->view('header');
?>
<script>
    <?php 
			echo form_default_js_submit(array(
												'ds_resposta', 
												'nr_ordem',
												'vl_valor'
											  )); 
	?>
    
    function ir_lista()
    {
        location.href='<?= site_url("ecrm/operacional_enquete") ?>';
    }

    function ir_cadastro()
    {
        location.href='<?= site_url("ecrm/operacional_enquete/cadastro/".intval($ar_cadastro['cd_enquete'])) ?>';
    }	
	
    function ir_estrutura()
    {
        location.href='<?= site_url("ecrm/operacional_enquete/estrutura/".intval($ar_cadastro['cd_enquete'])) ?>';
    }
	
    function ir_resultado()
    {
        location.href='<?= site_url("ecrm/operacional_enquete/resultado/".intval($ar_cadastro['cd_enquete'])) ?>';
    }

    function respostaExcluir()
    {
        var confirmacao = 'Confirma a EXCLUSÃO?\n\n'+
                'Clique [Ok] para Sim\n\n'+
                'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        {
           location.href='<?= site_url("ecrm/operacional_enquete/agrupamentoExcluir/".intval($ar_cadastro['cd_enquete'])."/".intval($row['cd_resposta'])) ?>';
        }
    }
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
$abas[] = array('aba_estrutura', 'Estrutura', FALSE, 'ir_estrutura();');
$abas[] = array('aba_resposta', 'Resposta', TRUE, 'location.reload();');
$abas[] = array('aba_resultado', 'Resultados', FALSE, 'ir_resultado();');

echo aba_start( $abas );
    echo form_open('ecrm/operacional_enquete/respostaSalvar');
        echo form_start_box("default_box", "Cadastro");
			echo form_default_hidden('cd_enquete', '', $ar_cadastro['cd_enquete']);
			echo form_default_hidden('cd_resposta', '', $row['cd_resposta']);
			echo form_default_row('cd_enquete_label', "Pesquisa:", '<span class="label label-success">'.$ar_cadastro["cd_enquete"].' - '.$ar_cadastro["ds_titulo"].'</span>');
			
			echo form_default_text('ds_resposta', 'Resposta:(*)', $row, 'style="width: 500px;"');
			echo form_default_integer('nr_ordem', 'Ordem:(*)', $row);
			echo form_default_numeric('vl_valor', 'Valor:(*)', $row);


        echo form_end_box("default_box");
		
        echo form_command_bar_detail_start();    
			if(trim($ar_cadastro['fl_editar']) == "S")
			{
				echo button_save("Salvar");

				if(intval($row['cd_resposta']) > 0)
				{
					#echo button_save("Excluir", "respostaExcluir()", "botao_vermelho");
				}
            }
        echo form_command_bar_detail_end();
		
    echo form_close();
    echo br(5);	
echo aba_end();

$this->load->view('footer_interna');
?>