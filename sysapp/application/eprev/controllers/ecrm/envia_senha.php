<?php
class envia_senha extends Controller
{
    function __construct()
    {
        parent::Controller();
        
        CheckLogin();
        
        $this->load->model('projetos/envia_senha_model');
    }
    
    function index($cd_empresa = 0, $cd_registro_empregado = 0, $seq_dependencia = 0)
    {        
		$data   = Array();
		$args   = Array();
		$result = null;	        
		
		if(intval($cd_registro_empregado) > 0)
		{
			$data['cd_empresa']            = intval($cd_empresa);
			$data['cd_registro_empregado'] = intval($cd_registro_empregado);
			$data['seq_dependencia']       = intval($seq_dependencia);
		}
		else
		{
			$data['cd_empresa']            = "";
			$data['cd_registro_empregado'] = "";
			$data['seq_dependencia']       = "";		
		}
		
		$this->load->view('ecrm/envia_senha/index.php',$data);
    }
    
    function tipo_senha()
    {
        if(gerencia_in(array('GAP')))
        {
            $this->load->model('projetos/envia_senha_model');

            $data   = Array();
            $args   = Array();
            $result = null;	

            $args["cd_empresa"]            = $this->input->post("cd_empresa", TRUE);
            $args["cd_registro_empregado"] = $this->input->post("cd_registro_empregado", TRUE);
            $args["seq_dependencia"]       = $this->input->post("seq_dependencia", TRUE);

            $this->envia_senha_model->get_tipo_senha($result, $args);

            $row = $result->row_array();

            echo $row['participante_tipo_senha'];
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function enviar($cd_empresa = 0, $cd_registro_empregado = 0, $seq_dependencia = 0)
    {
        if(gerencia_in(array('GAP')))
        {
            $data   = Array();
            $args   = Array();
            $result = null;	

            $args["cd_empresa"]            = $cd_empresa;
            $args["cd_registro_empregado"] = $cd_registro_empregado;
            $args["seq_dependencia"]       = $seq_dependencia;

            $this->envia_senha_model->envia_senha($result, $args);
            $row = $result->row_array();
            echo $row['participante_envia_senha'];   
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function relatorio()
    {
        if(gerencia_in(array('GAP')))
        {		
            $this->load->view('ecrm/envia_senha/relatorio.php');
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function listar()
    {
        if(gerencia_in(array('GAP')))
        {		
            $result = null;
            $data = Array();
            $args = Array();

            $args["cd_empresa"]            = $this->input->post("cd_empresa", TRUE);
            $args["cd_registro_empregado"] = $this->input->post("cd_registro_empregado", TRUE);
            $args["seq_dependencia"]       = $this->input->post("seq_dependencia", TRUE);
            $args["dt_email_ini"]          = $this->input->post("dt_email_ini", TRUE);
            $args["dt_email_fim"]          = $this->input->post("dt_email_fim", TRUE);
            $args["dt_envio_ini"]          = $this->input->post("dt_envio_ini", TRUE);
            $args["dt_envio_fim"]          = $this->input->post("dt_envio_fim", TRUE);

            manter_filtros($args);

            $this->envia_senha_model->listar( $result, $args );
            $data['collection'] = $result->result_array();		

            $this->load->view('ecrm/envia_senha/relatorio_result', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }		
    }
}

?>