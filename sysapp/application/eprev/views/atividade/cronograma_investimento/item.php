<?php
set_title('Cronograma - GIN - Cadastro - Item');
$this->load->view('header');
?>
<script>
<?php
echo form_default_js_submit(Array('descricao', 'fl_concluido'));
?>
    function ir_lista()
    {
        location.href='<?php echo site_url("atividade/cronograma_investimento"); ?>';
    }
    
    function ir_cadastro()
    {
        location.href='<?php echo site_url("atividade/cronograma_investimento/cadastro/".$cd_cronograma_investimento); ?>';
    }
    
    function excluir_item(cd_cronograma_investimento_item)
    {
        if(confirm("ATENÇÃO\n\nDeseja excluir?\n\n"))
        {
            location.href='<?php echo site_url("atividade/cronograma_investimento/excluir_item/".$cd_cronograma_investimento); ?>' + "/" + cd_cronograma_investimento_item;
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
	echo form_open('atividade/cronograma_investimento/troca_analista', 'name="filter_bar_form"');
		if(intval($row['cd_cronograma_investimento_item']) > 0)
		{
			echo form_start_box("default_box", "Cronograma");
				echo form_default_hidden('cd_cronograma_investimento', "", $analista);				
				echo form_default_hidden('cd_cronograma_investimento_item', "", $row);	
				echo form_default_dropdown('cd_analista', 'Analista:*', $analistas, array($analista['cd_analista']));
				echo form_default_text('mes_ano', "Mês/Ano:", $analista['mes_ano'], "style='width:500px; border: 0px;' readonly");
			echo form_end_box("default_box");
		}
		echo form_command_bar_detail_start();
			echo button_save("Salvar");
		echo form_command_bar_detail_end();
	echo form_close();	
    echo form_open('atividade/cronograma_investimento/salvar_item', 'name="filter_bar_form"');    
        echo form_start_box("default_box", "Cadastro");
            echo form_default_hidden('cd_cronograma_investimento_item', "", $row, "style='width:100%;border: 0px;' readonly");
            echo form_default_hidden('cd_cronograma_investimento', "", $cd_cronograma_investimento, "style='width:100%;border: 0px;' readonly");
            echo form_default_integer('nr_prioridade', 'Prioridade:', $row);
            echo form_default_textarea('descricao', 'Descrição:*', $row);
            echo form_default_dropdown('fl_concluido', 'Concluído:*', $concluido, array($row['fl_concluido']));
			echo form_default_date('dt_limite', 'Dt Limite:', $row);
            echo form_default_textarea('observacao', 'Observação:', $row);
        echo form_end_box("default_box");
        echo form_command_bar_detail_start();
            echo button_save("Salvar");
            if(intval($row['cd_cronograma_investimento_item']) > 0)
            {
				echo button_save("Excluir", "excluir_item(" . $row['cd_cronograma_investimento_item'] . ")", "botao_vermelho");
            }
        echo form_command_bar_detail_end();
    
    echo form_close();
    echo br();

echo aba_end();

$this->load->view('footer_interna');
?>