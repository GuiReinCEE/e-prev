<?php
class conceito extends Controller
{
    function __construct()
    {
        parent::Controller();
        CheckLogin();
		
        $this->load->model('projetos/conceito_model');
    }

    function cadastro()
    {
        if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
            $args = Array();
			$data = Array();
			$result = null;

            $this->conceito_model->carrega( $result, $args );
            $data['row'] = $result->result_array();

            $this->load->view('cadastro/conceito/cadastro', $data);
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
            $args[] = Array('codigo'=> 'CACI', 'descricao'=> $this->input->post("CACI", TRUE));
            $args[] = Array('codigo'=> 'CBCI', 'descricao'=> $this->input->post("CBCI", TRUE));
            $args[] = Array('codigo'=> 'CCCI', 'descricao'=> $this->input->post("CCCI", TRUE));
            $args[] = Array('codigo'=> 'CDCI', 'descricao'=> $this->input->post("CDCI", TRUE));
            $args[] = Array('codigo'=> 'CECI', 'descricao'=> $this->input->post("CECI", TRUE));
            $args[] = Array('codigo'=> 'CFCI', 'descricao'=> $this->input->post("CFCI", TRUE));

            $args[] = Array('codigo'=> 'CACE', 'descricao'=> $this->input->post("CACE", TRUE));
            $args[] = Array('codigo'=> 'CBCE', 'descricao'=> $this->input->post("CBCE", TRUE));
            $args[] = Array('codigo'=> 'CCCE', 'descricao'=> $this->input->post("CCCE", TRUE));
            $args[] = Array('codigo'=> 'CDCE', 'descricao'=> $this->input->post("CDCE", TRUE));
            $args[] = Array('codigo'=> 'CECE', 'descricao'=> $this->input->post("CECE", TRUE));
            $args[] = Array('codigo'=> 'CFCE', 'descricao'=> $this->input->post("CFCE", TRUE));

            $args[] = Array('codigo'=> 'CARE', 'descricao'=> $this->input->post("CARE", TRUE));
            $args[] = Array('codigo'=> 'CBRE', 'descricao'=> $this->input->post("CBRE", TRUE));
            $args[] = Array('codigo'=> 'CCRE', 'descricao'=> $this->input->post("CCRE", TRUE));
            $args[] = Array('codigo'=> 'CDRE', 'descricao'=> $this->input->post("CDRE", TRUE));
            $args[] = Array('codigo'=> 'CERE', 'descricao'=> $this->input->post("CERE", TRUE));
            $args[] = Array('codigo'=> 'CFRE', 'descricao'=> $this->input->post("CFRE", TRUE));

            $retorno = $this->conceito_model->salvar($result, $args);

			redirect("cadastro/conceito/cadastro", "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
}
?>