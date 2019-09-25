define([
  "jquery",
  'domReady!',
  'jquery/ui'
], 
function($) {
  "use strict";
	
	$(document ).ajaxComplete(function() {
	  $('#block-discount .content').css('display','block');
	});

});