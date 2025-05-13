<?php
    set_title('Demonstrativo de Resultados');
    $this->load->view('header');
?>
<script>
    function ir_lista()
    {
        location.href = "<?= site_url('gestao/demonstrativo_resultado/index') ?>";
    }    

    function ir_estrutura()
    {
        location.href = "<?= site_url('gestao/demonstrativo_resultado/estrutura/'.$demonstrativo['cd_demonstrativo_resultado']) ?>";
    }

    function ir_meses()
    {
        location.href = "<?= site_url('gestao/demonstrativo_resultado/meses/'.$demonstrativo['cd_demonstrativo_resultado']) ?>";
    } 

    function abrir_item(cd_demonstrativo_resultado_estrutura_mes)
    {
        var confirmacao = 'Deseja abrir o item?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        { 
            location.href = "<?= site_url('gestao/demonstrativo_resultado/abrir_item/'.$demonstrativo['cd_demonstrativo_resultado'].'/'.$demonstrativo_mes['cd_demonstrativo_resultado_mes']) ?>/"+cd_demonstrativo_resultado_estrutura_mes;
        }
    }   
</script>
<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_estrutura', 'Estrutura', FALSE, 'ir_estrutura();');
    $abas[] = array('aba_meses', 'Meses', FALSE, 'ir_meses();');
    $abas[] = array('aba_anexo_mes', mes_format($demonstrativo_mes['cd_mes'], 'mmmm'), TRUE, 'location.reload();');

    $head = array( 
        'Item',
        'Anexo',
        'Dt. Fechamento',
        'Usuário',
        ''
    );

    $body = array();

    foreach($collection as $item)
    {
        if(intval($item['cd_demonstrativo_resultado_estrutura_tipo']) != 2)
        { 
            $body[] = array(
                array($item['nr_ordem'].' - '.$item['ds_demonstrativo_resultado_estrutura'], 'text-align:left;'),
                array(anchor(base_url().'up/demonstrativo_resultado/'.$item['arquivo'], $item['arquivo_nome'], array('target' => '_blank')), 'text-align:left;'),
                $item['dt_fechamento'],
                array($item['ds_usuario'], 'text-align:left;'),
                (trim($item['dt_fechamento']) != '' ? '<a href="javascript:void(0);" onclick="abrir_item('.$item['cd_demonstrativo_resultado_estrutura_mes'].' )">[abrir item]</a>' : '')
            );
        }
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;

    echo aba_start($abas);
        echo form_open();        
        	echo form_start_box('default_arquivo_box', 'Estrutura');
            	echo form_default_hidden('cd_demonstrativo_resultado', '', $demonstrativo['cd_demonstrativo_resultado']);
                echo form_default_hidden('cd_demonstrativo_resultado_mes', '', $demonstrativo_mes['cd_demonstrativo_resultado_mes']);
                echo form_default_hidden('nr_mes', '', $demonstrativo_mes['cd_mes']);
                echo form_default_row('nr_ano', 'Ano:', '<span class="label label-inverse">'.$demonstrativo_mes['cd_mes'].'/'.$demonstrativo['nr_ano'].'</span>');
            echo form_end_box('default_arquivo_box');        	
        echo form_close();
        echo $grid->render();
	    echo br();
    echo aba_end();

$this->load->view('footer_interna');
?>        