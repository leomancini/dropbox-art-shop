function popup(mode, itemID) {
	artPath = "/art/";

	title = $(window.popupClass + " .info .title");
	description = $(window.popupClass + " .info .more");
	price = $(window.popupClass + " .info .right .price");
	buy_button = $(window.popupClass + " .info .right .buy-button");
	sold_indicator = $(window.popupClass + " .info .right .sold-indicator");
	window.image = $(window.popupClass + " .image-wrapper img");
	
	console.log(itemID);
	
	// console.log(window.image);
	
	if(mode == "show" || mode == "switch" || mode == "permalink") {
		
		window.popupState = 1;
		
		$(".spinner").show();
		$(".popup-wrapper").children().addClass("visible");
		
		if(mode == "switch" || mode == "permalink") {
			$('html, body').scrollTop($(".image-wrapper[data-itemID='"+itemID+"']").offset().top - 200);
		}
		
		thumbnail = $("a.image-wrapper[data-itemid='"+itemID+"'] img");
		
		window.image.attr("src", thumbnail.attr("src")).addClass("loading");

		// console.log("window = " + window.image.attr("src"));
		
		if(mode == "show") {
			$(".popup-wrapper .close").show();
			$(".arrow").hide();
		}
		
		title.hide();
		description.hide();
		price.hide();
		buy_button.hide();
		sold_indicator.hide();
		
		$(".popup-wrapper").show();
		
		// $("body").delay(5000, function() {
			$.get("/resources/helpers/get-item-info.php?itemID="+itemID, function(data) {		
				json = jQuery.parseJSON(data)[0];
				
				console.log(json.nextItemID);

				/*
				// preload image to get full size
				$("<img/>").attr("src", artPath + json.collectionTitle + "/full_" + json.title)
				.load(function() {
						window.popupImageHeight = this.height;
						window.popupImageWidth = this.width;
						setPopupImageScale();
				});
				*/
			
				if(json.title != "") {
					title.text(json.title).show();
				} else {
					title.text("Untitled #" + itemID.replace(/^[0]+/g,"")).show();
				}
				
				if(json.description != "")  { description.text(json.description).show(); } else { description.empty(); }
				
				if(json.for_sale == "1") {
					if(json.availability == "0")  {
						buy_button.hide();
						sold_indicator.show();
					} else {
						if(json.price != "")  { price.text("$"+json.price).show(); } else { price.empty(); }
						buy_button.show();
						sold_indicator.hide();
					}
				} else {
					buy_button.hide();
					sold_indicator.hide();
				}
				
				buy_button.attr("href", "/"+json.collectionID+"/"+itemID+"/buy");
				
				// if(json.description == "" && json.for_sale == 0)  { title.addClass("centered"); }
				
				window.image.attr("src", artPath + json.collectionTitlePath + "/full_" + json.filename);
				
				$(window.image).one("load", function() {
					window.image.show().removeClass("loading");
					$(".spinner").hide();
				}).each(function() {
				  if(this.complete) $(this).load();
				});

				$(".arrow").show();
			
				window.thisItemID = itemID;
				window.prevItemID = json.prevItemID;
				window.nextItemID = json.nextItemID;
		
				history.pushState(null, null, "/"+json.collectionID+"/"+itemID);	
			});	
	// });
		
	} else if(mode == "hide") {
		window.popupState = 0;
		
		$(window.popupClass + " .image-wrapper img").attr("src", "");

		$(".popup-wrapper .close").hide();
		
		$(".popup-wrapper").children().removeClass("visible").delay(100, function() {
			$(".popup-wrapper").hide();
		});
		
		if($(".collection.wrapper").hasClass("collection-permalink")) {
			history.pushState(null, null, "/"+$(".collection.wrapper").data("collection-id"));
		} else {
			history.pushState(null, null, "/");
		}
		
		window.image.addClass("loading");
		
	}
	
	mode = null;
	
}

function setPopupImageScale() {
	if($(window).width() >= 1300) {
		window.popupscale = 0;
	} else {
		window.popupscale = 150;
	}

	if($(window).height() > window.popupImageHeight + 350) {
		$(".popup-wrapper .popup").css("top", ($(window).height()*.15));
	} else {
		$(".popup-wrapper .popup").css("top", "");
	}
		
	$(window.image)
		.css("max-height", window.popupImageHeight - window.popupscale)
		.css("max-width", window.popupImageWidth - window.popupscale);
	$(window.popupClass).css("max-height", window.popupImageHeight - window.popupscale);
	
	if($(window).height() > 700) {
		$(".popup").css("top", ($(window).height() - $(".popup").height())/2);
	}
}

function getPlaceholderForInput(input) {
	return input.parent().children(".placeholder[data-for-input='"+input.attr("id")+"']");
}

function validateForm() {
	var basicinfo;
	var address;
	
	if(
		$("input#firstname").val() != "" &&
		$("input#lastname").val() != "" &&
		$("input#email").val() != ""
	) {
		basicinfo = true;
	} else {		
		$(".error#basicinfo").show().text("Please fill in your full name and email address.");
		toggleForm("enable");
		basicinfo = false;
	}
	
	if(
		$("input#address1").val() != "" &&
		$("input#zip").val() != "" &&
		$("input#city").val() != "" &&
		$("input#state").val() != ""
	) {
		address = true;
	} else {
		$(".error#address").show().text("Please fill in your shipping address.");
		toggleForm("enable");
		address = false;
	}
	
	if(address == true && basicinfo == true) {
		return true;
	} else {
		return false;
	}
}

function toggleForm(mode) {
	if(mode == "enable") {
		$("form #loading").hide();
		spinner.stop($("form #submit-wrapper").get(0));
		$("form input[type=submit]").val("Purchase").removeClass("active");
	} else if(mode == "disable") {
		$("form #loading").fadeIn(200);
		spinner.spin($("form #submit-wrapper").get(0));
		$("form input[type=submit]").val("").addClass("active");
	}
}

var spinner_config = {
  lines: 11, // The number of lines to draw
  length: 5, // The length of each line
  width: 2, // The line thickness
  radius: 7, // The radius of the inner circle
  corners: 1, // Corner roundness (0..1)
  rotate: 0, // The rotation offset
  direction: 1, // 1: clockwise, -1: counterclockwise
  color: '#000', // #rgb or #rrggbb
  speed: 1, // Rounds per second
  trail: 60, // Afterglow percentage
  shadow: false, // Whether to render a shadow
  hwaccel: true, // Whether to use hardware acceleration
  className: 'spinner', // The CSS class to assign to the spinner
  zIndex: 2e9 // The z-index (defaults to 2000000000)
};

window.popupClass =	".popup-wrapper .popup";

$(document).ready(function() { 
	setPopupImageScale();

	$(".collection .images .image-wrapper").click(function() {
		itemID = $(this).attr("data-itemID");
		popup("show", itemID);
		setPopupImageScale();
	});
	
	$(".overlay, .popup-wrapper .close").click(function() {
		popup("hide");
	});	
	
	spinner = new Spinner(spinner_config).spin();
	$(window.popupClass + " .image-wrapper").prepend(spinner.el);
	
	$(".arrow").click(function() {
		direction = $(this).attr("class").replace("arrow ", "");
		if(direction == "right") {
			if(window.nextItemID != null) {
				popup("switch", window.nextItemID);
			}
		} else if(direction == "left") {
			if(window.prevItemID != null) {
				popup("switch", window.prevItemID);
			}
		}
	});
	
	$(".navigation a").click(function() {
		if(!$(this).hasClass("external")) {		
			if($(this).hasClass("selected")) {
				$(".generic.navigation.wrapper#"+$(this).attr("id")).addClass("hidden");
		
				$(this).removeClass("selected");
			} else {
		
				$(".generic.navigation.wrapper#"+$(this).attr("id")).removeClass("hidden");
				$(".generic.navigation.wrapper").not("#"+$(this).attr("id")).addClass("hidden");
		
				$(this).addClass("selected");
				$(".navigation a").not($(this)).removeClass("selected");
			}
		
			history.pushState(null, null, "/"+$(this).attr("id"));
		}
	});
	
	$(".generic.navigation.wrapper .close").click(function() {
		$(this).parent().addClass("hidden");
		$(".navigation a").removeClass("selected");
		history.pushState(null, null, "/");
	});
		
	$(document).keydown(function(key) {
		// key codes
		escape = 27;
		leftArrow = 37;
		rightArrow = 39;
		
		if(key.keyCode == escape) {
			if(window.popupState == 1) {
				popup("hide");
			}
		} else if(key.keyCode == leftArrow) {
			if(window.prevItemID != null) {
				popup("switch", window.prevItemID);
			}
		}	else if(key.keyCode == rightArrow) {
			if(window.nextItemID != null) {
				popup("switch", window.nextItemID);
			}
		}	
	});		
	
	if($("form")[0]) {
		
		form = $("form");
		submit = $("form input[type='submit']");

		$("form .input-wrapper .placeholder").click(function() {
			input = $(this).parent().children("#"+$(this).data("for-input"));
			$(this).hide();
			input.focus();
		});

		$("form .input-wrapper input").blur(function() {
			placeholder = getPlaceholderForInput($(this));
			if($(this).val() == "") {
				placeholder.show();
			}
		}).focus(function() {
			placeholder = getPlaceholderForInput($(this));
			placeholder.hide();
		}).keyup(function() {
			if(
				$("input#firstname").val() != "" &&
				$("input#lastname").val() != "" &&
				$("input#email").val() != "" &&
				$("input#address1").val() != "" &&
				$("input#zip").val() != "" &&
				$("input#city").val() != "" &&
				$("input#state").val() != "" &&
				$("input#cardname").val() != "" &&
				$("input#cardnumber").val() != "" &&
				$("input#expirationmonth").val() != "" &&
				$("input#expirationyear").val() != "" &&
				$("input#cvc").val() != ""
			) {
				submit.removeAttr("disabled");
			} else {
				submit.attr("disabled", "disabled");
			}
		});

		$("form .input-wrapper input#firstname, form .input-wrapper input#lastname").bind("blur, keydown, keyup", function() {
			$("input#cardname").val($("input#firstname").val() + " " + $("input#lastname").val());
			if($("input#firstname").val() != "" && $("input#lastname").val() != "") {
				placeholder = getPlaceholderForInput($("input#cardname"));
				placeholder.hide();
			}
		});

		$("form .input-wrapper input#zipcode").bind("blur, keydown, keyup", function() {
			zipCode = $(this).val();
			if(zipCode.length == 5) {
				// get city and state based on zip code
				var zipCodeRequest = new XMLHttpRequest();
				zipCodeRequest.open("GET", "http://zip.elevenbasetwo.com/v2/US/"+zipCode, true);
				zipCodeRequest.onreadystatechange = function() {
					if(zipCodeRequest.readyState == 4) {
						zipCodeResponse = jQuery.parseJSON(zipCodeRequest.responseText);
						if(typeof zipCodeResponse.city != "undefined") {
							$("form .input-wrapper input#city").val(zipCodeResponse.city);
							$("form .input-wrapper").children(".placeholder[data-for-input='city']").hide();
						}
						if(typeof zipCodeResponse.state != "undefined") {
							$("form .input-wrapper input#state").val(zipCodeResponse.state);
							$("form .input-wrapper").children(".placeholder[data-for-input='state']").hide();
						}
					};
				};
				zipCodeRequest.send();
				
				// get tax rate based on zip code and base amount
				baseAmount = $("#price-breakdown #base-amount").text().replace("$", "");
				$.get("/resources/helpers/calculate-tax.php?baseAmount="+baseAmount+"&zipCode="+zipCode, function(taxAmount) {
					$("#price-breakdown #tax-amount").html(taxAmount);

					// re-calculate total
					/*
					preTaxTotal = $("#pre-tax-total-amount").text();
					taxAmount = taxAmount.replace("$", "");
					postTaxTotal = parseFloat(preTaxTotal) + parseFloat(taxAmount);
					postTaxTotal = postTaxTotal.toFixed(2);
					$("#price-breakdown #total-amount").text("$"+postTaxTotal);
					$("input[name=totalAmount]").val(postTaxTotal);
					*/
					
				});
				
			}
		});
	
		$("form .input-wrapper input.numeric").numeric();

		var stripeResponseHandler = function(status, response) {
			if (response.error) {
				// show the errors on the form
				$(".error#payment").show().text(response.error.message + ". ");
				toggleForm("enable");
				submit.prop('disabled', false);
			} else {
				// token contains id, last4, and card type
				var token = response.id;
				// Insert the token into the form so it gets submitted to the server
				form.append($('<input type="hidden" name="stripeToken" />').val(token));
				// and submit
				if(validateForm(form) == true) {
					form.get(0).submit();
				}
			}
		};

		$("form").submit(function(event) {		
	 	   // Disable the submit button to prevent repeated clicks
		   	submit.prop('disabled', true);
			toggleForm("disable");
		    Stripe.createToken(form, stripeResponseHandler);
		    return false;
		});

	}
		
});

$(window).resize(function() {
	// console.log($(window).height());
	setPopupImageScale();
});