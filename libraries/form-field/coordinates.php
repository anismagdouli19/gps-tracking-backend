<?php
class coordinates extends form {
	function __construct(){
		$this->CI =& get_instance();
	}
	public function input($fieldKey,$fieldData){
		$lable = preg_replace("/<.*?>/", "", $fieldData->lable);
		$attribute = '';
		$fullname =lang('tag coordinates on map');

		$html = '<span class="input-with-button" ><input type="text" name="'.parent::protection($fieldKey).'" value="'.$fieldData->value.'" class="" '.$attribute.' READONLY placeholder="'.lang('tag coordinates on map').'" aria-label="'.lang('tag coordinates on map').'" />'
			.'<button class="grey" type="button" name="'.parent::protection("$fieldKey-button").'" >'.lang('Open Map').'</button>'
			.'</span>'
			.'<div id="maps" ></div>'
		;
		$this->CI->template->add_js('http://maps.google.com/maps/api/js?sensor=true&language=vi');
		
		//$html.=parent::inputHidden($fieldKey,$fieldData->value);
		$script = '$("input[name='.parent::protection("$fieldKey-readonly").']").focus(function() {'
					.'choose_coordinates();'
				.'});'
				.'$("button[name='.parent::protection("$fieldKey-button").']").click(function() {'
					.'choose_coordinates();'
				.'});'
				.'function choose_coordinates(){'
					//.'if( $("#modal").length > 0 &&  !$("#modal").is(":hidden")  ) return false;'
					.'$( "#maps" ).dialog({'
						//.'content: "<div id=\"maps\" style=\"height:100%; width:100%; display:inline-block; top:0;\" ></div>",'
						.'title: "'.lang('Google Map').'", width: 800,height:400, resizable: false, modal: true, '
						.'buttons: { '
							.' "Select" :function(){ '
								.'$("input[name='.parent::protection($fieldKey).']").val($("input[name=complex-latitude]").val()+","+$("input[name=complex-longitude]").val());'
								.'$( "#maps" ).dialog( "destroy" );'
								.'return true;'
							.'}, '
							.' "Close": function() { $( this ).dialog( "close" ); $( this ).dialog( "destroy" ) } '
						.'}'
					.'});'
					
					.'$(".ui-widget-content").css({"padding":0,"margin":0});'
					//.'$(".ui-widget-content").css({"padding":15,"margin":0});'
					.'$("#maps").after('
						.'\'<p class="input-50"><label>Latitude</label><input type="text" class="grid_11 align-center" value="16.509833" name="complex-latitude"></p>\''
						.'+\'<p class="input-50"><label>Longitude</label><input type="text" class="grid_11 align-center" value="107.006836" name="complex-longitude"></p>\''
						//.'+\'<button type="button" class="float-left red use-this" >Select</button>\''
					.');'
					
					.'initialize();'
					.'$("button.use-this").click(function(){'
						.'$("input[name='.parent::protection($fieldKey).']").val($("input[name=complex-latitude]").val()+","+$("input[name=complex-longitude]").val());'
						.'$( "#maps" ).dialog( "destroy" );'
						.'return true;'
					.'});'
					
				.'}'
				.'var map, marker;'
				.'function initialize() {'
					.'var $inputLat = $("input[name=complex-latitude]");'
					.'var $inputLng = $("input[name=complex-longitude]");'
					.'var myLatlng = new google.maps.LatLng( $inputLat.val(),$inputLng.val() );'
					.'var myOptions = {zoom: 5, center: myLatlng, mapTypeId: google.maps.MapTypeId.ROADMAP};'
					.'map = new google.maps.Map(document.getElementById("maps"), myOptions);'
					.'marker = new google.maps.Marker({ draggable: true, position: myLatlng, map: map, title: "Your location" });'
					.'google.maps.event.addListener(marker,"dragend",function(event){'
						.'$inputLat.val( this.getPosition().lat() );'
						.'$inputLng.val( this.getPosition().lng() );'
					.'});'
					.'$inputLat.change(function(){ updateMarkerCenter($inputLat.val(), $inputLng.val() ); });'
					.'$inputLng.change(function(){ updateMarkerCenter($inputLat.val(), $inputLng.val() ); });'

    			.'};'
    			.'function updateMarkerCenter(lat,lng){'
    				.'$point = new google.maps.LatLng(lat,lng);'
    				.'map.panTo($point);'
    				.'marker.setPosition($point);'
    			.'}'
  				;
		$this->CI->template->add_js_ready($script);
		return $html;
	}
	
}
