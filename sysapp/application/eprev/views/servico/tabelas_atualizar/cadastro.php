<?php
set_title('Tabelas a Atualizar');
$this->load->view('header');
?>

<script>
<?php
    echo form_default_js_submit(Array('tabela'));
?>
    function ir_lista()
    {
        location.href='<?php echo site_url("servico/tabelas_atualizar"); ?>';
    } 
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_nc', 'Cadastro', TRUE, 'location.reload();');

$arr_periodicidade[] = array('value' => 'D', 'text' => 'Diário');            
$arr_periodicidade[] = array('value' => 'S', 'text' => 'Sincronizado');    
$arr_periodicidade[] = array('value' => 'M', 'text' => 'Mensal');    
$arr_periodicidade[] = array('value' => 'E', 'text' => 'Eventual');    
$arr_periodicidade[] = array('value' => 'I', 'text' => 'Inativa');    

echo aba_start( $abas );
    echo form_open('servico/tabelas_atualizar/salvar', 'name="filter_bar_form"');
        echo form_start_box( "default_box", "Cadastro" );
            echo form_default_hidden('codigo', '', $row['tabela']);
            echo form_default_text('tabela', 'Tabela:*', $row['tabela'], (trim($row['tabela']) == '' ? 'style="width:400px"' : "style='font-weight: bold; width:100%;border: 0px;' readonly"));
            echo form_default_textarea('comando', 'Comando de seleção:', $row['comando'], 'style="height:150px;"');
            echo form_default_textarea('condicao', 'Condição:', $row['condicao'], 'style="height:150px;"');
            echo form_default_textarea('contagem', 'Comando de contagem:', $row['contagem'], 'style="height:150px;"');
            echo form_default_textarea('comando_inicial', 'Comando Inicial:', $row['comando_inicial'], 'style="height:150px;"');
            echo form_default_textarea('comando_final', 'Comando Final:', $row['comando_final'], 'style="height:150px;"');
            echo form_default_dropdown('periodicidade', 'Periodicidade:', $arr_periodicidade, array($row['periodicidade']));
            
            if(trim($row['tabela']) != '')
            {
                echo form_default_text("dt_inicio", "Dt Início:", $row['dt_inicio'], "style='font-weight: bold; width:100%;border: 0px;' readonly" );
                echo form_default_text("dt_final", "Dt Final:", $row['dt_final'], "style='font-weight: bold; width:100%;border: 0px;' readonly" );
                echo form_default_text("hr_tempo", "Tempo:", $row['hr_tempo'], "style='font-weight: bold; width:100%;border: 0px;' readonly" );
            }
            
            echo form_default_text('postgres', 'Postgres:', $row['postgres'], 'style="width:100px"');
            echo form_default_text('oracle', 'Oracle:', $row['oracle'], 'style="width:100px"');
            echo form_default_text('access_callcenter', 'Access Call Center:', $row['access_callcenter'], 'style="width:100px"');
            echo form_default_text('truncar', 'Truncar:', $row['truncar'], 'style="width:100px"');
            echo form_default_text('incrementar', 'Incrementar :', $row['incrementar'], 'style="width:100px"');
            echo form_default_text('campo_controle_incremental', 'Campo controle:', $row['campo_controle_incremental'], 'style="width:100px"');
        echo form_end_box("default_box");
        
        echo form_command_bar_detail_start();     
            echo button_save("Salvar");
        echo form_command_bar_detail_end();
    echo form_close();
    echo br(3);
echo aba_end();

$this->load->view('footer_interna');
?>