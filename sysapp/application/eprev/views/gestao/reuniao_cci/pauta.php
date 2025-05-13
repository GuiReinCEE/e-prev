<?php
set_title('Reunião CCI - Cadastro');
$this->load->view('header');
?>
<script>
<?php
    echo form_default_js_submit(Array('ds_reuniao_cci_pauta'));
?>
    function ir_lista()
    {
        location.href='<?php echo site_url("gestao/reuniao_cci"); ?>';
    }
    
    function ir_cadastro()
    {
        location.href='<?php echo site_url("gestao/reuniao_cci/cadastro/".intval($row['cd_reuniao_cci'])); ?>';
    }
    
    function excluir(cd_reuniao_cci_pauta)
    {
        if(confirm('Excluir a pauta?'))
        {
            location.href='<?php echo site_url("gestao/reuniao_cci/excluir_pauta/".intval($row['cd_reuniao_cci'])); ?>/'+cd_reuniao_cci_pauta;
        }
    }
    
    $(function(){
        if($('#cd_reuniao_cci').val() > 0)
        {
            var ob_resul = new SortableTable(document.getElementById("table-1"),
            [
                'CaseInsensitiveString',
                'DateTimeBR',
                'CaseInsensitiveString',
                'CaseInsensitiveString',
                null

            ]);
            ob_resul.onsort = function ()
            {
                var rows = ob_resul.tBody.rows;
                var l = rows.length;
                for (var i = 0; i < l; i++)
                {
                    removeClassName( rows[i], i % 2 ? "sort-par" : "sort-impar" );
                    addClassName( rows[i], i % 2 ? "sort-impar" : "sort-par" );
                }
            };
            ob_resul.sort(1, true);
        }
    });
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_nc', 'Cadastro', FALSE, 'ir_cadastro();');
$abas[] = array('aba_lista', 'Pauta', TRUE, 'location.reload();');

$config_tipo  = array('gestao.reuniao_cci_tipo', 'cd_reuniao_cci_tipo', 'ds_reuniao_cci_tipo');
$config_local = array('gestao.reuniao_cci_local', 'cd_reuniao_cci_local', 'ds_reuniao_cci_local');




$this->load->helper('grid');
$grid = new grid();
$grid->view_count = false;
$grid->head = $head;
$grid->body = $body;

echo aba_start( $abas );
    echo form_open('gestao/reuniao_cci/salvar_pauta');
        echo form_start_box("default_box", "Reunião");
            echo form_default_hidden('cd_reuniao_cci', '', $row['cd_reuniao_cci']);
            echo form_default_integer_ano('nr_numero', 'nr_ano', 'Número / Ano :*', $row['nr_numero'], $row['nr_ano']);
            echo form_default_dropdown_db('cd_reuniao_cci_tipo', 'Tipo :', $config_tipo, $row['cd_reuniao_cci_tipo'], '', '', FALSE);	
            echo form_default_date('dt_reuniao_cci', 'Data :', $row);
            echo form_default_time('hr_reuniao_cci', 'Hora :', $row);
            echo form_default_dropdown_db('cd_reuniao_cci_local', 'Local :', $config_local, $row['cd_reuniao_cci_local'], '', '', FALSE);
            echo form_default_usuario_ajax('cd_usuario_coordenador_cci', $row['cd_gerencia_coordenador_cci'], $row['cd_usuario_coordenador_cci'], "Coordenador CCI :", "Gerência Coordenador CCI :");
        echo form_end_box("default_box");
        if(trim($row['dt_enviado']) == '')
        {
            
        }
        echo form_command_bar_detail_start();    
            if(trim($row['dt_enviado']) == '')
            {
                echo button_save("Adicionar");
                echo br(2);
            }
        echo form_command_bar_detail_end();
    echo form_close();
    echo $grid->render();
    echo br(2);	
echo aba_end();

$this->load->view('footer_interna');
?>