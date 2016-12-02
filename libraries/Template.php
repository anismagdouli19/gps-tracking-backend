<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
class Template {

   var $CI;
   var $config;
   var $template;
   var $master;
   var $regions = array(
		'_scripts' => array(),
   		'_scripts_code'=>array(),
		'_scripts.ready'=>array(),
		'_styles' => array(),
   		'_style_code'=>array(),
       'scripts_head'=>array(),
   );
   var $output;
   var $js = array();
   var $css = array();
   var $parser = 'parser';
   var $parser_method = 'parse';
   var $parse_template = FALSE;

   function __construct(){
      $this->CI =& get_instance();
      if (!defined('EXT')) {
          define('EXT','.php');
      }

      include(APPPATH.'config/template'.EXT);
      if (isset($template)){
         $this->config = $template;
         $this->set_template($template['active_template']);
      }
      if (!defined('DS')) {
      	define('DS','/');
      }
   }

   // --------------------------------------------------------------------

   /**
    * Use given template settings
    *
    * @access  public
    * @param   string   array key to access template settings
    * @return  void
    */

   function set_template($group){
      if (isset($this->config[$group]))
      {
         $this->template = $this->config[$group];
      }
      else
      {
         show_error('The "'. $group .'" template group does not exist. Provide a valid group name or add the group first.');
      }
      $this->initialize($this->template);
   }

      // --------------------------------------------------------------------

   /**
    * Set master template
    *
    * @access  public
    * @param   string   filename of new master template file
    * @return  void
    */

   function set_master_template($filename)
   {
      if (file_exists(APPPATH .'views/'. $filename) or file_exists(APPPATH .'views/'. $filename . EXT))
      {
         $this->master = $filename;
      }
      else
      {
         show_error('The filename provided does not exist in <strong>'. APPPATH .'views</strong>. Remember to include the extension if other than ".php"');
      }
   }

   // --------------------------------------------------------------------

   /**
    * Dynamically add a template and optionally switch to it
    *
    * @access  public
    * @param   string   array key to access template settings
    * @param   array properly formed
    * @return  void
    */

   function add_template($group, $template, $activate = FALSE){
      if ( ! isset($this->config[$group])){
         $this->config[$group] = $template;
         if ($activate === TRUE){
            $this->initialize($template);
         }
      } else {
         show_error('The "'. $group .'" template group already exists. Use a different group name.');
      }
   }

   // --------------------------------------------------------------------

   /**
    * Initialize class settings using config settings
    *
    * @access  public
    * @param   array   configuration array
    * @return  void
    */

   function initialize($props){
      // Set master template
      if (isset($props['template'])
         && (file_exists(APPPATH .'views/'. $props['template']) or file_exists(APPPATH .'views/'. $props['template'] . EXT)))
      {
         $this->master = $props['template'];
      } else {
         // Master template must exist. Throw error.
         show_error('Either you have not provided a master template or the one provided does not exist in <strong>'. APPPATH .'views</strong>. Remember to include the extension if other than ".php"');
      }

      // Load our regions
      if (isset($props['regions'])) {
         $this->set_regions($props['regions']);
      }

      // Set parser and parser method
      if (isset($props['parser'])) {
         $this->set_parser($props['parser']);
      }
      if (isset($props['parser_method']))
      {
         $this->set_parser_method($props['parser_method']);
      }

      // Set master template parser instructions
      $this->parse_template = isset($props['parse_template']) ? $props['parse_template'] : FALSE;
   }

   // --------------------------------------------------------------------

   /**
    * Set regions for writing to
    *
    * @access  public
    * @param   array   properly formed regions array
    * @return  void
    */

   function set_regions($regions){
      if (count($regions)){
         $this->regions = array(
            '_scripts' => array(),
         	'scripts_code' => array(),
            '_styles' => array(),
         	'_style_code'=>array(),
         );
         foreach ($regions as $key => $region){
            // Regions must be arrays, but we take the burden off the template
            // developer and insure it here
            if ( ! is_array($region)){
               $this->add_region($region);
            } else {
               $this->add_region($key, $region);
            }
         }
      }
   }

   // --------------------------------------------------------------------

   /**
    * Dynamically add region to the currently set template
    *
    * @access  public
    * @param   string   Name to identify the region
    * @param   array Optional array with region defaults
    * @return  void
    */

   function add_region($name, $props = array()){
      if (!is_array($props)){
         $props = array();
      }

      if (!isset($this->regions[$name])){
         $this->regions[$name] = $props;
      } else {
      	$this->regions[$name] += $props;
         //show_error('The "'. $name .'" region has already been defined.');
      }
   }

   // --------------------------------------------------------------------

   /**
    * Empty a region's content
    *
    * @access  public
    * @param   string   Name to identify the region
    * @return  void
    */

   function empty_region($name){
      if (isset($this->regions[$name]['content'])) {
         $this->regions[$name]['content'] = array();
      } else {
         show_error('The "'. $name .'" region is undefined.');
      }
   }

   // --------------------------------------------------------------------

   /**
    * Set parser
    *
    * @access  public
    * @param   string   name of parser class to load and use for parsing methods
    * @return  void
    */

   function set_parser($parser, $method = NULL){
      $this->parser = $parser;
      $this->CI->load->library($parser);

      if ($method) {
         $this->set_parser_method($method);
      }
   }

   // --------------------------------------------------------------------

   /**
    * Set parser method
    *
    * @access  public
    * @param   string   name of parser class member function to call when parsing
    * @return  void
    */

   function set_parser_method($method){
      $this->parser_method = $method;
   }

   // --------------------------------------------------------------------

   /**
	 * Write contents to a region
	 *
	 * @access	public
	 * @param	string	region to write to
	 * @param	string	what to write
	 * @param	boolean	FALSE to append to region, TRUE to overwrite region
	 * @return	void
	 */

   function write($region, $content, $overwrite = FALSE){
      if (isset($this->regions[$region])){
         if ($overwrite === TRUE){ // Should we append the content or overwrite it
            $this->regions[$region]['content'] = array($content);
         } else {
         	if($region=='_scripts' || $region=='_styles')
         		$this->regions[$region][] = $content;
        	else
            	$this->regions[$region]['content'][] = $content;

//          	$this->regions[$region][] = $content;
         }
//          bug($this->regions[$region]);
      }
      // Regions MUST be defined
      else {
         show_error("Cannot write to the '{$region}' region. The region is undefined.");
      }
   }

   // --------------------------------------------------------------------

   /**
	 * Write content from a View to a region. 'Views within views'
	 *
	 * @access	public
	 * @param	string	region to write to
	 * @param	string	view file to use
	 * @param	array	variables to pass into view
	 * @param	boolean	FALSE to append to region, TRUE to overwrite region
	 * @return	void
	 */

   function write_view($region, $view, $data = NULL, $overwrite = FALSE){
      $args = func_get_args();

      // Get rid of non-views
      unset($args[0], $args[2], $args[3]);

      // Do we have more view suggestions?
      if (count($args) > 1){
         foreach ($args as $suggestion) {
            if (file_exists(APPPATH .'views/'. $suggestion . EXT) or file_exists(APPPATH .'views/'. $suggestion)) {
               // Just change the $view arg so the rest of our method works as normal
               $view = $suggestion;
               break;
            }
         }
      }

      $content = $this->CI->load->view($view, $data, TRUE);
      $this->write($region, $content, $overwrite);

   }

	public function view($region, $view, $data = NULL, $overwrite = FALSE){
		return self::write_view($region, $view, $data, $overwrite);
   	}
   // --------------------------------------------------------------------

   /**
    * Parse content from a View to a region with the Parser Class
    *
    * @access  public
    * @param   string   region to write to
    * @param   string   view file to parse
    * @param   array variables to pass into view for parsing
    * @param   boolean  FALSE to append to region, TRUE to overwrite region
    * @return  void
    */

   function parse_view($region, $view, $data = NULL, $overwrite = FALSE)
   {
      $this->CI->load->library('parser');

      $args = func_get_args();

      // Get rid of non-views
      unset($args[0], $args[2], $args[3]);

      // Do we have more view suggestions?
      if (count($args) > 1)
      {
         foreach ($args as $suggestion)
         {
            if (file_exists(APPPATH .'views/'. $suggestion . EXT) or file_exists(APPPATH .'views/'. $suggestion))
            {
               // Just change the $view arg so the rest of our method works as normal
               $view = $suggestion;
               break;
            }
         }
      }

      $content = $this->CI->{$this->parser}->{$this->parser_method}($view, $data, TRUE);
      $this->write($region, $content, $overwrite);

   }

   // --------------------------------------------------------------------

   /**
    * Dynamically include javascript in the template
    *
    * NOTE: This function does NOT check for existence of .js file
    *
    * @access  public
    * @param   string   script to import or embed
    * @param   string  'import' to load external file or 'embed' to add as-is
    * @param   boolean  TRUE to use 'defer' attribute, FALSE to exclude it
    * @return  TRUE on success, FALSE otherwise
    */

   function get_absolute_path($path) {
   		$pathInfo = parse_url($path);
//    		bug($pathInfo);
	   	$path = str_replace(array('/', '\\'), '/', $pathInfo['path']);
	   	$parts = array_filter(explode('/', $path), 'strlen');
	   	$absolutes = array();
	   	foreach ($parts as $part) {
	   		if ('.' == $part) continue;
	   		if ('..' == $part) {
	   			array_pop($absolutes);
	   		} else {
	   			$absolutes[] = $part;
	   		}
	   	}
	   	$path = implode('/', $absolutes);
	   	return $pathInfo['scheme'].'://'.$pathInfo['host'].DS.$path.(( isset($pathInfo['query']))?'?'.$pathInfo['query']:null);
   }

   function add_js($script, $type = 'import', $defer = FALSE){
      $success = TRUE;
      $js = NULL;

      $this->CI->load->helper('url');


      switch ($type){
         case 'import':
//             $filepath = base_url() . $script;
//             echo 'a='.is_uri($script);exit;
//             $filepath = (is_uri($script)==true)?$script: base_url().$script;

//             $js = '<script type="text/javascript" src="'. $filepath .'"';
//             if ($defer){
//                $js .= ' defer="defer"';
//             }
//             $js .= "></script>";
            $this->write('_scripts',$script);
            break;

         case 'embed':
            $js = '<script type="text/javascript"';
            if ($defer){
               $js .= ' defer="defer"';
            }
            $js .= ">";
            $js .= $script;
            $js .= '</script>';
            $this->write('scripts_code',$js);
            //bug('call me');exit;
            break;

         default:
            $success = FALSE;
            break;
      }

      // Add to js array if it doesn't already exist
//       if ($js != NULL && !in_array($js, $this->js)){
//          $this->js[] = $js;
//          $this->write('_scripts', $js);
//       }

     // bug($script);

      return $success;
   }

   public function add_js_ready($script,$ready=true){
   		$success = TRUE;
   		if($ready===true){
   			$this->regions['scripts_ready'][]=$script;
   			//$this->write('scripts_ready',$script);
   		} else
   			$this->regions['scripts_head'][]=$script;
//    			$this->write('scripts_head',$script);
// bug($this->regions['scripts_ready']);
   		return $success;
   }
   // --------------------------------------------------------------------

   /**
    * Dynamically include CSS in the template
    *
    * NOTE: This function does NOT check for existence of .css file
    *
    * @access  public
    * @param   string   CSS file to link, import or embed
    * @param   string  'link', 'import' or 'embed'
    * @param   string  media attribute to use with 'link' type only, FALSE for none
    * @return  TRUE on success, FALSE otherwise
    */

   function add_css($style, $type = 'import', $media = FALSE){
      $success = TRUE;
      $css = NULL;

      $this->CI->load->helper('url');
      $filepath = base_url() . $style;

      switch ($type){
         case 'link':

            $css = '<link type="text/css" rel="stylesheet" href="'. $filepath .'"';
            if ($media)
            {
               $css .= ' media="'. $media .'"';
            }
            $css .= ' />';
            break;

         case 'import':
//             $css = '<style type="text/css">@import url('. $filepath .');</style>';
            //$css = $style;
         	$this->write('_styles',$style);
            break;

         case 'embed':
//             $css = '<style type="text/css">';
//             $css .= $style;
//             $css .= '</style>';
         	$this->write('_style_code',$style);
            break;

         default:
            $success = FALSE;
            break;
      }

      // Add to js array if it doesn't already exist
//       if ($css != NULL && !in_array($css, $this->css)){
//          $this->css[] = $css;
//          $this->write('_styles', $css);
//       }


      return $success;
   }

   // --------------------------------------------------------------------

   /**
	 * Render the master template or a single region
	 *
	 * @access	public
	 * @param	string	optionally opt to render a specific region
	 * @param	boolean	FALSE to output the rendered template, TRUE to return as a string. Always TRUE when $region is supplied
	 * @return	void or string (result of template build)
	 */

   function render($region = NULL, $buffer = FALSE, $parse = FALSE){
      // Just render $region if supplied
      if ($region){

         if (isset($this->regions[$region])){
            $output = $this->_build_content($this->regions[$region]);
         } else {
            show_error("Cannot render the '{$region}' region. The region is undefined.");
         }
      } else  { // Build the output array
//       	print_r($this->regions);exit;
         foreach ($this->regions as $name => $region) {
         	switch($name){
         		case '_scripts':
//
         			$this->output[$name] = $this->_build_assets('scripts',$region); break;
         		case 'scripts_ready':
//          			bug($name);exit;
					if(is_array($region) && count($region)>0){
						$this->output[$name] = "jQuery(document).ready(function(){ ";
						foreach($region AS $script){
							$this->output[$name].="$script;";
						}
						$this->output[$name].= "})";
// 						bug($name);exit;
					} else
         				$this->output[$name] = "";
         			break;
//          		case 'scripts_head':
//          			$this->output[$name] = "";
//          			if(is_array($region) && count($region)>0){
// 						foreach($region AS $script){
// 							$this->output[$name].="$script;";
// 						}
// 					}

// 					break;
         		case '_styles':
         			$this->output[$name] = $this->_build_assets('css',$region);break;
         		case '_style_code':
         			$this->output[$name] = $this->_build_content($region,'<style>');break;
         		default:
         			$this->output[$name] = $this->_build_content($region);break;
         	}


         }
//      bug($this->regions['_scripts']);exit;
         if ($this->parse_template === TRUE or $parse === TRUE){
            // Use provided parser class and method to render the template
            $output = $this->CI->{$this->parser}->{$this->parser_method}($this->master, $this->output, TRUE);

            // Parsers never handle output, but we need to mimick it in this case
            if ($buffer === FALSE){
               $this->CI->output->set_output($output);
            }
         } else {
            // Use CI's loader class to render the template with our output array
            $output = $this->CI->load->view($this->master, $this->output, $buffer);
         }
      }

      return $output;
   }

   // --------------------------------------------------------------------

   /**
    * Load the master template or a single region
    *
    * DEPRECATED!
    *
    * Use render() to compile and display your template and regions
    */

    function load($region = NULL, $buffer = FALSE){
       $region = NULL;
       $this->render($region, $buffer);
    }

   // --------------------------------------------------------------------

   /**
	 * Build a region from it's contents. Apply wrapper if provided
	 *
	 * @access	private
	 * @param	string	region to build
	 * @param	string	HTML element to wrap regions in; like '<div>'
	 * @param	array	Multidimensional array of HTML elements to apply to $wrapper
	 * @return	string	Output of region contents
	 */

   function _build_content($region, $wrapper = NULL, $attributes = NULL){
      $output = NULL;

      // Can't build an empty region. Exit stage left
      if ( ! isset($region['content']) or ! count($region['content'])){
         return FALSE;
      }

      // Possibly overwrite wrapper and attributes
      if ($wrapper){
         $region['wrapper'] = $wrapper;
      }
      if ($attributes){
         $region['attributes'] = $attributes;
      }

      // Open the wrapper and add attributes
      if (isset($region['wrapper'])) {
         // This just trims off the closing angle bracket. Like '<p>' to '<p'
         $output .= substr($region['wrapper'], 0, strlen($region['wrapper']) - 1);

         // Add HTML attributes
         if (isset($region['attributes']) && is_array($region['attributes'])){
            foreach ($region['attributes'] as $name => $value){
               // We don't validate HTML attributes. Imagine someone using a custom XML template..
               $output .= " $name=\"$value\"";
            }
         }

         $output .= ">";
      }

      // Output the content items.
      foreach ($region['content'] as $content){
         $output .= $content;
      }

      // Close the wrapper tag
      if (isset($region['wrapper'])){
         // This just turns the wrapper into a closing tag. Like '<p>' to '</p>'
         $output .= str_replace('<', '</', $region['wrapper']) . "\n";
      }

      return $output;
   }

	public function _build_assets($type='scripts',$region){
		$assets='';
		if(is_array($region)){
			foreach($region AS $key=>$file){
				$assets .= $this->resourceDir($type,$file);
			}
		}
		if($type=='css')
			return "<style type=\"text/css\" title=\"importStyle\">$assets</style>";
		else
			return $assets;
	}

	private function resourceDir($type='',$file){
		if( !function_exists('subdomain')) {
			$this->CI->load->helper('resource');;
		}
		if(!$file){
			return '';
		} else if($type=='scripts'){
			$src = (preg_match("/^http:/", $file))?$file:subdomain("assets_url",0).'/'.$file;
// 			bug($src);
// 			bug($this->CI->config->item("assets_url"));
			//$src = self::get_absolute_path($src);
			return '<script type="text/javascript" src="'.$src.'"></script>';
		} else if($type=='css'){
// 			bug($file);
			$url = (preg_match("/^http/", $file))?$file:subdomain("assets_url",0).'/'.$file;
// 			$url = self::get_absolute_path($url);
			//bug($url);
// 			$url = $this->CI->config->item("assets_url").DS.$file;
			return '@import url("'.$url.'");';
		}
	}


}
// END Template Class

/* End of file Template.php */
/* Location: ./system/application/libraries/Template.php */