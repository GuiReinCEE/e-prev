<?php
class Formulario_peculio_model extends Model
{
    function __construct()
    {
        parent::Model();
    }
    
    function participante(&$result, $args=array())
    {
        $qr_sql = "
			SELECT nome,
				   cd_empresa,
				   cd_registro_empregado,
				   seq_dependencia,
				   endereco,
				   nr_endereco,
				   complemento_endereco,
				   bairro,
				   cidade,
				   unidade_federativa,
			       funcoes.format_cpf(TO_CHAR(cpf_mf,'FM00000000000')) AS cpf_mf,
				   TO_CHAR(cep,'FM00000') || '-' || TO_CHAR(complemento_cep,'FM000') AS cep,
				   TO_CHAR(dt_obito, 'DD/MM/YYYY') AS dt_obito
              FROM public.participantes 
             WHERE cd_empresa            = ".intval($args['cd_empresa'])."
               AND cd_registro_empregado = ".intval($args['cd_registro_empregado'])."
               AND seq_dependencia       = ".intval($args['seq_dependencia']).";";

        $result = $this->db->query($qr_sql);
    }
	
	function get_assinatura(&$result, $args=array())
    {
        $qr_sql = "SELECT assinatura 
                     FROM projetos.usuarios_controledi
                    WHERE codigo = ". intval($args['cd_usuario']);

        $result = $this->db->query($qr_sql);
    }
}
?>