<?php
class controle_tarefa extends Controller
{
    function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->load->model('projetos/tarefas_model');
    }

    function index()
    {
		if(gerencia_in(array('GI')))
		{
			$result = null;
			$data = Array();
			$args = Array();
			
			$this->tarefas_model->listar_solicitante($result, $args);
			$data['solicitante_dd'] = $result->result_array();
			
			$this->tarefas_model->listar_atendente($result, $args);
		    $data['atendente_dd'] = $result->result_array();

			$this->load->view('atividade/controle_tarefa/index.php', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}			
    }

    function listar()
    {
		if(gerencia_in(array('GI')))
		{		
			$result = null;
			$data = Array();
			$args = Array();

			$args['status_atual']=array();

			if($this->input->post('status_aman')!='') $args['status_atual'][]=$this->input->post('status_aman', TRUE);
			if($this->input->post('status_eman')!='') $args['status_atual'][]=$this->input->post('status_eman', TRUE);
			if($this->input->post('status_susp')!='') $args['status_atual'][]=$this->input->post('status_susp', TRUE);
			if($this->input->post('status_libe')!='') $args['status_atual'][]=$this->input->post('status_libe', TRUE);
			if($this->input->post('status_conc')!='') $args['status_atual'][]=$this->input->post('status_conc', TRUE);

			if( sizeof($args['status_atual'])==0 )
			{
				$args['status_atual'] = array('AMAN','EMAN','SUSP','LIBE');
			}

			$args['dt_encaminhamento_inicio'] = $this->input->post('dt_encaminhamento_inicio', TRUE);
			$args['dt_encaminhamento_fim']    = $this->input->post('dt_encaminhamento_fim', TRUE);
			$args['dt_ok_anal_inicio']        = $this->input->post('dt_concluido_inicio', TRUE);
			$args['dt_ok_anal_fim']           = $this->input->post('dt_concluido_fim', TRUE);
			$args['cd_mandante']              = $this->input->post('cd_solicitante', TRUE);
			$args['cd_recurso']               = $this->input->post('cd_atendente', TRUE);
			$args['prioridade']               = $this->input->post('prioridade', TRUE);
			$args['cd_atividade']             = $this->input->post('cd_atividade', TRUE);
			$args['cd_tarefa']                = $this->input->post('cd_tarefa', TRUE);
			
			manter_filtros($args);
			
			$this->tarefas_model->controle_listar( $result, $args );
			$data['collection'] = $result->result_array();

			$this->load->view('atividade/controle_tarefa/partial_result', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}			
    }
}
