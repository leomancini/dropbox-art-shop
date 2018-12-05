<?php
	session_start();
	
	// Get the page ID if it's set in GET
	$thisPage = (isset($_GET['page']) ? $_GET['page'] : null);
	
	// Array of real detail pages
	// 		These are pages that are connected to
	// 		a specific item
	// 			i.e. /04/00123/buy
	// 			(where 04 is the collection ID
	// 			and 00123 is the item ID)
	$detailPages = Array("buy", "thanks");
	
	// Array of navigation items	
	$navigation = Array(
		"Artist Statement" => 0, 
		"Email" => "mailto:laura@artoflauramancini.com",
		"Facebook" => "http://facebook.com/artoflauramancini"
	);
?>

<!DOCTYPE HTML>
<html>

	<head>
		<title>Laura Mancini</title>
		<meta name="google-site-verification" content="sSsWnXYSkcjc6i3t3OL2TDlPdn-RMcxerFBUY-SFCNE" />
		<link rel="stylesheet/less" href="/resources/css/style.less">
		<script type="text/javascript" src="/resources/js/less.js"></script>
		<script type="text/javascript" src="/resources/js/jquery.js"></script>
		<script type="text/javascript" src="/resources/js/delay.js"></script>
		<script type="text/javascript" src="/resources/js/spin.js"></script>
		<script type="text/javascript" src="/resources/js/main.js"></script>
		<?php if($thisPage == "buy") { ?>
			<script type="text/javascript" src="/resources/js/validation.js"></script>
			<script type="text/javascript" src="https://js.stripe.com/v1/"></script>
			<script type="text/javascript">
			  Stripe.setPublishableKey('SECRET: STRIPE PUBLISHABLE KEY');
			</script>
		<?php } ?>
		<?php if($thisPage == "buy") { ?>
			<script type="text/javascript">
			var fb_param = {};
			fb_param.pixel_id = 'SECRET: FB PIXEL ID';
			fb_param.value = '0.00';
			fb_param.currency = 'USD';
			(function(){
			  var fpw = document.createElement('script');
			  fpw.async = true;
			  fpw.src = '//connect.facebook.net/en_US/fp.js';
			  var ref = document.getElementsByTagName('script')[0];
			  ref.parentNode.insertBefore(fpw, ref);
			})();
			</script>
			<noscript><img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/offsite_event.php?id=SECRET: FB PIXEL ID&amp;value=0&amp;currency=USD" /></noscript>
		<?php } elseif($thisPage == "thanks") { ?>
			<script type="text/javascript">
			var fb_param = {};
			fb_param.pixel_id = 'SECRET: FB PIXEL ID';
			fb_param.value = '0.00';
			fb_param.currency = 'USD';
			(function(){
			  var fpw = document.createElement('script');
			  fpw.async = true;
			  fpw.src = '//connect.facebook.net/en_US/fp.js';
			  var ref = document.getElementsByTagName('script')[0];
			  ref.parentNode.insertBefore(fpw, ref);
			})();
			</script>
			<noscript><img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/offsite_event.php?id=SECRET: FB PIXEL ID&amp;value=0&amp;currency=USD" /></noscript>
		<? } ?>
	</head>
	<body>
		
		<div id="container">
			<header<?php
				// Only use the condensed title and navigation
				// for the buy and thanks pages
				if($thisPage == "buy" || $thisPage == "thanks") { echo ' class="condensed"'; }
			?>>			
				<a href="/" class="header">
				  <div class="title"></div>
					<h1>Laura Mancini</h1>
					<h3>New York City</h3>
				</a>
				<ul class="navigation">
					<?php
						if($thisPage == "buy" || $thisPage == "thanks") {
					?>
						<li><a class="external" href="/" id="<?php echo $itemID; ?>">back to collections</a></li>
					<?php
						} else {
							// Display navigation items
							foreach($navigation as $item => $action) {
								$itemID = strtolower(str_replace(" ", "-", $item));
					?>
								<li>
									<a
										id="<?php echo $itemID; ?>"
										<?php
											if($_GET['collection'] == $itemID) { echo ' class="selected"'; }
											if($action != "0") {
												$action_components = explode(":", $action);
												$protocol = $action_components[0];
												if($protocol == "http" || $protocol == "https") { $target = "_blank"; }
												if($target) { echo 'target="'.$target.'" '; }
										
												echo 'href="'.$action.'" '.$target.'class="external"';
											}	
										?>
									>
										<?php echo $item; ?>
									</a>
								</li>
					<?php
							}
						}
					?>
				</ul>
			</header>

			<div class="generic wrapper navigation <?php if($_GET['collection'] != "about") { echo "hidden"; } ?>" id="about">
				<div class="close"></div>
				<p>
					Laura is a local Queens artist who graduated from Queens College with a BFA in Painting and a BA in Art History. She completed post-graduate work at the Skowhegan School of Painting and Sculpture, the Art Students League and NYU. She also trained as a Professional Chef at the Natural Gourmet Institute. 
				</p>
				<p>
					Today, she paints in her studio, sews bags and purses, knits hats for the homeless with NY Cares, meditates and makes one-of-a-kind handmade cards as a legally blind person.
				</p>
			</div>
			
			<div class="generic wrapper navigation <?php if($_GET['collection'] != "artist-statement") { echo "hidden"; } ?>" id="artist-statement">
				<div class="close"></div>
				<p>
					Laura is a local Queens artist who graduated from Queens College with a BFA in Painting and a BA in Art History. She completed post-graduate work at the Skowhegan School of Painting and Sculpture, the Art Students League and NYU. She also trained as a Professional Chef at the Natural Gourmet Institute. 
				</p>
				<p>
					Today, she paints in her studio, sews bags and purses, knits hats for the homeless with NY Cares, meditates and makes one-of-a-kind handmade cards as a legally blind person.
				</p>
			</div>
			
			<?php
				// Rendering logic				
				if(isset($_GET['collection']) && $_GET['collection'] != "" && $_GET['collection'] != "index.php" && !isset($navigation[ucwords(str_replace("-", " ", $_GET['collection']))]) && $_GET['collection'] != "work") { // If collection is set and not set to homempage
					
					if($_GET['item'] != "") { // If item is set
						if(isset($thisPage) && in_array($thisPage, $detailPages)) { // if detail page is set
							include("views/item/".$thisPage.".php");
						} else { // If detail page is not set, show item page
							include("views/item.php");
						}	
					} else { // No item is set, fallback to collection
						include("views/collection.php");
					}
				} else { // No collection or item is set, fallback to homepage
					include("views/home.php");
				}
				
				include("views/footer.php");
			?>
			
		</div>
		
		<script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

			ga('create', 'UA-45827407-1', 'artoflauramancini.com');
			ga('send', 'pageview');
		</script>
		
	</body>
</html>