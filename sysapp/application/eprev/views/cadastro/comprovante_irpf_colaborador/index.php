<?php
set_title('Comprovante IRPF - Colaborador');
$this->load->view('header');
?>
<link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet" type="text/css">
<link href="https://fonts.googleapis.com/css?family=Oxygen+Mono" rel="stylesheet" type="text/css">
<style>
	.box-extrato-content a{
		font-weight: normal;
		text-decoration:none;
	}

	.box-extrato-content {
		float:left;
		margin-top: 15px;
		margin-right: 15px;
		width: 195px;
		background: none repeat scroll 0 0 white;
		border: 1px solid #DDDDDD;
		box-shadow: 0 1px 3px rgba(0, 0, 0, 0.055);
		display: block;
		padding: 10px;
		
		color: #333333;
		font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
		font-size: 14px;
		line-height: 20px;						
	}
	
	.box-extrato-content small{
		color: #333333;
		font-family: "Oxygen Mono", "Helvetica Neue",Helvetica,Arial,sans-serif;
		font-size: 12px;
		line-height: 20px;
		font-weight: normal;
	}					
	
	.box-extrato-statistic {
		background-color: white;
		padding: 5px 10px;
		position: relative;
	}	
	
	.box-extrato-statistic .title-extrato {
		margin: 0px;
		line-height: 28px;
	}
	
	.text-extrato-success {
		font-family: Montserrat;
		font-weight: 400;					
		font-size: 18px;
		color: #134E84 !important;
	}					
</style>
<script>
	function filtrar()
	{
		$("#result_div").html("<?php echo loader_html(); ?>");
			
		$.post('<?php echo site_url('cadastro/comprovante_irpf_colaborador/listar');?>',
		$("#filter_bar_form").serialize(),
		function(data)
		{
			$("#result_div").html(data);
		});
	}

	$(function(){
		filtrar();
	})
</script>
<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

echo aba_start($abas);
    echo (form_list_command_bar());
    echo form_start_box_filter();
		if(trim($this->session->userdata('indic_04')) == "*")
		{
			echo filter_integer('cd_coladorador', 'RE Colaborador:');
		}
		
        echo filter_integer('nr_ano_exercicio', 'Ano Exercício:');
        echo filter_integer('nr_ano_calendario', 'Ano Calendário:');
    echo form_end_box_filter();
	echo '<div id="result_div" style="width: 100%;"></div>';
	echo br(5);
echo aba_end();
$this->load->view('footer'); 
?>