<style>
	.circle_equipe {
		line-height: 0;		/* remove line-height */ 
		display: inline-block;	/* circle wraps image */
		margin: 5px;
		border-radius: 50%;	
	    -webkit-border-radius: 50%;
	    -moz-border-radius: 50%;		
		transition: linear 0.25s;
		height: 128px;
		width: 128px;
		-webkit-box-shadow: 0 0 0 3px #fff, 0 0 0 4px #999, 0 2px 5px 4px rgba(0,0,0,.2);
	    -moz-box-shadow: 0 0 0 3px #fff, 0 0 0 4px #999, 0 2px 5px 4px rgba(0,0,0,.2);
	    box-shadow: 0 0 0 3px #fff, 0 0 0 4px #999, 0 2px 5px 4px rgba(0,0,0,.2);		
	}
	.circle_equipe img {
		border-radius: 50%;	/* relative value for adjustable image size */
	    -webkit-border-radius: 50%;
	    -moz-border-radius: 50%;	
	}
	.circle_equipe:hover {
		transition: ease-out 0.2s;
		-webkit-transition: ease-out 0.2s;
	}
	a.circle_equipe {
		color: transparent;
	} /* IE fix: removes blue border */
</style>
<center>
	<div style="width: 90%">
		<!--
		<div style="float:left; margin-right: 2%; width: 260px; height: 260px;">			
			<img border="0" width="260" src="<?= base_url('img/flag_area/'.($cd_divisao == 'GAP.' ? 'GAP' : $cd_divisao).'.jpg') ?>">
		</div>
		-->
		<?php
			foreach($collection as $item)
			{
				$avatar_arquivo = trim($item['avatar']);

				$ferias = '';
				
				if (trim($item['fl_ferias']) == 'S')
				{
					#$ferias = '<img src="'.base_url().'img/travel.png" border="0" title="EM FÉRIAS ('.$item['periodo_ferias'].')"> ';
					$ferias = '
						<div style="width: 100% text-aling:center; margin-top: 2px;">
							<span style="color:blue; font-weight:bold;">em férias</span>
							<br/>
							('.$item['periodo_ferias'].')
						</div>';
				}
				
				echo '
					<div style="float:left; margin-right: 2%; width: 180px; height: 260px;">
						<div href="javascript: void(0);" class="circle_equipe">
							<img class="corner iradius128" height="128" width="128" src="'.base_url('up/avatar/'.$avatar_arquivo).'">
						</div>
						<div style="width: 100% text-aling:center; font-weight:bold;">'.trim($item['nome']).'</div>
						<div style="width: 100% text-aling:center; margin-top: 2px;">'.trim($item['papel']).'</div>
						'.$ferias.'				
						<div style="width: 100% text-aling:center; font-weight:bold; margin-top: 2px;">
							<table border="0">
								<tr>
									<td>
										<img src="'.base_url('img/telefone_p.png').'" border="0">
									</td>
									<td>'.trim($item['nr_ramal']).'</td>
								</tr>
							</table>
						</div>
					</div>';		
			}

			$head = array( 
				'Processo',
				'Envolvidos'
			);

			$body = array();

			foreach($processos as $item)
			{
				$body[] = array(
					array($item['procedimento'], 'text-align:left;'),
				    array(implode(br(), $item['usuario_responsavel']), 'text-align:left;')
				);
			}

			$this->load->helper('grid');
			$grid = new grid();
			$grid->head = $head;
			$grid->body = $body;
			$grid->view_count = false;

			echo $grid->render();
		?>

	</div>
	<div style="clear:both;"><?= br(6) ?></div>
</center>