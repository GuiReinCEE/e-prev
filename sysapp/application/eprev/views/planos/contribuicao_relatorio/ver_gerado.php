<?php
    set_title('Contribuição - Envio SMS');
    $this->load->view('header');
?>
<script>
    function ir_lista()
    {
        location.href = "<?= site_url('planos/contribuicao_relatorio') ?>";
    }

    function ir_gerado()
    {
        location.href = "<?= site_url('planos/contribuicao_relatorio/gerado') ?>";
    }

    function ir_debito_conta()
    {
        location.href = "<?= site_url('planos/contribuicao_relatorio/debito_conta') ?>";
    }

    function configure_result_table()
    {
        if(document.getElementById("table-1"))
        {
            var ob_resul = new SortableTable(document.getElementById("table-1"),[
                "CaseInsensitiveString",
                "CaseInsensitiveString",
                "RE",
                "CaseInsensitiveString",
                "CaseInsensitiveString",
                "Number",
                "DateTimeBR",
                "CaseInsensitiveString"
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
            ob_resul.sort(6, true);
        }
    }	

    $(function(){
        configure_result_table();
    })
</script>
<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_gerados', 'CSV Gerados', FALSE, 'ir_gerado();');
    $abas[] = array('aba_gerado', 'CSV', TRUE, 'location.reload();');
    $abas[] = array('aba_debito_conta', 'Débito em Conta', FALSE, 'ir_debito_conta();');
	
    $head = array( 
		'Ano/Mês',
		'Origem',
		'RE',
		'Nome',
		'Plano',
		'Telefone',
		'Dt. Inclusão',
		'Usuário'
	);

	$body = array();

	foreach($collection as $item)
	{
		$body[] = array(
			$item['dt_referencia'],
			$item['ds_contribuicao_relatorio_origem'],
			$item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia'],
			array($item['ds_nome'], 'text-align:left'),
			$item['ds_plano'],
			$item['ds_telefone'],
			$item['dt_inclusao'],
			$item['ds_usuario']
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo aba_start($abas);
		echo form_start_box('default_box', 'CSV Gerado');
			echo form_default_row('arquivo', 'Arquivo:', anchor(base_url().'up/contribuicao_sms/'.$row['arquivo'], $row['arquivo']));
			echo form_default_row('dt_inclusao', 'Dt. Geração:', $row['dt_inclusao']);
			echo form_default_row('ds_usuario', 'Usuário:', $row['ds_usuario']);
		echo form_end_box('default_box');
			
		echo $grid->render();
		echo br(2);
	echo aba_end();

	$this->load->view('footer_interna');

?>