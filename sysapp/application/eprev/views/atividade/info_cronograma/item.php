<?php
set_title('Cronograma - Analistas - Cadastro - Item');
$this->load->view('header');
?>
<script>
<?php
echo form_default_js_submit(Array('descricao', 'fl_concluido'));
?>
    function ir_lista()
    {
        location.href='<?php echo site_url("atividade/info_cronograma"); ?>';
    }
    
    function ir_cadastro()
    {
        location.href='<?php echo site_url("atividade/info_cronograma/cadastro/".$cd_cronograma); ?>';
    }
    
    function excluir_item(cd_cronograma_item)
    {
        if(confirm("ATENÇÃO\n\nDeseja excluir?\n\n"))
        {
            location.href='<?php echo site_url("atividade/info_cronograma/excluir_item/".$cd_cronograma); ?>' + "/" + cd_cronograma_item;
        }
    }
    
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_nc', 'Cadastro', FALSE, 'ir_cadastro();');
$abas[] = array('aba_nc', 'Item', TRUE, 'location.reload();');

$concluido[] = array('text'=> 'Sim', 'value' => 'S');
$concluido[] = array('text'=> 'Não', 'value' => 'N');

echo aba_start($abas);
    echo form_open('atividade/info_cronograma/salvar_item', 'name="filter_bar_form"');
		if(intval($row['cd_cronograma_item']) > 0)
		{
			echo form_start_box("default_box", "Cronograma");
				echo form_default_text('cd_analista', "Analista:", $analista['analista'], "style='width:500px; border: 0px;' readonly");
				echo form_default_text('mes_ano', "Mês/Ano:", $analista['mes_ano'], "style='width:500px; border: 0px;' readonly");
			echo form_end_box("default_box");
		}
        
        echo form_start_box("default_box", "Cadastro");
            echo form_default_hidden('cd_cronograma_item', "", $row, "style='width:100%;border: 0px;' readonly");
            echo form_default_hidden('cd_cronograma', "", $cd_cronograma, "style='width:100%;border: 0px;' readonly");
            echo form_default_integer('nr_prioridade', 'Prioridade:', $row);
            echo form_default_textarea('descricao', 'Descrição:*', $row);
            echo form_default_dropdown('fl_concluido', 'Concluído:*', $concluido, array($row['fl_concluido']));
            echo form_default_textarea('observacao', 'Observação:', $row);
        echo form_end_box("default_box");
        echo form_command_bar_detail_start();
            echo button_save("Salvar");
            if(intval($row['cd_cronograma_item']) > 0)
            {
            echo button_save("Excluir", "excluir_item(" . $row['cd_cronograma_item'] . ")", "botao_vermelho");
            }
        echo form_command_bar_detail_end();
    
    echo form_close();
    echo "<BR><BR><BR>";

echo aba_end();

$this->load->view('footer_interna');
?>