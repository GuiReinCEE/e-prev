<?PHP
include 'inc/sessao.php';
include 'inc/conexao.php';
include 'inc/ePrev.Service.Projetos.php';

class contribuicao_senge_partial_email
{
	private $service; 
	private $db; 
	private $command;
	private $params;

	public $tudo_ok = true;
	public $inconsistencia_msg = '';

	function __construct($db)
	{
		$this->db = $db;
		$this->service = new service_projetos($db);
		$this->requestParams();
	}
	
	function __destruct()
	{
	}
	
	private function requestParams()
	{
		$this->params = array(

			'tipo'=>$_REQUEST['tipo'],
			'ano'=>$_REQUEST['ano'],
			'mes'=>$_REQUEST['mes'],
			'cd_evento'=>enum_projetos_eventos::SENGE_EMAIL_CONTRIBUICAO

		);
	}

	public function start()
	{
	}

	public function get_emails()
	{
		$service = new service_projetos($this->db);
		$ret = $service->envia_emails__get( $this->params );
		
		return $ret;
	}
}

$esta = new contribuicao_senge_partial_email($db);
$esta->start();
?>

<div style="text-align:center;">

	<br><br>

	<table class="sort-table" id="table-1" align="center" width="100%" cellspacing="2" cellpadding="2">
    	<thead>
		<tr>
			<td><b>Código</b></td>
			<td><b>Data de solicitação</b></td>
			<td><b>Situação</b></td>
			<td><b>De</b></td>
			<td><b>Para</b></td>
			<td><b>Assunto</b></td>
			<td><b>Data de envio</b></td>
			<td><b>Participante</b></td>
		</tr>
    	</thead>
		<tbody>
		<? $collection = $esta->get_emails(); ?>
		<? $item = new entity_projetos_envia_emails_extended() ?>
		<? foreach( $collection as $item ) : ?>
		<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);">
			<td align="center">
				<a href="cad_envia_emails.php?op=A&c=<?= $item->get_cd_email(); ?>&e="><?= $item->get_cd_email(); ?></a>
			</td>
			<td align="center"><?= $item->get_dt_envio(); ?></td>
			<td align="center"><? if($item->dt_retorno!='') echo '<div style="color:red;font-weight:bold;">retornou</div>'; else if($item->get_dt_email_enviado()!='') echo 'enviado'; else echo 'aguarda envio'; ?></td>
			<td align="center"><?= $item->get_de(); ?></td>
			<td align="center"><?= $item->get_para(); ?></td>
			<td align="center"><?= $item->get_assunto(); ?></td>
			<td align="center"><?= $item->get_dt_email_enviado(); ?></td>
			<td align="center"><?= $item->get_cd_empresa().'/'.$item->get_cd_registro_empregado().'/'.$item->get_seq_dependencia(); ?></td>
		</tr>
		<? endforeach; ?>
	</tbody>
	</table>
	
</div>