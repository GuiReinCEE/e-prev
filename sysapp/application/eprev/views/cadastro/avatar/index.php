<?php
set_title('Perfil');
$this->load->view('header');
?>
<script language="Javascript">
	<?php
		echo form_default_js_submit
			 (
				Array
					(
						'arquivo',
						'avatar_x',
						'avatar_y',
						'avatar_w',
						'avatar_h',
						'img_w',
						'img_h'
					)
			 );
	?>

	var jcrop_api;
	
	function carregaAvatar()
	{
		$("#arqImgAvatar").html('<img id="imgAvatar" src="<?php echo base_url();?>up/avatar/'+$("#arquivo").val()+'" border="0" style="width:500px">');
		
		if(jQuery.trim($("#arquivo").val()) != "")
		{
			$('#imgAvatar').Jcrop({
				allowSelect: false,
				onSelect:    updateCoords,
				bgColor:     'black',
				bgOpacity:   .4,
				setSelect:   [ 10, 10, 160, 160 ],
				aspectRatio: 1
			});
		}
	}
	
	function updateCoords(c)
	{
		$('#avatar_x').val(c.x);
		$('#avatar_y').val(c.y);
		$('#avatar_w').val(c.w);
		$('#avatar_h').val(c.h);
		
		$('#img_w').val($('#arqImgAvatar').width());
		$('#img_h').val($('#arqImgAvatar').height());
	};	
	
	$(function(){
		if(jQuery.trim($("#arquivo").val()) != "")
		{
			carregaAvatar();
		}
	});

	function ir_cadastro()
	{
		location.href = '<?php echo site_url('cadastro/rh/cadastro/'.intval($ar_usuario["codigo"])); ?>';
	}

	function ir_occorencia()
	{
		location.href = "<?= site_url('cadastro/ocorrencia_ponto/index/'.intval($ar_usuario['codigo'])) ?>";
	}	
	
	function ir_lista()
	{
		location.href = '<?php echo site_url('cadastro/rh'); ?>';
	}
</script>
<?php
	$avatar_arquivo = $ar_usuario["avatar"];
	
	if(trim($avatar_arquivo) == "")
	{
		$avatar_arquivo = $ar_usuario["usuario"].".png";
	}
	
	if(!file_exists( "./up/avatar/".$avatar_arquivo))
	{
		$avatar_arquivo = "user.png";
	}
	
	if(((gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")) OR ($this->session->userdata('indic_05') == "S"))
	{
		$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
		$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
		$abas[] = array('aba_ocorrencia_ponto', 'Ocorrência Ponto', FALSE, 'ir_occorencia();');
	}	
	$abas[] = array('aba_avatar', 'Perfil', TRUE, 'location.reload();');

	echo aba_start( $abas );
	
	echo form_open('cadastro/avatar/salvar');
	
	echo form_start_box("default_box", "Cadastro");
		echo form_default_text('cd_usuario', "Código:", intval($ar_usuario["codigo"]), "style='width:100%;border: 0px;' readonly" );
		echo form_default_text('usuario', "Usuário da rede:", $ar_usuario["usuario"], "style='width:100%;border: 0px;' readonly" );
		echo form_default_row('nome', "Nome:", '<span class="label label-info">'.$ar_usuario["nome"].'</span>');
		echo form_default_text('guerra', "Ident. Usual:", $ar_usuario["guerra"], "style='width:500px;border: 0px;' readonly'");
		echo form_default_row('', "Foto atual:", '<img height="48" width="48" src="'.base_url().'up/avatar/'.$avatar_arquivo.'">');
	echo form_end_box("default_avatar_box");
	
	echo form_start_box("default_box", "Foto");
		echo form_default_upload_iframe('arquivo', 'avatar', 'Arquivo:', array($ar_avatar["arquivo"],$ar_avatar["arquivo_nome"]), 'avatar', TRUE, "carregaAvatar()");
		echo form_default_row("","Selecione:",'<div id="arqImgAvatar"></div>');
		echo form_default_hidden('avatar_x', "X (sel):",$ar_avatar["crop_x"], "style='width:300px;border: 0px;' readonly");
		echo form_default_hidden('avatar_y', "Y (sel):",$ar_avatar["crop_y"], "style='width:300px;border: 0px;' readonly");
		echo form_default_hidden('avatar_w', "Largura (sel):",$ar_avatar["crop_largura"], "style='width:300px;border: 0px;' readonly");
		echo form_default_hidden('avatar_h', "Altura (sel):",$ar_avatar["crop_altura"], "style='width:300px;;border: 0px;' readonly");
		echo form_default_hidden('img_w', "Largura (img):","", "style='width:100%;border: 0px;' readonly");
		echo form_default_hidden('img_h', "Altura (img):","", "style='width:100%;border: 0px;' readonly");
		
	echo form_end_box("default_avatar_box");	
	
	
	echo form_command_bar_detail_start();
		echo button_save("Salvar");
	echo form_command_bar_detail_end();
	
	echo form_close();
	
	echo br(5);
	echo aba_end();
	$this->load->view('footer_interna');
?>