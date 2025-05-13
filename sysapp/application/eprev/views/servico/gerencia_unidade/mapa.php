<?php
set_title('Gerência - Unidade');
$this->load->view('header');
?>
<script>
	function gerar_pdf()
	{
		window.open("<?=site_url("servico/gerencia_unidade/pdf") ?>");
	}

	function ir_lista()
    {
        location.href = "<?= site_url('servico/gerencia_unidade')?>";
    }
</script>
<style>
	#menu 
	{
		text-align:left;
		float:left;
		padding: 25px;
		margin: 20px;
		display:inline-block;
		color:#1E90FF
	}

	#menu ul 
	{
		color:#1E90FF
	}

	#menu ul li 
	{
		text-align:left;
	}

	main 
	{
		position: relative;
		height: 100%;
		margin: 5px;
		width: 100%;
	}

	main div
	{
		position: absolute;
		top: 8%;
		margin: 5%
		padding: 25px;
	}
</style>
<?php
	$abas[] = array('aba_lista', 'Lista', false, 'ir_lista();');
	$abas[] = array('aba_mapa', 'Mapa', true, 'location.reload();');
	$config['button'][] = array('PDF', 'gerar_pdf();');
?>
<?= aba_start($abas) ?>
<?= form_list_command_bar($config)?>
<main >
<div>
	<?php foreach($collection as $item) { ?>
		<ul id="menu" >
	        <li>
	        	<p style="font-weight:bold; color:#0000FF"><?php echo $item["ds_gerencia"] ?></p>
	        	<ul>
	           	<li><b><?php echo (trim($item['ds_gerente']) != '' ? 'Gerente: '.$item['ds_gerente'].br() : '')?></b></li> 
	           	<li><b><?php echo (count($item['supervisor']) > 0 ? 'Supervisor: '.br().implode(br(), $item['supervisor']).br() : '')?></b></li>
	           	<ul>
	           		<li><?php echo (count($item['usuario']) > 0 ? implode(br(), $item['usuario']).br() : '')?></li>
	           		<ul>
	           		    <li><?php echo (count($item['unidade']) > 0 ? implode(br(), $item['unidade']['usuario_unidade']) : '')?></li>
					</ul>
		        </ul>
		        </ul>
		    </li>
	    </ul>	
	<?php } ?>
</div>		      
</main>
<?= aba_end()?>
<?= form_command_bar_detail_end()?>	      
<?php $this->load->view('footer_interna'); ?>