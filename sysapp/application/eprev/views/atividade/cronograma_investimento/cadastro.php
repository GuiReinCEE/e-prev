<?php
set_title('Cronograma - GIN - Cadastro');
$this->load->view('header');
?>
<script>
<?php
echo form_default_js_submit(Array('cd_analista', 'nr_mes', 'nr_ano'));
?>
    function ir_lista()
    {
        location.href='<?php echo site_url("atividade/cronograma_investimento"); ?>';
    }

    function ir_cronograma_item()
    {
        location.href='<?php echo site_url("atividade/cronograma_investimento/item/".$row['cd_cronograma_investimento']); ?>';
    }

    function excluir_item(cd_cronograma_investimento_item)
    {
        if(confirm("ATENÇÃO\n\nDeseja excluir?\n\n"))
        {
            location.href='<?php echo site_url("atividade/cronograma_investimento/excluir_item/".$row['cd_cronograma_investimento']); ?>' + "/" + cd_cronograma_investimento_item;
        }
    }

    function excluir(cd_cronograma_investimento)
    {
        if(confirm("ATENÇÃO\n\nDeseja excluir?\n\n"))
        {
            location.href='<?php echo site_url("atividade/cronograma_investimento/excluir"); ?>' + "/" + cd_cronograma_investimento;
        }
    }
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_nc', 'Cadastro', TRUE, 'location.reload();');

if(intval($row['cd_cronograma_investimento']) > 0)
{
    $body = array();
    $head = array(
      'Prior.',
      'Descrição',
	  'Restam',
	  'Dt_limite',
      'Concluído',
      ''
    );

    foreach($collection as $item)
    {
        $body[] = array(
          $item['nr_prioridade'],
          array(anchor('atividade/cronograma_investimento/item/'.$row['cd_cronograma_investimento'].'/'.$item['cd_cronograma_investimento_item'],$item['descricao']),'text-align:left'),
		  (intval($item['restam']) <= 3 ? '<span class="label label-important">'.$item['restam'].'</span>' : $item['restam']),
		   $item['dt_limite'],
          '<span class="label '.($item['fl_concluido'] == 'S' ? 'label-info">Sim' : 'label-important">Não').'</span>',
          '<a href="javascript:void(0)" onclick="excluir_item('.$item['cd_cronograma_investimento_item'].')">[Excluir]</a>'
        );
    }
    
    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;
}

echo aba_start($abas);
    echo form_open('atividade/cronograma_investimento/salvar', 'name="filter_bar_form"');
        echo form_start_box("default_box", "Cadastro");
            echo form_default_hidden('cd_cronograma_investimento', "", $row, "style='width:100%;border: 0px;' readonly");
            echo form_default_dropdown('cd_analista', 'Analista:*', $analistas, array($row['cd_analista']));
            if(intval($row['cd_cronograma_investimento']) > 0)
            {
                echo form_default_text('mes_ano', "Mês/Ano:", $row['mes_ano'], "style='width:100%;border: 0px;' readonly");
            }
            else
            {
                echo form_default_mes_ano('nr_mes', 'nr_ano', 'Mês/Ano:*', $row['mes_ano']);
            }
            
        echo form_end_box("default_box");
        
        echo form_command_bar_detail_start();
            echo button_save("Salvar");
            if(intval($row['cd_cronograma_investimento']) > 0)
            {
                echo button_save("Adicionar Item", "ir_cronograma_item()", "botao_disabled");
                echo button_save("Excluir", "excluir(" . $row['cd_cronograma_investimento'] . ")", "botao_vermelho");
            }
        echo form_command_bar_detail_end();
        
        if(intval($row['cd_cronograma_investimento']) > 0)
        {
            echo form_start_box("default_box", "Cronograma Item"); 
                echo $grid->render();
            echo form_end_box("default_box");
        }
    echo form_close();
    echo br(3);
echo aba_end();

$this->load->view('footer_interna');
?>
