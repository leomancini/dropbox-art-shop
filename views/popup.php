<div class="popup-wrapper">
	<div class="overlay"></div>
	<div class="close"></div>
	<div class="popup">
		<div class="arrow left"></div>
		<div class="arrow right"></div>
		<div class="popup-contents">
			<div class="info<?php if(isset($_COOKIE['gallerymode'])) { echo " center"; } ?>">
				<div class="inner">
					<div class="title"></div>
					<div class="more"></div>
						<?php 
							if(!isset($_COOKIE['gallerymode'])) {
						?>
							<div class="right">
								<div class="sold-indicator">SOLD</div>
								<a href="#" class="buy-button">Purchase</a>
								<div class="price"></div>
							</div>
						<?php
							}
						?>
				</div>
			</div>
			<div class="image-wrapper">
				<div class="loading-overlay"></div>
				<img class="loading">
			</div>
		</div>
	</div>
</div>