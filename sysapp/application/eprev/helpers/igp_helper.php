<?php
	/**
	 * Gravaзгo de histуrido dos anos anterios dos indicadores do IGP
	 * para exibir na apresentaзгo dos grбficos.
	 *
	 * TODO: Validaзгo dos parвmetros
	 */
	function igp_limpar_historico($cd_indicador)
	{
		$ci=&get_instance();
		$ci->db->query( " DELETE FROM igp.igp_historico ih WHERE ih.cd_indicador=?; ",array(intval($cd_indicador)) );
	}

	function igp_gravar_historico($ano,$meta,$resultado,$desvio_meta,$cd_indicador)
	{
		$ci=&get_instance();
		$query = $ci->db->query( " INSERT INTO igp.igp_historico ( cd_indicador,nr_ano, nr_meta, nr_resultado_acumulado, nr_desvio_meta ) VALUES ( ?,?,?,?,? ); ",
			array( intval($cd_indicador), intval($ano),floatval($meta),floatval($resultado),floatval($desvio_meta) ) );
	}
?>