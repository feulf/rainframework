<?php


	class Charts{

		protected static	$charts_count = 0;
		protected static	$colors = array('#3366cc','#dc3912','#ff9900','#109618','#990099','#0099c6','#dd4477');

		/**
		 * Load data from CSV file
		 */
		function load_csv( $file ){
			$file = file_get_contents( $file );
			$rows = explode("\n",$file);
			for( $i=0;$i<count($rows);$i++ )
				$this->data[$i] = explode( ",", $rows[$i] );
		}

		/**
		 * Load data from array
		 */
		function set_data($array){
			$this->data = $array;
		}



		/**
		 * Draw line
		 */
		function draw_line(){
			return $this->_draw('line');
		}
		
		
		
		/**
		 * Draw pie
		 */
		function draw_pie(){
			return $this->_draw('pie');
		}



		private function _draw( $chart = 'line'){

			$id = 'name' . self::$charts_count;
			$js = "";
			if( !self::$charts_count )
				$js .= $this->_init_script();

			self::$charts_count++;

			$js .= '<script>' . "\n";
			$js .= '	google.load("visualization", "1", {packages:["corechart"]});' . "\n";
			$js .= '	google.setOnLoadCallback(drawChart);' . "\n";
			$js .= '	function drawChart() {' . "\n";
			$js .= '		var data = new google.visualization.DataTable();' . "\n";

			$data = $this->data;
			$n_rows = count($data);
			$n_col = count($data[0]);

			switch( $chart ){

				case 'pie':
					// number of rows
					$js .= "		data.addRows($n_rows);" . "\n";

					// define column
					$column = $data[0];
					foreach( $column as $k => $v ){
							if( is_numeric($v) )
								$js .= "		data.addColumn('number', '$k' );" . "\n";
							else
								$js .= "		data.addColumn('string', '$k' );" . "\n";
					}

					// define the values
					for( $i=0;$i<$n_rows;$i++){
						$j=0;
						foreach( $data[$i] as $k => $v ){
							if( is_numeric($v) )
								$js .= "		data.setValue($i, $j, $v )". "\n";
							else
								$js .= "		data.setValue($i, $j, '$v' )". "\n";
							$j++;
						}
					}

					break;

				case 'line':
					// number of rows
					$js .= "		data.addColumn('string', '{$data[0][0]}' );" . "\n";
					$js .= "		data.addRows(" . ( $n_rows - 1 ) . " );" . "\n";

					//dump( $data );
					$column = $data[0];
					foreach( $column as $k => $v )
						if( $k > 0 )
							$js .= "		data.addColumn('number', '$v' );" . "\n";

					// define the values
					for( $i=1;$i<$n_rows;$i++){
						$j=0;
						foreach( $data[$i] as $k => $v ){
							if( $k > 0 )
								$js .= "		data.setValue(" . ($i-1) . ", $j, $v )". "\n";
							else
								$js .= "		data.setValue(" . ($i-1) . ", $j, '$v' )". "\n";
							$j++;
						}
					}	
						
					break;
					
					
				
			}





			// define the colors
			$colors = '[';
			foreach( self::$colors as $k => $v )
				$colors .= "'$v',";
			$colors = substr($colors,0,-1);
			$colors .= "]";



			switch( $chart ){
				
				case 'line':
					$js .= "		var chart = new google.visualization.LineChart(document.getElementById('$id'));" . "\n";
					$js .= "		chart.draw(data, {lineType: 'function', colors:$colors, width:750, height: 300, vAxis: {maxValue: 10}} );" . "\n";
					break;
				
				case 'pie':
					$js .= "		var chart = new google.visualization.PieChart(document.getElementById('$id'));". "\n";
					$js .= "		chart.draw(data, {width: 430, height: 350, colors:$colors, is3D:true });". "\n";
					break;

			}
			
			$js .= '	}' . "\n";
			$js .= '</script>' . "\n";

			$html = '<div id="'.$id.'"></div>';

			return $js . $html;

		}
		

                function get_colors(){
                    return self::$colors;
                }

                function set_colors($array){
                    self::$colors = $array;
                }

		function _init_script(){
			return '<script type="text/javascript" src="https://www.google.com/jsapi"></script>' . "\n";
		}

	}


?>



