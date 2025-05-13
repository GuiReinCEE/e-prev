<?php
set_title('Pesquisa - Cadastro');
$this->load->view('header');
?>
<script>
    <?php 
			echo form_default_js_submit(array(
												'ds_titulo', 
												'nr_publico_total',
												'dt_inicio',
												'hr_inicio',
												'dt_final',
												'hr_final',
												'tp_controle_resposta',
												'cd_divisao_responsavel',
												'cd_responsavel'
											  )); 
	?>
    
    function ir_lista()
    {
        location.href='<?= site_url("ecrm/operacional_enquete") ?>';
    }

    function ir_estrutura()
    {
        location.href='<?= site_url("ecrm/operacional_enquete/estrutura/".intval($row['cd_enquete'])) ?>';
    }

    function ir_resultado()
    {
        location.href='<?= site_url("ecrm/operacional_enquete/resultado/".intval($row['cd_enquete'])) ?>';
    }

    function limparResposta()
    {
        var confirmacao = 'ATENÇÃO\n\nEsta ação é IRREVERSÍVEL.\n\n\nConfirma a EXCLUSÃO das RESPOSTAS?\n\n'+
                'Clique [Ok] para Sim\n\n'+
                'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        {
           location.href='<?= site_url("ecrm/operacional_enquete/limparResposta/".intval($row['cd_enquete'])) ?>';
        }
    }
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

if((trim($row['fl_aba']) == "S") or ($this->session->userdata("codigo") == 170) or ($this->session->userdata("codigo") == 251))
{
   $abas[] = array('aba_estrutura', 'Estrutura', FALSE, 'ir_estrutura();');
   $abas[] = array('aba_resultado', 'Resultados', FALSE, 'ir_resultado();');
}

echo aba_start( $abas );
    echo form_open('ecrm/operacional_enquete/salvar');
        echo form_start_box("default_box", "Cadastro");
			echo form_default_hidden('cd_enquete', '', $row['cd_enquete']);
			echo form_default_row('cd_enquete_label', "Cód:", '<span class="label label-inverse">'.$row["cd_enquete"].'</span>');
			echo form_default_row('ds_url_pesquisa', "URL:", '<span class="label label-success"><a href="'.$row["ds_url_pesquisa"].'" style="color: white; font-weight: bold;" target="_blank">'.$row["ds_url_pesquisa"].'</a></span>');
			echo form_default_text('ds_titulo', 'Pesquisa:(*)', $row, 'style="width: 500px;"');
			echo form_default_integer('nr_publico_total', 'Público total:(*)', $row);
			echo form_default_date('dt_inicio', 'Dt Início:(*)', $row);
			echo form_default_time('hr_inicio', 'Hr Início:(*)', $row);
			echo form_default_date('dt_final', 'Dt Final:(*)', $row);
			echo form_default_time('hr_final', 'Hr Final:(*)', $row);
			echo form_default_dropdown('tp_controle_resposta', 'Controle:(*)', $ar_controle_resposta, Array($row['tp_controle_resposta']));
			echo form_default_dropdown('cd_divisao_responsavel', 'Área Responsável:(*)', $ar_area_responsavel, Array($row['cd_divisao_responsavel']));
			echo form_default_usuario_ajax('cd_responsavel', $row["cd_gerencia"], $row["cd_responsavel"], "Responsável:(*)");
			echo form_default_textarea('texto_abertura', "Texto de Abertura:", $row, "style='width:500px; height: 100px;'");
			echo form_default_textarea('texto_encerramento', "Texto de Encerramento:", $row, "style='width:500px; height: 100px;'");
        echo form_end_box("default_box");
        echo form_command_bar_detail_start();    
			if((trim($row['fl_editar']) == "S") or ($this->session->userdata("codigo") == 251))
			{
				echo button_save("Salvar");

				if(intval($row['cd_enquete']) > 0)
				{
					echo button_save("Limpar todas as respostas", "limparResposta()", "botao_vermelho");
				}
            }
        echo form_command_bar_detail_end();
    echo form_close();
    echo br(5);	
echo aba_end();

$this->load->view('footer_interna');
?>