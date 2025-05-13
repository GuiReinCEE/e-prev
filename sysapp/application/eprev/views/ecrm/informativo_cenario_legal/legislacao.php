<?php
	set_title('Cenário Legal');
	$this->load->view('header');
?>
<script>
	function ir_capa()
	{
		location.href = "<?= site_url('ecrm/informativo_cenario_legal/capa/'.$edicao['cd_edicao']) ?>";
	}
	
	function ir_ponto_vista()
	{
		location.href = "<?= site_url('ecrm/informativo_cenario_legal/ponto_vista/'.$edicao['cd_edicao']) ?>";
	}
	
	function ir_agenda()
	{
		location.href = "<?= site_url('ecrm/informativo_cenario_legal/agenda/'.$edicao['cd_edicao']) ?>";
	}
	
	function ir_anterioes()
	{
		location.href = "<?= site_url('ecrm/informativo_cenario_legal/edicoes/'.$edicao['cd_edicao']) ?>";
	}
</script>
<style>
	.lbl {
		font-family: Verdana, Arial, Helvetica, sans-serif;
	}
</style>
<?php
	$abas[] = array('aba_capa', 'Capa', FALSE, 'ir_capa();');
	$abas[] = array('aba_ponto_vist', 'Ponto de Vista', FALSE, 'ir_ponto_vista();');
	$abas[] = array('aba_legislacao', 'Legislação na Íntegra', TRUE, 'location.reload();');
	$abas[] = array('aba_agenda', 'Agenda', FALSE, 'ir_agenda();');
	$abas[] = array('aba_edicoes', 'Edições Anteriores', FALSE, 'ir_anterioes();');

	$head = array('Arquivos');
	$body = array();

	foreach($collection_anexo as $item)
	{
		$body[] = array(
			array(anchor(base_url().'up/cenario/'.$item['arquivo'] ,$item['arquivo_nome'], array('target' => '_black')), 'text-align:left')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->view_count = false;
	$grid->head = $head;
	$grid->body = $body;

	echo aba_start($abas);
	?>
		
		<div style="font-size:12pt; font-weight:bold; padding-left:25px; padding-bottom:20px;">Edição nº <?= $edicao['cd_edicao'].' - '.$edicao['tit_capa'] ?></div>
		<?php if(count($legislacao) > 0): ?>
			<?php if(trim($legislacao['dt_cancelamento']) != ''): ?>
			<div style="font-size:12pt; font-weight:bold; padding-left:25px; padding-bottom:20px; color:red;">Legislação Cancelada nº <?= $legislacao['dt_cancelamento'] ?></div>
			<?php endif; ?>
			<table width="98%" border="0" cellspacing="0" cellpadding="0">
				<tr> 
					<td>&nbsp;&nbsp;&nbsp;</td>
					<td valign="top" style="width:55%;"> 
						<h2 style="margin: 0px; padding-top: 5px; padding-bottom: 5px; color: #0046AD; font-family: calibri, arial; font-size: 16pt;">
							<?= trim($legislacao['titulo']) ?>
						</h2>
						<br/>
						<label style="font-weight:bold;">
							<?= trim($legislacao['referencia']) ?>	
						</label>
						<br/>
						<label>
							Fonte: <?= trim($legislacao['fonte']) ?> 
							<br/><br/>
							Área indicada: <?= trim($legislacao['gerencia']) ?> 
							<br/>
						</label>
						<br/><br/>
						<?= trim($legislacao['conteudo']) ?>
						<br/><br/>
						<label style="font-weight:bold;">links relacionados:</label><br/><br/>
						<?= (trim($legislacao['link1']) != '' ? '<a href="'.$legislacao['link1'].'">'.$legislacao['link1'].'</a>'.br() : '') ?>
						<?= (trim($legislacao['link2']) != '' ? '<a href="'.$legislacao['link2'].'">'.$legislacao['link2'].'</a>'.br() : '') ?>
						<?= (trim($legislacao['link3']) != '' ? '<a href="'.$legislacao['link3'].'">'.$legislacao['link3'].'</a>'.br() : '') ?>
						<?= (trim($legislacao['link4']) != '' ? '<a href="'.$legislacao['link4'].'">'.$legislacao['link4'].'</a>'.br() : '') ?>
						<center><label>Atualizado em <?= $legislacao['data_inc'] ?></label></center>
						<br/><br/>
	
						<?php if(count($collection_anexo) > 0): ?>
							<?= $grid->render() ?>
						<?php endif; ?>
					</td>
					<td valign="top"> 
						<table  border="0" align="right" class="menu_intranet_gerencia" >
							<tr>
								<td style="padding-top: 5px; padding-left: 10px; width:400px;"> 
									<h1 style="font-size: 18pt; font-family: calibri, arial; padding-left: 50px; padding-bottom: 5px; color:#0046AD;">
										Veja também nesta edição: 
									</h1>
										<?php foreach($collection as $item): ?>
											<p>
											<?= anchor( 
													site_url('ecrm/informativo_cenario_legal/legislacao/'.$edicao['cd_edicao'].'/'.$item['cd_cenario']),  
													img(base_url().'img/intranet/work_seta.png').' '.$item['titulo'], 
													array('style' => 'font-size: 13pt; font-family: calibri, arial; font-weight:bold;') 
												) ?>
											</p>
											<div style="padding-left:20px;">
												<label class="lbl"><?= $item['referencia'] ?></label><br/>
											    <label>Área indicada:<label> 
												<label class="lbl" style="font-weight:bold;"><?= $item['gerencia'] ?></label>
											</div>
											<br/>
										<? endforeach; ?>
									</h1>
								</td>
							</tr> 
						</table>
					</td> 
				</tr>
			</table>
		<?php endif; ?>
		<?php
		echo br(2);
	echo aba_end(); 

	$this->load->view('footer');
?>