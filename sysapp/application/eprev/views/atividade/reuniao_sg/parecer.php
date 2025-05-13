<?php
set_title('Reunião SG - Cadastro');
$this->load->view('header');
?>
<script>
    <?= form_default_js_submit( array(
        'relato',
        'parecer',
        'dt_reuniao_ini',
        'hr_reuniao_ini',
        'dt_reuniao_fim',
        'hr_reuniao_ini'
        ), 'assunto(form)');
    ?>
    function ir_lista()
    {
        location.href='<?= site_url("atividade/reuniao_sg") ?>';
    }

    function ir_cadastro()
    {
        location.href='<?= site_url("atividade/reuniao_sg/detalhe/".$cd_reuniao_sg) ?>';
    }
	
	function ir_anexo()
    {
        location.href='<?= site_url("atividade/reuniao_sg/anexo/".$cd_reuniao_sg) ?>';
    }
    
    function adicionar_assunto()
    {
        location.href='<?= site_url("atividade/reuniao_sg/assunto/".$cd_reuniao_sg) ?>';
    }

    function assunto(form)
    {
        if(<?= count($assuntos) ?> == 0)
        {
            alert( "Não foi adicionado nenhum assunto." );
            return false;
        }
        else
        {
            if( confirm('Salvar?') )
            {
                form.submit();
            }
        }
    }

    function excluir_assunto(cd_reuniao_sg_assunto_parecer)
    {
        if( confirm('Deseja excluir?') )
        {
            location.href='<?= site_url("atividade/reuniao_sg/excluir_assunto/".$cd_reuniao_sg) ?>/'+cd_reuniao_sg_assunto_parecer;
        }
    }

    function encerrar(form)
    {
        if($('#fl_qualificacao').val() != '')
        {
            if($('#relato').val() != '' && $('#parecer').val() != '' && <?= count($assuntos) ?> > 0)
            {
                if( confirm('Deseja encerrar?') )
                {
                    //location.href='<?php echo site_url("atividade/reuniao_sg/encerrar/" . $cd_reuniao_sg); ?>/'+$('#fl_qualificacao').val()+'/'+$('#relato').val()+'/'+$('#parecer').val();
					$(form).attr('action', '<?= site_url("atividade/reuniao_sg/encerrar/" . $cd_reuniao_sg) ?>');
					$(form).submit();
                }
            }
            else
            {
                alert('Campos em branco.');
            }
        }
        else
        {
            alert('Campos qualificação deve ser preenchido.');
        }

    }

    function enviar()
    {
        var confirmacao = 'Deseja enviar email para os usuários aprovarem o parecer?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para Não\n\n';


        if($('#relato').val() != '' && $('#parecer').val() != '' && <?php echo count($assuntos); ?> > 0)
        {
            if(confirm(confirmacao))
            {
                location.href='<?= site_url("atividade/reuniao_sg/enviar/".$cd_reuniao_sg) ?>';
            }
        }
        else
        {
            alert('Campos em branco.');
        }

    }

    function imprimir_pdf()
    {
        location.href='<?= site_url("atividade/reuniao_sg/imprimir/".$cd_reuniao_sg) ?>';
    }
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_lista', 'Agendamento', FALSE, 'ir_cadastro();');
$abas[] = array('aba_anexo', 'Anexo', FALSE, 'ir_anexo();');
$abas[] = array('aba_pa', 'Parecer', TRUE, 'location.reload();');

$qualificacao[] = array('value' => 'P', 'text' => 'Positivo');
$qualificacao[] = array('value' => 'R', 'text' => 'Neutro');
$qualificacao[] = array('value' => 'N', 'text' => 'Negativo');

$this->load->helper('grid');
$grid   = new grid();
$grid_2 = new grid();

$body   = array();
$body_2 = array();

$head = array(
  'Assunto',
  'Complemento',
  ''
);

foreach ($assuntos as $item)
{
    $body[] = array(
		array(anchor("atividade/reuniao_sg/assunto/".$cd_reuniao_sg.'/'.$item['cd_reuniao_sg_assunto_parecer'], $item['ds_reuniao_sg_assunto']),"text-align:left;"),
		array($item['complemento'], "text-align:justify;"),
		($row['dt_encerrado'] == '' ? '<a onclick="excluir_assunto('.$item["cd_reuniao_sg_assunto_parecer"].')" href="javascript:void(0);">[Excluir]</a>' : '')
    );
}

$head_2 = array(
  'Usuário',
  'Aprovado',
  'Data de Aprovação',
  'Data do Envio'
);

foreach ($usuarios as $item)
{
    switch ($item['fl_validacao'])
    {
        case 'S':
            $validacao = '<label style="font-weight:bold">Sim</label>';
            break;
        case 'N':
            $validacao = '<label style="font-weight:bold; color:red">Não</label>';
            break;
        default :
            $validacao = 'Não Informado';
            break;
    }

    $body_2[] = array(
      array($item['nome'], "text-align:left;"),
      $validacao,
      $item['dt_validacao'],
      $item['dt_envio']
    );
}

$grid->head = $head;
$grid->body = $body;
$grid->view_count = false;

$grid_2->head = $head_2;
$grid_2->body = $body_2;
$grid_2->view_count = false;

echo aba_start($abas);
    echo form_start_box("default_box", "Assunto");
    if ($this->session->userdata('divisao') != 'SG')
    {
        if ($row['dt_encerrado'] == '')
        {
            echo button_save("Adicionar", "adicionar_assunto()");
    		echo br(2);
        }
    }
    echo $grid->render();
    echo form_end_box("default_box");

    echo form_open("atividade/reuniao_sg/salvar_parecer");
        echo form_default_hidden("cd_reuniao_sg", "", $cd_reuniao_sg);
       
        echo form_start_box("default_reuniao_box", "Reunião");
            echo form_default_date('dt_reuniao_ini', "Dt Início da Reunião:* ", $row);
            echo form_default_time('hr_reuniao_ini', "Hr Início da Reunião:* ", $row);
            echo form_default_date('dt_reuniao_fim', "Dt Fim da Reunião:* ", $row);
            echo form_default_time('hr_reuniao_fim', "Hr Fim da Reunião:* ", $row);
        echo form_end_box("default_reuniao_box");

        echo form_start_box("default_aprovacao_box", "Aprovação");
            echo form_default_checkbox_group('arr_participante', 'Usuários :', $arr_participante, $arr_participante_checked, 120);
        echo form_end_box("default_aprovacao_box");

        echo br(2);
        echo $grid_2->render();

        echo form_start_box("default_box", "Cadastro");
            echo form_default_hidden("cd_reuniao_sg", "", $cd_reuniao_sg);
            echo form_default_checkbox_group('arr_participante_parecer', 'Participantes Internos:*', $arr_participante_parecer, $arr_participante_parecer_checked, 120);
            echo form_default_textarea('relato', "Participantes Externos:* ", $row);
            echo form_default_textarea('parecer', "Parecer:* ", $row);
            echo form_default_dropdown('fl_qualificacao', "Qualificação: ", $qualificacao, array($row['parecer_qualificacao']));
        echo form_end_box("default_box");

        if ($row['dt_encerrado'] != '')
        {
            echo form_start_box("default_encerramento_box", "Encerramento");
                echo form_default_text('nome', "Usuário: ", $row, "style='width:150%;border: 0px;' readonly");
                echo form_default_text('dt_encerrado', "Dt. Encerramento: ", $row['dt_encerrado'], "style='width:150%;border: 0px;' readonly");
            echo form_end_box("default_encerramento_box");
        }

        echo form_command_bar_detail_start();
            if ($this->session->userdata('divisao') != 'SG')
            {
                if ($row['dt_encerrado'] == '')
                {
                    echo button_save("Salvar");
                    echo button_save("Enviar", 'enviar();');
                    echo button_save("Encerrar", 'encerrar(form);', 'botao_vermelho');
                }

                echo button_save("Imprimir PDF", 'imprimir_pdf();', 'botao_disabled');
            }
        echo form_command_bar_detail_end();

    echo form_close();
    echo br(2);
/*
if ($this->session->userdata('divisao') != 'SG')
{
    if ($row['dt_encerrado'] == '')
    {
		echo form_open('atividade/reuniao_sg/salvar_usuario');
			echo form_start_box("default_box", "Aprovação");
				echo form_default_hidden("cd_reuniao_sg", "", $cd_reuniao_sg);
				echo form_default_checkbox_group('arr_participante', 'Participantes :', $arr_participante, $arr_participante_checked, 120);
			echo form_end_box("default_box");
			echo form_command_bar_detail_start();
				echo button_save("Salvar", "salvar_usuario(form);");
			echo form_command_bar_detail_end();
		echo form_close();
    }
}

echo br(2);
echo $grid->render();

echo form_open('atividade/reuniao_sg/salvar_parecer', "form_parecer");
echo form_start_box("default_box", "Cadastro");
echo form_default_hidden("cd_reuniao_sg", "", $cd_reuniao_sg);
echo form_default_textarea('relato', "Participantes Externos:* ", $row);
echo form_default_textarea('parecer', "Parecer:* ", $row);
echo form_default_date('dt_reuniao_ini', "Dt Início da Reunião:* ", $row);
echo form_default_time('hr_reuniao_ini', "Hr Início da Reunião:* ", $row);
echo form_default_date('dt_reuniao_fim', "Dt Fim da Reunião:* ", $row);
echo form_default_time('hr_reuniao_fim', "Hr Fim da Reunião:* ", $row);
echo form_default_dropdown('fl_qualificacao', "Qualificação: ", $qualificacao, array($row['parecer_qualificacao']));
echo form_end_box("default_box");

if ($row['dt_encerrado'] != '')
{
    echo form_start_box("default_box", "Encerramento");
    echo form_default_text('nome', "Usuário: ", $row, "style='width:150%;border: 0px;' readonly");
    echo form_default_text('dt_encerrado', "Dt. Encerramento: ", $row['dt_encerrado'], "style='width:150%;border: 0px;' readonly");
    echo form_end_box("default_box");
}

echo form_command_bar_detail_start();
if ($this->session->userdata('divisao') != 'SG')
{
    if ($row['dt_encerrado'] == '')
    {
        echo button_save("Salvar");
        echo button_save("Enviar", 'enviar();');
		echo button_save("Encerrar", 'encerrar(form);', 'botao_vermelho');
    }

    echo button_save("Imprimir PDF", 'imprimir_pdf();', 'botao_disabled');
}

echo form_command_bar_detail_end();
echo form_close();
echo br(2);
*/
echo aba_end();
$this->load->view('footer_interna');
?>