<?php
set_title('Pesquisa - Agrupamento');
$this->load->view('header');
?>
<script>
    <?php 
			echo form_default_js_submit(array(
												'ds_agrupamento', 
												'nr_ordem',
												'indic_escala',
												'mostrar_valores',
												'disposicao'
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

    function agrupamentoExcluir()
    {
        var confirmacao = 'Confirma a EXCLUSÃO?\n\n'+
                'Clique [Ok] para Sim\n\n'+
                'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        {
           location.href='<?= site_url("ecrm/operacional_enquete/agrupamentoExcluir/".intval($ar_cadastro['cd_enquete'])."/".intval($row['cd_agrupamento'])) ?>';
        }
    }
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
$abas[] = array('aba_estrutura', 'Estrutura', FALSE, 'ir_estrutura();');
$abas[] = array('aba_agrupamento', 'Agrupamento', TRUE, 'location.reload();');
$abas[] = array('aba_resultado', 'Resultados', FALSE, 'ir_resultado();');

echo aba_start( $abas );
    echo form_open('ecrm/operacional_enquete/agrupamentoSalvar');
        echo form_start_box("default_box", "Cadastro");
			echo form_default_hidden('cd_enquete', '', $ar_cadastro['cd_enquete']);
			echo form_default_hidden('cd_agrupamento', '', $row['cd_agrupamento']);
			echo form_default_row('cd_enquete_label', "Pesquisa:", '<span class="label label-success">'.$ar_cadastro["cd_enquete"].' - '.$ar_cadastro["ds_titulo"].'</span>');
			
			echo form_default_text('ds_agrupamento', 'Agrupamento:(*)', $row, 'style="width: 500px;"');
			echo form_default_integer('nr_ordem', 'Ordem:(*)', $row);

			echo form_default_dropdown('indic_escala', 'Considerar escala de valores:(*)', Array(Array("value" => "S", "text" => "Sim"),Array("value" => "N", "text" => "Não")), Array($row['indic_escala']));
			echo form_default_dropdown('mostrar_valores', 'Mostrar valores junto à opção:(*)', Array(Array("value" => "S", "text" => "Sim"),Array("value" => "N", "text" => "Não")), Array($row['mostrar_valores']));
			echo form_default_dropdown('disposicao', 'Disposição das questões:(*)', Array(Array("value" => "V", "text" => "Vertical"),Array("value" => "H", "text" => "Horizontal")), Array($row['disposicao']));

			echo form_default_textarea('nota_rodape', "Nota de rodapé:", $row, "style='width:500px; height: 100px;'");
        echo form_end_box("default_box");
		
        echo form_command_bar_detail_start();    
			if(trim($ar_cadastro['fl_editar']) == "S")
			{
				echo button_save("Salvar");

				if(intval($row['cd_agrupamento']) > 0)
				{
					echo button_save("Excluir", "agrupamentoExcluir()", "botao_vermelho");
				}
            }
        echo form_command_bar_detail_end();
		
    echo form_close();
    echo br(5);	
echo aba_end();

$this->load->view('footer_interna');
?>