<?php
set_title('Atendimentos');
$this->load->view('header');
?>
<script>
    function filtrar()
    {
        $("#result_div").html("<?php echo loader_html(); ?>");
    
        $.post('<?php echo site_url('/ecrm/atendimento_lista/listar'); ?>',$('#filter_bar_form').serialize(),
        function(data)
        {
            $("#result_div").html(data);
            configure_result_table();
        });
    }

    function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("table-1"),
        [
            'Number',
            'CaseInsensitiveString',
            'DateTimeBR',
            'DateTimeBR',
            'CaseInsensitiveString',
            'CaseInsensitiveString',
            'CaseInsensitiveString',
            'CaseInsensitiveString',
            'RE',
            'CaseInsensitiveString',
            'CaseInsensitiveString',
            'CaseInsensitiveString',
            'CaseInsensitiveString'
        ]);
        ob_resul.onsort = function()
        {
            var rows = ob_resul.tBody.rows;
            var l = rows.length;
            for (var i = 0; i < l; i++)
            {
                removeClassName( rows[i], i % 2 ? "sort-par" : "sort-impar" );
                addClassName( rows[i], i % 2 ? "sort-impar" : "sort-par" );
            }
        };
        ob_resul.sort(0, true);
    }

    function novo()
    {
        location.href='<?php echo site_url("ecrm/atendimento_lista/detalhe/0"); ?>';
    }

    function mudar_obs(v)
    {
        $('#obs').val(v);
        filtrar();
    }

    function ir_location(local)
    {
        switch(local)
        {
            case 1:
              location.href='<?php echo site_url("ecrm/atendimento_lista/atendente"); ?>';
              break;
            case 2:
              location.href='<?php echo site_url("ecrm/atendimento_lista/data"); ?>';
              break;
            case 3:
              location.href='<?php echo site_url("ecrm/atendimento_lista/tipo"); ?>';
              break;
            case 4:
              location.href='<?php echo site_url("ecrm/atendimento_lista/programa"); ?>';
              break;
            case 5:
              location.href='<?php echo site_url("ecrm/atendimento_lista/index"); ?>';
              break;
        }
    }

    $(function(){
        filtrar();
    });
</script>

<?php
$abas[] = array('aba_lista', 'Atendente', FALSE, 'ir_location(1);');
$abas[] = array('aba_lista', 'Data', FALSE, 'ir_location(2);');
$abas[] = array('aba_lista', 'Tipo', FALSE, 'ir_location(3);');
$abas[] = array('aba_lista', 'Programa', FALSE, 'ir_location(4);');
$abas[] = array('aba_lista', 'Todos', TRUE, 'location.reload();');

$config['button'][]=array('Novo', 'novo()');

$participante = Array();

if($re!='')
{
    $dini=calcular_data('', '1 year');
    $dfim=date('d/m/Y');
    $participante['cd_empresa']            = $emp;
    $participante['cd_registro_empregado'] = $re;
    $participante['seq_dependencia']       = $seq;		
}
else
{
  /*  if($dt != '')
    {
        $dt = substr($dt, 0,2).'/'.substr($dt, 2,2).'/'.substr($dt, 4,4);

        $dini = $dt;
        $dfim = $dt;
    }
    else
    {*/
      $dini=calcular_data('', '3 days');
      $dfim=date('d/m/Y');
    #}
}

$icones="<table style='font-family:arial;font-size:12px;' cellspacing='10'>";
$icones.='<tr>';
$icones.='<td align="center"><a href="javascript:void(0);" onclick="mudar_obs(\'R\');"><img src="'.base_url().'img/atendimento/img_reclamacao.png" border="0"></a></td>';
$icones.='<td align="center"><a href="javascript:void(0);" onclick="mudar_obs(\'O\');"><img src="'.base_url().'img/atendimento/img_observacao.png" border="0"></a></td>';
$icones.='<td align="center"><a href="javascript:void(0);" onclick="mudar_obs(\'E\');"><img src="'.base_url().'img/atendimento/img_encaminhamento.png" border="0"></a></td>';
$icones.='<td align="center"><a href="javascript:void(0);" onclick="mudar_obs(\'T\');"><img src="'.base_url().'img/atendimento/img_retorno.png" border="0"></a></td>';
$icones.='</tr><tr>';
$icones.='<td>Reclamação/Sugestão</td>';
$icones.='<td>Observação/Elogio</td>';
$icones.='<td>Encaminhamento</td>';
$icones.='<td>Retorno</td>';
$icones.='</tr>';
$icones.="</table>";

echo aba_start( $abas );
    echo form_list_command_bar($config);
    echo form_start_box_filter('filter_bar', 'Filtros');
        echo filter_integer('cd_atendimento', 'Cód Protocolo :');
        echo filter_date_interval('dt_inicio', 'dt_fim', 'Data :', $dini, $dfim, FALSE);
        echo filter_participante(array('cd_empresa','cd_registro_empregado','seq_dependencia', 'nome_participante'), "Participante :", $participante, TRUE, FALSE );	
	
        if($id_atendente != '')
        {
            echo filter_dropdown('id_atendente', 'Atendente :', $atendente_dd, array($id_atendente));
        }
        else
        {
            echo filter_dropdown('id_atendente', 'Atendente :', $atendente_dd);
        }

        if($tp_atendimento != '')
        {
             echo filter_dropdown('tipo_atendimento', 'Tipo :', $tipo_dd,  array($tp_atendimento));
        }
        else
        {
             echo filter_dropdown('tipo_atendimento', 'Tipo :', $tipo_dd);
        }
        
        echo filter_dropdown('obs', 'Obs :', $obs_dd);
        echo filter_dropdown('cd_programa_fceee', 'Programa :', $programa_dd);
        echo form_default_row("","",$icones);
    echo form_end_box_filter();
    echo '<div id="result_div"></div>';
    echo br(2);
echo aba_end();

$this->load->view('footer');
?>