<?php 
//
// Gerencia o skin usado pelo usu�rio!
// 
// get_header() em helpers/app_helper !
// 

$data['topo_titulo'] = (isset($topo_titulo))?$topo_titulo:"ePrev";

$this->load->view( get_header(), $data ); 
?>