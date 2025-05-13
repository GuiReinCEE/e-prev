<?php
class contrato_ppe extends Controller
{
    function __construct()
    {
        parent::Controller();
        
        CheckLogin();
        
        $this->load->model("projetos/contrato_model");
		$this->load->model('projetos/eventos_email_model');
    }


    function index()
    {

        echo "EMAILS GERADOS EM 15/06/2023"; exit;
		$meses = array("janeiro","fevereiro","março","abril","maio","junho","julho","agosto","setembro","outubro","novembro","dezembro");
        
        $result = null;
        $args = Array();
        $data = Array();

		$cd_evento = 456;

		$tpl_email = $this->eventos_email_model->carrega($cd_evento);
		$ar_tags = array('[DATA_LIMITE]','[NOME_FANTASIA]', '[RAZAO_SOCIAL]', '[COD_CONTRATOS]', '[CNPJ]','[AREA_GESTORA]');
		
		$this->contrato_model->listarContratosPPE($result, $args);
		$ar_contrato = $result->result_array(); 

		$i = 0;
		foreach($ar_contrato as $item)
		{
			$ar_subs = array('15/07/2023',$item['ds_empresa'],$item['nm_entidade'],$item['ar_seq_contrato'],$item['nr_registro'],$item['ar_gestor_contrato']);

			$texto_email = str_replace($ar_tags, $ar_subs, $tpl_email['email']);

			$ar_contrato[$i]['assunto_email'] = str_replace('[NOME_FANTASIA]',$item['nm_entidade'], $tpl_email['assunto']);
			$ar_contrato[$i]['texto_email'] = $texto_email;
			$i++; 
		}		

		#echo "<PRE>"; print_r($ar_contrato); #exit;
		
		
		$cd_usuario = $this->session->userdata('codigo');
        
		foreach($ar_contrato as $item)
		{
			$args = array( 
				'tp_email' => 'C',
				'de'       => 'Contratos',
				'assunto'  => $item['assunto_email'],
				'para'     => $item['ar_contato'],  
				'cc'       => $item['ar_email_gestor_contrato'],
				'cco'      => "contratos@familiaprevidencia.com.br",
				'texto'    => $item['texto_email']
			);

			#echo "<PRE>"; print_r($args);

			#$this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);		
		}
		
        
        exit; ######################
            
    }


    
   
}
?>