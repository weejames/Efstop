$(document).ready (
	function() {
		
		$('#LightboxSharingModal').dialog({
			modal: true,
			autoOpen: false,
			minWidth:500
		});
		$('.OpenLightboxSharing').click( function(e) {
			e.preventDefault();
			$('#LightboxSharingModal').dialog('open');
		});
		
		$('#LightboxAccessModal').dialog({
			modal: true,
			autoOpen: false,
			minWidth:500
		});
		$('.OpenLightboxAccess').click( function(e) {
			e.preventDefault();
			$('#LightboxAccessModal').dialog('open');
		});
		
	
	 	$('ul#Navotron li span').click( function() {
	 		if ( $(this).next('div.tagpicker').is(':visible') ) {
	 			$(this).next('div.tagpicker').hide();
	 			$(this).removeClass('open');
	 		} else {
	 			$(this).next('div.tagpicker').show();
	 			$(this).addClass('open');
	 		}
	 		
	 		return this;
	 	});
	 	
	 	$('ul#Navotron li div.tagpicker ul.alpha li a').click( function(e) {
	 		e.preventDefault();
	 		
	 		searchTerm = $(this).text();
	 		
	 		if ( searchTerm == 'All'){
	 			$('ul#Navotron li div.tagpicker ul.tags li').fadeIn();
	 		} else {
	 			$('ul#Navotron li div.tagpicker ul.tags li').each( function(x) {
					var matched = false;
					$(this).find('a').each( function(y) {
						if( $(this).text().toUpperCase().substr(0,1) == searchTerm ) matched = true;
					});
					if (!matched) $(this).fadeOut();
					else $(this).fadeIn();
				});
	 		}
	 		
	 	});
	 	
	 	$('.door').hideIfNoneSelected();
	 	
	 	$('.handle').showHideNextItem();
		

		$('.lightboxselect').setLightboxHandler();
		
		$('.addkeywords').addKeywords();	
		$('.taglist').removeKeywords();
		
		$('input[type=text]').focus( function (e) {
			if ($(this).attr('value') == $(this).attr('title')) $(this).attr('value', '');
		});
		$('input[type=text]').blur( function (e) {
			if ($(this).attr('value') == '' || typeof($(this).attr('value')) == 'undefined' ) $(this).attr('value', $(this).attr('title'));
		});
		$('input[type=text]').blur();
		
		/*$('#fileUpload').children().hide();
		$('#fileUpload').flash({
							src: baseURL + 'assets/dam/flash/FlashFileUpload.swf',
							width: 680,
							height: 360,
							wmode: 'transparent',
							flashvars: {
								uploadPage: baseURL + 'index.php/dam_controllers/image/upload/ajax/'+sessionID,
								completeFunction: 'uploadComplete()'
								}
							},
							{ version: '9.0.47',
							  expressInstall: true });*/
		$('div#upload ol li:eq(2)').hide(); 
		
		
		$('.deleteLink').confirmDelete();
		
		
		if ( $('input#searchterms_ajax').attr('title') != undefined && $('input#searchterms_ajax').val() == '' ) $('input#searchterms_ajax').val($('input#searchterms_ajax').attr('title'));
		
		$('input#searchterms_ajax').focus( function(e) {
			if ( $(this).attr('title') != undefined && $(this).val() == $(this).attr('title') ) $(this).val('');
			$(this).autocomplete({ajax_get:ajaxSearch, callback:checkURL, height:400, cache: false});
			
		});
		
		$('div#MessageArea p').animate({opacity: 1.0}, 3000)
					.fadeOut('slow', function() {
				 		$(this).remove();
				});
		
		$('h2#currentSetTitle').setTitleEdit();
		$('h2#currentBoxTitle').boxTitleEdit();
		
		$('a.createImageSet').createImageSet();
		$('a.createLightbox').createLightbox();
		
		$('form#accessControl').accessMonitor();
		
		$('form.usersdetailsForm').userDetailsMonitor();
		
		$('.notify').dismissNotification();
	}
);

$.fn.dismissNotification = function() {
	if ($(this).length < 1) return false;
	
	$(this).append('<p><a href="#" class="dismiss">Don\'t show me this again</a></p>');
	$(this).find('a.dismiss').click( function (e) {
		e.preventDefault();
		
		var container = $(this).parent().parent();
		
		$.ajax({
			type: "post",
			url: siteURL + "/dam/dismissNotification",
				data: {
					ajax: true,
					notification: container.attr('id')
				},
				success: function(obj) {
					container.fadeOut();
				},
				error: function() {
					
				}
		});
		
		
		
	});
	
}

$.fn.userDetailsMonitor = function() {
	if ($(this).length < 1) return false;

	var myParentForm = $(this);

	$(this).find('fieldset.userControl select[name=usertype]').change( function(e) {
		if ($(this).val() == 'limited') {
			$(myParentForm).find('div.grouplist').show();
			$(myParentForm).find('div.grouplist label.group').show();
			$(myParentForm).find('div.grouplist label.groupset input').hide();
			$(myParentForm).find('div.grouplist label.groupset a.addgroup').show();
			$(myParentForm).find('div.addgroupset').hide();
		} else if ($(this).val() == 'admin') {
			$(myParentForm).find('div.grouplist').show();
			$(myParentForm).find('div.grouplist label.group').hide();
			$(myParentForm).find('div.grouplist label.groupset input').show();
			$(myParentForm).find('div.grouplist label.groupset a.addgroup').hide();
			$(myParentForm).find('div.addgroupset').show();
			
			if ( $(myParentForm).find('div.grouplist label.groupset').length == 1 )  $(myParentForm).find('div.grouplist label.groupset input:first').attr('selected', 'selected'); 
			
		} else if ($(this).val() == 'super') {
			$(myParentForm).find('div.grouplist').hide();
			$(myParentForm).find('div.addgroupset').hide();
		}
	});
	
	$(myParentForm).find('div.grouplist label.groupset a.addgroup').click( function(e) {
		e.preventDefault();
		
		var header = $(this).parent();
		var createLink = $(this);
		
		$(this).after('<br /><input type="text" name="grouptitle" title="Enter name of new group" value="" id="grouptitle" /><a href="#" class="creategroup">Create Group</a> <a href="#" class="cancelcreategroup">Cancel</a>').hide();
		
		$(header).find('input[name=grouptitle]').focus(function() {
			if ($(this).val() == $(this).attr('title')) $(this).val('');
		}).blur(function() {
			if ($(this).val() == '') $(this).val($(this).attr('title'));
		});
		
		$(header).find('input[name=grouptitle]').focus();
		
		$(header).find('a.cancelcreategroup').click( function(e) {
			e.preventDefault();
			$(createLink).show();
			$(header).find('a.creategroup').remove();
			$(header).find('a.cancelcreategroup').remove();
			$(header).find('input[name=grouptitle]').remove();
			$(header).find('br').remove();
		});
		
		$(header).find('a.creategroup').click( function(e) {
			e.preventDefault();
			
			var newTitle = $(header).find('input[name=grouptitle]').val();
			var setid = $(header).find('input[name="groupsetsid[]"]').val();
		
		
			$.ajax({
				type: "post",
				url: siteURL + "/dam_controllers/damusers/addGroupToSet",
				data: {
					ajax: true,
					grouptitle: newTitle,
					groupsetid: setid
				},
				success: function(obj) {
					$(createLink).show();
					$(header).find('a.creategroup').remove();
					$(header).find('a.cancelcreategroup').remove();
					$(header).find('input[name=grouptitle]').remove();
					$(header).find('br').remove();
					
					$(header).after('<label class="group" for="groups_'+obj['groupid']+'"><input id="groups_'+obj['groupid']+'" class="checkbox push20" type="checkbox" value="'+obj['groupid']+'" name="groupsid[]"/>'+obj['grouptitle']+'</label>');
					
				},
				error: function() {
					$(header).after('We can\'t create your group at the moment.  Try again later on.');
				},
				dataType: 'json'
			});
		});
		
		
	});
	
	$(myParentForm).find('div.addgroupset a.addgroupset').click( function(e) {
		e.preventDefault();
		var header = $(this).parent();
		var createLink = $(this);
		
		$(this).after('<input type="text" name="setname" title="Enter name of new group set" value="" id="setname" /><a href="#" class="creategroupset">Create Group Set</a> <a href="#" class="cancelcreategroupset">Cancel</a>').hide();
		
		$(header).find('input[name=setname]').focus(function() {
			if ($(this).val() == $(this).attr('title')) $(this).val('');
		}).blur(function() {
			if ($(this).val() == '') $(this).val($(this).attr('title'));
		});

		$(header).find('input[name=setname]').focus();
		
		$(header).find('a.cancelcreategroupset').click( function(e) {
			e.preventDefault();
			$(createLink).show();
			$(header).find('a.creategroupset').remove();
			$(header).find('a.cancelcreategroupset').remove();
			$(header).find('input[name=setname]').remove();
		});
		
		$(header).find('a.creategroupset').click( function(e) {
			e.preventDefault();
			
			var newTitle = $(header).find('input[name=setname]').val();
		
		
			$.ajax({
				type: "post",
				url: siteURL + "/dam_controllers/damusers/addGroupSet",
				data: {
					ajax: true,
					setname: newTitle
				},
				success: function(obj) {
					$(createLink).show();
					$(header).find('a.creategroupset').remove();
					$(header).find('a.cancelcreategroupset').remove();
					$(header).find('input[name=setname]').remove();
					
					$('div.grouplist').append('<label class="groupset" for="groupset_'+obj['groupsetid']+'"><input id="groupset_'+obj['groupsetid']+'" class="checkbox" type="checkbox" value="'+obj['groupsetid']+'" name="groupsetsid[]" /><strong>'+obj['setname']+'</strong><a class="addgroup" href="#" style="display: none;">Add Group</a></label>');
					
					$('div.grouplist label:last').after('<label class="group" for="groups_'+obj['groupid']+'"><input id="groups_'+obj['groupid']+'" class="checkbox push20" type="checkbox" value="'+obj['groupid']+'" name="groupsid[]"/>'+obj['grouptitle']+'</label>');
					
					$('div.grouplist label:last').hide();
					
					$('form.usersdetailsForm').userDetailsMonitor();
				},
				error: function() {
					$(header).after('We can\'t create your group at the moment.  Try again later on.');
				},
				dataType: 'json'
			});
		});
	});
	
	$(this).find('fieldset.userControl select[name=usertype]').change();

}

$.fn.accessMonitor = function() {
	if ($(this).length < 1) return false;

	var myForm = $(this);

	$(this).find('input[name=whoto]').click( function (e) {
		if ( $(this).val() == 'group' ) {
			myForm.find('div.group').show();
			myForm.find('div.user').hide();
			myForm.find('div.guest').hide();
		} else if ( $(this).val() == 'user' ) {
			myForm.find('div.group').hide();
			myForm.find('div.user').show();
			myForm.find('div.guest').hide();
		} else if ( $(this).val() == 'guest' ) {
			myForm.find('div.group').hide();
			myForm.find('div.user').hide();
			myForm.find('div.guest').show();
		}
		
	});
	
	$(this).find('input[name=whoto]:first').click();
	
}


$.fn.createImageSet = function() {
	$(this).click( function(e) {
		e.preventDefault();
		
		var header = $(this).parent();
		var createLink = $(this);
		
		$(this).after('<input type="text" name="settitle" title="Enter name of new set" value="" id="settitle" /><a href="#" class="createset">Create Set</a> <a href="#" class="cancelcreateset">Cancel</a>').hide();
		
		$(header).find('input').val( $(header).find('input').attr('title') );
		
		$(header).find('input').focus(function() {
			if ($(this).val() == $(this).attr('title')) $(this).val('');
		}).blur(function() {
			if ($(this).val() == '') $(this).val($(this).attr('title'));
		});
		
		$(header).find('a.cancelcreateset').click( function(e) {
			e.preventDefault();
			$(createLink).show();
			$(header).find('a.createset').remove();
			$(header).find('a.cancelcreateset').remove();
			$(header).find('input').remove();
		});
		
		$(header).find('a.createset').click( function(e) {
			var newTitle = $(header).find('input').val();
		
			$.ajax({
				type: "post",
				url: siteURL + "/dam_controllers/image/createImageset",
				data: {
					ajax: true,
					setname: newTitle
				},
				success: function(obj) {
					$(createLink).show();
					$(header).find('a.createset').remove();
					$(header).find('a.cancelcreateset').remove();
					$(header).find('input').remove();
					
					//only invoked on show image sets page.
					$('ul.ImageSetsContainer').prepend('<li class="ImageSetsContainer"><a href="'+siteURL+'/dam_controllers/image/viewSet/'+obj['imagesetid']+'">'+obj['settitle']+'</a><small>0 Images</small><hr/><ul class="clearfix"/></li>');
					
					$('ul.ImageSetsContainer').find('p.notice').remove();
					
					//only invoked on orphans page.
					
					$('select#imagesetid').siblings('em').remove();
					$('select#imagesetid option[selected=selected]').attr('selected', '');
					$('select#imagesetid').append('<option value="'+obj['imagesetid']+'" selected="selected">'+obj['settitle']+'</option>');
					$('ul.orphans select').append('<option value="'+obj['imagesetid']+'>'+obj['settitle']+'</option>');
					$('select#imagesetid').show();
					
				},
				error: function() {
					$(header).after('We can\'t create your set at the moment.  Try again later on.');
				},
				dataType: 'json'
			});
		});
		
	});
};


$.fn.createLightbox = function() {
	$(this).click( function(e) {
		e.preventDefault();
		
		var header = $(this).parent();
		var createLink = $(this);
		
		$(this).after('<input type="text" name="boxtitle" title="Enter name of new lightbox" value="" id="boxtitle" /><a href="#" class="createbox">Create Lightbox</a> <a href="#" class="cancelcreatebox">Cancel</a>').hide();
		
		$(header).find('input').val( $(header).find('input').attr('title') );
		
		$(header).find('input').focus(function() {
			if ($(this).val() == $(this).attr('title')) $(this).val('');
		}).blur(function() {
			if ($(this).val() == '') $(this).val($(this).attr('title'));
		});
		
		$(header).find('a.cancelcreatebox').click( function(e) {
			e.preventDefault();
			$(createLink).show();
			$(header).find('a.createbox').remove();
			$(header).find('a.cancelcreatebox').remove();
			$(header).find('input').remove();
		});
		
		$(header).find('a.createbox').click( function(e) {
			var newTitle = $(header).find('input').val();
		
			$.ajax({
				type: "post",
				url: siteURL + "/dam_controllers/lightbox/create",
				data: {
					ajax: true,
					boxtitle: newTitle
				},
				success: function(obj) {
					$(createLink).show();
					$(header).find('a.createbox').remove();
					$(header).find('a.cancelcreatebox').remove();
					$(header).find('input').remove();
					
					//this will only work on the lightboxes page
					
					$('ul.lightboxes').prepend('<li class="ImageSetsContainer"><a href="'+siteURL+'/dam_controllers/lightbox/viewBox/'+obj['lightboxid']+'">'+obj['boxtitle']+'</a><small>0 Images</small><hr/><ul class="clearfix"/></li>');
					
					$('ul.lightboxes').find('p.notice').remove();
					
					//this will only work on a page with the 'global lightboxes' panel
					$('#globalLightboxContainer').find('select').show(); //in case it was hidden
					$('#globalLightboxContainer').find('em.nolightboxes').remove();
					$('#globalLightboxContainer').find('select option').removeAttr('selected');
					$('#globalLightboxContainer').find('select').append('<option value="'+obj['lightboxid']+'" selected="selected">'+obj['boxtitle']+'</option>');
					$('#globalLightboxContainer').find('select').change();
					
					//will only work on homepage of efstop
					$('#Lightboxes ul').append('<li><a class="red" href="'+siteURL+'/dam_controllers/lightbox/viewBox/'+obj['lightboxid']+'">'+obj['boxtitle']+'</a><small>0 Images</small><hr /></li>');
					$('#Lightboxes ul').find('p.notice').remove();
					
				},
				error: function() {
					$(header).after('We can\'t create your lightbox at the moment.  Try again later on.');
				},
				dataType: 'json'
			});
		});
		
	});
};

$.fn.setTitleEdit = function() {
	/*$(this).hover(function() {
			$(this).addClass('edit-highlight');	
		}, function() {
			$(this).removeClass('edit-highlight');	
	});*/
	
	$(this).children('a.renameset').click( function(e) {
		e.preventDefault();
		
		var titleContainer = $(this).parent().children('span');
		var titleValue = titleContainer.text();
		
		$(titleContainer).after('<input type="text" name="settitle" id="settitle" value="'+titleValue+'" /> <a href="#" class="savesettitle">Save</a> <a href="#" class="cancelsettitle">Cancel</a>').hide();
		$(this).hide();
		$(titleContainer).parent().find('a.deleteLink').hide();
		
		$(titleContainer).parent().find('a.cancelsettitle').click( function(e) {
			e.preventDefault();
			$(titleContainer).show();
			$(titleContainer).parent().find('a.renameset').show();
			$(titleContainer).parent().find('a.savesettitle').remove();
			$(titleContainer).parent().find('a.cancelsettitle').remove();
			$(titleContainer).parent().find('input').remove();
			$(titleContainer).parent().find('a.deleteLink').show();
		});
		$(titleContainer).parent().find('a.savesettitle').click( function(e) {
			var newTitle = $(titleContainer).parent().find('input').val();
		
			$.ajax({
				type: "post",
				url: siteURL + "/dam_controllers/image/setSetName/"+ $('input.imagesetid').val(),
				data: {
					ajax: true,
					setname: newTitle
				},
				success: function() {
					$(titleContainer).text(newTitle);
					$(titleContainer).show();
					$(titleContainer).parent().find('a.renameset').show();
					$(titleContainer).parent().find('a.savesettitle').remove();
					$(titleContainer).parent().find('a.cancelsettitle').remove();
					$(titleContainer).parent().find('input').remove();
					$(titleContainer).parent().find('a.deleteLink').show();
				},
				error: function() {
					$(titleContainer).after('We can\'t change the name of your set at the moment.  Try again later on.');
				}
			});
		});
		
	});

}

$.fn.boxTitleEdit = function() {
	$(this).children('a.renamebox').click( function(e) {
		e.preventDefault();
		
		var titleContainer = $(this).parent().children('span');
		var titleValue = titleContainer.text();
		
		$(titleContainer).after('<input type="text" name="boxtitle" id="boxtitle" value="'+titleValue+'" /> <a href="#" class="saveboxtitle">Save</a> <a href="#" class="cancelboxtitle">Cancel</a>').hide();
		$(this).hide();
		$(titleContainer).parent().find('a.deleteLink').hide();
		
		$(titleContainer).parent().find('a.cancelboxtitle').click( function(e) {
			e.preventDefault();
			$(titleContainer).show();
			$(titleContainer).parent().find('a.renamebox').show();
			$(titleContainer).parent().find('a.deleteLink').show();
			$(titleContainer).parent().find('a.saveboxtitle').remove();
			$(titleContainer).parent().find('a.cancelboxtitle').remove();
			$(titleContainer).parent().find('input').remove();
		});
		$(titleContainer).parent().find('a.saveboxtitle').click( function(e) {
			var newTitle = $(titleContainer).parent().find('input').val();
		
			$.ajax({
				type: "post",
				url: siteURL + "/dam_controllers/lightbox/setName/"+ $('input.lightboxid').val(),
				data: {
					ajax: true,
					boxtitle: newTitle
				},
				success: function() {
					$(titleContainer).text(newTitle);
					$(titleContainer).show();
					$(titleContainer).parent().find('a.renamebox').show();
					$(titleContainer).parent().find('a.deleteLink').show();
					$(titleContainer).parent().find('a.saveboxtitle').remove();
					$(titleContainer).parent().find('a.cancelboxtitle').remove();
					$(titleContainer).parent().find('input').remove();
				},
				error: function() {
					$(titleContainer).after('We can\'t change the name of your lightbox at the moment.  Try again later on.');
				}
			});
		});
		
	});

}

function uploadComplete() {
	$('div#upload ol li:eq(0)').hide();
	$('div#upload ol li:eq(1)').hide();
	$('div#upload ol li:eq(2)').show();
	
}

function checkURL(obj) {
	window.location = siteURL+'/dam_controllers/image/viewImage/'+obj['id']
}

function ajaxSearch(v, cont){ 
   var a=[];
   
   $.ajax({
			type: "POST",
			url: siteURL + "/dam_controllers/image/ajax_search",
			data: {
				ajax: true,
				searchterms: v
			},
			success: function(imagedata){
				for (var i = 0; i<imagedata.length; i++){
					entrystyle = "background-image:url('"+imagedata[i]['image_src']+"');";
					a.push({id:imagedata[i]['id'], value:imagedata[i]['title'], style:entrystyle});
				}
				
				//a.push({id:i, value:v+names[i], info:"demo string #"+i, extra:'extra fields remains!'});
				cont(a);
			},
			dataType: 'json'
		});
   
   return a;
}

$.fn.confirmDelete = function() {
	this.click( function (e) {
		if (!confirm('Are you sure you want to delete this?  Once its gone its gone forever!')) return false;
		else return true;
	});
}

$.fn.showHideNextItem = function () {
	$(this).css('cursor', 'pointer');
	
	$.each(this, function(i, n){
		if ($( '#' + $(n).attr('id') + '_door').is(':visible')) $(n).addClass('open');
	});
	
	this.click( function (e) {
	   
	   if ( $(this).hasClass('open') ) $(this).removeClass('open');
	   else $(this).addClass('open');

		$( '#' + $(this).attr('id') + '_door').slideToggle();
		
	});	
}

$.fn.hideIfNoneSelected = function() {
	$.each(this, function(i, n){
		//find selected boxes
		var checked = $(this).find('input:checked');
		if (checked.length == 0) $(n).hide();
	});
}

$.fn.setLightboxHandler = function () {
	var lightboxTarget = '#globalLightboxContainer';
	
	$('body').append('<div id="globalLightboxContainer"></div>');
	$('div.Wrapper').css('marginBottom', '120px');
	
	$(lightboxTarget).prepend('<h2 id="lightboxHeader">Current Lightbox</h2>');
	
	$(lightboxTarget).append('<a href="#" class="createLightbox">Add New Lightbox</a>');
	
	$(lightboxTarget).append('<div><ul></ul></div>');
	
	var lightboxes = $(this).children('select[name=lightboxid] option');
	
	$('a.addall').click( function(e) {
		e.preventDefault();
		
		var tagstring = $('input#tag_string').val();
		
		if (tagstring) {
			var lightboxid = $('#globalLightbox').attr('value');
		
			if (lightboxid != undefined) {
			
				$.ajax({
					type: "POST",
					url: siteURL + "/dam_controllers/image/addTaggedToLightbox/",
					data: {
						ajax: true,
						tagstring: tagstring,
						lightboxid: lightboxid
					},
					success: function(msg){
						$('#lightboxHeader').after('<p id="newMessage">'+ msg + '</p>' );
						
						$('#newMessage').animate({opacity: 1.0}, 3000)
							.fadeOut('slow', function() {
								$(this).remove();
						});
						
						loadLightboxContent(lightboxid, $(lightboxTarget), true);
						
					}
				});
				
			}
			
		}
		
		return this;
	});

	$.getJSON(siteURL + "/dam_controllers/lightbox/ajax_lightboxes",
		function(obj) {
			$( '#lightboxHeader' ).after( '<select id="globalLightbox"></select>' );

			if (obj[0] != false) {
				$.each(obj, function(i,item){
					var thisoption = '<option value="'+item['id']+'"'; 
					if (item['selected'] == true) thisoption = thisoption + ' selected="selected"';
					thisoption = thisoption +'>'+item['boxtitle']+'</option>';
					
					$( lightboxTarget + ' select' ).append(thisoption);
				});
				
			} else {
				$( lightboxTarget + ' select' ).after( '<em class="nolightboxes">No lightboxes available</em>' );
				$( lightboxTarget + ' select' ).hide();
			}
			
			$( lightboxTarget + ' select').lightboxChangeHandler(lightboxTarget);
			$( lightboxTarget + ' select').change();
		});

	
	$(this).children('select').hide();
	$(this).find('input[type=submit]').click(function (e) {
		e.preventDefault();

		var imageid =  $(this).parents('form').find('input[name=imageid]').attr('value');
		var lightboxid = $('#globalLightbox').attr('value');
		
		if (lightboxid != undefined) {
		
			$.ajax({
				type: "POST",
				url: siteURL + "/dam_controllers/image/addToLightbox/"+imageid,
				data: {
					ajax: true,
					imageid: imageid,
					lightboxid: lightboxid
				},
				success: function(msg){
					$('#lightboxHeader').after('<p id="newMessage">'+ msg + '</p>' );
					
					$('#newMessage').animate({opacity: 1.0}, 3000)
						.fadeOut('slow', function() {
							$(this).remove();
					});
					
					loadLightboxContent(lightboxid, $(lightboxTarget), true);
					
				}
			});	
		
		} else {
			$('#lightboxHeader').after('<p id="newMessage">No lightboxes are available, create one before adding images.</p>' );
					
			$('#newMessage').animate({opacity: 1.0}, 3000)
				.fadeOut('slow', function() {
					$(this).remove();
			});
		
		}
		return false;
	});
	
	var lightboxid = $('#globalLightbox').attr('value');

	loadLightboxContent(lightboxid, $(lightboxTarget));
	
	$('#globalLightboxContainer').find('select').lightboxChangeHandler(lightboxTarget);
	
	if (readCookie('lightbox') != 'open') $(lightboxTarget).css('height','20px');
	
	$(lightboxTarget).find('h2').click(function(e) {
		if ($(lightboxTarget).css('height') == '20px') {
			$(lightboxTarget).animate( { height: 110 });
			createCookie('lightbox','open',1);
		} else {
			$(lightboxTarget).animate( { height: 20 });
			createCookie('lightbox','closed',1);
		}
	});
	
}

$.fn.lightboxChangeHandler = function(lightboxTarget) {
	$(this).change( function(e) {
		var boxid = $(this).val();
		$.get(siteURL + "/dam_controllers/lightbox/setCurrentLightbox/"+boxid);
		loadLightboxContent(boxid, $(lightboxTarget));
	});
}

function loadLightboxContent(lightboxid, target, scrollToLast) {
	$.getJSON(siteURL + "/dam_controllers/lightbox/ajax_lightboxcontents/"+lightboxid,
        function(data){
			$(target).find('ul li').remove();
			$.each(data, function(i,item){
				$(target).find('ul').append('<li><a href="'+item['url']+'" title="View '+item['title']+'" style="background-image: url(\''+item['display']+'\');">'+item['title']+'</a></li>');
			});
			var totalwidth = $(target).find('ul li').length * ($(target).find('ul li').width() + 10);
			$(target).find('ul').css('width', totalwidth);
			if (scrollToLast != undefined && scrollToLast == true) {
				$(target).find('div').animate( {scrollLeft: $(target).find('ul li:last').offset().left}, 0);
				
			}
    });
}

$.fn.addKeywords = function () {
	if (this.length == 0) return false;
	
	$(this).children(':submit').click(function (e) {
		e.preventDefault();
		var keywordInput = $(this).siblings("input:text");
		var keywords =  keywordInput.attr('value');
		var formTarget = $(this).parent().attr('action');
		
		$.ajax({
			type: "POST",
			url: formTarget,
			data: {
				ajax: true,
				keywords: keywords
			},
			success: function(imageid){
				$().refreshTagList(imageid);
				keywordInput.attr('value', '');
			}
		});
		
		
		return false;
	});
}

$.fn.removeKeywords = function () {
	if (this.length == 0) return false;
	
	$(this).children('li').children('a').click(function (e) {
		e.preventDefault();
		var link = $(this).attr('href');
		
		$.ajax({
			type: "POST",
			url: link,
			data: {
				ajax: true
			},
			success: function(imageid){
				$().refreshTagList(imageid);
			}
		});
		return false;
	});
}

$.fn.refreshTagList = function (imageid) {
	$('.taglist').load(siteURL+'/dam_controllers/image/refreshTags/'+imageid, '', function() {
											$('.taglist').removeKeywords()
											});
}

// cookie functions http://www.quirksmode.org/js/cookies.html
function createCookie(name,value,days)
{
	if (days)
	{
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
}
function readCookie(name)
{
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++)
	{
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}
function eraseCookie(name)
{
	createCookie(name,"",-1);
}
// /cookie functions
