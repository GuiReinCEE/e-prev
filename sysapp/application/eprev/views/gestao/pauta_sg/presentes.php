<?php
    set_title('Pauta SG - Presentes');
	$this->load->view("header");
?>
<script>
	function ir_lista()
	{
		location.href = "<?= site_url('gestao/pauta_sg') ?>";
	}

	function ir_cadastro()
	{
		location.href = "<?= site_url('gestao/pauta_sg/cadastro/'.$row['cd_pauta_sg']) ?>";
	}

	function ir_assunto()
	{
		location.href = "<?= site_url('gestao/pauta_sg/assunto/'.$row['cd_pauta_sg']) ?>";
	}

    function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
		    null,
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "CaseInsensitiveString"
		]);
		ob_resul.onsort = function ()
		{
			var rows = ob_resul.tBody.rows;
			var l = rows.length;
			for (var i = 0; i < l; i++)
			{
				removeClassName(rows[i], i % 2 ? "sort-par" : "sort-impar");
				addClassName(rows[i], i % 2 ? "sort-impar" : "sort-par");
			}
		};
		ob_resul.sort(0, false);
	}

    function check_item(t)
    {
        salvar_item(t.val(), t.is(':checked') ? 'S' : 'N');
    }

    function salvar_item(cd_pauta_sg_integrante_presente, fl_checked)
    {
        $.post("<?= site_url('gestao/pauta_sg/salvar_presente') ?>",
        {
            cd_pauta_sg_integrante_presente : cd_pauta_sg_integrante_presente,
            fl_salvar                       : fl_checked
        },
        function(data){
            
        });
    }

    function salvar_presidente(cd_pauta_sg_integrante_presente)
    {
    	var fl_presidente = $("#fl_presidente_"+cd_pauta_sg_integrante_presente).val();

    	$.post("<?= site_url('gestao/pauta_sg/salvar_presidente') ?>",
        {
            cd_pauta_sg_integrante_presente : cd_pauta_sg_integrante_presente,
            fl_presidente                   : fl_presidente
        },
        function(data){
            
        });
    }

    function atualziar_presentes()
    {
    	location.href = "<?= site_url('gestao/pauta_sg/atualizar_integrante/'.$row['cd_pauta_sg']).'/'.$row['fl_sumula'] ?>";
    }

    $(function(){
    	configure_result_table();
    });
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
	$abas[] = array('aba_assunto', 'Assuntos', FALSE, 'ir_assunto();');
	$abas[] = array('aba_presentes', 'Presentes', TRUE, 'location.reload();');

	$head = array(
		'Presente',
		'Nome',
		'Tipo',
		'Suplente do Titular',
		'Presidente',
        'Secretária',
        'Indicado/Eleito'
	);

	$body = array();

	foreach ($collection as $key => $item)
	{
		$campo_check = array(
            'name'     => 'cd_pauta_sg_integrante_presente_'.$item['cd_pauta_sg_integrante_presente'],
            'id'       => 'cd_pauta_sg_integrante_presente_'.$item['cd_pauta_sg_integrante_presente'],
            'value'    => $item['cd_pauta_sg_integrante_presente'],
            'checked'  => ($item['fl_presente'] == 'S' ? TRUE : FALSE),
            'onchange' => 'check_item($(this))'   
        ); 

        if(trim($row['dt_aprovacao']) == '')
        {
        	$dropdown = form_dropdown('fl_presidente_'.$item['cd_pauta_sg_integrante_presente'], array('S'=> 'Sim', 'N' => 'Não'), array($item['fl_presidente']), 'onchange="salvar_presidente('.$item['cd_pauta_sg_integrante_presente'].')"');
        }
        else
        {
        	$dropdown = $item['ds_presidente'];
        }

		$body[] = array(
			form_checkbox($campo_check),
			array($item['ds_pauta_sg_integrante_presente'], 'text-align:left'),
			(trim($item['fl_secretaria']) == 'N' ? array($item['ds_tipo'], 'text-align:left') : ""),
			array($item['ds_pauta_sg_integrante_presente_titular'], 'text-align:left'),
			$item['ds_presidente'],   //$dropdown,
			$item['ds_secretaria'],
			$item['ds_indicado_eleito']
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	if(trim($row['dt_aprovacao']) != '')
	{
		$grid->col_oculta = array(0);
	}

	echo aba_start($abas);
		echo form_start_box('default_box', 'Pauta');
			echo form_default_row('nr_ata', 'Nº da Ata:', '<label class="label label-inverse">'.$row['nr_ata'].'</label>');
			echo form_default_row('fl_sumula', 'Colegiado:', '<span class="'.$row['class_sumula'].'">'.$row['fl_sumula'].'</span>');
			
			echo form_default_row('link_pauta', 'Link para envio:', 'https://www.fundacaoceee.com.br/link/?p='.$row['cd_pauta_sg_md5']);	

			if(trim($row['ds_tipo_reuniao']) != '')
			{
				echo form_default_row('ds_tipo_reuniao', 'Tipo Reunião:', $row['ds_tipo_reuniao']);
			}

			echo form_default_row('local', 'Local:', $row['local']);

			echo form_default_row('dt_reuniao', 'Dt. Reunião:', $row['dt_pauta'].' '.$row['hr_pauta']);

			if(trim($row['dt_pauta_sg_fim']) != '')
			{	
				echo form_default_row('dt_reuniao_fim', 'Dt. Reunião Encerramento:', $row['dt_pauta_sg_fim'].' '.$row['hr_pauta_sg_fim']);
			}

			if(trim($row['dt_envio_responsavel']) != '')
			{
				echo form_default_row('dt_envio_responsavel', 'Dt. Envio Responsáveis:', $row['dt_envio_responsavel']);
				echo form_default_row('ds_usuario_envio_responsavel', 'Usuário:', $row['ds_usuario_envio_responsavel']);
			}
			
			if(trim($row['dt_aprovacao']) != '')
			{
				echo form_default_row('dt_aprovacao', 'Dt. Encerramento:', $row['dt_aprovacao']);
				echo form_default_row('ds_usuario_aprovacao', 'Usuário:', $row['ds_usuario_aprovacao']);
			}

		echo form_end_box('default_box');
		echo br();
		echo form_command_bar_detail_start(); 
		echo button_save('Atulizar Presentes', 'atualziar_presentes()', 'botao_verde');
		echo form_command_bar_detail_end();
		echo br();
		
		echo $grid->render();
		
		echo br(2);
	echo aba_end();
	$this->load->view('footer');
?>