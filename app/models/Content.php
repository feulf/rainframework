<?php

	class Content_Model extends Model{
		
		function get_list( $parent_id ){
		}

		/**
		 * Get the selected content
		 */
		function get(){
			return array('title'=>'Title', 'content'=>'Lorem ipsum dolor...' );
		}

		
	}

?>