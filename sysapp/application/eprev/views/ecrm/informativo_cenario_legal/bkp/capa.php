<?php
set_title('Cenário Legal');
$this->load->view('header');
?>
<script>
	function ir_ponto_vista()
	{
		location.href='<?php echo site_url("ecrm/informativo_cenario_legal/ponto_vista/".$cd_edicao); ?>';
	}
	
	function ir_legislacao()
	{
		location.href='<?php echo site_url("ecrm/informativo_cenario_legal/legislacao/".$cd_edicao); ?>';
	}
	
	function ir_agenda()
	{
		location.href='<?php echo site_url("ecrm/informativo_cenario_legal/agenda/".$cd_edicao); ?>';
	}
	
	function ir_anterioes()
	{
		location.href='<?php echo site_url("ecrm/informativo_cenario_legal/edicoes/".$cd_edicao); ?>';
	}
</script>
<style>
	.lbl {
		font-family: Verdana, Arial, Helvetica, sans-serif;
	}
	
</style>
<?php
$abas[] = array('aba_lista', 'Capa', TRUE, 'location.reload();');
$abas[] = array('aba_lista', 'Ponto de Vista', false, 'ir_ponto_vista();');
$abas[] = array('aba_lista', 'Legislação na Íntegra', false, 'ir_legislacao();');
$abas[] = array('aba_lista', 'Agenda', false, 'ir_agenda();');
$abas[] = array('aba_lista', 'Edições Anteriores', false, 'ir_anterioes();');

$config['button'][] = array('Nova Edição do Cenário Legal', 'novo()');

echo aba_start( $abas );
	echo '<div style="font-size:12pt; font-weight:bold; padding-left:25px; padding-bottom:20px;">Edição nº '.$cd_edicao.br().$tit_capa.'</div>';
	?>
	<table width="98%" border="0" cellspacing="0" cellpadding="0">
		<tr> 
			<td>&nbsp;&nbsp;&nbsp;</td>
			<td valign="top"> 
				<h2 style="margin: 0px; padding-top: 5px; padding-bottom: 5px; color: #0046AD; font-family: calibri, arial; font-size: 18pt;">
					Gerência  de Controladoria
				</h2>
				<br/>
				<label class="lbl">Missão:<br/><br/>
					<?php echo trim($texto_capa); ?>
				</label>
			</td>
			<td valign="top"> 
			<table  border="0" align="right" class="menu_intranet_gerencia" >
				<tr>
					<td style="padding-top: 5px; padding-left: 10px; width:400px;"> 
						<h1 style="font-size: 18pt; font-family: calibri, arial; padding-left: 50px; padding-bottom: 5px; color:#0046AD;">
							Veja também nesta edição: 
						</h1>
							<?php
							foreach($collection as $item)
							{
								echo '<p>';
								echo anchor( site_url("ecrm/informativo_cenario_legal/legislacao/".$cd_edicao."/".$item['cd_cenario']),  img(base_url().'img/intranet/work_seta.png').' '.$item['titulo'], array('style' => 'font-size: 13pt; font-family: calibri, arial; font-weight:bold;') );
								echo '</p>';
								echo '
									<div style="padding-left:20px;">
										<label class="lbl" >'.$item['referencia'].'</label>'.br().'
									    <label>Área indicada:<label> 
										<label class="lbl" style="font-weight:bold;">'.$item['area'].'</label>
									</div>';
								echo br();
							}
							?>
						</h1>
					</td>
				</tr> 
			</table>
		</td> 
		</tr>
	</table>
	<?php
	#
	#echo '<pre>';
	#print_r($collection);

	echo br(2);
echo aba_end(); 

$this->load->view('footer');
?>