<?php
#header("Content-Type: text/html; charset=iso-8859-1");
include ('inc/fpdf153/fpdf.php');
include ('inc/sessao.php');
include ('inc/conexao.php');
include ('inc/ePrev.Service.Projetos.php');


class PDF extends FPDF
{
	var $widths;
	var $aligns;

	function SetWidths($w)
	{
	    //Set the array of column widths
	    $this->widths=$w;
	}

	function SetAligns($a)
	{
	    //Set the array of column alignments
	    $this->aligns=$a;
	}

	function Row($data)
	{
	    //Calculate the height of the row
	    $nb=0;
	    for($i=0;$i<count($data);$i++)
	        $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
	    $h=5*$nb;
	    //Issue a page break first if needed
	    $this->CheckPageBreak($h);
	    //Draw the cells of the row
	    for($i=0;$i<count($data);$i++)
	    {
	        $w=$this->widths[$i];
	        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
	        //Save the current position
	        $x=$this->GetX();
	        $y=$this->GetY();
	        //Draw the border
	        $this->Rect($x,$y,$w,$h);
	        //Print the text
	        $this->MultiCell($w,5,$data[$i],0,$a);
	        //Put the position to the right of the cell
	        $this->SetXY($x+$w,$y);
	    }
	    //Go to the next line
	    $this->Ln($h);
	}

	function CheckPageBreak($h)
	{
	    //If the height h would cause an overflow, add a new page immediately
	    if($this->GetY()+$h>$this->PageBreakTrigger)
	        $this->AddPage($this->CurOrientation);
	}

	function NbLines($w,$txt)
	{
	    //Computes the number of lines a MultiCell of width w will take
	    $cw=&$this->CurrentFont['cw'];
	    if($w==0)
	        $w=$this->w-$this->rMargin-$this->x;
	    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
	    $s=str_replace("\r",'',$txt);
	    $nb=strlen($s);
	    if($nb>0 and $s[$nb-1]=="\n")
	        $nb--;
	    $sep=-1;
	    $i=0;
	    $j=0;
	    $l=0;
	    $nl=1;
	    while($i<$nb)
	    {
	        $c=$s[$i];
	        if($c=="\n")
	        {
	            $i++;
	            $sep=-1;
	            $j=$i;
	            $l=0;
	            $nl++;
	            continue;
	        }
	        if($c==' ')
	            $sep=$i;
	        $l+=$cw[$c];
	        if($l>$wmax)
	        {
	            if($sep==-1)
	            {
	                if($i==$j)
	                    $i++;
	            }
	            else
	                $i=$sep+1;
	            $sep=-1;
	            $j=$i;
	            $l=0;
	            $nl++;
	        }
	        else
	            $i++;
	    }
	    return $nl;
	}		
	
    /**
     * Sets line style
     *
     * @param string $style Line style. Array with keys among the following:
     *         . width: Width of the line in user units
     *         . cap: Type of cap to put on the line (butt, round, square). The difference between 'square' and 'butt' is that 'square' projects a flat end past the end of the line.
     *         . join: miter, round or bevel
     *         . dash: Dash pattern. Is 0 (without dash) or array with series of length values, which are the lengths of the on and off dashes.
     *                 For example: (2) represents 2 on, 2 off, 2 on , 2 off ...
     *                              (2,1) is 2 on, 1 off, 2 on, 1 off.. etc
     *         . phase: Modifier of the dash pattern which is used to shift the point at which the pattern starts
     *         . color: Draw color. Array with components (red, green, blue)
     */
    function SetLineStyle($style) {
        extract($style);
        if (isset($width)) {
            $width_prev = $this->LineWidth;
            $this->SetLineWidth($width);
            $this->LineWidth = $width_prev;
        }
        if (isset($cap)) {
            $ca = array('butt' => 0, 'round'=> 1, 'square' => 2);
            if (isset($ca[$cap]))
                $this->_out($ca[$cap] . ' J');
        }
        if (isset($join)) {
            $ja = array('miter' => 0, 'round' => 1, 'bevel' => 2);
            if (isset($ja[$join]))
                $this->_out($ja[$join] . ' j');
        }
        if (isset($dash)) {
            $dash_string = '';
            if ($dash) {
                if(preg_match('/^.+,/', $dash))
                    $tab = explode(',', $dash);
                else
                    $tab = array($dash);
                $dash_string = '';
                foreach ($tab as $i => $v) {
                    if ($i > 0)
                        $dash_string .= ' ';
                    $dash_string .= sprintf('%.2f', $v);
                }
            }
            if (!isset($phase) || !$dash)
                $phase = 0;
            $this->_out(sprintf('[%s] %.2f d', $dash_string, $phase));
        }
        if (isset($color)) {
            list($r, $g, $b) = $color;
            $this->SetDrawColor($r, $g, $b);
        }
    }

    /**
     * Draws a line
     *
     * @param unknown_type $x1 Start point
     * @param unknown_type $y1 Start point
     * @param unknown_type $x2 End point
     * @param unknown_type $y2 End point
     * @param unknown_type $style Line style. Array like for SetLineStyle
     */
    function Line($x1, $y1, $x2, $y2, $style = null) {
        if ($style)
            $this->SetLineStyle($style);
        parent::Line($x1, $y1, $x2, $y2);
    }

    /**
     * Draws a rectangle
     *
     * @param unknown_type $x Top left corner
     * @param unknown_type $y Top left corner
     * @param unknown_type $w Width and height
     * @param unknown_type $h Width and height
     * @param unknown_type $style Style of rectangle (draw and/or fill: D, F, DF, FD)
     * @param unknown_type $border_style Border style of rectangle. Array with some of this index
     *    . all: Line style of all borders. Array like for SetLineStyle
     *    . L: Line style of left border. null (no border) or array like for SetLineStyle
     *    . T: Line style of top border. null (no border) or array like for SetLineStyle
     *    . R: Line style of right border. null (no border) or array like for SetLineStyl
     *    . B: Line style of bottom border. null (no border) or array like for SetLineStylee
     * @param unknown_type $fill_color Fill color. Array with components (red, green, blue)
     */
    function Rect($x, $y, $w, $h, $style = '', $border_style = null, $fill_color = null) {
        if (!(false === strpos($style, 'F')) && $fill_color) {
            list($r, $g, $b) = $fill_color;
            $this->SetFillColor($r, $g, $b);
        }
        switch ($style) {
            case 'F':
                $border_style = null;
                parent::Rect($x, $y, $w, $h, $style);
                break;
            case 'DF': case 'FD':
                if (!$border_style || isset($border_style['all'])) {
                    if (isset($border_style['all'])) {
                        $this->SetLineStyle($border_style['all']);
                        $border_style = null;
                    }
                } else
                    $style = 'F';
                parent::Rect($x, $y, $w, $h, $style);
                break;
            default:
                if (!$border_style || isset($border_style['all'])) {
                    if (isset($border_style['all']) && $border_style['all']) {
                        $this->SetLineStyle($border_style['all']);
                        $border_style = null;
                    }
                    parent::Rect($x, $y, $w, $h, $style);
                }
                break;
        }
        if ($border_style) {
            if (isset($border_style['L']) && $border_style['L'])
                $this->Line($x, $y, $x, $y + $h, $border_style['L']);
            if (isset($border_style['T']) && $border_style['T'])
                $this->Line($x, $y, $x + $w, $y, $border_style['T']);
            if (isset($border_style['R']) && $border_style['R'])
                $this->Line($x + $w, $y, $x + $w, $y + $h, $border_style['R']);
            if (isset($border_style['B']) && $border_style['B'])
                $this->Line($x, $y + $h, $x + $w, $y + $h, $border_style['B']);
        }
    }

    /**
     * Draws a Bézier curve (the Bézier curve is tangent to the line between the control points at either end of the curve)
     *
     * @param unknown_type $x0 Start point
     * @param unknown_type $y0 Start point
     * @param unknown_type $x1 Control point 1
     * @param unknown_type $y1 Control point 1
     * @param unknown_type $x2 Control point 2
     * @param unknown_type $y2 Control point 2
     * @param unknown_type $x3 End point
     * @param unknown_type $y3 End point
     * @param unknown_type $style Style of rectangule (draw and/or fill: D, F, DF, FD)
     * @param unknown_type $line_style Line style for curve. Array like for SetLineStyle
     * @param unknown_type $fill_color Fill color. Array with components (red, green, blue)
     */
    function Curve($x0, $y0, $x1, $y1, $x2, $y2, $x3, $y3, $style = '', $line_style = null, $fill_color = null) {
        if (!(false === strpos($style, 'F')) && $fill_color) {
            list($r, $g, $b) = $fill_color;
            $this->SetFillColor($r, $g, $b);
        }
        switch ($style) {
            case 'F':
                $op = 'f';
                $line_style = null;
                break;
            case 'FD': case 'DF':
                $op = 'B';
                break;
            default:
                $op = 'S';
                break;
        }
        if ($line_style)
            $this->SetLineStyle($line_style);

        $this->_Point($x0, $y0);
        $this->_Curve($x1, $y1, $x2, $y2, $x3, $y3);
        $this->_out($op);
    }

    /**
     * Draws an ellipse
     *
     * @param unknown_type $x0 Center point
     * @param unknown_type $y0 Center point
     * @param unknown_type $rx Horizontal and vertical radius (if ry = 0, draws a circle)
     * @param unknown_type $ry Horizontal and vertical radius (if ry = 0, draws a circle)
     * @param unknown_type $angle Orientation angle (anti-clockwise)
     * @param unknown_type $astart Start angle
     * @param unknown_type $afinish Finish angle
     * @param unknown_type $style Style of ellipse (draw and/or fill: D, F, DF, FD, C (D + close))
     * @param unknown_type $line_style Line style for ellipse. Array like for SetLineStyle
     * @param unknown_type $fill_color Fill color. Array with components (red, green, blue)
     * @param unknown_type $nSeg Ellipse is made up of nSeg Bézier curves
     */
    function Ellipse($x0, $y0, $rx, $ry = 0, $angle = 0, $astart = 0, $afinish = 360, $style = '', $line_style = null, $fill_color = null, $nSeg = 8) {
        if ($rx) {
            if (!(false === strpos($style, 'F')) && $fill_color) {
                list($r, $g, $b) = $fill_color;
                $this->SetFillColor($r, $g, $b);
            }
            switch ($style) {
                case 'F':
                    $op = 'f';
                    $line_style = null;
                    break;
                case 'FD': case 'DF':
                    $op = 'B';
                    break;
                case 'C':
                    $op = 's'; // small 's' means closing the path as well
                    break;
                default:
                    $op = 'S';
                    break;
            }
            if ($line_style)
                $this->SetLineStyle($line_style);
            if (!$ry)
                $ry = $rx;
            $rx *= $this->k;
            $ry *= $this->k;
            if ($nSeg < 2)
                $nSeg = 2;

            $astart = deg2rad((float) $astart);
            $afinish = deg2rad((float) $afinish);
            $totalAngle = $afinish - $astart;

            $dt = $totalAngle/$nSeg;
            $dtm = $dt/3;

            $x0 *= $this->k;
            $y0 = ($this->h - $y0) * $this->k;
            if ($angle != 0) {
                $a = -deg2rad((float) $angle);
                $this->_out(sprintf('q %.2f %.2f %.2f %.2f %.2f %.2f cm', cos($a), -1 * sin($a), sin($a), cos($a), $x0, $y0));
                $x0 = 0;
                $y0 = 0;
            }

            $t1 = $astart;
            $a0 = $x0 + ($rx * cos($t1));
            $b0 = $y0 + ($ry * sin($t1));
            $c0 = -$rx * sin($t1);
            $d0 = $ry * cos($t1);
            $this->_Point($a0 / $this->k, $this->h - ($b0 / $this->k));
            for ($i = 1; $i <= $nSeg; $i++) {
                // Draw this bit of the total curve
                $t1 = ($i * $dt) + $astart;
                $a1 = $x0 + ($rx * cos($t1));
                $b1 = $y0 + ($ry * sin($t1));
                $c1 = -$rx * sin($t1);
                $d1 = $ry * cos($t1);
                $this->_Curve(($a0 + ($c0 * $dtm)) / $this->k,
                            $this->h - (($b0 + ($d0 * $dtm)) / $this->k),
                            ($a1 - ($c1 * $dtm)) / $this->k,
                            $this->h - (($b1 - ($d1 * $dtm)) / $this->k),
                            $a1 / $this->k,
                            $this->h - ($b1 / $this->k));
                $a0 = $a1;
                $b0 = $b1;
                $c0 = $c1;
                $d0 = $d1;
            }
            $this->_out($op);
            if ($angle !=0)
                $this->_out('Q');
        }
    }

    // Draws a circle
    // Parameters:
    // - x0, y0: Center point
    // - r: Radius
    // - astart: Start angle
    // - afinish: Finish angle
    // - style: Style of circle (draw and/or fill) (D, F, DF, FD, C (D + close))
    // - line_style: Line style for circle. Array like for SetLineStyle
    // - fill_color: Fill color. Array with components (red, green, blue)
    // - nSeg: Ellipse is made up of nSeg Bézier curves
    function Circle($x0, $y0, $r, $astart = 0, $afinish = 360, $style = '', $line_style = null, $fill_color = null, $nSeg = 8) {
        $this->Ellipse($x0, $y0, $r, 0, 0, $astart, $afinish, $style, $line_style, $fill_color, $nSeg);
    }

    // Draws a polygon
    // Parameters:
    // - p: Points. Array with values x0, y0, x1, y1,..., x(np-1), y(np - 1)
    // - style: Style of polygon (draw and/or fill) (D, F, DF, FD)
    // - line_style: Line style. Array with one of this index
    //   . all: Line style of all lines. Array like for SetLineStyle
    //   . 0..np-1: Line style of each line. Item is 0 (not line) or like for SetLineStyle
    // - fill_color: Fill color. Array with components (red, green, blue)
    function Polygon($p, $style = '', $line_style = null, $fill_color = null) {
        $np = count($p) / 2;
        if (!(false === strpos($style, 'F')) && $fill_color) {
            list($r, $g, $b) = $fill_color;
            $this->SetFillColor($r, $g, $b);
        }
        switch ($style) {
            case 'F':
                $line_style = null;
                $op = 'f';
                break;
            case 'FD': case 'DF':
                $op = 'B';
                break;
            default:
                $op = 'S';
                break;
        }
        $draw = true;
        if ($line_style)
            if (isset($line_style['all']))
                $this->SetLineStyle($line_style['all']);
            else { // 0 .. (np - 1), op = {B, S}
                $draw = false;
                if ('B' == $op) {
                    $op = 'f';
                    $this->_Point($p[0], $p[1]);
                    for ($i = 2; $i < ($np * 2); $i = $i + 2)
                        $this->_Line($p[$i], $p[$i + 1]);
                    $this->_Line($p[0], $p[1]);
                    $this->_out($op);
                }
                $p[$np * 2] = $p[0];
                $p[($np * 2) + 1] = $p[1];
                for ($i = 0; $i < $np; $i++)
                    if (!empty($line_style[$i]))
                        $this->Line($p[$i * 2], $p[($i * 2) + 1], $p[($i * 2) + 2], $p[($i * 2) + 3], $line_style[$i]);
            }

        if ($draw) {
            $this->_Point($p[0], $p[1]);
            for ($i = 2; $i < ($np * 2); $i = $i + 2)
                $this->_Line($p[$i], $p[$i + 1]);
            $this->_Line($p[0], $p[1]);
            $this->_out($op);
        }
    }

    // Draws a regular polygon
    // Parameters:
    // - x0, y0: Center point
    // - r: Radius of circumscribed circle
    // - ns: Number of sides
    // - angle: Orientation angle (anti-clockwise)
    // - circle: Draw circumscribed circle or not
    // - style: Style of polygon (draw and/or fill) (D, F, DF, FD)
    // - line_style: Line style. Array with one of this index
    //   . all: Line style of all lines. Array like for SetLineStyle
    //   . 0..ns-1: Line style of each line. Item is 0 (not line) or like for SetLineStyle
    // - fill_color: Fill color. Array with components (red, green, blue)
    // - circle_style: Style of circumscribed circle (draw and/or fill) (D, F, DF, FD) (if draw)
    // - circle_line_style: Line style for circumscribed circle. Array like for SetLineStyle (if draw)
    // - circle_fill_color: Fill color for circumscribed circle. Array with components (red, green, blue) (if draw fill circle)
    function RegularPolygon($x0, $y0, $r, $ns, $angle = 0, $circle = false, $style = '', $line_style = null, $fill_color = null, $circle_style = '', $circle_line_style = null, $circle_fill_color = null) {
        if ($ns < 3)
            $ns = 3;
        if ($circle)
            $this->Circle($x0, $y0, $r, 0, 360, $circle_style, $circle_line_style, $circle_fill_color);
        $p = null;
        for ($i = 0; $i < $ns; $i++) {
            $a = $angle + ($i * 360 / $ns);
            $a_rad = deg2rad((float) $a);
            $p[] = $x0 + ($r * sin($a_rad));
            $p[] = $y0 + ($r * cos($a_rad));
        }
        $this->Polygon($p, $style, $line_style, $fill_color);
    }

    // Draws a star polygon
    // Parameters:
    // - x0, y0: Center point
    // - r: Radius of circumscribed circle
    // - nv: Number of vertices
    // - ng: Number of gaps (ng % nv = 1 => regular polygon)
    // - angle: Orientation angle (anti-clockwise)
    // - circle: Draw circumscribed circle or not
    // - style: Style of polygon (draw and/or fill) (D, F, DF, FD)
    // - line_style: Line style. Array with one of this index
    //   . all: Line style of all lines. Array like for SetLineStyle
    //   . 0..n-1: Line style of each line. Item is 0 (not line) or like for SetLineStyle
    // - fill_color: Fill color. Array with components (red, green, blue)
    // - circle_style: Style of circumscribed circle (draw and/or fill) (D, F, DF, FD) (if draw)
    // - circle_line_style: Line style for circumscribed circle. Array like for SetLineStyle (if draw)
    // - circle_fill_color: Fill color for circumscribed circle. Array with components (red, green, blue) (if draw fill circle)
    function StarPolygon($x0, $y0, $r, $nv, $ng, $angle = 0, $circle = false, $style = '', $line_style = null, $fill_color = null, $circle_style = '', $circle_line_style = null, $circle_fill_color = null) {
        if ($nv < 2)
            $nv = 2;
        if ($circle)
            $this->Circle($x0, $y0, $r, 0, 360, $circle_style, $circle_line_style, $circle_fill_color);
        $p2 = null;
        $visited = null;
        for ($i = 0; $i < $nv; $i++) {
            $a = $angle + ($i * 360 / $nv);
            $a_rad = deg2rad((float) $a);
            $p2[] = $x0 + ($r * sin($a_rad));
            $p2[] = $y0 + ($r * cos($a_rad));
            $visited[] = false;
        }
        $p = null;
        $i = 0;
        do {
            $p[] = $p2[$i * 2];
            $p[] = $p2[($i * 2) + 1];
            $visited[$i] = true;
            $i += $ng;
            $i %= $nv;
        } while (!$visited[$i]);
        $this->Polygon($p, $style, $line_style, $fill_color);
    }

    // Draws a rounded rectangle
    // Parameters:
    // - x, y: Top left corner
    // - w, h: Width and height
    // - r: Radius of the rounded corners
    // - round_corner: Draws rounded corner or not. String with a 0 (not rounded i-corner) or 1 (rounded i-corner) in i-position. Positions are, in order and begin to 0: top left, top right, bottom right and bottom left
    // - style: Style of rectangle (draw and/or fill) (D, F, DF, FD)
    // - border_style: Border style of rectangle. Array like for SetLineStyle
    // - fill_color: Fill color. Array with components (red, green, blue)
    function RoundedRect($x, $y, $w, $h, $r, $round_corner = '1111', $style = '', $border_style = null, $fill_color = null) {
        if ('0000' == $round_corner) // Not rounded
            $this->Rect($x, $y, $w, $h, $style, $border_style, $fill_color);
        else { // Rounded
            if (!(false === strpos($style, 'F')) && $fill_color) {
                list($red, $g, $b) = $fill_color;
                $this->SetFillColor($red, $g, $b);
            }
            switch ($style) {
                case 'F':
                    $border_style = null;
                    $op = 'f';
                    break;
                case 'FD': case 'DF':
                    $op = 'B';
                    break;
                default:
                    $op = 'S';
                    break;
            }
            if ($border_style)
                $this->SetLineStyle($border_style);

            $MyArc = 4 / 3 * (sqrt(2) - 1);

            $this->_Point($x + $r, $y);
            $xc = $x + $w - $r;
            $yc = $y + $r;
            $this->_Line($xc, $y);
            if ($round_corner[0])
                $this->_Curve($xc + ($r * $MyArc), $yc - $r, $xc + $r, $yc - ($r * $MyArc), $xc + $r, $yc);
            else
                $this->_Line($x + $w, $y);

            $xc = $x + $w - $r ;
            $yc = $y + $h - $r;
            $this->_Line($x + $w, $yc);

            if ($round_corner[1])
                $this->_Curve($xc + $r, $yc + ($r * $MyArc), $xc + ($r * $MyArc), $yc + $r, $xc, $yc + $r);
            else
                $this->_Line($x + $w, $y + $h);

            $xc = $x + $r;
            $yc = $y + $h - $r;
            $this->_Line($xc, $y + $h);
            if ($round_corner[2])
                $this->_Curve($xc - ($r * $MyArc), $yc + $r, $xc - $r, $yc + ($r * $MyArc), $xc - $r, $yc);
            else
                $this->_Line($x, $y + $h);

            $xc = $x + $r;
            $yc = $y + $r;
            $this->_Line($x, $yc);
            if ($round_corner[3])
                $this->_Curve($xc - $r, $yc - ($r * $MyArc), $xc - ($r * $MyArc), $yc - $r, $xc, $yc - $r);
            else {
                $this->_Line($x, $y);
                $this->_Line($x + $r, $y);
            }
            $this->_out($op);
        }
    }

    /* PRIVATE METHODS */

    /**
     * Sets a draw point
     *
     * @param unknown_type $x Point
     * @param unknown_type $y Point
     */
    function _Point($x, $y){
        $this->_out(sprintf('%.2f %.2f m', $x * $this->k, ($this->h - $y) * $this->k));
    }

    /**
     * Draws a line from last draw point
     *
     * @param unknown_type $x End point
     * @param unknown_type $y End point
     */
    function _Line($x, $y) {
        $this->_out(sprintf('%.2f %.2f l', $x * $this->k, ($this->h - $y) * $this->k));
    }

    /**
     * Draws a Bézier curve from last draw point
     *
     * @param unknown_type $x1 Control point 1
     * @param unknown_type $y1 Control point 1
     * @param unknown_type $x2 Control point 2
     * @param unknown_type $y2 Control point 2
     * @param unknown_type $x3 End point
     * @param unknown_type $y3 End point
     */
    function _Curve($x1, $y1, $x2, $y2, $x3, $y3) {
        $this->_out(sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c', $x1 * $this->k, ($this->h - $y1) * $this->k, $x2 * $this->k, ($this->h - $y2) * $this->k, $x3 * $this->k, ($this->h - $y3) * $this->k));
    }		
	
	function Footer()
	{
	    //Go to 1.5 cm from bottom
	    $this->SetY(-15);
	    //Select Arial italic 8
	    $this->SetFont('Courier','I',8);
	    //Print current and total page numbers
	    //$this->Cell(0,10,$this->PageNo().'/{nb}',0,0,'C');
	}

}

$ar_mes = array("Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
function ConvertSize($size=5,$maxsize=0)
{
	// Depends of maxsize value to make % work properly. Usually maxsize == pagewidth
	// Identify size (remember: we are using 'mm' units here)
	if ( stristr($size,'px') ) $size *= 0.2645; //pixels
  	elseif ( stristr($size,'cm') ) $size *= 10; //centimeters
  	elseif ( stristr($size,'mm') ) $size += 0; //millimeters
  	elseif ( stristr($size,'in') ) $size *= 25.4; //inches 
  	elseif ( stristr($size,'pc') ) $size *= 38.1/9; //PostScript picas 
  	elseif ( stristr($size,'pt') ) $size *= 25.4/72; //72dpi
  	elseif ( stristr($size,'%') )
  	{
  		$size += 0; //make "90%" become simply "90" 
 	 	$size *= $maxsize/100;
  	}
  	else $size *= 0.2645; //nothing == px
	
  	return $size;
}



#### BUSCA DADOS DA AVALIAÇÃO ####
class controle_projetos_avaliacao_config_partial_relatorio_pdf
{
    private $db;
    private $service;
    private $id;
    private $filtro;
    public  $capas;

    function __construct($db)
    {
        $this->service = new service_projetos( $db );
        $this->filtro = new helper__avaliacao_capa__fetch_by_filter__filter();
        $this->db=$db;
        $this->requestParams();
    }

    private function requestParams()
    {
        if(isset($_REQUEST["dt_periodo_text"]))
        {
        	$this->filtro->dt_periodo = $_REQUEST["dt_periodo_text"];
        }
        if(isset($_REQUEST["gerencia_select"]))
        {
        	$this->filtro->gerencia = $_REQUEST["gerencia_select"];
        }
        if(isset($_REQUEST["avaliado_select"]))
        {
        	$this->filtro->avaliado = $_REQUEST["avaliado_select"];
        }
        if(isset($_REQUEST["tipo_select"]))
        {
        	$this->filtro->tipo_promocao = $_REQUEST["tipo_select"];
        }
    }

    public function load()
    {
		//var_dump($this->filtro);exit;
    	$this->capas = $this->service->projetos__avaliacao_capa__fetch_by_filter( $this->filtro );
    }
    
    public function resultado_get($capa)
    {
    	// Resultado
        $avaliacao_capa = new entity_projetos_avaliacao_capa_extended();
        $avaliacao_capa->set_cd_avaliacao_capa($capa->cd_avaliacao_capa);
    	$capas = $this->service->avaliacao_capa_FetchAll($avaliacao_capa);

    	$capa_ext = $capas[0];

    	$helper = new helper_avaliacao_resultado( $this->db, $capa_ext->get_cd_avaliacao_capa(), $_SESSION['Z'] );
        $helper->load();

		$dados = Array();
		
		foreach($capa_ext->avaliacoes as $avaliacao)
        {
            if(!is_null($avaliacao))
            {
	            if ($avaliacao->get_tipo()=="S")
	            {
	            	$helper->load_valores( $avaliacao->get_cd_avaliacao() );
	            	$dados["superior_ci"] = number_format( $helper->get_val_ci(), 2 );
			        $dados["superior_esc"] = number_format( $helper->get_val_esc(), 2 );
			        $dados["superior_media_1"] = number_format( $helper->get_grau_1(), 2 );
			        $dados["superior_ce"] = number_format( $helper->get_val_ce(), 2 );
			        $dados["superior_resp"] = number_format( $helper->get_val_resp(), 2 );
			        $dados["superior_media_2"] = number_format( $helper->get_grau_2(), 2 );
			        $dados["superior_resultado"] = number_format( $helper->get_grau_final(), 2 );
	            }
	            if ($avaliacao->get_tipo()=="A")
	            {
	            	$helper->load_valores( $avaliacao->get_cd_avaliacao() );
	            	$dados["avaliado_ci"] = number_format( $helper->get_val_ci(), 2 );
			        $dados["avaliado_esc"] = number_format( $helper->get_val_esc(), 2 );
			        $dados["avaliado_media_1"] = number_format( $helper->get_grau_1(), 2 );
			        $dados["avaliado_ce"] = number_format( $helper->get_val_ce(), 2 );
			        $dados["avaliado_resp"] = number_format( $helper->get_val_resp(), 2 );
			        $dados["avaliado_media_2"] = number_format( $helper->get_grau_2(), 2 );
			        $dados["avaliado_resultado"] = number_format( $helper->get_grau_final(), 2 );
	            }
            }
        }

        $helper->calcula_media_ci_comite();

        $esc = "";
        $esc = trim( $capa_ext->get_grau_escolaridade() );

        if( sizeof($capa_ext->comite)>1 && $capa_ext->get_dt_publicacao()!="" )
        {
	        $dados["comite_ci"] = number_format( $helper->get_media_ci_comite(), 2 );
	        $dados["comite_esc"] = number_format( $helper->get_val_esc(), 2 );
	        $dados["comite_media_1"] = number_format( $helper->get_grau_media_comite_esc(), 2 );
	        $dados["comite_ce"] = number_format( $dados["superior_ce"], 2 );
	        $dados["comite_resp"] = number_format( $dados["superior_resp"], 2 );
	        $dados["comite_media_2"] = number_format( $dados["superior_media_2"], 2 );
	        $dados["comite_resultado"] = number_format( $helper->get_media_final_comite(), 2 );
        }

        return $dados;

        /*return array( 
	          'media_ci'=>number_format($helper->get_media_ci_comite(), 2)
	        , 'escolaridade'=> number_format( $capa_ext->get_grau_escolaridade(), 2 )
	        , 'media_1'=>number_format($helper->get_grau_media_comite_esc(), 2)
	        , 'media_ce'=>number_format($helper->get_val_ce(), 2)
	        , 'media_resp'=>number_format($helper->get_val_resp(), 2)
	        , 'media_2'=>number_format($helper->get_grau_2(), 2)
	        , 'media_geral'=>number_format($helper->get_media_final_comite(), 2)
        );*/
    	
    }
}



#### MONTA PDF ####
$esta = new controle_projetos_avaliacao_config_partial_relatorio_pdf( $db );

$esta->load();

$capa = new helper__avaliacao_capa__fetch_by_filter__entity();
$pdf = new PDF();

$incremento=0;
$plus=0;

	foreach ($esta->capas as $capa)
	{
		$resultado = $esta->resultado_get($capa);

		$pdf->AddPage();

		$pdf->SetX(10);
		$pdf->Image('img/logofundacao_carta.jpg', $pdf->GetX(), $pdf->GetY(), 60, 0, '', '');
		
		$pdf->SetFont('Arial','', 20);
		$pdf->Text(90,20,"Processo de Avaliação");


		$pdf->SetY($pdf->GetY() + 25);
		$pdf->SetFont('Arial','', 12);
		$pdf->Text(10,$pdf->GetY(), "Período: ".$capa->periodo);
		$pdf->Text(10,$pdf->GetY() + 6, "Avaliado: ".$capa->nome_avaliado);
		$pdf->Text(10,$pdf->GetY() + 12, "Avaliador: ".$capa->nome_avaliador);
		$pdf->Text(10,$pdf->GetY() + 18, "Tipo: ".($capa->tipo_promocao == "H" ? "Horizontal" : ($capa->tipo_promocao == "V" ? "Vertical" : "Não identificado")));
		
		

		$pdf->SetY($pdf->GetY() + 20);
		$pdf->SetLineWidth(0);
		$pdf->Line(10,$pdf->GetY(),200,$pdf->GetY());	
		$pdf->SetY($pdf->GetY() + 2);
		
		$pdf->SetFont('Arial','B', 10);
		$pdf->MultiCell(0, 5, "Resultado:", 0, 1);	
		


		$pdf->SetY($pdf->GetY() + 4);
		$linha = $pdf->GetY();
		
		$pdf->SetFont('Arial','', 8);
		$pdf->Text(10, $linha, "Resultado Avaliado:");
		$pdf->Text(10, $linha + 5, "Competências Institucionais: ".number_format($resultado["avaliado_ci"],2,",","."));
		$pdf->Text(10, $linha + 8, "Escolaridade: ".number_format($resultado["avaliado_esc"],2,",","."));
		$pdf->Text(10, $linha + 10, "-----------------------------------------------");
		$pdf->Text(10, $linha + 13, "MÉDIA (M1): ".number_format($resultado["avaliado_media_1"],2,",","."));
		$pdf->Text(10, $linha + 20, "Competências Específicas: ".number_format($resultado["avaliado_ce"],2,",","."));
		$pdf->Text(10, $linha + 23, "Responsabilidades: ".number_format($resultado["avaliado_resp"],2,",","."));
		$pdf->Text(10, $linha + 25, "-----------------------------------------------");
		$pdf->Text(10, $linha + 28, "MÉDIA (M2): ".number_format($resultado["avaliado_media_2"],2,",","."));
		$pdf->Text(10, $linha + 35, "Resultado: ".number_format($resultado["avaliado_resultado"],2,",","."));
		$pdf->Text(10, $linha + 38, "Resultado=40% de M1 + 60% de M2");

		$pdf->Text(70, $linha, "Resultado Superior:");
		$pdf->Text(70, $linha + 5, "Competências Institucionais: ".number_format($resultado["superior_ci"],2,",","."));
		$pdf->Text(70, $linha + 8, "Escolaridade: ".number_format($resultado["superior_esc"],2,",","."));
		$pdf->Text(70, $linha + 10, "-----------------------------------------------");
		$pdf->Text(70, $linha + 13, "MÉDIA (M1): ".number_format($resultado["superior_media_1"],2,",","."));
		$pdf->Text(70, $linha + 20, "Competências Específicas: ".number_format($resultado["superior_ce"],2,",","."));
		$pdf->Text(70, $linha + 23, "Responsabilidades: ".number_format($resultado["superior_resp"],2,",","."));
		$pdf->Text(70, $linha + 25, "-----------------------------------------------");
		$pdf->Text(70, $linha + 28, "MÉDIA (M2): ".number_format($resultado["superior_media_2"],2,",","."));
		$pdf->Text(70, $linha + 35, "Resultado: ".number_format($resultado["superior_resultado"],2,",","."));
		$pdf->Text(70, $linha + 38, "Resultado=40% de M1 + 60% de M2");

		if(intval($resultado["comite_media_2"]) > 0)
		{
			$pdf->Text(130, $linha, "Resultado Comitê:");
			$pdf->Text(130, $linha + 5, "Competências Institucionais: ".number_format($resultado["comite_ci"],2,",","."));
			$pdf->Text(130, $linha + 8, "Escolaridade: ".number_format($resultado["comite_esc"],2,",","."));
			$pdf->Text(130, $linha + 10, "-----------------------------------------------");
			$pdf->Text(130, $linha + 13, "MÉDIA (M1): ".number_format($resultado["comite_media_1"],2,",","."));
			$pdf->Text(130, $linha + 20, "Competências Específicas: ".number_format($resultado["comite_ce"],2,",","."));
			$pdf->Text(130, $linha + 23, "Responsabilidades: ".number_format($resultado["comite_resp"],2,",","."));
			$pdf->Text(130, $linha + 25, "-----------------------------------------------");
			$pdf->Text(130, $linha + 28, "MÉDIA (M2): ".number_format($resultado["comite_media_2"],2,",","."));
			$pdf->Text(130, $linha + 35, "Resultado: ".number_format($resultado["comite_resultado"],2,",","."));
			$pdf->Text(130, $linha + 38, "Resultado=40% de M1 + 60% de M2");
		}
		$pdf->SetY($linha + 38);

		if(count($capa->expectativas) > 0)
		{
			$pdf->SetY($pdf->GetY() + 2);
			$pdf->SetLineWidth(0);
			$pdf->Line(10,$pdf->GetY(),200,$pdf->GetY());	
			$pdf->SetY($pdf->GetY() + 2);			
			
			$pdf->SetFont('Arial','B', 10);
			$pdf->MultiCell(0, 5, "Expectativas:", 0, 1);
			
			$pdf->SetLineWidth(0);
			$pdf->SetDrawColor(0,0,0);
			$pdf->SetWidths(array(40, 150));
			$pdf->SetAligns(array('L','L'));
			$pdf->SetFont('Arial', '', 10 );
			$pdf->SetY($pdf->GetY() + 2);
			
			$expectativa = new entity_projetos_avaliacao_aspecto();
			$idx=0;
			foreach($capa->expectativas as $expectativa)
			{
				if($idx!=0)
				{
					$pdf->SetY($pdf->GetY() + 2);
				}
					
				$pdf->Row(array("Competências",$expectativa->aspecto));	
				$pdf->Row(array("Resultados esperados",$expectativa->resultado_esperado));	
				$pdf->Row(array("Ações",$expectativa->acao));	

				$idx++;
			}

		}
		
		$pdf->SetY($pdf->GetY() + 2);
		$pdf->SetLineWidth(0);
		$pdf->Line(10,$pdf->GetY(),200,$pdf->GetY());	
		$pdf->SetY($pdf->GetY() + 10);	
		
		$pdf->SetFont('Arial','', 10);
		$pdf->Text(10,$pdf->GetY(),"Porto Alegre, ".date("d")." de ".$ar_mes[(date("m") - 1)]." de ".date("Y").".");		
		
		$pdf->SetY($pdf->GetY() + 8);
		
		$pdf->SetFont('Courier','', 12);
		$pdf->Text(10,$pdf->GetY(), "( ".($capa->fl_acordo == "A" ? "X" : " ")." )");
		$pdf->Text(10,$pdf->GetY()+6,  "( ".($capa->fl_acordo == "C" ? "X" : " ")." )");

		$pdf->SetFont('Arial','', 12);
		$pdf->Text(25,$pdf->GetY(), "Concordo com o resultado da avaliação (houve consenso)");
		$pdf->Text(25,$pdf->GetY()+6,  "Estou ciente do resultado da avaliação (não houve consenso)");

		
		$pdf->SetY($pdf->GetY() + 25);	
		
		$pdf->SetLineWidth(0);
		$pdf->Line(10,$pdf->GetY(),95,$pdf->GetY());	
		$pdf->Line(120,$pdf->GetY(),200,$pdf->GetY());	
		$pdf->SetY($pdf->GetY() + 4);
		$pdf->Text(10,$pdf->GetY(),$capa->nome_avaliado);
		$pdf->Text(120,$pdf->GetY(),$capa->nome_avaliador);
	}
	
	$pdf->Output();

?>