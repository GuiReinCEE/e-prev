<?php
set_title('Reunião CCI - Cadastro');
$this->load->view('header');
?>
<script>
<?php
    echo form_default_js_submit(Array('nr_numero', 'nr_ano', 'cd_reuniao_cci_tipo', 'dt_reuniao_cci', 'hr_reuniao_cci', 'cd_reuniao_cci_local', 'cd_usuario_coordenador_cci'));
?>
    function ir_lista()
    {
        location.href='<?php echo site_url("gestao/reuniao_cci"); ?>';
    }
    
    function pdf()
    {
        window.open('<?php echo site_url("gestao/reuniao_cci/pdf/".intval($row['cd_reuniao_cci'])); ?>');
    }
    
    function salvar_membro_efetivo()
    {
        var cd_reuniao_cci_membro_efetivo = $('#cd_reuniao_cci_membro_efetivo').val();
        
        if(cd_reuniao_cci_membro_efetivo != '')
        {
            location.href='<?php echo site_url("gestao/reuniao_cci/salvar_membro_efetivo/".intval($row['cd_reuniao_cci'])); ?>/'+cd_reuniao_cci_membro_efetivo;
        }
        else
        {
            alert('Informe o membro efetivo');
        }
    }
    
    function excluir_membro_efetivo(cd_reuniao_cci_membro_efetivo_item)
    {
        if(confirm('Excluir o membro efetivio?'))
        {
            location.href='<?php echo site_url("gestao/reuniao_cci/excluir_membro_efetivo/".intval($row['cd_reuniao_cci'])); ?>/'+cd_reuniao_cci_membro_efetivo_item;
        }
    }
    
    function salvar_convidado()
    {
        var cd_reuniao_cci_convidado = $('#cd_reuniao_cci_convidado').val();
        
        if(cd_reuniao_cci_convidado != '')
        {
            location.href='<?php echo site_url("gestao/reuniao_cci/salvar_convidado/".intval($row['cd_reuniao_cci'])); ?>/'+cd_reuniao_cci_convidado;
        }
        else
        {
            alert('Informe o membro efetivo');
        }
    }
    
    function excluir_convidado(cd_reuniao_cci_convidado_item)
    {
        if(confirm('Excluir o convidado?'))
        {
            location.href='<?php echo site_url("gestao/reuniao_cci/excluir_convidado/".intval($row['cd_reuniao_cci'])); ?>/'+cd_reuniao_cci_convidado_item;
        }
    }
    
    function enviar()
    {
        if(confirm('Deseja enviar?'))
        {
            location.href='<?php echo site_url("gestao/reuniao_cci/enviar/".intval($row['cd_reuniao_cci'])); ?>';
        }
    }
    
    function salvar_pauta()
    {
        var ds_reuniao_cci_pauta = $('#ds_reuniao_cci_pauta').val();
        
        if(ds_reuniao_cci_pauta != '')
        {
            $.post( '<?php echo site_url('gestao/reuniao_cci/salvar_pauta') ?>',
            {
                cd_reuniao_cci       : $('#cd_reuniao_cci').val(),
                ds_reuniao_cci_pauta : ds_reuniao_cci_pauta
            },
            function(data)
            {
                location.reload();
            });
        }
        else
        {
            alert('Informe a descrição da pauta.');
        }
    }
    
    function excluir_pauta(cd_reuniao_cci_pauta)
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
                
            var ob_resul = new SortableTable(document.getElementById("table-2"),
            [
                'CaseInsensitiveString',
                'DateTimeBR',
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
            
            var ob_resul = new SortableTable(document.getElementById("table-3"),
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
$abas[] = array('aba_nc', 'Cadastro', TRUE, 'location.reload();');

$config_tipo      = array('gestao.reuniao_cci_tipo', 'cd_reuniao_cci_tipo', 'ds_reuniao_cci_tipo');
$config_local     = array('gestao.reuniao_cci_local', 'cd_reuniao_cci_local', 'ds_reuniao_cci_local');
$config_membro    = array('gestao.reuniao_cci_membro_efetivo', 'cd_reuniao_cci_membro_efetivo', 'ds_reuniao_cci_membro_efetivo');
$config_convidado = array('gestao.reuniao_cci_convidado', 'cd_reuniao_cci_convidado', 'ds_reuniao_cci_convidado');

if(intval($row['cd_reuniao_cci']) > 0) 
{
    $body = array();
    $head = array(
        'Membro Efetivo',
        'Dt. Inclusão',
        'Usuário',
        ''
    );
    
    foreach ($collection_membro_efetivo as $item)
    {	
        $body[] = array(
            array($item['ds_reuniao_cci_membro_efetivo'], 'text-align:left'),
            $item['dt_inclusao'],
            array($item['nome'], 'text-align:left'),
            (trim($row['dt_enviado']) == '' ? '<a href="javascript:void(0);" onclick="excluir_membro_efetivo('.intval($item['cd_reuniao_cci_membro_efetivo_item']).');">[excluir]</a>' : '')
        );
    }
    
    $this->load->helper('grid');
    $grid1 = new grid();
    $grid1->view_count = false;
    $grid1->id_tabela = 'table-1';
    $grid1->head = $head;
    $grid1->body = $body;
    
    $body = array();
    $head = array(
        'Convidado',
        'Dt. Inclusão',
        'Usuário',
        ''
    );
    
    foreach ($collection_convidado as $item)
    {	
        $body[] = array(
            array($item['ds_reuniao_cci_convidado'], 'text-align:left'),
            $item['dt_inclusao'],
            array($item['nome'], 'text-align:left'),
            (trim($row['dt_enviado']) == '' ? '<a href="javascript:void(0);" onclick="excluir_convidado('.intval($item['cd_reuniao_cci_convidado_item']).');">[excluir]</a>' : '')
        );
    }
    
    $this->load->helper('grid');
    $grid2 = new grid();
    $grid2->view_count = false;
    $grid2->id_tabela = 'table-2';
    $grid2->head = $head;
    $grid2->body = $body;
     
    $body = array();
    $head = array(
        'Pauta',
        'Dt. Inclusão',
        'Usuário',
        'Situação',
        ''
    );

    foreach ($collection_pauta as $item)
    {	
        $body[] = array(
            array(nl2br($item['ds_reuniao_cci_pauta']), 'text-align:justify;'),
            $item['dt_inclusao'],
            array($item['nome'], 'text-align:left'),
            (((trim($row['dt_aprovado']) != "") OR (trim($row['dt_desaprovado']) != "")) ? '<span class="'.trim($item['class_status']).'">'.$item['status'].'</span>' : ""),
            (trim($row['dt_enviado']) == '' ? '<a href="javascript:void(0);" onclick="excluir_pauta('.intval($item['cd_reuniao_cci_pauta']).');">[excluir]</a>' : '')
        );
    }
    
    $this->load->helper('grid');
    $grid3 = new grid();
    $grid3->view_count = false;
    $grid3->id_tabela = 'table-3';
    $grid3->head = $head;
    $grid3->body = $body;
}
echo aba_start( $abas );
    echo form_open('gestao/reuniao_cci/salvar');
        echo form_start_box("default_box", "Cadastro");			
			echo form_default_hidden('cd_reuniao_cci', '', $row['cd_reuniao_cci']);
            echo form_default_integer_ano('nr_numero', 'nr_ano', 'Número / Ano :*', $row['nr_numero'], $row['nr_ano']);
            echo form_default_dropdown_db('cd_reuniao_cci_tipo', 'Tipo :*', $config_tipo, $row['cd_reuniao_cci_tipo'], '', '', TRUE);	
            echo form_default_date('dt_reuniao_cci', 'Data :*', $row);
            echo form_default_time('hr_reuniao_cci', 'Hora :*', $row);
            echo form_default_dropdown_db('cd_reuniao_cci_local', 'Local :*', $config_local, $row['cd_reuniao_cci_local'], '', '', TRUE);
            echo form_default_usuario_ajax('cd_usuario_coordenador_cci', $row['cd_gerencia_coordenador_cci'], $row['cd_usuario_coordenador_cci'], "Coordenador CCI :*", "Gerência Coordenador CCI :");
            if(trim($row['dt_aprovado']) != '') 
            {
                echo form_default_row('aprovado', '', '<span class="label label-success">Aprovado</span>');
            }
            else if(trim($row['dt_desaprovado']) != '')
            {
                echo form_default_row('aprovado', '', '<span class="label label-important">Desaprovado</span>');
            }
        echo form_end_box("default_box");
        echo form_command_bar_detail_start();    
            if(trim($row['dt_enviado']) == '')
            {
                echo button_save("Salvar");
            }
            
            if((intval($row['cd_reuniao_cci']) > 0) AND (trim($row['dt_enviado']) == '') AND (intval($row['qt_membro_efetivo']) > 0) AND (intval($row['qt_convidado']) > 0) AND (intval($row['qt_pauta']) > 0))
            {
                echo button_save("Enviar", "enviar()", "botao_verde");
            }
            
            if(trim($row['dt_aprovado']) != '') 
            {
                echo button_save("PDF", "pdf()", "botao_disabled");
            }
        echo form_command_bar_detail_end();
    echo form_close();
    echo br(2);
    if((intval($row['cd_reuniao_cci']) > 0) AND (trim($row['dt_enviado']) == ''))
    {
        echo form_start_box("default_membro_efetivo_box", "Membros Efetivos" );	
            echo form_default_dropdown_db('cd_reuniao_cci_membro_efetivo', 'Membro Efetivo :*', $config_membro, '', '', '', TRUE);	
        echo form_end_box("default_membro_efetivo_box");
        echo form_command_bar_detail_start();    
            echo button_save("Adicionar", "salvar_membro_efetivo();");
            echo br(2);
        echo form_command_bar_detail_end();
    }
    
    if(intval($row['cd_reuniao_cci']) > 0)
    {
        echo $grid1->render();
    }
    
    if((intval($row['cd_reuniao_cci']) > 0) AND (trim($row['dt_enviado']) == ''))
    {   
        echo form_start_box("default_convidado_box", "Convidados" );	
            echo form_default_dropdown_db('cd_reuniao_cci_convidado', 'Convidado :*', $config_convidado, '', '', '', TRUE);	
        echo form_end_box("default_convidado_box");
        echo form_command_bar_detail_start();    
            echo button_save("Adicionar", "salvar_convidado();");
            echo br(2);
        echo form_command_bar_detail_end();
    }
    
    if(intval($row['cd_reuniao_cci']) > 0)
    {
        echo $grid2->render();
    }
    
    if((intval($row['cd_reuniao_cci']) > 0) AND (trim($row['dt_enviado']) == ''))
    {  
        echo form_start_box("default_pauta_box", "Pauta");			
            echo form_default_hidden('cd_reuniao_cci', '', $row['cd_reuniao_cci']);
            echo form_default_textarea('ds_reuniao_cci_pauta', 'Descrição :*', '', 'style="height:120px; width:500px;"');
        echo form_end_box("default_pauta_box");
        
        echo form_command_bar_detail_start();    
            echo button_save("Adicionar", "salvar_pauta();");
            echo br(2);
        echo form_command_bar_detail_end();
    }
    
    if(intval($row['cd_reuniao_cci']) > 0)
    {
        echo $grid3->render();
    }
    
    echo br(2);	
echo aba_end();

$this->load->view('footer_interna');
?>