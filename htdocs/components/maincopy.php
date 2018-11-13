				<?php 
				$elt = $page->getElement('main-copy');
				$copy = getElementCopy($page, $elt);
				?>
				<div <?php echo getCMSForm('copy', $elt); ?> class="content-copy">
					<?php echo $copy ? $copy : 'Copy goes here...'; ?>
				</div>