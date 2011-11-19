<?php

	class Charts_Controller extends Controller{

		function index(){

			$chart_pie = $chart_line = null;
			
			//love easy
			$this->load_library("Charts");
            $data = array( array('OSX', 10), array('Win', 3 ), array('Unix', 7 ) );
			$this->Charts->set_data($data) ;
            //$chart_pie = $this->Charts->draw_pie();

			$this->Charts->load_csv( WEBSITE_DIR . "assign_execution_time.csv" ) ;
            $chart_line = $this->Charts->draw_line();

			$tpl = new View;
            $tpl->assign( "chart_pie", $chart_pie );
            $tpl->assign( "chart_line", $chart_line );
            $tpl->draw( "charts/charts" );

		}

	}
	
?>