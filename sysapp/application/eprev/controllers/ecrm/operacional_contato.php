<?php
class operacional_contato extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index()
    {
		CheckLogin();
        if(gerencia_in(array('GAP')))
        {
           $data['anos']=$this->db->query( "
            SELECT DISTINCT TO_CHAR(data, 'YYYY') AS value, TO_CHAR(data, 'YYYY') as text
            FROM public.contatos_internet
            WHERE dt_exclusao IS NULL
            ORDER BY value DESC
            " )->result_array();

            $this->load->view('ecrm/operacional_contato/index.php', $data);
        }
        else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
		
    }

    function listar()
    {
        CheckLogin();
        if(gerencia_in(array('GAP')))
        {
            $this->load->model('public/Contatos_internet_model');

            $data['collection'] = array();
            $result = null;

            // --------------------------
            // filtros ...

            $count = 0;
            $args=array();

            $args["ano"] = intval($this->input->post("ano", TRUE));

            // --------------------------
            // listar ...

            $this->Contatos_internet_model->listar( $result, $count, $args );

            $data['collection'] = $result->result_array();

            $data['quantos'] = sizeof($data['collection']);
            if( $result )
            {
                $data['collection'] = $result->result_array();
            }

            // --------------------------

            $this->load->view('ecrm/operacional_contato/partial_result', $data);
        }
        else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }

    function detalhe($cd)
    {
        CheckLogin();
        if(gerencia_in(array('GAP')))
        {
            if(intval($cd) > 0)
            {
                $this->load->model('public/Contatos_internet_model');
                $args=array();

                $args['codigo'] = intval($cd);

                $this->Contatos_internet_model->carrega($result, $args);

                $data['row'] = $result->row_array();

                $this->Contatos_internet_model->carregaTipoAtendimento($result, $args);

                $data['tipo_atendimento'] = $result->result_array();

                $this->load->view('ecrm/operacional_contato/detalhe',$data);
            }
            else
            {
                exibir_mensagem("Registro não encontrado.");
            }
        }
        else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }

    function salvar()
    {
        CheckLogin();
        if(gerencia_in(array('GAP')))
        {
            $this->load->model('public/Contatos_internet_model');
            $args=array();

            $data['row'] = array();
            $result = null;
            $args = Array();

            $args["codigo"] = $this->input->post("codigo", TRUE);
            $args["cd_tipo_atendimento"] = $this->input->post("cd_tipo_atendimento", TRUE);
            $args["fl_envia_email"] = $this->input->post("fl_envia_email", TRUE);
            $args["resposta"] = $this->input->post("resposta", TRUE);
            $args["usuario"] = $this->session->userdata('usuario');

            $this->Contatos_internet_model->salvar($result, $args);

            redirect("ecrm/operacional_contato", "refresh");
        }
        else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }
}