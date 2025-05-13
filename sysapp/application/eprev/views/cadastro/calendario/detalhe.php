<?php
set_title('Cadastro de Calendário');
$this->load->view('header');
?>
<script>
<?php
    echo form_default_js_submit(array("codigo","nome"));
?>
    function ir_lista()
    {
        location.href='<?php echo site_url("cadastro/calendario"); ?>';
    }
        
    function excluir()
    {
        var confirmacao = 'Deseja excluir?\n\n'+
                'Clique [Ok] para Sim\n\n'+
                'Clique [Cancelar] para Não\n\n';
        
        if(confirm(confirmacao))
        {
           location.href='<?php echo site_url("cadastro/calendario/excluir/".$row['cd_calendario']); ?>';
        }

    }

    function set_turno()
    {
        if($('#tp_calendario').val() == 'T')
        {
            $('#turno_row').show();
        }
        else
        {
            $('#turno_row').hide();
            $('#turno').val('');
        }
    }
    
    $(function(){
        set_turno();
        
        $('#tp_calendario').change(function(){
            set_turno();
      })
   });
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');

$arr_tipo[] = array('value' => 'E', 'text' => 'Evento');
$arr_tipo[] = array('value' => 'C', 'text' => 'Feriado FCEEE');
$arr_tipo[] = array('value' => 'F', 'text' => 'Feriado');
$arr_tipo[] = array('value' => 'T', 'text' => 'Feriado  FCEEE  Meio Turno');
$arr_tipo[] = array('value' => 'P', 'text' => 'Pagamento Colaboradores');

$arr_tipo[] = array('value' => 'DE', 'text' => 'Reunião Diretoria Executiva');
$arr_tipo[] = array('value' => 'CF', 'text' => 'Reunião Conselho Fiscal');
$arr_tipo[] = array('value' => 'CD', 'text' => 'Reunião Conselho Deliberativo');

$arr_tipo[] = array('value' => 'EN', 'text' => 'Evento Endomarketing');


$arr_turno[] = array('value' => 'M', 'text' => 'Manhã');
$arr_turno[] = array('value' => 'T', 'text' => 'Tarde');
$arr_turno[] = array('value' => 'N', 'text' => 'Noite');

echo aba_start($abas);
    echo form_open('cadastro/calendario/salvar');
        echo form_start_box("default_box", "Cadastro");
            echo form_default_hidden('cd_calendario', '', $row["cd_calendario"]);
            echo form_default_date('dt_calendario', 'Data:* ', $row["dt_calendario"]);
            echo form_default_dropdown('tp_calendario', "Tipo: *", $arr_tipo, array($row['tp_calendario']));
            echo form_default_dropdown('turno', "Turno: *", $arr_turno, array($row['turno']));
            echo form_default_text("descricao", "Descrição: *", $row['descricao'], "style='width:500px;'", "50");
            echo form_default_text("ds_url", "URL (EVENTOS): ", $row['ds_url'], "style='width:500px;'", "50");
        echo form_end_box("default_box");
        echo form_command_bar_detail_start();
            echo button_save();
            if(intval($row["cd_calendario"]) > 0)
            {
                 echo button_save('Excluir', 'excluir()', 'botao_vermelho');
            }
        echo form_command_bar_detail_end();
    echo form_close();
echo aba_end();

$this->load->view('footer_interna');
?>