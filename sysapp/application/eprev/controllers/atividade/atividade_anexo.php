<?php

class Atividade_anexo extends Controller
{
	function __construct()
    {
        parent::Controller();

        CheckLogin();
		
		$this->load->model('projetos/atividade_anexo_model');
	}
	
	function index($cd_atividade, $cd_gerencia)
    {
		$result = null;
		$data = Array();
		$args = Array();
		
		$data['cd_atividade'] = $cd_atividade;
		$data['cd_gerencia']  = $cd_gerencia;
		
		$this->load->view('atividade/atividade_anexo/index', $data);
	}
	
	function listar()
	{
		$result = null;
		$data = Array();
		$args = Array();
		
		$args['cd_atividade'] = $this->input->post("cd_atividade", TRUE);
		$args['cd_usuario']   = $this->session->userdata('codigo');
		
		$this->atividade_anexo_model->listar($result, $args);
		$data['collection'] = $result->result_array();
		
		$data['cd_usuario']   = $this->session->userdata('codigo');
		
		$this->load->view('atividade/atividade_anexo/index_result', $data);
	}
	
	function salvar()
	{
		/*
		$result = null;
		$data = Array();
		$args = Array();
		
		$args['arquivo_nome'] = $this->input->post("arquivo_nome", TRUE);
		$args['arquivo']      = $this->input->post("arquivo", TRUE);
		$args['cd_atividade'] = $this->input->post("cd_atividade", TRUE);
		$args["cd_gerencia"]  = $this->input->post("cd_gerencia", TRUE);
		$args["cd_usuario"]   = $this->session->userdata('codigo');
		
		$this->atividade_anexo_model->salvar($result, $args);
		*/

		$result = null;
		$data = Array();
		$args = Array();
	
		$qt_arquivo = intval($this->input->post("arquivo_m_count", TRUE));

		$cd_gerencia  = $this->input->post('cd_gerencia', TRUE);
		$cd_atividade = $this->input->post("cd_atividade", TRUE);
		
		if($qt_arquivo > 0)
		{
			$this->load->model('projetos/atividade_atendimento_model');

			$args['cd_atividade'] = $cd_atividade;
			$args['cd_gerencia']  = $cd_gerencia;

			$this->atividade_atendimento_model->atividade($result, $args);
			$ar_atividade = $result->row_array();

			$nr_conta = 0;
			while($nr_conta < $qt_arquivo)
			{
				$result = null;
				$data = Array();
				$args = Array();		
				
				$args['arquivo_nome']  = $this->input->post("arquivo_m_".$nr_conta."_name", TRUE);
				$args['arquivo']       = $this->input->post("arquivo_m_".$nr_conta."_tmpname", TRUE);
				
				$args['cd_atividade'] = $cd_atividade;
				$args["cd_gerencia"]  = $cd_gerencia;
				$args["cd_usuario"]   = $this->session->userdata('codigo');
				
				$this->atividade_anexo_model->salvar($result, $args);
				/*
				if(trim($ar_atividade['tipo_ativ']) == 'L' AND (intval($args['pertinencia']) == 1 OR intval($args['pertinencia']) == 2))
				{
					$this->load->plugin('encoding_pi');

					$this->load->model('projetos/cenario_model');

					$cenario = $this->cenario_model->carrega_conteudo(
						intval($ar_atividade['cd_cenario'])
					);

					$caminho_cenario = '../eletroceee/pydio/data/DOCUMENTOS_APROVADOS/CENARIO-LEGAL/'.$cenario['ds_ano_edicao'];

		            if(!is_dir($caminho_cenario))
		            {
		                mkdir($caminho_cenario, 0777);
		            }

		            $caminho_cenario .= '/'.$cenario['cd_edicao'].'_'.$cenario['cd_cenario'].'_'.str_replace(' ', '-', $cenario['tit_capa']);;

		            if(!is_dir($caminho_cenario))
		            {
		                mkdir($caminho_cenario, 0777);
		            }

		            if(trim($cenario['arquivo']) != '')
		            {
		            	copy('../cieprev/up/cenario/'.$cenario['arquivo'], $caminho_cenario.'/'.fixUTF8($cenario['arquivo_nome']));
		        	}
		            copy('../cieprev/up/atividade_anexo/'.$args['arquivo'], $caminho_cenario.'/'.fixUTF8($args['arquivo_nome']));
				}
				*/
				
				$nr_conta++;
			}
		
			$this->enviar_novo_anexo($args);

			if(trim($cd_gerencia) == 'GAP')
			{
				$data['ar_atividade'] = $ar_atividade;

				if(trim($data['ar_atividade']['status_atual']) == 'SUST')
				{
					$args['cd_atividade']               = $args['cd_atividade'];
					$args['numero']                     = $args['cd_atividade'];
					$args['dt_env_teste']               = '';
					$args['cd_gerencia_destino']        = trim($cd_gerencia);
					$args['cd_atividade_solucao']       = '';
					$args['cd_solucao_categoria']       = '';
					$args['ds_solucao_assunto']         = '';
					$args['sistema']                    = $data['ar_atividade']['sistema'];
					$args['status_anterior']            = $data['ar_atividade']['status_atual'];
					$args['status_atual']               = 'AIST';
					$args['dt_inicio_prev']             = '';
					$args['dt_fim_prev']                = '';
					$args['dt_inicio_real']             = '';
					$args['dt_limite_teste']            = '';
					$args['fl_teste_relevante']         = '';
					$args['cod_testador']               = '';
					$args['solucao']                    = '';
					$args['complexidade']               = '';
					$args['fl_balanco_gi']              = '';
					$args['cd_atividade_classificacao'] = '';
					$args['cd_gerencia_solicitante']    = $data['ar_atividade']['cd_gerencia_solicitante'];
					$args['cd_empresa']                 = $data['ar_atividade']['cd_empresa'];
					$args['cd_registro_empregado']      = $data['ar_atividade']['cd_registro_empregado'];
					$args['cd_sequencia']               = $data['ar_atividade']['cd_sequencia'];
					$args['cd_usuario']                 = $this->session->userdata('codigo');
					
					$this->atividade_atendimento_model->salvar($result, $args);
				}
			}
		}	
		redirect("atividade/atividade_anexo/index/".intval($args["cd_atividade"])."/".$args["cd_gerencia"], "refresh");
	}
	
	function excluir($cd_atividade, $cd_gerencia, $cd_atividade_anexo)
	{
		$result = null;
		$data = Array();
		$args = Array();
		
		$args['cd_atividade']       = $cd_atividade;
		$args['cd_gerencia']        = $cd_gerencia;
		$args['cd_atividade_anexo'] = $cd_atividade_anexo;
		$args["cd_usuario"]         = $this->session->userdata('codigo');

		$this->atividade_anexo_model->excluir($result, $args);
	
		redirect("atividade/atividade_anexo/index/".intval($args["cd_atividade"])."/".$args["cd_gerencia"], "refresh");
	}
	
	public function enviar_novo_anexo($args)
    {
		$this->load->model('projetos/eventos_email_model');
		
		$cd_evento = 224;
		
		$cd_atividade = intval($args['cd_atividade']);
		
		$email = $this->eventos_email_model->carrega($cd_evento);
		
		$anexo = $this->atividade_anexo_model->anexo_email($cd_atividade);
		
		$email_para = $this->atividade_anexo_model->get_emails($anexo);
		
		$tags = array('[NUMERO_ATIVIDADE]', '[SOLICITANTE]', '[ATENDENTE]', '[STATUS]', '[LINK]');
        $subs = array($cd_atividade, $anexo['solicitante'], $anexo['atendente'].($anexo['substituto'] != '' ? ' / '.$anexo['substituto'] : ''), $anexo['status'], site_url('atividade/atividade_anexo/index/'.intval($anexo['numero']).'/'.trim($anexo['area'])));

		$texto = str_replace($tags, $subs, $email['email']);
		
		$assunto = str_replace('[NUMERO_ATIVIDADE]', $cd_atividade, $email['assunto']);
		
		$cd_usuario = $this->session->userdata('codigo');
		
		$args = array(
			'de'      => 'Atividade - Anexo',
			'assunto' => $assunto,
			'para'    => $email_para['para'],
			'cc'      => $email['cc'],
			'cco'     => $email['cco'],
			'texto'   => $texto
		);
		
		$this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
    }
}


?>