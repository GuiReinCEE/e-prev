<?php
set_title('Cronograma - Analistas - Cadastro');
$this->load->view('header');
?>
<script>
<?php
echo form_default_js_submit(Array('cd_analista', 'nr_mes', 'nr_ano'));
?>
    function ir_lista()
    {
        location.href='<?php echo site_url("atividade/info_cronograma"); ?>';
    }

    function ir_cronograma_item()
    {
        location.href='<?php echo site_url("atividade/info_cronograma/item/".$row['cd_cronograma']); ?>';
    }

    function excluir_item(cd_cronograma_item)
    {
        if(confirm("ATENÇÃO\n\nDeseja excluir?\n\n"))
        {
            location.href='<?php echo site_url("atividade/info_cronograma/excluir_item/".$row['cd_cronograma']); ?>' + "/" + cd_cronograma_item;
        }
    }

    function excluir(cd_cronograma)
    {
        if(confirm("ATENÇÃO\n\nDeseja excluir?\n\n"))
        {
            location.href='<?php echo site_url("atividade/info_cronograma/excluir"); ?>' + "/" + cd_cronograma;
        }
    }
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_nc', 'Cadastro', TRUE, 'location.reload();');

if(intval($row['cd_cronograma']) > 0)
{
    $body = array();
    $head = array(
      'Prior.',
      'Descrição',
      'Concluído',
      ''
    );

    foreach($collection as $item)
    {
        $body[] = array(
          $item['nr_prioridade'],
          array(anchor('atividade/info_cronograma/item/'.$row['cd_cronograma'].'/'.$item['cd_cronograma_item'], nl2br($item['descricao'])),'text-align:jutify'),
          ($item['fl_concluido'] == 'S' ? '<font style="color:blue; font-weight:bold";>Sim</font>' : '<font style="color:red; font-weight:bold;">Não</font>'),
          '<a href="javascript:void(0)" onclick="excluir_item('.$item['cd_cronograma_item'].')">[Excluir]</a>'
        );
    }
    
    
    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;
}

echo aba_start($abas);
    echo form_open('atividade/info_cronograma/salvar', 'name="filter_bar_form"');
        echo form_start_box("default_box", "Cadastro");
            echo form_default_hidden('cd_cronograma', "", $row, "style='width:100%;border: 0px;' readonly");
            echo form_default_dropdown('cd_analista', 'Analista:*', $analistas, array($row['cd_analista']));
            if(intval($row['cd_cronograma']) > 0)
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
            if(intval($row['cd_cronograma']) > 0)
            {
                echo button_save("Adicionar Item", "ir_cronograma_item()", "botao_disabled");
                echo button_save("Excluir", "excluir(" . $row['cd_cronograma'] . ")", "botao_vermelho");
            }
        echo form_command_bar_detail_end();
        
        if(intval($row['cd_cronograma']) > 0)
        {
            echo form_start_box("default_box", "Cronograma Item"); 
                echo $grid->render();
            echo form_end_box("default_box");
        }
    echo form_close();

    echo "<BR><BR><BR>";

echo aba_end();

$this->load->view('footer_interna');
?>
