<?php
class reenvio_email extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index($cd_email = "")
    {
		CheckLogin();
	
		if(gerencia_in(array('GCM','GFC','GTI','GC','GAP.')))
		{
			if(intval($cd_email) > 0)
			{
			
				$this->load->model('projetos/Envia_emails');
				$args = Array();
				$args['cd_email'] = $cd_email;
				
				$this->Envia_emails->busca_email( $result, $args );
				$data['row'] = $result->row_array();
								
				$this->load->view('ecrm/reenvio_email/index.php',$data);
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
		if(gerencia_in(array('GCM','GFC','GTI','GC')))
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
			$args["fl_comprova"]           = $this->input->post("fl_comprova", TRUE);
			$args["tp_email"]              = $this->input->post("tp_email", TRUE);
			$args["cd_usuario"]            = $this->session->userdata('codigo');

			$cd_email_new = $this->Envia_emails->reenviar_email( $result, $args );
			redirect("ecrm/reenvio_email/index/".$cd_email_new, "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }	
	
    function listarAnexo()
    {
		CheckLogin();
	
		if(gerencia_in(array('GCM','GFC','GTI','GC')))
		{
			$this->load->model('projetos/Envia_emails');
			
			$result = null;
			$data = Array();
			$args = Array();

			$args["cd_email"] = $this->input->post('cd_email', TRUE);
			
			$this->Envia_emails->listarAnexo( $result, $args );
			$data['ar_anexo'] = $result->result_array();

			
			$this->load->view('ecrm/reenvio_email/index_anexo_result', $data);			
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }		

    function abrirAnexo($cd_email_anexo = "")
    {
		CheckLogin();
	
		if(gerencia_in(array('GCM','GFC','GTI','GC')))
		{
			$this->load->model('projetos/Envia_emails');
			
			$result = null;
			$data = Array();
			$args = Array();

			$args["cd_email_anexo"] = $cd_email_anexo;
			
			$this->Envia_emails->abrirAnexo($result, $args);
			$ar_anexo = $result->row_array();
			
			//$ext = pathinfo($ar_anexo['arquivo_nome'], PATHINFO_EXTENSION);
			
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename='.basename($ar_anexo['arquivo_nome']));
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			echo base64_decode($ar_anexo['arquivo']);
			//header('Content-Length: ' . filesize($file));
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }
	
    function listaLink()
    {
		CheckLogin();
	
		if(gerencia_in(array('GCM','GFC','GTI','GC')))
		{
			$this->load->model('projetos/Envia_emails');
			
			$result = null;
			$data = Array();
			$args = Array();

			$args["cd_email"] = $this->input->post('cd_email', TRUE);
			
			$this->Envia_emails->listaLink( $result, $args );
			$data['ar_link'] = $result->result_array();
			
			
			$data['ar_link_log'] = Array();
			foreach($data['ar_link'] as $ar_item )
			{
				$this->Envia_emails->listaLinkLog($result, $ar_item);
				$data['ar_link_log'][$ar_item['cd_email_link']] = $result->result_array();			
			}			
			
			
			$this->load->view('ecrm/reenvio_email/index_result', $data);			
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
}
