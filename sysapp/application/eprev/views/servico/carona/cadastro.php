<?php
set_title('Mão na Roda');
$this->load->view('header');
?>
<script>
<?php
		echo form_default_js_submit(Array('trajeto_vinda','trajeto_retorno','nr_vaga'));
?>
</script>
<script type="text/javascript">
    function irLista()
	{
		location.href='<?php echo site_url("servico/carona"); ?>';
	}

    function excluir(cd_carona)
	{
		var aviso = "Atenção\n\nDeseja excluir a carona?\n\n";

		if(confirm(aviso))
		{
            location.href='<?php echo site_url("servico/carona/excluir"); ?>'+'/'+ cd_carona;
		}
	}
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'irLista();');
$abas[] = array('aba_nc', 'Carona ', TRUE, 'location.reload();');

echo aba_start( $abas );
    echo form_open('servico/carona/salvar', 'name="filter_bar_form"');
        echo form_start_box( "default_box", "Cadastro" );
            echo form_default_text('cd_carona', "Código:", $row, "style='font-weight: bold; width:100%;border: 0px;' readonly" );
            echo form_default_textarea('trajeto_vinda', "Trajeto de Vinda:*", $row, "style='width:500px;'");
            echo form_default_textarea('trajeto_retorno', "Trajeto de Retorno:*", $row, "style='width:500px;'");
            echo form_default_text('nr_vaga', "Vagas:", $row);
        echo form_end_box("default_box");

        echo form_command_bar_detail_start();
            echo button_save("Salvar");

            if($row['cd_carona'] > 0)
            {
                echo button_save("Excluir","excluir(".$row['cd_carona'].")","botao_vermelho");
            }
        echo form_command_bar_detail_end();

    echo form_close();

    if($row['cd_carona'] > 0)
    {
        echo form_start_box( "default_box", "Caroneiros" );
        $this->load->helper('grid');

        $body=array();
        $head = array(
            'Caroneiro',
            'Data'
        );

        foreach($caroneiros as $item)
        {
            $body[] = array(
                array($item['nome'],'text-align:left'),
                $item['dt_inclusao']
            );
        }

        $grid = new grid();
        $grid->head = $head;
        $grid->body = $body;
        echo $grid->render();

        echo form_end_box("default_box");

        
    }
	echo "<BR><BR><BR>";

echo aba_end();

$this->load->view('footer_interna');