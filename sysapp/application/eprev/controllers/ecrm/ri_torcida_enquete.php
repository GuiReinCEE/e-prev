<?php
class ri_torcida_enquete extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index()
    {
		if(CheckLogin())
		{
	        $this->load->view('ecrm/ri_torcida_enquete/index.php');
		}
    }

    function listar()
    {
        if(CheckLogin())
        {
	        $this->load->model('torcida/Enquete_model');

	        $data['collection'] = array();
	        $result = null;

	        // --------------------------
			// filtros ...

			$args=array();

			manter_filtros($args);

			// --------------------------
			// listar ...

	        $this->Enquete_model->listar( $result, $args );

			$data['collection'] = $result->result_array();

	        if( $result )
	        {
	            $data['collection'] = $result->result_array();
	        }

	        // --------------------------

	        $this->load->view('ecrm/ri_torcida_enquete/partial_result', $data);
        }
    }

	function detalhe($cd=0)
	{
		if(CheckLogin())
		{
			$this->load->model('torcida/Enquete_model');
			$row=$this->Enquete_model->carregar( $cd );
			if($row){ $data['row'] = $row; }
			$this->load->view('ecrm/ri_torcida_enquete/detalhe', $data);
		}
	}

	function salvar()
	{
		if(CheckLogin())
		{
			$this->load->model('torcida/Enquete_model');

			$args['cd_enquete']=intval($this->input->post('cd_enquete', false));

			$args["nome"] = $this->input->post("nome", false);
			$args["dt_inicio"] = $this->input->post("dt_inicio", false);
			$args["cd_usuario_inclusao"] = usuario_id();
			$args["cd_enquete"] = $this->input->post("cd_enquete", false);

			$msg=array();
			$retorno = $this->Enquete_model->salvar( $args,$msg );

			if($retorno)
			{
				redirect( "ecrm/ri_torcida_enquete", "refresh" );			
			}
			else
			{
				$mensagens = implode('<br>',$msg);
				exibir_mensagem($msg[0]);
			}
		}
	}

	function excluir($id)
	{
		if(CheckLogin())
		{
			$this->load->model('torcida/Enquete_model');

			$this->Enquete_model->excluir( $id );

			redirect( 'ecrm/ri_torcida_enquete', 'refresh' );
		}
	}

	function liberar()
	{
		if(CheckLogin())
		{
			$this->load->model( 'torcida/Enquete_model', 'dbModel' );
			$cd=$this->input->post('cd');

			$msg=array();
			$b = $this->dbModel->liberar( $cd, usuario_id(), $msg );

			if($b){ echo 'true'; }
			else{ echo 'Algum problema ocorreu.'; }
		}
	}

	function bloquear()
	{
		if(CheckLogin())
		{
			$this->load->model( 'torcida/Enquete_model', 'dbModel' );
			$cd=$this->input->post('cd');

			$msg=array();
			$b = $this->dbModel->bloquear( $cd, $msg );

			if($b){ echo 'true'; }
			else{ echo 'Algum problema ocorreu.'; }
		}
	}

	// *** estrutura
	function estrutura($cd)
	{
		if(CheckLogin() && intval($cd)>0)
		{
			$this->load->model( 'torcida/Enquete_model', 'dbModel' );
			$pergunta=$this->dbModel->carregar_pergunta( intval($cd), usuario_id() );
			$itens=$this->dbModel->listar_pergunta_item( intval($cd) );

			$lista_simples=array();
			foreach( $itens as $item )
			{
				$lista_simples[]=array( 'cd_enquete_pergunta_item'=>$item['cd_enquete_pergunta_item'], 'label'=>$item['ds_item'] );
			}

			$data['cd_enquete']=intval($cd);
			$data['pergunta']=$pergunta;
			$data['pergunta_item']=$lista_simples;
			$this->load->view('ecrm/ri_torcida_enquete/estrutura', $data);
		}
	}
	function salvar_pergunta()
	{
		if(CheckLogin())
		{
			$cd_enquete=$this->input->post('cd_enquete');
			$ds_pergunta=$this->input->post('ds_pergunta');

			$sql="UPDATE torcida.enquete_pergunta SET ds_pergunta='{ds_pergunta}' WHERE cd_enquete={cd_enquete} AND dt_exclusao IS NULL;";
			esc( '{cd_enquete}', $cd_enquete, $sql, 'int' );
			esc( '{ds_pergunta}', $ds_pergunta, $sql, 'str' );
			$query=$this->db->query($sql);

			echo 'true';
		}
	}
	function excluir_pergunta_item($cd_md5)
	{
		if(CheckLogin())
		{
			$sql="UPDATE torcida.enquete_pergunta_item SET dt_exclusao=current_timestamp , cd_usuario_exclusao={cd_usuario_exclusao} WHERE md5(cd_enquete_pergunta_item::varchar)='{cd}'";
			esc( '{cd}', $cd_md5, $sql, 'str' );
			esc( '{cd_usuario_exclusao}', usuario_id(), $sql, 'int' );
			$query=$this->db->query($sql);

			$sql="
			SELECT ep.cd_enquete
			FROM torcida.enquete_pergunta_item epi
			JOIN torcida.enquete_pergunta ep ON ep.cd_enquete_pergunta=epi.cd_enquete_pergunta
			WHERE md5(cd_enquete_pergunta_item::varchar)='{cd}'
			";
			esc( '{cd}', $cd_md5, $sql, 'str' );
			$q=$this->db->query($sql);
			$r=$q->row_array();
			redirect( 'ecrm/ri_torcida_enquete/estrutura/'.intval($r['cd_enquete']) );
		}
	}

	function adicionar_pergunta_item()
	{
		if(CheckLogin())
		{
			$cd_enquete=$this->input->post('cd_enquete');
			$ds_item=$this->input->post('ds_item');
			$fl_certo="S";
			$nr_ordem=$this->input->post('nr_ordem');
			$cd_usuario_inclusao=usuario_id();

			$sql="
INSERT INTO torcida.enquete_pergunta_item( cd_enquete_pergunta, ds_item, fl_certo, nr_ordem, dt_inclusao, cd_usuario_inclusao )
SELECT cd_enquete_pergunta, '{ds_item}', '{fl_certo}', {nr_ordem}, CURRENT_TIMESTAMP, {cd_usuario_inclusao}
FROM torcida.enquete_pergunta WHERE cd_enquete={cd_enquete}
";
			esc( '{cd_enquete}', $cd_enquete, $sql, 'int' );
			esc( '{ds_item}', $ds_item, $sql, 'str' );
			esc( '{fl_certo}', $fl_certo, $sql, 'str' );
			esc( '{nr_ordem}', $nr_ordem, $sql, 'int' );
			esc( '{cd_usuario_inclusao}', $cd_usuario_inclusao, $sql, 'int' );
			$this->db->query( $sql );

			echo 'true';
 		}
	}
	// *** estrutura

	// *** resposta
	function resultado($cd)
	{
		if(CheckLogin() && intval($cd)>0)
		{
			$args['inicio']=$this->input->post('periodo_inicio');
			$args['fim']=$this->input->post('periodo_fim');
			$args['origem']=$this->input->post('origem');

			$this->load->model( 'torcida/Enquete_model', 'dbModel' );
			$pergunta=$this->dbModel->carregar_pergunta( intval($cd), usuario_id() );
			$resposta=$this->dbModel->listar_pergunta_resposta( intval($cd), $args );

			$data['ds_pergunta']=$pergunta['ds_pergunta'];
			$data['cd_enquete']=intval($cd);
			$data['resposta']=$resposta;
			$data['periodo_inicio']=$args['inicio'];
			$data['periodo_fim']=$args['fim'];
			$data['origem']=$args['origem'];
			$this->load->view('ecrm/ri_torcida_enquete/resultado', $data);
		}
	}
	
	// *** resposta
}
