<?php
class Indicacao extends Controller
{
	function __construct()
	{
		parent::Controller();

		CheckLogin();
	}

	public function index()
	{
		$this->load->model('expansao/indicacao_model');

		$data = array(
			'collection'    => $this->indicacao_model->listar($this->session->userdata('codigo')),
			'drop_usuarios' => $this->indicacao_model->get_usuarios(),
			'cd_usuario'    => $this->session->userdata('codigo')
		);

		$data['drop_parentesco'] = array(
			array('value' => 'AVÔ/AVÓ',           'text' => 'AVÔ/AVÓ'),
			array('value' => 'BISAVÔ/BISAVÓ',     'text' => 'BISAVÔ/BISAVÓ'),
			array('value' => 'COMPANHEIRO(A)',    'text' => 'COMPANHEIRO(A)'),
			array('value' => 'DESIGNADO INSS',    'text' => 'DESIGNADO INSS'),
			array('value' => 'ENTEADO(A)',        'text' => 'ENTEADO(A)'),
			array('value' => 'EX-EXPOSA(O)',      'text' => 'EX-EXPOSA(O)'),
			array('value' => 'FILHO(A)',          'text' => 'FILHO(A)'),
			array('value' => 'IRMÃO/IRMÃ',        'text' => 'IRMÃO/IRMÃ'),
			array('value' => 'MARIDO/ESPOSA',     'text' => 'MARIDO/ESPOSA'),
			array('value' => 'MENOR SOB GUARDA',  'text' => 'MENOR SOB GUARDA'),
			array('value' => 'MENOR SOB TUTELA',  'text' => 'MENOR SOB TUTELA'),
			array('value' => 'NÃO POSSUI', 	      'text' => 'NÃO POSSUI'),
			array('value' => 'NETO/BISNETO',      'text' => 'NETO/BISNETO'),
			array('value' => 'OUTROS IR',         'text' => 'OUTROS IR'),
			array('value' => 'PAI/MÃE',           'text' => 'PAI/MÃE'),
			array('value' => 'RESPONSAVEL LEGAL', 'text' => 'RESPONSAVEL LEGAL'),
			array('value' => 'SOBRINHO(A)',       'text' => 'SOBRINHO(A)'),
			array('value' => 'TIO(A)',            'text' => 'TIO(A)')
		);

		$data['drop_tipo_indicacao'] = array(
			array('value' => 'Nova adesão',             'text' => 'Nova adesão'),
			array('value' => 'Aumento de contribuição', 'text' => 'Aumento de contribuição'),
			array('value' => 'Aporte',                  'text' => 'Aporte'),
			array('value' => 'Portabilidade',           'text' => 'Portabilidade')
		);

		$this->load->view('planos/indicacao/index', $data);
	}

	public function salvar()
	{
		$this->load->model('expansao/indicacao_model');

		$cd_usuario_indicacao = $this->input->post('cd_usuario_indicacao', TRUE);
		$row 				  = $this->indicacao_model->get_usuario($cd_usuario_indicacao);

		$ds_observacao 		  = $this->input->post('ds_observacao', TRUE);
		$ds_parentesco 		  = $this->input->post('ds_parentesco', TRUE);

		$ds_observacao_interessado = 'Indicado por '.$row['ds_usuario_inclusao'].
									 (trim($ds_parentesco) !=  '' ? "\nGrau de Parentesco : ".$ds_parentesco : '').
									 (trim($ds_observacao) !=  '' ? "\n\nObservações : ".$ds_observacao : '');

		$args = array(
			'ds_indicado'   			=> $this->input->post('ds_indicado', TRUE),
			'nr_telefone'   			=> $this->input->post('nr_telefone', TRUE),
			'ds_email'      			=> $this->input->post('ds_email', TRUE),
			'ds_parentesco' 			=> $ds_parentesco,
			'ds_cidade'     			=> $this->input->post('ds_cidade', TRUE),
			'ds_observacao' 			=> $ds_observacao,
			'ds_observacao_interessado' => $ds_observacao_interessado,
			'ds_tipo_indicacao'         => $this->input->post('ds_tipo_indicacao', TRUE),
			'cd_usuario'    			=> $row['cd_usuario'],
			'cd_gerencia'   			=> $row['cd_gerencia']
		);

		$this->indicacao_model->salvar($args);

		$this->indicacao_model->salvar_interessado_familia($args);

		$this->envia_email_interessado($args);

		redirect('planos/indicacao', 'refresh');
	}

	private function envia_email_interessado($args)
	{
 		$this->load->model('projetos/eventos_email_model');

        $cd_evento = 381;

        $email = $this->eventos_email_model->carrega($cd_evento);
      
        $tags = array('[NOME]', '[EMAIL]', '[TELEFONE]', '[CIDADE]', '[COMENTARIO]');

        $subs = array(
            $args['ds_indicado'],
            $args['ds_email'],
            $args['nr_telefone'],
            $args['ds_cidade'],
            $args['ds_observacao']
        );

        $texto = str_replace($tags, $subs, $email['email']);

        $cd_usuario = $this->session->userdata('codigo');

        $args = array( 
            'de'      => 'Registro de Solicitações, Fiscalizações e Auditorias',
            'assunto' => $email['assunto'],
            'para'    => $email['para'],
            'cc'      => $email['cc'],
            'cco'     => $email['cco'],
            'texto'   => $texto
        );

        $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);   
	}
}