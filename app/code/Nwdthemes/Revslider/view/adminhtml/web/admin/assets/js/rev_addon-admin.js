
(function( $ ) {
	$(document).ready(function(){
		//hover toggles buttons


		//click event to install plugin
			$(".rs-addon-not-installed").click(function(){
				showWaitAMinute({fadeIn:300,text:rev_slider_addon.please_wait_a_moment});
				$this = $(this);
				//console.log("install process");
				$.ajax({
				url : rev_slider_addon.ajax_url,
				type : 'post',
				data : {
					action : 'install_plugin',
					nonce: 	$('#ajax_rev_slider_addon_nonce').text(),// The security nonce
                    form_key:window.FORM_KEY,
					plugin: $this.data("plugin")
				},
				success : function( response ) {
					switch(response){
						case "0":
                                showWaitAMinute({fadeOut:300,text:rev_slider_addon.please_wait_a_moment});
                                UniteAdminRev.showErrorMessage("Something went wrong!");
								break;
						case "1":
								location.reload();
								break;
						case "-1":
                                showWaitAMinute({fadeOut:300,text:rev_slider_addon.please_wait_a_moment});
                                UniteAdminRev.showErrorMessage("Nonce missing!");
								break;
					}
				},
				error : function ( response ){
                    showWaitAMinute({fadeOut:300,text:rev_slider_addon.please_wait_a_moment});
                    UniteAdminRev.showErrorMessage("Something went wrong!");
				}
			}); // End Ajax
		}); // End Click

		//click event to activate plugin
			$(".rs-addon-not-activated").click(function(){
				showWaitAMinute({fadeIn:300,text:rev_slider_addon.please_wait_a_moment});
				
				$this = $(this);
				$.ajax({
				url : rev_slider_addon.ajax_url,
				type : 'post',
				data : {
					action : 'activate_plugin',
					nonce: 	$('#ajax_rev_slider_addon_nonce').text(),// The security nonce
                    form_key:window.FORM_KEY,
					plugin: $this.data("plugin")
				},
				success : function( response ) {
					switch(response){
						case "0":
								console.log("Something Went Wrong");
								break;
						case "1":
								console.log("Plugin activated");
								 location.reload();
								break;
						case "-1":
								console.log("Nonce missing");
								break;
					}
				},
				error : function ( response ){
					console.log('Ajax Error');
				}
			}); // End Ajax
		}); // End Click

		//click event to deactivate plugin
		$(".rs-dash-deactivate-addon").click(function(){
				showWaitAMinute({fadeIn:300,text:rev_slider_addon.please_wait_a_moment});
				$this = $(this);
				$.ajax({
				url : rev_slider_addon.ajax_url,
				type : 'post',
				data : {
					action : 'deactivate_plugin',
					nonce: 	$('#ajax_rev_slider_addon_nonce').text(),// The security nonce
                    form_key:window.FORM_KEY,
					plugin: $this.data("plugin")
				},
				success : function( response ) {
					switch(response){
						case "0":
								console.log("Something Went Wrong");
								break;
						case "1":
								console.log("Plugin deactivated");
								location.reload();
								break;
						case "-1":
								console.log("Nonce missing");
								break;
					}
				},
				error : function ( response ){
				}
			}); // End Ajax
		}); // End Click
	});// End Document ready

})( jQuery );


if (showWaitAMinute===undefined)
	function showWaitAMinute(obj) {
		var wm = jQuery('#waitaminute');
		// SHOW AND HIDE WITH DELAY
		if (obj.delay!=undefined) {

			punchgs.TweenLite.to(wm,0.3,{autoAlpha:1,ease:punchgs.Power3.easeInOut});
			punchgs.TweenLite.set(wm,{display:"block"});

			setTimeout(function() {
				punchgs.TweenLite.to(wm,0.3,{autoAlpha:0,ease:punchgs.Power3.easeInOut,onComplete:function() {
					punchgs.TweenLite.set(wm,{display:"block"});
				}});
			},obj.delay)
		}

		// SHOW IT
		if (obj.fadeIn != undefined) {
			punchgs.TweenLite.to(wm,obj.fadeIn/1000,{autoAlpha:1,ease:punchgs.Power3.easeInOut});
			punchgs.TweenLite.set(wm,{display:"block"});
		}

		// HIDE IT
		if (obj.fadeOut != undefined) {

			punchgs.TweenLite.to(wm,obj.fadeOut/1000,{autoAlpha:0,ease:punchgs.Power3.easeInOut,onComplete:function() {
					punchgs.TweenLite.set(wm,{display:"block"});
				}});
		}

		// CHANGE TEXT
		if (obj.text != undefined) {
			switch (obj.text) {
				case "progress1":

				break;
				default:
					wm.html('<div class="waitaminute-message"><i class="eg-icon-emo-coffee"></i><br>'+obj.text+'</div>');
				break;
			}
		}
	}
