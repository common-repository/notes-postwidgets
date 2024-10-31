var NotesPostWidgets = {
		
	// Search
	search: function(searchId, chooseId) {
		
		// Show loading
		$('NotesPostWidgets-search-loading-'+ searchId).show();
		
		// Data to send
		var data = {
			action: 'notes_post_widgets_search',
			keyword: $(searchId).value
		}
		
		// Ajax
		new Ajax.Request(ajaxurl, {
			parameters: data,
			// Success
			onSuccess: function(response) {
			
				// Get output
				var output = "<ul style=\"font-size: 8pt;\">";
				for (i=0,end=response.responseJSON.length; i<end; i++) {
					output = output +"<li><a href=\"javascript:NotesPostWidgets.choose('"+ searchId +"', '"+ chooseId +"', '"+ response.responseJSON[i]['id'] +"');\">"+ response.responseJSON[i]['title'] +"</a></li>";
				}
				output = output +"</ul>";
				
				// Hide loading
				$('NotesPostWidgets-search-loading-'+ searchId).hide();
				
				// Show output
				if (!$('NotesPostWidgets-search-result-'+ searchId).visible()) {
					$('NotesPostWidgets-search-result-'+ searchId).show();
				}
				$('NotesPostWidgets-search-result-'+ searchId).innerHTML = output;
				
			}
		});
		
	},
	
	// View search box
	toggle: function(from, to) {
		$(from).hide();
		$(to).show();
	},
	
	// Choose a search result
	choose: function(searchId, chooseId, postId) {
	
		// Hide search box and show choose box
		this.toggle('NotesPostWidgets-container-'+ searchId, 'NotesPostWidgets-container-'+ chooseId);
		
		// Choose
		$(chooseId).value = postId;
	}
	
}











