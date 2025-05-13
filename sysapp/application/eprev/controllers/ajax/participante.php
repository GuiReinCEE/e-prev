<?php
class participante extends Controller
{
	function __construct()
	{
		parent::Controller();
	}

	function form_busca_por_nome()
	{
		$this->load->helper('string');
		$data['id']=md5( random_string() );
		$data['jscallback']=$this->input->post('jscallback');
		$data['close']=$this->input->post('close');
		$this->load->view('ajax_simplemodal/participante/busca_por_nome',$data);
	}

	function form_busca_por_nome_post()
	{
		$jscallback        = $this->input->post('jscallback');
		$nome_participante = $this->input->post('nome_participante');

		$qr_sql = "
					SELECT cd_empresa,
						   cd_registro_empregado,
						   seq_dependencia, 
						   cd_empresa || '/' || cd_registro_empregado || '/' || seq_dependencia AS re, 
						   nome 
					  FROM public.participantes 
					 WHERE UPPER(funcoes.remove_acento(nome)) LIKE UPPER(funcoes.remove_acento('%".trim(utf8_decode($nome_participante))."%'))
					 ORDER BY nome
				  ";
		#echo $qr_sql;
				  
		$ob_resul = $this->db->query($qr_sql);
		$ar_reg   = $ob_resul->result_array();

		$body = array();
		$head = array('','RE', 'Nome');

		foreach($ar_reg as $item )
		{
		    $body[] = array(
		        "<a href='javascript:void(0);' onclick='$jscallback( ".$item['cd_empresa'].", ".$item['cd_registro_empregado'].", ".$item['seq_dependencia']." )'>Selecionar</a>",
				$item['re'],
		        array($item['nome'],'text-align:left;')
		    );
		}

		$this->load->helper('grid');
		$grid = new grid();
		$grid->head = $head;
		$grid->body = $body;
		echo $grid->render();
	}

	function nome()
	{
		$emp = (int)$this->input->post('emp');
		$re = (int)$this->input->post('re');
		$seq = (int)$this->input->post('seq');

		$sql = "
			SELECT nome 
			FROM public.participantes 
			WHERE cd_empresa=? 
			AND cd_registro_empregado=? 
			AND seq_dependencia=?
		";

		$query = $this->db->query( $sql, array((int)$emp, (int)$re, (int)$seq) );
		$ret = "";
		if($query)
		{
			$row = $query->row_array();
			if($row)
			{
				$ret = $row["nome"];
			}
		}

		echo $ret;
	}

	function json_object()
	{
		$qr_sql = "
			SELECT p.col_rowid,
                   p.cd_empresa,
                   p.cd_registro_empregado,
                   p.seq_dependencia,
				   p.cd_estado_civil,
                   p.cd_grau_de_instrucao,
                   p.nome,
                   TO_CHAR(p.dt_nascimento, 'DD/MM/YYYY') AS dt_nascimento,
                   p.sexo,
                   EXTRACT(year from AGE(p.dt_nascimento)) AS nr_idade,
				   p.cd_instituicao,
                   p.cd_agencia,
                   p.cd_instituicao_pode_ter_conta_,
                   p.cd_agencia_pode_ter_conta_debi,
			       p.tipo_folha,
                   p.dt_obito,
                   p.logradouro,
                   p.bairro,
			       p.cidade,
                   p.unidade_federativa,
                   p.conta_folha,
			       p.conta_debitos,
                   TO_CHAR(p.cpf_mf,'FM00000000000') AS cpf_mf,
                   funcoes.format_cpf(p.cpf_mf::BIGINT) AS cpf,
                   p.dt_dig_obito,
                   p.bloqueio_ender,
                   p.dt_inicio_beneficio,
			       p.cd_registro_patroc,
                   p.dt_recadastramento,
                   p.dt_envio_recadastramento,
			       p.tipo_recadastramento,
                   p.cd_plano,
                   p.quant_dep_economico,
                   p.data_alteracao_dep_economico,
				   p.fax,
                   p.motivo_devolucao_correio,
                   p.dt_alteracao_endereco,
				   p.dt_envio_certificado,
                   p.dt_recebimento_compl_apos,
			       p.sigla_pais,
                   p.dt_inclusao,
                   p.usu_inclusao,
                   p.dt_alteracao,
                   p.usu_alteracao,
			       p.opcao_ir,
                   p.dt_opcao_ir,
                   p.dt_adesao_instituidor,
                   p.cd_grau_depen_instituidor,
				   TRIM(TO_CHAR(COALESCE(p.ddd,0),'000')) AS ddd,
                   COALESCE(p.telefone,0) AS telefone,
                   p.ramal,
				   TRIM(TO_CHAR(COALESCE(p.ddd_celular,0),'000')) AS ddd_celular,
                   COALESCE(p.celular,0) AS celular,
				   TRIM(TO_CHAR(COALESCE(p.ddd_outro,0),'000')) AS ddd_outro,
                   COALESCE(p.telefone_outro,0) AS telefone_outro,
				   COALESCE(p.email,'') AS email,
				   COALESCE(p.email_profissional,'') AS email_profissional,
				   COALESCE(p.endereco,'') AS endereco,
				   COALESCE(p.nr_endereco,'') AS nr_endereco,
				   COALESCE(p.complemento_endereco,'') AS complemento_endereco,
				   TRIM(TO_CHAR(p.cep, '00000')) AS cep,
				   TRIM(TO_CHAR(p.complemento_cep, '000')) AS complemento_cep,
                   pp.sigla AS ds_empresa,
				   ppn.sigla AS ds_empresa_nova
			  FROM public.participantes p
		      JOIN public.patrocinadoras pp
			    ON pp.cd_empresa = p.cd_empresa
			  LEFT JOIN public.titulares t
				ON t.cd_empresa            = p.cd_empresa
			   AND t.cd_registro_empregado = p.cd_registro_empregado
			   AND t.seq_dependencia       = p.seq_dependencia
			  LEFT JOIN public.patrocinadoras ppn
			    ON ppn.cd_empresa = t.nova_patrocinadora
		     WHERE p.cd_empresa            = ".intval($this->input->post('emp'))."
		       AND p.cd_registro_empregado = ".intval($this->input->post('re'))."
		       AND p.seq_dependencia       = ".intval($this->input->post('seq')).";";

		$row = $this->db->query($qr_sql)->row_array();
		$row = array_map('arrayToUTF8', $row);	
		echo json_encode($row);
	}
}
?>