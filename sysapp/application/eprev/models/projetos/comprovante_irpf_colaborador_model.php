<?php
class comprovante_irpf_colaborador_model extends Model
{
    function __construct()
    {
        parent::Model();
    }
    
	function listar(&$result, $args=array())
    {
        $qr_sql = "
					SELECT DISTINCT cic.cd_comprovante_irpf_colaborador, 
					       cici.cd_registro_empregado,
						   cic.nr_ano_exercicio, 
						   cic.nr_ano_calendario,
						   cic.dt_liberacao
					  FROM projetos.comprovante_irpf_colaborador cic
					  JOIN projetos.comprovante_irpf_colaborador_item cici
						ON cici.cd_comprovante_irpf_colaborador = cic.cd_comprovante_irpf_colaborador
					  JOIN projetos.usuarios_controledi uc
						ON uc.cd_registro_empregado = cici.cd_registro_empregado
					   AND uc.cd_patrocinadora      = 9
					 WHERE cic.dt_exclusao IS NULL
					   AND cici.dt_exclusao IS NULL
					   AND cici.cd_registro_empregado = ".intval($args["cd_re_coladorador"])."
					   ".((trim($this->session->userdata('indic_04')) == "*") ? "AND 1 = 1" : "AND cic.dt_liberacao IS NOT NULL")."
                       ".(trim($args['nr_ano_calendario']) != '' ? "AND cic.nr_ano_calendario = ".intval($args['nr_ano_calendario']) : "")."
                       ".(trim($args['nr_ano_exercicio']) != '' ? "AND cic.nr_ano_exercicio = ".intval($args['nr_ano_exercicio']) : "")."
					ORDER BY cic.nr_ano_calendario DESC
			      ";
        #echo "<PRE>$qr_sql</PRE>";
		$result = $this->db->query($qr_sql);
    }
    
	function comprovante(&$result, $args=array())
    {
        $qr_sql = "
					SELECT cici.nr_linha,
	                       cici.linha
					  FROM projetos.comprovante_irpf_colaborador_item cici
					  JOIN projetos.comprovante_irpf_colaborador cic
					    ON cic.cd_comprovante_irpf_colaborador = cici.cd_comprovante_irpf_colaborador
					   AND cic.dt_exclusao IS NULL
					 WHERE cici.dt_exclusao IS NULL
					   AND MD5(cici.cd_comprovante_irpf_colaborador::TEXT) = '".trim($args["cd_comprovante_irpf_colaborador"])."'
					   AND MD5(cici.cd_registro_empregado::TEXT) = '".trim($args["cd_re_coladorador"])."'
					 ORDER BY cici.nr_linha
			      ";
        #echo "<PRE>$qr_sql</PRE>"; exit;
		$result = $this->db->query($qr_sql);
    }	
}
?>