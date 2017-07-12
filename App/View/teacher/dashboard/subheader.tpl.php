<header class="sub_header container-fluid">
	<i class="<?php echo $subheader['icon']?> fa-3x fa-fw"></i>
	<div class="inline-block">
		<h3><?php echo $subheader['title']; ?></h3>
		<p>Description</p>
	</div>

	<?php
		if(!empty($subheader['items'])):
	?>
	<ul class="nav nav-tabs" role="tablist">
		<?php
			foreach($subheader['items'] as $key => $value):
				echo "<li role='presentation' class='".$value['active']."'>
					<a href='#' data-link='".$value['link']."' aria-controls='' role='tab' data-toggle='tab'>
					".$value['title']."
					</a>
				</li>";
			endforeach;
		?>
	</ul>

	<?php
		endif;
	?>
</header>