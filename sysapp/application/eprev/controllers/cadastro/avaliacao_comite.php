<?php
class Avaliacao_comite extends Controller
{
    function __construct()
    {
        parent::Controller();
        
        CheckLogin();
		
        $this->load->model('projetos/avaliacao_comite_model');
    }
	
	function index()
    {
		if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
			$args = Array();
			$data = Array();
			$result = null;
		
			$this->avaliacao_comite_model->gerencia( $result, $args );
            $data['arr_gerencia'] = $result->result_array();
			
			$this->avaliacao_comite_model->ano( $result, $args );
            $data['arr_ano'] = $result->result_array();
			
            $this->load->view('cadastro/avaliacao_comite/index', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	function listar()
    {
        if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
			$args = Array();
			$data = Array();
			$result = null;

            $args["cd_usuario_gerencia"] = $this->input->post("cd_usuario_gerencia", TRUE);
            $args["cd_usuario"]          = $this->input->post("cd_usuario", TRUE);
            $args["fl_status"]           = $this->input->post("fl_status", TRUE);
            $args["fl_tipo"]             = $this->input->post("fl_tipo", TRUE);
            $args["ano"]                 = $this->input->post("ano", TRUE);

            manter_filtros($args);

            $this->avaliacao_comite_model->listar( $result, $args );
            $arr = $result->result_array();
			
			$data['collection'] = array();
			
			$i = 0;
			
			foreach($arr as $item)
			{
				$args['cd_avaliacao_capa'] = $item['cd_avaliacao_capa'];
				
				$data['collection'][$i] = $item;
				
				$this->avaliacao_comite_model->comite( $result, $args );
				$data['collection'][$i]['arr_comite'] = $result->result_array();
				
				$this->avaliacao_comite_model->avaliador($result, $args);
                $row = $result->row_array();
				
				$data['collection'][$i]['avaliador'] = (count($row) > 0 ? $row['nome'] : '');

				$i ++;
			}
			
			$arr = $data['collection'];

            $this->load->view('cadastro/avaliacao_comite/index_result', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	function cadastro($cd_avaliacao_capa)
	{
		if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
			$args = Array();
			$data = Array();
			$result = null;

            $args["cd_avaliacao_capa"] = $cd_avaliacao_capa;
			
			$this->avaliacao_comite_model->usuario_comite( $result, $args );
            $data['arr_usuario'] = $result->result_array();
			
			$this->avaliacao_comite_model->carrega($result, $args);
			$data['row'] = $result->row_array();
			
			$this->avaliacao_comite_model->comite( $result, $args );
			$data['collection'] = $result->result_array();
			
			$this->avaliacao_comite_model->avaliador($result, $args);
			$row = $result->row_array();
			
			$data['avaliador'] = (count($row) > 0 ? $row['nome'] : '');
			
            $this->load->view('cadastro/avaliacao_comite/cadastro', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function salvar()
	{
		if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
			$args = Array();
			$data = Array();
			$result = null;

            $args["cd_avaliacao_capa"]    = $this->input->post("cd_avaliacao_capa", TRUE);
            $args["fl_responsavel"]       = $this->input->post("fl_responsavel", TRUE);
            $args["cd_usuario_avaliador"] = $this->input->post("cd_usuario", TRUE);
			
			$this->avaliacao_comite_model->salvar( $result, $args );
			
			redirect("cadastro/avaliacao_comite/cadastro/".$args["cd_avaliacao_capa"], "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function excluir($cd_avaliacao_capa, $cd_avaliacao_comite)
	{
		if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args["cd_avaliacao_capa"]   = $cd_avaliacao_capa;
            $args["cd_avaliacao_comite"] = $cd_avaliacao_comite;
			
			$this->avaliacao_comite_model->excluir( $result, $args );
			
			redirect("cadastro/avaliacao_comite/cadastro/".$args["cd_avaliacao_capa"], "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
	
	function alterar_responsavel()
	{
		if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args["cd_avaliacao_capa"]   = $this->input->post("cd_avaliacao_capa", TRUE);
			$args["cd_avaliacao_comite"] = $this->input->post("cd_avaliacao_comite", TRUE);
			
			$this->avaliacao_comite_model->alterar_responsavel( $result, $args );
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
	
	function alterar_responsavel_avaliador()
	{
		if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args["cd_avaliacao_capa"] = $this->input->post("cd_avaliacao_capa", TRUE);
			
			$this->avaliacao_comite_model->alterar_responsavel_avaliador( $result, $args );
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
	
	function encaminhar($cd_avaliacao_capa, $ajax = 1)
	{
		if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args["cd_avaliacao_capa"] = $cd_avaliacao_capa;
			$args["cd_usuario"]        = $this->session->userdata("codigo");
			
			$this->avaliacao_comite_model->encaminhar( $result, $args );
			
			if(intval($ajax) == 0)
			{
				redirect("cadastro/avaliacao_comite", "refresh");
			}
			else
			{
				redirect("cadastro/avaliacao_comite/cadastro/".$args["cd_avaliacao_capa"], "refresh");
			}
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
	
	function enviar_email($cd_avaliacao_capa)
	{
		if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args["cd_avaliacao_capa"] = $cd_avaliacao_capa;
			$args["cd_usuario"]        = $this->session->userdata("codigo");
			
			$this->avaliacao_comite_model->enviar_email( $result, $args );
			
			redirect("cadastro/avaliacao_comite", "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

}
?>