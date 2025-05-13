<?php
class agenda extends Controller
{
	function __construct()
	{
		parent::Controller();
	}

	function index($agenda_id=0)
	{
		$row['agenda_id']=intval($agenda_id);

		$this->load->model('Agenda_model');
		$this->Agenda_model->carregar( $row, usuario_id() );

		$grupos = $this->Agenda_model->grupos_listar( usuario_id() );
		$data['grupos'] = $grupos;

		// var_dump($grupos);exit;

		$data['row']=$row;
		$data['edicao'] = (intval($row['agenda_id'])>0);
		$this->load->view( 'agenda/index', $data );
	}

	function salvar()
	{
		$this->load->model('Agenda_model');

		// Agendar
		$data['assunto']=$this->input->post('assunto');
		$data['local']=$this->input->post('local');
		$data['descricao']=$this->input->post('descricao');
		$data['data']=$this->input->post('data');
		$data['hora']=$this->input->post('hora');
		$id = $this->Agenda_model->salvar( $data, usuario_id() );

		// Membros
		$membros = $this->input->post('convites');
		$ret = $this->Agenda_model->membros_salvar( $membros, usuario_id() );

		// redirect( 'agenda/index/'.$id,'refresh' );
	}

	function listar_membros()
	{
		$o = "";
		$this->load->model('Agenda_model');

		$agenda_id = intval($this->input->post('agenda_id'));

		$agenda['agenda_id'] = intval($agenda_id);
		$this->Agenda_model->carregar( $agenda, usuario_id() );
		$membros=$agenda['membros'];

		$o.="<table align='right'>";
		$id=0;
		$confirmaram=0;$repunaram=0;$sem_resposta=0;
		foreach( $membros as $membro )
		{
			$id++;

			if($membro['resposta']=='s')
			{
				$background='#CAFFCA';
				$color='green';
				$confirmaram++;
			}
			elseif($membro['resposta']=='n')
			{
				$background='#FFEAEA';
				$color='red';
				$repunaram++;
			}
			else
			{
				$background='';
				$color='';
				$sem_resposta++;
			}

			$o.="<tr>";
			$o.="<td><input value='".$membro['apelido']."' style='width:70px;border-style:solid;background:$background;color:$color;' /></td>";
			$o.="<td><input value='".$membro['email']."' style='width:200px;border-style:solid;background:$background;color:$color;' /></td>";

			$o.="<td nowrap>
				<input type='button' style='background:green;color:white;width:50px;' value='Dentro' 
				/><input type='button' style='background:red;color:white;width:50px;' value='Fora' />
			</td>";

			$o.="<td><input value='".$membro['comentario']."' style='width:100px;border-style:solid;background:$background;color:$color;' /></td>";

			$o.="<td><span id='status_$id' style='font-size:12;color:#999999;margin-left:10px;'></span></td>";
			$o .="</tr>";
		}
		$o.="</table>";

		echo $o;
	}

	function listar_membros_grupo()
	{
		$this->load->model('Agenda_model');
		$grupo_id=$this->input->post('grupo_id');

		echo $this->Agenda_model->membros_grupo_consultar( intval($grupo_id), usuario_id() );
	}
}