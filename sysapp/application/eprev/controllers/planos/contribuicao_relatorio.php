<?php
class Contribuicao_relatorio extends Controller
{
	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
	}

	private function get_permissao()
	{
		if(gerencia_in(array('GFC', 'GCM')))
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
	}

	public function index($cd_contribuicao_relatorio_origem = '', $cd_plano_empresa = '', $cd_plano = '', $nr_mes = '', $nr_ano = '', $fl_telefone = '', $fl_gerado = 'N', $fl_sms_enviado = 'S')
    {
    	if($this->get_permissao())
    	{
			$this->load->model('projetos/contribuicao_relatorio_model');

            if(trim($cd_contribuicao_relatorio_origem) == 'N')
            {
                $cd_contribuicao_relatorio_origem = '';
            }

            if(trim($cd_plano_empresa) == 'N')
            {
                $cd_plano_empresa = '';
            }

            if(trim($cd_plano) == 'N')
            {
                $cd_plano = '';
            }

            if(trim($nr_mes) == 'N')
            {
                $nr_mes = date('m');
            }

            if(trim($nr_ano) == 'N')
            {
                $nr_ano = date('Y');
            }

            if(trim($fl_telefone) == 'N')
            {
                $fl_telefone = '';
            }

	        $data = array(
				'origem'                           => $this->contribuicao_relatorio_model->get_contribuicao_origem(),
				'cd_contribuicao_relatorio_origem' => $cd_contribuicao_relatorio_origem,
				'cd_plano_empresa' 				   => $cd_plano_empresa,
				'cd_plano'         				   => $cd_plano, 
				'nr_mes'           				   => $nr_mes,
                'nr_ano'                           => $nr_ano,
                'fl_telefone'                      => $fl_telefone,
                'fl_gerado'                        => $fl_gerado,
				'fl_sms_enviado'                   => $fl_sms_enviado
	        );  
	        
	        $this->load->view('planos/contribuicao_relatorio/index', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}	
	
	public function listar()
    {		
		$this->load->model('projetos/contribuicao_relatorio_model');
            
        $args = array(
			'cd_plano_empresa'          	   => $this->input->post('cd_plano_empresa', TRUE),
			'cd_plano'         				   => $this->input->post('cd_plano', TRUE),
            'cd_empresa'                       => $this->input->post('cd_empresa', TRUE),
            'cd_registro_empregado'            => $this->input->post('cd_registro_empregado', TRUE),
            'seq_dependencia'                  => $this->input->post('seq_dependencia', TRUE),
            'dt_referencia_ini'                => $this->input->post('dt_referencia_ini', TRUE),
            'dt_referencia_fim'                => $this->input->post('dt_referencia_fim', TRUE),
			'nr_mes'           				   => $this->input->post('nr_mes', TRUE),
			'nr_ano'          				   => $this->input->post('nr_ano', TRUE),
			'cd_contribuicao_relatorio_origem' => $this->input->post('cd_contribuicao_relatorio_origem', TRUE),
			'fl_telefone'                      => $this->input->post('fl_telefone', TRUE),
            'fl_gerado'                        => $this->input->post('fl_gerado', TRUE),
			'fl_envio_sms'                     => $this->input->post('fl_envio_sms', TRUE)
        ); 
       
        manter_filtros($args);

        $data['collection'] = $this->contribuicao_relatorio_model->listar('S', $args);	
           
        $this->load->view('planos/contribuicao_relatorio/index_result', $data);
    }

    public function enviarSMS()
    {
		#### ENVIAR SMS ###
		if($this->get_permissao())
    	{
    		$this->load->model('projetos/contribuicao_relatorio_model');
			
			$args = Array();
			
    		$args['ar_contribuicao_relatorio'] = $this->input->post('contribuicao_relatorio', TRUE);
			$args['cd_usuario']                = $this->session->userdata('codigo');

    		if(!is_array($args['ar_contribuicao_relatorio']))
    		{
    			$args['ar_contribuicao_relatorio'] = array();
    		}

			$this->contribuicao_relatorio_model->enviarSMS($args);
			
			redirect('planos/contribuicao_relatorio', 'refresh');
    	}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}		
	}
	
    public function gerar()
    {
    	#### GERA ARQUIVO CSV ####
		if($this->get_permissao())
    	{
    		$this->load->model('projetos/contribuicao_relatorio_model');

    		$contribuicao_relatorio = $this->input->post('contribuicao_relatorio', TRUE);

    		if(!is_array($contribuicao_relatorio))
    		{
    			$contribuicao_relatorio = array();
    		}

    		$collection = $this->contribuicao_relatorio_model->get_relatorio_geracao($contribuicao_relatorio);	

    		$csv = 'Telefone;Link'."\r\n";

    		foreach ($collection as $key => $item) 
    		{
    			$csv .= $item['ds_telefone'].';'.$item['ds_mensagem']."\r\n";
    		}

    		$arquivo = 'file_'.date('YmdHis').'.csv';

    		$args = array(
    			'arquivo'    => $arquivo,
    			'cd_usuario' => $this->session->userdata('codigo')
    		);

    		$cd_contribuicao_relatorio_sms_geracao = $this->contribuicao_relatorio_model->salvar_geracao($args);

    		$args = array(
    			'cd_contribuicao_relatorio_sms_geracao' => $cd_contribuicao_relatorio_sms_geracao,
    			'contribuicao_relatorio'                => $contribuicao_relatorio,
    			'cd_usuario'                            => $this->session->userdata('codigo')
    		);

    		$this->contribuicao_relatorio_model->atualiza_geracao($args);

		    $file = fopen('./up/contribuicao_sms/'.$arquivo, 'a'); 
		
			fwrite($file, $csv);
		
			fclose($file);

			header('Expires: Tue, 03 Jul 2001 06:00:00 GMT');
			header('Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate');
			header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');

			header('Content-Type: application/force-download');
			header('Content-Type: application/octet-stream');
			header('Content-Type: application/download');

			header('Content-Disposition: attachment;filename='.$arquivo);
			header('Content-Transfer-Encoding: binary');

			echo $csv;
    	}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }

    public function gerado()
    {
    	if($this->get_permissao())
    	{   
	        $this->load->view('planos/contribuicao_relatorio/gerado');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }

    public function gerado_listar()
    {
		$this->load->model('projetos/contribuicao_relatorio_model');
            
        $data['collection'] = $this->contribuicao_relatorio_model->gerado_listar();	
           
        $this->load->view('planos/contribuicao_relatorio/gerado_result', $data);
    }

    public function ver_gerado($cd_contribuicao_relatorio_sms_geracao)
    {
    	if($this->get_permissao())
    	{   
    		$this->load->model('projetos/contribuicao_relatorio_model');

    		$data = array(
    			'row'        => $this->contribuicao_relatorio_model->carrega_gerado($cd_contribuicao_relatorio_sms_geracao),
    			'collection' => $this->contribuicao_relatorio_model->gerato_registro_listar($cd_contribuicao_relatorio_sms_geracao)
    		);

	        $this->load->view('planos/contribuicao_relatorio/ver_gerado', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }

    public function atualiza_telefone()
    {
    	if($this->get_permissao())
        {
            $this->load->model('projetos/contribuicao_relatorio_model');

        	$this->contribuicao_relatorio_model->atualiza_telefone($this->session->userdata('codigo'));

    		redirect('planos/contribuicao_relatorio', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function debito_conta()
    {
        if($this->get_permissao())
        {
            $this->load->view('planos/contribuicao_relatorio/debito_conta');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar_debito_conta()
    {
        $this->load->model('projetos/contribuicao_relatorio_model');
            
        $args = array(
            'cd_empresa'                       => $this->input->post('cd_plano_empresa', TRUE),
            'cd_plano'                         => $this->input->post('cd_plano', TRUE),
            'nr_mes'                           => $this->input->post('nr_mes', TRUE),
            'nr_ano'                           => $this->input->post('nr_ano', TRUE),
            'cd_contribuicao_relatorio_origem' => 1,
            'fl_telefone'                      => $this->input->post('fl_telefone', TRUE),
            'fl_gerado'                        => 'N',
        ); 
       
        manter_filtros($args);

        $data['collection'] = $this->contribuicao_relatorio_model->listar('N', $args);  
           
        $this->load->view('planos/contribuicao_relatorio/debito_conta_result', $data);
    }

    public function enviar_email()
    {
        $this->load->model('projetos/eventos_email_model');

        $cd_evento = 399;

        $email = $this->eventos_email_model->carrega($cd_evento);

        $cd_empresa                       = (trim($this->input->post('cd_plano_empresa', TRUE)) == '' ? 'N' : $this->input->post('cd_plano_empresa', TRUE));
        $cd_plano                         = (trim($this->input->post('cd_plano', TRUE)) == '' ? 'N' : $this->input->post('cd_plano', TRUE));
        $nr_mes                           = (trim($this->input->post('nr_mes', TRUE)) == '' ? 'N' : $this->input->post('nr_mes', TRUE));
        $nr_ano                           = (trim($this->input->post('nr_ano', TRUE)) == '' ? 'N' : $this->input->post('nr_ano', TRUE));
        $cd_contribuicao_relatorio_origem = (trim($this->input->post('cd_contribuicao_relatorio_origem', TRUE)) == '' ? 'N' : $this->input->post('cd_contribuicao_relatorio_origem', TRUE));
        $fl_telefone                      = (trim($this->input->post('fl_telefone', TRUE)) == '' ? 'N' : $this->input->post('fl_telefone', TRUE));
        $fl_gerado                        = (trim($this->input->post('fl_gerado', TRUE)) == '' ? 'N' : $this->input->post('fl_gerado', TRUE));
        $fl_envio_sms                     = (trim($this->input->post('fl_envio_sms', TRUE)) == '' ? 'N' : $this->input->post('fl_envio_sms', TRUE));

        $link = site_url('planos/contribuicao_relatorio/index/'.$cd_contribuicao_relatorio_origem.'/'.$cd_empresa.'/'.$cd_plano.'/'.$nr_mes.'/'.$nr_ano.'/'.$fl_telefone.'/'.$fl_gerado.'/'.$fl_envio_sms);

        $texto = str_replace('[LINK]', $link, $email['email']);

        $cd_usuario = $this->session->userdata('codigo');

        $args = array( 
            'de'      => 'Contribuição - Envio SMS',
            'assunto' => $email['assunto'],
            'para'    => $email['para'],
            'cc'      => $email['cc'],
            'cco'     => $email['cco'],
            'texto'   => $texto
        );

        $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);     

        redirect('planos/contribuicao_relatorio', 'refresh');
    }
}
?>