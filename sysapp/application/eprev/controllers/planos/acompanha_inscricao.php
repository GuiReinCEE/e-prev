<?php
class Acompanha_inscricao extends Controller
{
	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
    }

    private function get_permissao()
    {
    	if(gerencia_in(array('GCM', 'GP')))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
    }
	
	public function index()
    {
		if($this->get_permissao())
		{		
			$this->load->model('expansao/acompanha_inscricao_model');
			
			$data = array(
				'forma_pagamento' => $this->acompanha_inscricao_model->forma_pagamento(),
				'empresa'         => $this->acompanha_inscricao_model->empresa()
			);

			$this->load->view('planos/acompanha_inscricao/index', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }	
	
	public function listar()
    {		
		if($this->get_permissao())
		{		
			$this->load->model('expansao/acompanha_inscricao_model');

			$args = array(
				'cd_tipo_cliente'       => $this->input->post('cd_tipo_cliente', TRUE),
				'cd_plano_empresa'      => $this->input->post('cd_plano_empresa', TRUE),
				'cd_plano'              => $this->input->post('cd_plano', TRUE),
				'dt_solicitacao_ini'    => $this->input->post('dt_solicitacao_ini', TRUE),
				'dt_solicitacao_fim'    => $this->input->post('dt_solicitacao_fim', TRUE),
				'dt_inclusao_ini'       => $this->input->post('dt_inclusao_ini', TRUE),
				'dt_inclusao_fim'       => $this->input->post('dt_inclusao_fim', TRUE),
				'dt_confirma_ini'       => $this->input->post('dt_confirma_ini', TRUE),
				'dt_confirma_fim'       => $this->input->post('dt_confirma_fim', TRUE),
				'dt_cobranca_ini'       => $this->input->post('dt_cobranca_ini', TRUE),
				'dt_cobranca_fim'       => $this->input->post('dt_cobranca_fim', TRUE),
				'dt_envio_ini'          => $this->input->post('dt_envio_ini', TRUE),
				'dt_envio_fim'          => $this->input->post('dt_envio_fim', TRUE),
				'dt_dig_ingresso_ini'   => $this->input->post('dt_dig_ingresso_ini', TRUE),
				'dt_dig_ingresso_fim'   => $this->input->post('dt_dig_ingresso_fim', TRUE),
				'dt_ingresso_ini'       => $this->input->post('dt_ingresso_ini', TRUE),
				'dt_ingresso_fim'       => $this->input->post('dt_ingresso_fim', TRUE),
				'fl_participante'       => $this->input->post('fl_participante', TRUE),
				'fl_ingresso'           => $this->input->post('fl_ingresso', TRUE),
				'fl_cancela_inscricao'  => $this->input->post('fl_cancela_inscricao', TRUE),
				'cd_empresa'            => $this->input->post('cd_empresa', TRUE),
				'cd_registro_empregado' => $this->input->post('cd_registro_empregado', TRUE),
				'seq_dependencia'       => $this->input->post('seq_dependencia', TRUE),
				'nome'                  => $this->input->post('nome', TRUE),
				'cpf_mf'                => $this->input->post('cpf_mf', TRUE),
				'id_tipo_liquidacao'    => $this->input->post('id_tipo_liquidacao', TRUE)
			);

			manter_filtros($args);
			
			$data['collection'] = $this->acompanha_inscricao_model->listar($args);
			
			$this->load->view('planos/acompanha_inscricao/index_result', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }
}
?>