<?php
class Solic_fiscalizacao_audit_recorrencia extends Controller
{
	function __construct()
    {
        parent::Controller();
    }

    public function gera()
    {
    	
    }
	
	public function copia_modelo($cd_solic_fiscalizacao_audit, $cd_solic_fiscalizacao_audit_recorrencia)
	{
		$this->load->model('projetos/solic_fiscalizacao_audit_recorrencia_model');
		
		$documentacao = $this->solic_fiscalizacao_audit_recorrencia_model->get_documentacao_original($cd_solic_fiscalizacao_audit);
		
		foreach ($documentacao as $key => $item) 
		{
			$cd_solic_fiscalizacao_audit_recorrencia_documentacao = $this->solic_fiscalizacao_audit_recorrencia_model->insere_documento($cd_solic_fiscalizacao_audit_recorrencia, $item);
			
			$responsavel = $this->solic_fiscalizacao_audit_recorrencia_model->get_responsavel_documentacao_original($item['cd_solic_fiscalizacao_audit_documentacao']);
			
			foreach($responsavel as $key2 => $item2)
			{
				$this->solic_fiscalizacao_audit_recorrencia_model->insere_documento_responsavel($cd_solic_fiscalizacao_audit_recorrencia_documentacao, $item2);
			}
		}
	}

    public function gera_mensal($nr_dia_recorrencia = '')
    {
    	$this->load->model('projetos/solic_fiscalizacao_audit_recorrencia_model');
    	
    	if(intval($nr_dia_recorrencia) == 0)
    	{
    		$nr_dia_recorrencia = date('d');
    	}
    
    	$recorrencia = $this->solic_fiscalizacao_audit_recorrencia_model->get_mensal($nr_dia_recorrencia);
    	
    	if(count($recorrencia) > 0)
    	{
    		$this->load->model(array(
	            'projetos/solic_fiscalizacao_audit_model',
	            'projetos/eventos_email_model'
	        ));

    		echo '<pre>';

    		foreach ($recorrencia as $key => $item) 
	    	{
	    		$cd_usuario = 999999;

	    		$args = array(
	                'cd_solic_fiscalizacao_audit_origem' => $item['cd_solic_fiscalizacao_audit_origem'],
	                'ds_origem'                          => $item['ds_origem'],
	                'dt_recebimento'                     => $item['dt_recebimento'],
	                'cd_solic_fiscalizacao_audit_tipo'   => $item['cd_solic_fiscalizacao_audit_tipo'],
	                'ds_tipo'                            => $item['ds_tipo'],
	                'cd_gerencia'                        => $item['cd_gerencia'],
	                'ds_documento'                       => $item['ds_documento'],
	                'ds_teor'                            => $item['ds_teor'],
	                'fl_prazo'                           => 'D',
	                'nr_dias_prazo'                      => '',
	                'dt_prazo'                           => $item['dt_prazo'],
	                'arquivo'                            => '',
	                'arquivo_nome'                       => '',
	                'cd_correspondencia_recebida_item'   => '',
	                'cd_usuario'                         => $cd_usuario,
	                'gerencia_opcional'                  => array(),
	                'gestao'                             => array(),
	                'grupo_opcional'                     => array()
	            );

	    		foreach ($this->solic_fiscalizacao_audit_recorrencia_model->get_gestao($item['cd_solic_fiscalizacao_audit_recorrencia']) as $key2 => $item2) 
	    		{
	    			$args['gestao'][] = $item['cd_gerencia'];
	    		}

	    		$cd_solic_fiscalizacao_audit = $this->solic_fiscalizacao_audit_model->salvar($args);

	    		$this->solic_fiscalizacao_audit_model->enviar(
	                intval($cd_solic_fiscalizacao_audit), 
	                0,
	                $cd_usuario
	            );

	    		$row = $this->solic_fiscalizacao_audit_model->carrega($cd_solic_fiscalizacao_audit);

	    		$this->enviar_email_gestao($row);

	    		echo $cd_solic_fiscalizacao_audit.br();
	    		echo 'DOCUMENTAÇÃO'.br();
	    		foreach ($this->solic_fiscalizacao_audit_recorrencia_model->get_documentacao($item['cd_solic_fiscalizacao_audit_recorrencia']) as $key2 => $item2) 
	    		{
	    			$usuario = array();

	    			foreach ($this->solic_fiscalizacao_audit_recorrencia_model->get_documentacao_responsavel($item2['cd_solic_fiscalizacao_audit_recorrencia_documentacao']) as $key3 => $item3) 
	    			{
	    				$usuario[] = $item3['cd_usuario'];
	    			}

	    			$args = array(
		                'cd_solic_fiscalizacao_audit'              => $cd_solic_fiscalizacao_audit,
		                'ds_solic_fiscalizacao_audit_documentacao' => $item2['ds_solic_fiscalizacao_audit_documentacao'],
		                'nr_item'                                  => $item2['nr_item'],
		                'cd_gerencia'                              => $item2['cd_gerencia'],
		                'dt_prazo_retorno'                         => $item['dt_prazo'],
		                'usuario'                                  => $usuario,
		                'cd_usuario'                               => $cd_usuario,
		                'fl_verificar_gerencia'                    => 'N'
		            );

		            $cd_solic_fiscalizacao_audit_documentacao = $this->solic_fiscalizacao_audit_model->salvar_documentacao($args);

		            echo $cd_solic_fiscalizacao_audit_documentacao.br();
	    		}

	    		$this->solic_fiscalizacao_audit_model->enviar_solicitacao(
	                $cd_solic_fiscalizacao_audit, 
	                $cd_usuario
	            );

	            foreach ($this->solic_fiscalizacao_audit_model->listar_documentacao($cd_solic_fiscalizacao_audit) as $item2)
	            {
	                $this->solic_fiscalizacao_audit_model->salvar_envio_solicitacao(
	                    $item2['cd_solic_fiscalizacao_audit_documentacao'],
	                    $cd_usuario
	                );

	                $this->email_solicitacao_documento($cd_solic_fiscalizacao_audit, $item2['cd_solic_fiscalizacao_audit_documentacao']);
	            }
	    	}
    	}
    }

    public function enviar_email_gestao($solic_fiscalizacao_audit)
    {
    	$gestao = array();

        foreach($this->solic_fiscalizacao_audit_model->get_gerencia_opcional(intval($solic_fiscalizacao_audit['cd_solic_fiscalizacao_audit'])) as $gerencia)
        {               
            $gestao[] = strtolower($gerencia['cd_gerencia']).'@eletroceee.com.br';
        }    
   
        foreach($this->solic_fiscalizacao_audit_model->get_gestao(intval($solic_fiscalizacao_audit['cd_solic_fiscalizacao_audit'])) as $gerencia)
        {               
            $gestao[] = strtolower($gerencia['cd_gerencia']).'@eletroceee.com.br';
        }

        foreach($this->solic_fiscalizacao_audit_model->get_gerencia_opcional_grupo(intval($solic_fiscalizacao_audit['cd_solic_fiscalizacao_audit'])) as $grupo)
        {               
            if(trim($grupo['ds_email_grupo']) == '')
            {
            	foreach ($this->solic_fiscalizacao_audit_model->get_gerencia_opcional_grupo_integrante($grupo['cd_solic_fiscalizacao_audit_grupo']) as $grupo2) 
            	{
            		$gestao[] = strtolower($grupo2['ds_usuario']).'@eletroceee.com.br';
            	}
            }
            else
            {
            	$gestao[] = strtolower($grupo['ds_email_grupo']);
            }
        } 

        if(count($gestao) > 0)
        {
            $cd_evento = 299;

            $email = $this->eventos_email_model->carrega($cd_evento);
          
            $tags = array('[DS_NOME_DOCUMENTO]', '[DT_RECEBIMENTO]', '[DS_REMETENTE]', '[DS_TEOR]', '[DT_PRAZO]');

            $subs = array(
                $solic_fiscalizacao_audit['ds_documento'],
                $solic_fiscalizacao_audit['dt_recebimento'],
                $solic_fiscalizacao_audit['ds_solic_fiscalizacao_audit_origem'],
                $solic_fiscalizacao_audit['ds_teor'],
                $solic_fiscalizacao_audit['dt_prazo']
            );

            $texto = str_replace($tags, $subs, $email['email']);

            $cd_usuario = $this->session->userdata('codigo');

            $args = array( 
                'de'      => 'Registro de Solicitações, Fiscalizações e Auditorias',
                'assunto' => $email['assunto'],
                'para'    => '',
                'cc'      => 'lrodriguez@eletroceee.com.br', //implode(';', $gestao),  
                'cco'     => $email['cco'],
                'texto'   => $texto
            );

            $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);  
        }
    }

    public function email_solicitacao_documento($cd_solic_fiscalizacao_audit, $cd_solic_fiscalizacao_audit_documentacao)
    {
    	$cd_evento = 347;

        $email = $this->eventos_email_model->carrega($cd_evento);   
        
        $documentacao = $this->solic_fiscalizacao_audit_model->carrega_documentacao(intval($cd_solic_fiscalizacao_audit_documentacao));

        $responsavel = array();

        $usuario = $this->solic_fiscalizacao_audit_model->get_usuario_responsavel(intval($documentacao['cd_solic_fiscalizacao_audit_documentacao']));

        if(count($usuario) > 0)
        {
            foreach ($usuario as $key2 => $item2) 
            {
                $responsavel[] = $item2['ds_usuario_email'];
            }
        }
        else
        {
            $responsavel[] = $documentacao['cd_gerencia'].'@eletroceee.com.br';
        }

        $row = $this->solic_fiscalizacao_audit_model->carrega($cd_solic_fiscalizacao_audit);

        $row['ext'] = pathinfo($row['arquivo'], PATHINFO_EXTENSION);

        $ds_info_email = 'para providências';
        $ds_assunto = 'Solicitação de Documentação';

        if(trim($documentacao['fl_verificar_gerencia']) == 'S')
        {
            $ds_info_email = 'para validação de competência desta Gerência';
            $ds_assunto = 'Validar Competência';
        }

        $tags = array('[DS_INFO_EMAIL]','[DS_DOCUMENTO]', '[DS_ORIGEM]', '[DS_TIPO]', '[DS_DESCRICAO_ITEM]', '[DT_PRAZO]', '[LINK]');

        $subs = array(
            $ds_info_email,
            $row['ds_documento'],
            $row['ds_solic_fiscalizacao_audit_origem'],
            $row['ds_solic_fiscalizacao_audit_tipo'],
            $documentacao['ds_solic_fiscalizacao_audit_documentacao'],
            $documentacao['dt_prazo_retorno'],
            site_url('atividade/solic_fiscalizacao_audit/responder/'.intval($documentacao['cd_solic_fiscalizacao_audit_documentacao']))
        );

        $texto = str_replace($tags, $subs, $email['email']);

        $cd_usuario = $this->session->userdata('codigo');

        $args = array( 
            'de'      => 'Registro de Solicitações, Fiscalizações e Auditorias',
            'assunto' => str_replace('[DS_ASSUNTO]', $ds_assunto, $email['assunto']),
            'para'    => 'lrodriguez@eletroceee.com.br', //strtolower(implode(';', $responsavel)),
            'cc'      => $email['cc'],
            'cco'     => $email['cco'],
            'texto'   => $texto
        );

        $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
    }
}