<?php
class enviar_email extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index($cd_email = "")
    {
		CheckLogin();
	
		if(gerencia_in(array('GAP','GRI','GF','GI')))
		{
			if(intval($cd_email) > 0)
			{
			
				$this->load->model('projetos/Envia_emails');
				$args = Array();
				$args['cd_email'] = $cd_email;
				
				$this->Envia_emails->busca_email( $result, $args );
				$data['row'] = $result->row_array();
								
				$this->load->view('ecrm/enviar_email/index.php',$data);
			}
			else
			{
				exibir_mensagem("EMAIL NÃO ENCONTRADO");
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
		if(gerencia_in(array('GAP','GRI','GF','GI')))
		{		
			$this->load->model('projetos/Envia_emails');

			$data['row'] = array();
			$result = null;
			$args = Array();

			$args["cd_email"]              = $this->input->post("cd_email", TRUE);
			$args["cd_empresa"]            = $this->input->post("cd_empresa", TRUE);
			$args["cd_registro_empregado"] = $this->input->post("cd_registro_empregado", TRUE);
			$args["seq_dependencia"]       = $this->input->post("seq_dependencia", TRUE);
			$args["cd_divulgacao"]         = $this->input->post("cd_divulgacao", TRUE);
			$args["cd_evento"]             = $this->input->post("cd_evento", TRUE);
			$args["de"]                    = $this->input->post("de", TRUE);
			$args["para"]                  = $this->input->post("para", TRUE);
			$args["cc"]                    = $this->input->post("cc", TRUE);
			$args["cco"]                   = $this->input->post("cco", TRUE);
			$args["assunto"]               = $this->input->post("assunto", TRUE);
			$args["texto"]                 = $this->input->post("texto", TRUE);
			$args["formato"]               = $this->input->post("formato", TRUE);
			$args["tp_email"]              = $this->input->post("tp_email", TRUE);
			$args["cd_usuario"]            = $this->session->userdata('codigo');

			$cd_email_new = $this->Envia_emails->enviar_email( $result, $args );
			redirect("ecrm/enviar_email/index/".$cd_email_new, "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }	
}
