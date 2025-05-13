<?php
set_title('Reunião SG - Cadastro');
$this->load->view('header');
?>
<script>
<?php
echo form_default_js_submit(Array('cd_usuario_participante'));
?>
    function ir_lista()
    {
        location.href='<?php echo site_url("atividade/reuniao_sg"); ?>';
    }

    function ir_cadastro()
    {
        location.href='<?php echo site_url("atividade/reuniao_sg/detalhe/" . $cd_reuniao_sg); ?>';
    }
	
	function ir_anexo()
    {
        location.href='<?php echo site_url("atividade/reuniao_sg/anexo/" . $cd_reuniao_sg); ?>';
    }

    function ir_parecer()
    {
        location.href='<?php echo site_url("atividade/reuniao_sg/parecer/" . $cd_reuniao_sg); ?>';
    }	
    
    function excluir_participante(cd_reuniao_sg_participante)
    {
        if( confirm('Deseja excluir?') )
        {
            location.href='<?php echo site_url("atividade/reuniao_sg/excluir_participante/" . $cd_reuniao_sg); ?>/'+cd_reuniao_sg_participante;
        }
    }
    
    function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("table-1"),
        [
            'CaseInsensitiveString', 
            'CaseInsensitiveString'
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
        ob_resul.sort(0, true);
    }
    
    $(document).ready(function (){
        configure_result_table();
    })
    
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_lista', 'Agendamento', FALSE, 'ir_cadastro();');
$abas[] = array('aba_anexo', 'Anexo', FALSE, 'ir_anexo();');
$abas[] = array('aba_participante', 'Participantes', TRUE, 'location.reload();');

$this->load->helper('grid');
$grid = new grid();

$body = array();

$head = array(
  'Participante',
  'Gerência',
  ''
);

foreach ($collection as $item)
{
    $body[] = array(
      array($item['nome'], "text-align:left;"),
      $item['divisao'],
      ($fl_encerrado['fl_encerrado'] == 'S' ? '<a onclick="excluir_participante(' . $item["cd_reuniao_sg_participante"] . ')" href="javascript:void(0);">[Excluir]</a>' : '')
    );
}

$grid->head = $head;
$grid->body = $body;


echo aba_start($abas);
    echo form_open('atividade/reuniao_sg/salvar_participante');
        echo form_start_box("default_box", "Cadastro");
            echo form_default_hidden("cd_reuniao_sg", "", $cd_reuniao_sg);
            echo form_default_usuario_ajax('cd_usuario_participante', 'GIN', '', 'Usuário:');
        echo form_end_box("default_box");
        echo form_command_bar_detail_start();
        if($fl_encerrado['fl_encerrado'] == 'S')
        {
            echo button_save("Salvar");
        }

    echo form_command_bar_detail_end();
    echo form_close();
    echo form_start_box("default_box", "Participantes");
        echo $grid->render();
    echo form_end_box("default_box");
     

echo br(2);

echo aba_end();
$this->load->view('footer_interna');
?>