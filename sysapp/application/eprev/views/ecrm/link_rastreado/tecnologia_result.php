<?php
$this->load->helper('grid');

echo '<table border="0" width="100%" cellpadding="5" cellspacing="5">';
#### LINHA 1 ####
echo '
			<tr>
				<td valign="top" align="center" >
	 ';
				#### DEVICE TYPE ####
				$body = array();
				$head = array('Item','Quantidade');
				foreach($ar_DeviceType as $item)
				{
					$body[] = array(
						array($item["ds_item"],"text-align:left;"),
						array(number_format($item["qt_item"],0,",","."),'text-align:right;','int')
					);
				}

				$grid = new grid();
				$grid->head = $head;
				$grid->body = $body;
				$grid->view_count = false;
				echo '<img src="'.$img_DeviceType.'" border="0">';
				echo $grid->render();
	
echo ' 
				</td>
				<td valign="top" align="center">
	 ';
				#### DEVICE NAME ####
				$body = array();
				$head = array('Item','Quantidade');
				foreach($ar_DeviceName as $item)
				{
					$body[] = array(
						array($item["ds_item"],"text-align:left;"),
						array(number_format($item["qt_item"],0,",","."),'text-align:right;','int')
					);
				}

				$grid = new grid();
				$grid->head = $head;
				$grid->body = $body;
				$grid->view_count = false;
				echo '<img src="'.$img_DeviceName.'" border="0">';
				echo $grid->render();
echo ' 
				</td>
			</tr>
	 ';
	 
#### LINHA 2 ####
echo '
			<tr>
				<td valign="top" align="center" >
	 ';
				#### UA TIPO ####
				$body = array();
				$head = array('Item','Quantidade');
				foreach($ar_UATipo as $item)
				{
					$body[] = array(
						array($item["ds_item"],"text-align:left;"),
						array(number_format($item["qt_item"],0,",","."),'text-align:right;','int')
					);
				}

				$grid = new grid();
				$grid->head = $head;
				$grid->body = $body;
				$grid->view_count = false;
				echo '<img src="'.$img_UATipo.'" border="0">';
				echo $grid->render();
	
echo ' 
				</td>
				<td valign="top" align="center">
	 ';
				#### UA FAMILIA ####
				$body = array();
				$head = array('Item','Quantidade');
				foreach($ar_UAFamily as $item)
				{
					$body[] = array(
						array($item["ds_item"],"text-align:left;"),
						array(number_format($item["qt_item"],0,",","."),'text-align:right;','int')
					);
				}

				$grid = new grid();
				$grid->head = $head;
				$grid->body = $body;
				$grid->view_count = false;
				#echo '<img src="'.$img_UAFamily.'" border="0">';
				echo $grid->render();
echo ' 
				</td>
			</tr>
	 ';	 
	 
	 
#### LINHA 3 ####	 
echo '
			<tr>
				<td valign="top" align="center" >
	 ';
				#### OS FAMILIA ####
				$body = array();
				$head = array('Item','Quantidade');
				foreach($ar_OSFamily as $item)
				{
					$body[] = array(
						array($item["ds_item"],"text-align:left;"),
						array(number_format($item["qt_item"],0,",","."),'text-align:right;','int')
					);
				}

				$grid = new grid();
				$grid->head = $head;
				$grid->body = $body;
				$grid->view_count = false;
				echo '<img src="'.$img_OSFamily.'" border="0">';
				echo $grid->render();				
				
echo ' 
				</td>
				<td valign="top" align="center">
	 ';
				
				#### OS NOME ####
				$body = array();
				$head = array('Item','Quantidade');
				foreach($ar_OSName as $item)
				{
					$body[] = array(
						array($item["ds_item"],"text-align:left;"),
						array(number_format($item["qt_item"],0,",","."),'text-align:right;','int')
					);
				}

				$grid = new grid();
				$grid->head = $head;
				$grid->body = $body;
				$grid->view_count = false;
				#echo '<img src="'.$img_OSName.'" border="0">';
				echo $grid->render();
echo ' 
				</td>
			</tr>
	 ';		 
	 
echo '</table>';

echo br(5);
?>