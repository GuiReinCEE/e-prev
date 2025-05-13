<?php 
set_title('Torcida - Estrutura da Enquete');
$this->load->view('header'); 
?>
<script>
	function ir_lista()
	{
		location.href='<?php echo site_url( 'ecrm/ri_torcida_enquete' ); ?>';
	}

	function ir_cadastro()
	{
		location.href="<?php echo site_url( 'ecrm/ri_torcida_enquete/detalhe/'.$cd_enquete ); ?>";
	}

	function ir_estrutura()
	{
		location.href="<?php echo site_url( 'ecrm/ri_torcida_enquete/estrutura/'.$cd_enquete ); ?>";
	}

	function ir_resultado()
	{
		location.reload();
	}
	
	function filtrar(f)
	{
		f.submit();
	}
</script>
<?php
$abas[] = array('aba_lista', 'Lista', false, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', false, 'ir_cadastro();');
$abas[] = array('aba_estrutura', 'Estrutura', false, 'ir_estrutura();');
$abas[] = array('aba_resultado', 'Resultado', true, 'ir_resultado();');
echo aba_start( $abas );

echo form_open( 'ecrm/ri_torcida_enquete/resultado/'.intval($cd_enquete) );
echo form_hidden( 'cd_enquete', intval($cd_enquete) );

$total=0;
foreach( $resposta as $item ) {$total+=$item['qt_item'];}

$head = array( 'Resposta', 'Quantidade', 'Percentual' );
$body=array();
foreach( $resposta as $item )
{
	$percentual=round( ( $item['qt_item']*100 )/$total, 2 ); 
	$body[] = array( array($item['ds_item'], 'text-align:left;'), array($item['qt_item'],'text-align:right;','int'), array($percentual.'%','text-align:right;') ); 
}

$this->load->helper('grid');
$grid = new grid();
$grid->view_count=false;
$grid->head = $head;
$grid->body = $body;

echo "<big><b>$ds_pergunta<b></big>".br();
echo 
	form_start_box( "default_box", "Filtros" )
	.form_default_date_interval('periodo_inicio', 'periodo_fim', 'Período',$periodo_inicio,$periodo_fim)
	.form_default_dropdown('origem', 'Origem', array(
		array('text'=>'Todos', 'value'=>'todos')
		, array('text'=>'Interna', 'value'=>'interna')
		, array('text'=>'Externa', 'value'=>'externa')
	), array($origem))
	.form_default_row('','',comando('filtrar_btn','Filtrar', 'filtrar(this.form);'))
	.form_end_box('default_box');
echo form_close();

echo 
	form_start_box( "default_box", "Respostas", false )
		.$grid->render()
	.form_end_box( "default_box", false );
?>
<script>
	$('#ds_item').focus();
</script>
<?php
echo aba_end();
echo form_close();
$this->load->view('footer_interna');
?>
