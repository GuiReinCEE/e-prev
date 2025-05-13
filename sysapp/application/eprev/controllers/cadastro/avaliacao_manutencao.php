<?php
class avaliacao_manutencao extends Controller
{
    function __construct()
    {
        parent::Controller();
        CheckLogin();
		
        $this->load->model('projetos/avaliacao_manutencao_model');
    }

    function index()
    {
        if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
            $this->load->view('cadastro/avaliacao_manutencao/index');
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
            $result = null;
			$args = array();
			$data = array();

            $args["periodo"]                      = $this->input->post("periodo", TRUE);
            $args["tipo"]                         = $this->input->post("tipo", TRUE);
            $args["cd_usuario_avaliado_gerencia"] = $this->input->post("cd_usuario_avaliado_gerencia", TRUE);
            $args["cd_usuario_avaliado"]          = $this->input->post("cd_usuario_avaliado", TRUE);
            $args["fl_publicado"]                 = $this->input->post("fl_publicado", TRUE);

            manter_filtros($args);

            $this->avaliacao_manutencao_model->combo_usuario( $result, $args );
            $data['ar_superior'] = $result->result_array();			
			
            $this->avaliacao_manutencao_model->listar( $result, $args );
            $data['collection'] = $result->result_array();

            $this->load->view('cadastro/avaliacao_manutencao/partial_result', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function excluir($cd_avaliacao_capa)
    {
        if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
            $result = null;
			$args = array();
			$data = array();

            $args["cd_avaliacao_capa"] = $cd_avaliacao_capa;

            $this->avaliacao_manutencao_model->excluir( $result, $args );

            redirect("cadastro/avaliacao_manutencao", "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function reabrir($cd_avaliacao_capa)
    {   
        if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
            $result = null;
			$args = array();
			$data = array();

            $args["cd_avaliacao_capa"] = $cd_avaliacao_capa;

            $this->avaliacao_manutencao_model->reabrir( $result, $args );

            redirect("cadastro/avaliacao_manutencao", "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function encerrar($cd_avaliacao_capa)
    {
        if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
            $result = null;
			$args = array();
			$data = array();

            $args["cd_avaliacao_capa"] = $cd_avaliacao_capa;

            $this->avaliacao_manutencao_model->encerrar( $result, $args );

            redirect("cadastro/avaliacao_manutencao", "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
    function editar_superior()
    {   
        if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
            $result = null;
			$args = array();
			$data = array();
		
            $args["cd_avaliacao_capa"] = $this->input->post("cd_avaliacao_capa", TRUE);
            $args["cd_avaliador"]      = $this->input->post("cd_avaliador", TRUE);
			
			$this->avaliacao_manutencao_model->editar_superior($result, $args);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }	
}
?>