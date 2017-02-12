<?php
/*
 * Frontier Layout Engine
 * Used to build responsive grid layouts
 * Based on PHP Scaffold https://github.com/Desertsnowman/PHP-Scaffold
 * 2014 - David Cramer
 */
if( !class_exists( 'Caldera_Form_Grid' )){
	
	class Caldera_Form_Grid {

		private $layoutString = array();
		private $debug = false;
		private $layoutType = false;
		private $config = array();
		private $nests = array();
		private $output = '';
		private $paged = false;
		public  $grid = array();
		

		function __construct($config) {
			
			$this->config = $config;//json_decode(file_get_contents(plugin_dir_path(__FILE__) . '/engine-config.json'), true);
			if(empty($this->config)){
				echo 'Error loading engine config';
				die;
			}
		}
		public function debug(){
			$this->debug = true;
		}
		public function setLayout($str){
			// find pages
			if( false !== strpos($str, '#') ){
				$this->paged = true;
			}
			// find nests
			preg_match_all("/\[[0-9:\|]+\]/", $str, $matches);
			if(!empty($matches[0])){
				foreach($matches[0] as $key=>$nest){
					$port = uniqid('__');
					$this->nests[$port] = substr($nest, 1, strlen($nest)-2);
					$str = str_replace($nest, $port, $str);
				}
			}
			$this->grid = $this->splitString($str);
		}
		private function splitString($str){
			
			$rows = explode('|', $str);
			$grid = array();
			foreach($rows as $row=>$cols){
				$cols = explode(':',$cols);
				foreach($cols as $col=>$span){                
					$nest = strpos($span, '__');
					if($nest !== false){
						$grid[$row+1][$col+1] = $this->splitString($this->nests[substr($span,$nest)]);
					}
					$grid[$row+1][$col+1]['span'] = $span;
					$grid[$row+1][$col+1]['html'] = '';
				}
			}

			return $grid;
		}
		static function mergeArray($first, $second, $type = 'replace'){       
			foreach($second as $key => $value) {
				if(is_array($value)){
					if(!isset($first[$key])){
						$first[$key] = array();
					}
					$first[$key] = self::mergeArray($first[$key], $value, $type);
				}else{
					switch ($type){
						case 'replace':
						$first[$key] = $value;
						break;
						case 'append':
						if(empty($first[$key])){
							$first[$key] = $value;
						}else{
							$first[$key] .= $value;
						}
						break;
						case 'prepend':
						if(empty($first[$key])){
							$first[$key] = $value;
						}else{
							$first[$key] = $value.$first[$key];
						}
						$first[$key] = $value.$first[$key];
						break;
					}
				}
			}
			return $first;
		}
		static function mapValue($type, $value, &$map){
			$out = '';$end = '';
			$map = explode(':', $map);
			foreach($map as $key=>$val){
				$out .= '{"'.$val.'":';
				$end .= "}";
			}
			$map = json_decode($out.json_encode(array($type=>$value)).$end, true);        
		}
		public function html($html, $map, $type = 'replace') {
			$this->mapValue('html', $html, $map);
			$this->grid = self::mergeArray($this->grid, $map, $type);
		}
		public function before($html, $map, $type = 'replace') {
			$this->mapValue('before', $html, $map);
			$this->grid = self::mergeArray($this->grid, $map, $type);
		}
		public function after($html, $map, $type = 'replace') {
			$this->mapValue('after', $html, $map);
			$this->grid = self::mergeArray($this->grid, $map, $type);
		}
		public function append($html, $map) {
			self::html($html, $map, 'append');
		}
		public function prepend($html, $map) {
			self::html($html, $map, 'prepend');
		}
		public function setClass($class, $map){
			$this->mapValue('class', $class, $map);
			$this->grid = self::mergeArray($this->grid, $map);
		}
		public function appendClass($class, $map){
			$this->mapValue('class', $class, $map);
			$this->grid = self::mergeArray($this->grid, $map, 'append');        
		}
		public function prependClass($class, $map){
			$this->mapValue('class', $class, $map);
			$this->grid = self::mergeArray($this->grid, $map, 'prepend');
		}    
		public function setRowID($ID, $row){
			if(!isset($this->grid[$row])){return;}
			$this->grid[$row]['id'] = $ID;
		}
		public function setID($ID, $map){
			$this->mapValue('id', $ID, $map);
			$this->grid = self::mergeArray($this->grid, $map);
		}
		public function renderLayout($grid = false) {
			$inner = true;
			if(empty($this->grid)){
				return 'ERROR: Layout string not set.';
			}
			if(empty($grid)){
				$inner = false;
				$grid = $this->grid;
			}
			
			foreach($grid as $row=>$cols){

				$rowID = '';
				$rowClass = '';
				$rowBefore = '';
				$rowAfter = '';
				
				if(isset($cols['id'])){
					$rowID = $cols['id'];
					unset($cols['id']);
				}

				if( empty( $rowID ) ){
					$rowID = $row;
				}

				if( ! empty( $this->config[ 'form_id_attr' ] ) ){
					$rowID = $this->config[ 'form_id_attr' ]  . '-row-' . $rowID;
				}

				/**
				 * Alter row ID attribute in Caldera Grid
				 *
				 * @since 1.4.9
				 *
				 * @param string $rowID The row's ID attribute
				 * @param int $row Row number
				 * @param array $config Grid config. Contains form ID in form_id key.
				 */
				$rowID = apply_filters( 'caldera_forms_grid_row_id', $rowID, $row, $this->config );

				$rowID = 'id="'.$rowID.'" ';

				if(isset($cols['class'])){
					$rowClass = $cols['class'];
					unset($cols['class']);
				}
				
				if(isset($grid['*']['class'])){
					$rowClass .= $grid['*']['class'];
				}

				if($row === 1 && $row !== count($grid)){
					$rowClass .= " ".$this->config['first'];
				}elseif ($row === count($grid) && $row !== 1){
					$rowClass .= " ".$this->config['last'];
				}elseif ($row === count($grid) && $row === 1){
					$rowClass .= " ".$this->config['single'];
				}
				
				if(isset($cols['before'])){
					$this->output .= $cols['before'];
					unset($cols['before']);
				}

				/**
				 * Alter row class attribute in Caldera Grid
				 *
				 * STRONGLY recommended you use this to add, but not subtract classes.
				 *
				 * @since 1.4.9
				 *
				 * @param string $rowClass The row's classes
				 * @param int $row Row number
				 * @param array $config Grid config. Contains form ID in form_id key.
				 */
				$rowClass = apply_filters( 'caldera_forms_grid_row_class', $rowClass, $row, $this->config );
				$this->output .= sprintf($this->config['before'], $rowID, $rowClass);
				
				if(!is_array($cols)){
					echo $cols;
				}else{
					foreach($cols as $col=>$content){
						if(!is_array($content) || empty($content)){
							continue;
						}
						if(!isset($content['span'])){continue;}
						$colClass = '';
						if(isset($content['class'])){
							$colClass = $content['class'];
							unset($content['class']);
						}
						if(isset($cols['*']['class'])){
							$colClass .= $cols['*']['class'];
						}
						
						if($col === 1 && $col !== count($cols)){
							$colClass .= " ".$this->config['column_first'];
						}elseif($col === count($cols) && $col !== 1){
							$colClass .= " ".$this->config['column_last'];
						}elseif($col === count($cols) && $col === 1){
							$colClass .= " ".$this->config['column_single'];
						}
						$colID = '';
						if(isset($content['id'])){
							$colID = 'id="'.$content['id'].'"';
							unset($content['id']);
						}
						if(isset($content['before'])){
							$this->output .= $content['before'];
							unset($content['before']);
						}
						$afterBuffer = '';
						if(isset($content['after'])){
							$afterBuffer = $content['after'];
							unset($content['after']);
						}
						$span = (!empty($this->config['column_spans'][$content['span']]) ? $this->config['column_spans'][$content['span']] : $content['span']);
						$this->output .= sprintf($this->config['column_before'], $colID, $span, $colClass);//"    <div class=\"span".$content['span']." ".$colClass."\">\n";
						$this->output .= $content['html'];
						unset($content['html']);
						unset($content['span']);
						if(!empty($content)){
							$this->output = $this->renderLayout($content);                       
						}
						$this->output .= $this->config['column_after'];
						$this->output .= $afterBuffer;                    
					}
				}
				$this->output .= $this->config['after'];//"</div>\n";            
				if(isset($cols['after'])){
					$this->output .= $cols['after'];
				}
				
			}

			return $this->output;
		}

	}
}
