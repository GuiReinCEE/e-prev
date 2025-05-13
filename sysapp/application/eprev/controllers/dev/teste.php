<?php
class teste extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

	function index()
	{
		$dados['title'] = '';
		$dados['content'] = anchor( 'dev/teste/protocolo_interno', 'ECRM - Protocolo Interno' ).br();
		$this->load->view('dev/teste.php', $dados);
	}

    function protocolo_interno()
    {
    	$this->load->library('unit_test');    

    	$this->load->model( 'projetos/Documento_recebido_model','m' );

    	// *** protocolo interno - model - relatorio

    	$res=array();
    	$args['ano']='';
		$args['contador']='';
		$args['cd_empresa']='';
		$args['cd_registro_empregado']='';
		$args['seq_dependencia']='';
		$args['nome']='';
		$args['cd_tipo_doc']='';
		$args['dt_envio_inicio']='';
		$args['dt_envio_fim']='';
		$args['dt_ok_inicio']='';
		$args['dt_ok_fim']='';
		$args['cd_usuario_envio']='';
		$args['cd_usuario_destino']='';
		$b = $this->m->relatorio( $res, $args );

    	$this->unit->run($b, true, 'Retorno do método Documento_recebido_model::relatorio');
    	$this->unit->run($res, 'is_array', 'Resultado do método Documento_recebido_model::relatorio');

    	// *** protocolo interno - model - listar

    	$dados['title'] = 'Protoloco Interno - model';
    	$dados['content'] = $this->unit->report();
		$this->load->view('dev/teste.php', $dados);
    }

	function usar_template_test()
	{
		usar_template( 
			'protocolo_interno/email_envio.txt', 
			array( '{LINK}'=>'http://www.google.com' ) 
		);
	}
}
