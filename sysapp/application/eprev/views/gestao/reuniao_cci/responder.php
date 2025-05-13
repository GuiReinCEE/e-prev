<?php
set_title('Reunião CCI - Responder');
$this->load->view('header');
?>
<script>
    function ir_lista()
    {
        location.href='<?php echo site_url("gestao/reuniao_cci/minhas"); ?>';
    }
    
    $(function(){
        if($('#cd_reuniao_cci').val() > 0)
        {
            var ob_resul = new SortableTable(document.getElementById("table-1"),
            [
                null,
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
            ob_resul.sort(2, true);
        }
    });
    
    function checkAll()
    {
        var ipts = $("#table-1>tbody").find("input:checkbox");
        var check = document.getElementById("checkboxCheckAll");
	 
        check.checked ?
            jQuery.each(ipts, function(){
            this.checked = true;
        }) :
            jQuery.each(ipts, function(){
            this.checked = false;
        });
    }
    
    function aprovar()
    {
        var arr = new Array();
	
		$("input[name='reuniao_cci_pauta[]']:checked").each(function(){
		   arr.push($(this).val());
		});
        
        var confirmacao = 'Deseja aprovar as pautas da reunião?\n\n'+
					'Clique [Ok] para Sim\n\n'+
					'Clique [Cancelar] para Não\n\n';
        
        if(arr.length > 0)
        {
            if(confirm(confirmacao))
            {
                $.post( '<?php echo site_url('gestao/reuniao_cci/aprovar') ?>',
                {
                    'reuniao_cci_pauta[]' : arr,
                    cd_reuniao_cci        : $('#cd_reuniao_cci').val()
                },
                function(data)
                {
                    ir_lista();
                });
            }
        }
        else
        {
            alert('Selecione os aprovados.');
        }
    }
    
    function desaprovar()
    {
        var arr = new Array();
	
		$("input[name='reuniao_cci_pauta[]']:checked").each(function(){
		   arr.push($(this).val());
		});
        
        var confirmacao = 'Deseja desaprovar as pautas da reunião?\n\n'+
					'Clique [Ok] para Sim\n\n'+
					'Clique [Cancelar] para Não\n\n';
        
        if(arr.length == 0)
        {
            if(confirm(confirmacao))
            {
                $.post( '<?php echo site_url('gestao/reuniao_cci/desaprovar') ?>',
                {
                    cd_reuniao_cci : $('#cd_reuniao_cci').val()
                },
                function(data)
                {
                    ir_lista();
                });
            }
        }
        else
        {
            alert('Nenhuma pauta deve estar selecionada.');
        }
    }
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_nc', 'Responder', TRUE, 'location.reload();');

$config_tipo  = array('gestao.reuniao_cci_tipo', 'cd_reuniao_cci_tipo', 'ds_reuniao_cci_tipo');
$config_local = array('gestao.reuniao_cci_local', 'cd_reuniao_cci_local', 'ds_reuniao_cci_local');

$body = array();
$head = array(
    '<input type="checkbox" id="checkboxCheckAll" onclick="checkAll();" title="Clique para Marcar ou Desmarcar Todos">',
    'Pauta',
    'Dt. Inclusão',
    'Usuário'
);

foreach ($collection as $item)
{	
    $campo_check = array(
      'name'    => 'reuniao_cci_pauta[]',
      'id'      => 'reuniao_cci_pauta[]',
      'value'   => $item['cd_reuniao_cci_pauta'],
      'checked' => FALSE,
    );
    
    $body[] = array(
        (trim($row['fl_edicao']) == 'S' ? form_checkbox($campo_check) : ''),
        array($item['ds_reuniao_cci_pauta'], 'text-align:left'),
        $item['dt_inclusao'],
        array($item['nome'], 'text-align:left')
    );
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo aba_start( $abas );
    echo form_start_box("default_box", "Reunião");
        echo form_default_hidden('cd_reuniao_cci', '', $row['cd_reuniao_cci']);
        echo form_default_integer_ano('nr_numero', 'nr_ano', 'Número / Ano :*', $row['nr_numero'], $row['nr_ano']);
        echo form_default_dropdown_db('cd_reuniao_cci_tipo', 'Tipo :', $config_tipo, $row['cd_reuniao_cci_tipo'], '', '', FALSE);	
        echo form_default_date('dt_reuniao_cci', 'Data :', $row);
        echo form_default_time('hr_reuniao_cci', 'Hora :', $row);
        echo form_default_dropdown_db('cd_reuniao_cci_local', 'Local :', $config_local, $row['cd_reuniao_cci_local'], '', '', FALSE);
        echo form_default_usuario_ajax('cd_usuario_coordenador_cci', $row['cd_gerencia_coordenador_cci'], $row['cd_usuario_coordenador_cci'], "Coordenador CCI :", "Gerência Coordenador CCI :");
    echo form_end_box("default_box");
    echo $grid->render();
    echo form_command_bar_detail_start();    
        if(trim($row['fl_edicao']) == 'S')
        {
            echo button_save("Aprovar", "aprovar()", "botao_verde");
            echo button_save("Desaprovar", "desaprovar()", "botao_vermelho");
        }
    echo form_command_bar_detail_end();
    echo br(2);	
echo aba_end();

$this->load->view('footer_interna');
?>