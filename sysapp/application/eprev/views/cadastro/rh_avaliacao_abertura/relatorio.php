<?php
	set_title('Sistema de Avaliação - Abertura');
	$this->load->view('header');
?>
<script>
	function ir_lista()
	{
		location.href = "<?= site_url('cadastro/rh_avaliacao_abertura') ?>";
	}

	function ir_cadastro()
	{
		location.href = "<?= site_url('cadastro/rh_avaliacao_abertura/cadastro/'.$cd_avaliacao) ?>";
	}

	function ir_avaliacao()
	{
		location.href = "<?= site_url('cadastro/rh_avaliacao_abertura/avaliacao/'.$cd_avaliacao) ?>";
	}

	function ir_pdi()
	{
		location.href = "<?= site_url('cadastro/rh_avaliacao_abertura/relatorio_pdi/'.$cd_avaliacao) ?>";
	}

	function gerar_pdf(cd_avaliacao_usuario)
	{
		window.open("<?= site_url('cadastro/rh_avaliacao/formulario_pdf') ?>"+ "/" + cd_avaliacao_usuario);
	}

	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
			
		$.post("<?= site_url('cadastro/rh_avaliacao_abertura/listar_relatorio') ?>",
		{
			cd_avaliacao : '<?= $cd_avaliacao ?>',
			ds_cargo     : $("#ds_cargo").val(),
			cd_gerencia  : $("#cd_gerencia").val()
		},
		function(data)
		{
			$("#result_div").html(data);
		});
	}

    $(function(){
    	filtrar();
	});
</script>
<style>
	div.quadrado_matriz {
		width:110px;
		height:110px;
		border: 1px solid #000;
		float:left;
		line-height:15px;
		text-align: center;
		margin: 4px 4px 4px 4px;
		z-index:1;
	}

	div.quadrado_matriz.span {
		padding: 1px;
	}

	div.circulo_resultado{
		background:green;
		color:#fff;
		width:30px;
		height:30px;
		line-height:30px;
		vertical-align:middle;
		text-align:center;
		z-index: 2;
		border-radius:50%;
		-moz-border-radius:50%;
		-webkit-border-radius:50%;
		position: absolute;
		border:1px solid #000;
		font-weight: bold;
		font-size: 15px;
	}

	.texto_grupo {
        font-size: 20px;
        font-weight: bold;
        color:#1E1E1E;
    }

    @media print {
		div.circulo_resultado{
			color:#000;
		}

		span.span_matriz {
			color:#000 !important; 
		}	
	}
</style>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
	$abas[] = array('aba_avaliacao', 'Avaliações', FALSE, 'ir_avaliacao();');
	$abas[] = array('aba_relatorio', 'Relatório', TRUE, 'location.reload();');
	$abas[] = array('aba_relatorio_pdi', 'PDI', FALSE, 'ir_pdi();');

	$dropdown = array(
		array('value' => 'N', 'text' => 'Não'),
		array('value' => 'S', 'text' => 'Sim')
	);

	echo aba_start($abas); 
		echo form_list_command_bar(array());
		echo form_start_box_filter();
			echo filter_dropdown('ds_cargo', 'Cargo:', $cargo);
			echo filter_dropdown('cd_gerencia', 'Gerência:', $gerencia);
	    echo form_end_box_filter();
	    echo form_start_box('default_box', 'Cadastro');
	        echo form_default_row('', 'Ano:', '<span class="label label-inverse">'.$row['nr_ano_avaliacao'].'</span>');
	        echo form_default_row('', 'Dt. Ínicio:', $row['dt_inicio']);
	        
	        echo form_default_row('', 'Dt. Encerramento:', $row['dt_encerramento']);
	        if(trim($row['dt_envio_email']) != '')
	        {
	            echo form_default_row('', 'Dt. Envio:', $row['dt_envio_email']);
	            echo form_default_row('', 'Usuário Envio:', $row['ds_usuario_envio_email']);
	        }

	        echo form_default_row('', 'Total de Avaliações:', '<span class="badge badge-warning">'.$row['qt_avaliacao'].'</span>');
	        echo form_default_row('', 'Total de Avaliações Encerradas:', '<span class="badge badge-success">'.$row['qt_avaliacao_encerrada'].'</span>');

	        $nr_media = 0;

	        foreach ($somatorio as $key => $item) 
	        {
	        	echo form_default_row('', $item['ds_grupo'].' (total):', '<span class="label label-info">'.number_format($item['nr_totalizador'], 2, ',', '.').'</span>');
	        	echo form_default_row('', $item['ds_grupo'].' (média):', '<span class="label">'.number_format($item['nr_media'], 2, ',', '.').'</span>');

	        	$nr_media += $item['nr_media'];
	        }

	        echo form_default_row('', 'Média Geral:', '<span class="label label-success">'.number_format(($nr_media/2), 2, ',', '.').'</span>');
	    echo form_end_box('default_box');

	echo br();
	echo '<div id="result_div"></div>';
	echo br(2);
	echo aba_end();

	$this->load->view('footer_interna');
?>