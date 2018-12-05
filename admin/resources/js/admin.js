$(function() {

	var arrayOfPieces = [];
	var arrayOfCollections = [];

	$(".collection").sortable({
		connectWith: ".collection",
		update: function(event, ui) {
			$.each($(this).sortable('toArray'), function(key, value) {
				arrayOfPieces.push(value.replace('itemID-', ''));
			});
			
			collectionID = $(this).attr("id").replace('sortable-', '');
			$.get("resources/helpers/updateDisplayOrder.php?type=pieces&order="+encodeURIComponent(arrayOfPieces) + "&collectionID=" + collectionID);
		},
		receive: function(event, ui) {
			newItemID = $(ui.item).attr("id").replace('itemID-', '');
			$.each($(this).sortable('toArray'), function(key, value) {
				arrayOfPieces.push(value.replace('itemID-', ''));
			});
			
			collectionID = $(this).attr("id").replace('sortable-', '');
			$.get("resources/helpers/updateDisplayOrder.php?type=pieces&order="+encodeURIComponent(arrayOfPieces) + "&collectionID=" + collectionID);
			$.get("resources/helpers/updateCollection.php?itemID="+newItemID+"&newCollectionID=" + collectionID);
		}
	}).disableSelection();

	$(".collections-wrapper").sortable({
		update: function(event, ui) {
			var arrayOfCollections = [];
			$.each($(this).sortable('toArray'), function(key, value) {
				arrayOfCollections.unshift(value.replace('collectionID-', '')); // reverse so that new dropbox folders/collections get added to the top of the website
			});
			$.get("resources/helpers/updateDisplayOrder.php?type=collections&order="+encodeURIComponent(arrayOfCollections));// 
			// console.log("resources/helpers/updateDisplayOrder.php?type=collections&order="+encodeURIComponent(arrayOfCollections));
		},
		axis: 'y'
	}).disableSelection();

	// $(".collections-wrapper .collection-sortable").click(function() {
	// 	$(this).toggleClass("collapsed");
	// });

	$("input.collection-title").blur(function() {
		collectionID = $(this).attr("data-collectionID");
		newCollectionTitle = $(this).val();
		$.get("resources/helpers/updateCollection.php?newCollectionTitle="+newCollectionTitle+"&collectionID=" + collectionID);
	}).focus(function() {
		$(this).siblings("button.autosave").show();
	});

	$(".image-wrapper").click(function(e) {
		itemID = $(this).attr("data-itemID");
	
		$(this).addClass("selected");
		$(".image-wrapper").not($(this)).removeClass("selected");
		
		offsetTop = $(this).offset().top;
		offsetLeft = $(this).offset().left + $(".image-wrapper").width() + 55;
		if(offsetLeft > ($(window).width() - $("#editor").width())) {
			offsetLeft = $(this).offset().left - $("#editor").width() - 25;
			$("#editor").attr("class", "arrow-right");
		} else {
			$("#editor").attr("class", "arrow-left");
		}
		
		if(window.editorItem == itemID) {
			hideEditor();
		} else {	
			$("#editor").show().css({ top: offsetTop, left: offsetLeft });
			window.editorItem = itemID;
		}
		
		$.get("/resources/helpers/get-item-info.php?itemID="+itemID+"&admin", function(data) {
			item = jQuery.parseJSON(data)[0];
			$("#editor form").children("input#itemID").val(itemID);
			$("#editor form").children("input#title").val(item.title).focus();
			$("#editor form").children("textarea#description").val(item.description).focus();
			$("#editor form").children("select#price").val(item.price).focus();
			if(item.availability == 1) {
				$("#editor form").children("input#availability").attr("checked", "checked").focus();
			} else {
				$("#editor form").children("input#availability").removeAttr("checked").focus();
			}
			$("#editor form").children("input[type=submit]").focus();
		});
		
		$("#editor-contents form").ajaxForm(function() { 
			$("#editor").addClass("success").delay(100, function() {
				$("#editor").removeClass("success").addClass("success-post").delay(10, function() {
					hideEditor();
				});
			});
		});
		
		e.stopPropagation();
	});
	
	
	$(".visibility-toggle").click(function(e) {
		itemID = $(this).parent().attr("data-itemID");
		if($(this).parent().hasClass("visibility-on")) {
			visibility = 0;
			$(this).parent().removeClass("visibility-on").addClass("visibility-off");
		} else if($(this).parent().hasClass("visibility-off")) {
			visibility = 1;
			$(this).parent().removeClass("visibility-off").addClass("visibility-on");
		}
		$.get("resources/helpers/updateItem.php?itemID="+itemID+"&visibility="+visibility);
		e.stopPropagation();
	});
		
	$("#editor").children().click(function(e) {
		e.stopPropagation();
	});

	$("html").click(function(e) {
		hideEditor();
	});
	
	$("button.autosave").click(function() {
		$(this).addClass("saved").text("Saved").delay(500, function() {
			$(this).hide().removeClass("saved").text("Save");
		});
	});
	
	$(".setDescriptionForCollectionItems").click(function() {
		$("form.setDescriptionForCollectionItemsForm[data-collectionID='"+$(this).attr("data-collectionID")+"']").slideToggle(200);
		hideEditor();
	});
	
	$("button.confirm").click(function() {
		descriptionElement = $("input.collection-items-description");
		description = descriptionElement.val();
		collectionID = descriptionElement.attr("data-collectionID");
		if(description == "") { descriptionAlert = "be removed! "; } else { descriptionAlert = 'be set to "' + description+'".'; } 
		confirmSetDescription = confirm('This is a one-time action that will overwrite the descriptions for each item in this collection.\n\nThe descriptions of every item in this collection will '+descriptionAlert+'\n\nAre you sure you want to do this?');
		if(confirmSetDescription) {
			$.get("resources/helpers/updateCollection.php?collectionID="+collectionID+"&setDescriptionForCollectionItems="+encodeURIComponent(description));
			$(this).addClass("saved").text("Saved").delay(500, function() {
				$(this).removeClass("saved").text("Save");
				$("form.setDescriptionForCollectionItemsForm[data-collectionID='"+$(this).attr("data-collectionID")+"']").slideUp(200);	
			});				
		}
		return false;
	});
	
	$("a.deleteCollection").click(function() {
		collectionID = $(this).attr("data-collectionID");
		if($("ul#sortable-"+collectionID).children().length == 0) {
			confirmDelete = confirm("Are you sure you want to delete this collection?");
			if(confirmDelete) {
				$.get("resources/helpers/updateCollection.php?collectionID="+collectionID+"&deleteCollection=true");
				$("li.collection-sortable#collectionID-"+collectionID).fadeOut();
			}
		} else {
			alert("You cannot delete a collection that contains work.\n\nTo delete this collection, remove all pieces from it.")
		}
	});
	
	$("a.collapse-all").click(function() {
		$("li.image-wrapper").toggleClass("mini");
		if($(this).hasClass("collapse-all")) {
			$(this).addClass("expand-all").removeClass("collapse-all").text("Expand All");
		} else {
			$(this).addClass("collapse-all").removeClass("expand-all").text("Collapse All");
		}
	});
	
	$(".order button.ship").click(function() {
		$("header a#shipped").addClass("success").delay(1000, function() {
			$("header a#shipped").removeClass("success");
		});
		trackingNumber = $(this).parent().parent().children().children("input#trackingNumber").val();
		trackingCarrier = $(this).parent().parent().children().children("select#trackingCarrier").val();
		orderID = $(this).closest(".order").attr("id");
		$.get("resources/helpers/updateOrder.php?orderID="+orderID+"&status=1&trackingNumber="+trackingNumber+"&trackingCarrier="+trackingCarrier);
		$(this).closest(".order").fadeOut(200);
	});
	
	$(".order button.unship").click(function() {
		$("header a#pending").addClass("success").delay(1000, function() {
			$("header a#pending").removeClass("success");
		});
		$(this).closest(".order").fadeOut(200);
		orderID = $(this).closest(".order").attr("id");
		$.get("resources/helpers/updateOrder.php?orderID="+orderID+"&status=0");
	});
		
});

$(document).keydown(function(key) {
	// key codes
	escape = 27;
	
	if(key.keyCode == escape) {
		hideEditor();
	}
});

function hideEditor() {
	$("#editor").hide();
	$(".image-wrapper").removeClass("selected");
	window.editorItem = null;
}