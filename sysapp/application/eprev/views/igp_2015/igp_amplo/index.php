<?php
set_title("IGP AMPLO");
$this->load->view("header");
?>

		<script src="<?= base_url() ?>js/jquery-plugins/flot/jquery.flot.js" type="text/javascript"></script>
		<script src="<?= base_url() ?>js/jquery-plugins/flot/jquery.flot.symbol.js" type="text/javascript"></script>
		<script src="<?= base_url() ?>js/jquery-plugins/flot/jquery.flot.navigate.js" type="text/javascript"></script>
		<script src="<?= base_url() ?>js/jquery-plugins/flot/jquery.flot.resize.min.js" type="text/javascript"></script>
		<script src="<?= base_url() ?>js/jquery-plugins/flot/jquery.flot.pie.js" type="text/javascript"></script>

<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
				
		$.post('<?= site_url("igp_2015/igp_amplo/listar") ?>',
		$("#filter_bar_form").serialize(),
		function(data)
		{
			$("#result_div").html(data);
		});	
	}

	$(function(){
		filtrar();
		
		if($("#nr_ano").val() == "")
		{
			var d = new Date();
			var n = d.getFullYear();			
			$("#nr_ano").val(n);
		}
	});
</script>
<?php
$abas[] = array("aba_lista", "Lista", TRUE, "location.reload();");

$ar_mes[] = array('text' => 'Janeiro',   'value' => 1);
$ar_mes[] = array('text' => 'Fevereiro', 'value' => 2);
$ar_mes[] = array('text' => 'Março',     'value' => 3);
$ar_mes[] = array('text' => 'Abril',     'value' => 4);
$ar_mes[] = array('text' => 'Maio',      'value' => 5);
$ar_mes[] = array('text' => 'Junho',     'value' => 6);
$ar_mes[] = array('text' => 'Julho',     'value' => 7);
$ar_mes[] = array('text' => 'Agosto',    'value' => 8);
$ar_mes[] = array('text' => 'Setembro',  'value' => 9);
$ar_mes[] = array('text' => 'Outubro',   'value' => 10);
$ar_mes[] = array('text' => 'Novembro',  'value' => 11);
$ar_mes[] = array('text' => 'Dezembro',  'value' => 12);

$ar_mes_chk = array(1,2,3,4,5,6,7,8,9,10,11,12);

echo aba_start( $abas );
	echo form_list_command_bar();
	echo form_start_box_filter(); 

		echo filter_integer('nr_ano', 'Ano:(*)');
		echo form_default_checkbox_group('ar_mes', 'Meses:(*)', $ar_mes, $ar_mes_chk, 100);

    echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br(10);
echo aba_end();
$this->load->view('footer');
?>