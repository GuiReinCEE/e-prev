<?php
set_title('Cenário Legal');
$this->load->view('header');
?>
<script>
	function ir_capa()
	{
		location.href='<?php echo site_url("ecrm/informativo_cenario_legal/capa/".$cd_edicao); ?>';
	}
	
	function ir_ponto_vista()
	{
		location.href='<?php echo site_url("ecrm/informativo_cenario_legal/ponto_vista/".$cd_edicao); ?>';
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
$abas[] = array('aba_lista', 'Capa', false, 'ir_capa();');
$abas[] = array('aba_lista', 'Ponto de Vista', false, 'ir_ponto_vista();' );
$abas[] = array('aba_lista', 'Legislação na Íntegra', true, 'location.reload();');
$abas[] = array('aba_lista', 'Agenda', false, 'ir_agenda();');
$abas[] = array('aba_lista', 'Edições Anteriores', false, 'ir_anterioes();');

$head = array('Arquivos');
$body = array();

foreach( $collection_anexo as $item )
{
	$body[] = array(
		array(anchor(base_url().'up/cenario/' . $item['arquivo'],$item['arquivo_nome'], array('target' => '_black') ),'text-align:left')
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->view_count = false;
$grid->head = $head;
$grid->body = $body;

echo aba_start( $abas );
	if((count($row) > 0) AND isset($row['conteudo']) AND isset($row['titulo']))
	{
	echo '<div style="font-size:12pt; font-weight:bold; padding-left:25px; padding-bottom:20px;">Edição nº '.$cd_edicao.br().$tit_capa.'</div>';
	?>
	<table width="98%" border="0" cellspacing="0" cellpadding="0">
		<tr> 
			<td>&nbsp;&nbsp;&nbsp;</td>
			<td valign="top" style="width:55%;"> 
			<?php
				echo '
					<h2 style="margin: 0px; padding-top: 5px; padding-bottom: 5px; color: #0046AD; font-family: calibri, arial; font-size: 16pt;">
						'.trim($row['titulo']).'
					</h2>'.br().'
					<label style="font-weight:bold;">'.trim($row['referencia']).'</label>'.br().'
					<label>Fonte: '.trim($row['fonte']).br().'
					Área indicada: '.trim($row['area']).br().'</label>'.br(2).
					$row['conteudo'].br(2).'
					<label style="font-weight:bold;">links relacionados:</label> '.br(2).
					(trim($row['link1']) != '' ? '<a href="'.$row['link1'].'">'.$row['link1'].'</a>'.br() : '').
					(trim($row['link2']) != '' ? '<a href="'.$row['link2'].'">'.$row['link2'].'</a>'.br() : '').
					(trim($row['link3']) != '' ? '<a href="'.$row['link3'].'">'.$row['link3'].'</a>'.br() : '').
					(trim($row['link4']) != '' ? '<a href="'.$row['link4'].'">'.$row['link4'].'</a>'.br() : '').
					'<center><label>Atualizado em '.$row['data_inc'].'</label></center>'.br(2);
					if(count($collection_anexo) > 0)
					{
						echo $grid->render();
					}
					#echo '<pre>';
	                #print_r($row);
				?>
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
	}
	#
	#echo '<pre>';
	#print_r($collection);

	echo br(2);
echo aba_end(); 

$this->load->view('footer');
?>