<h2>Viewing <span class='muted'>#<?php echo $item->id; ?></span></h2>

<p>
	<strong>Title:</strong>
	<?php echo $item->title; ?></p>

<?php echo Html::anchor('item/edit/'.$item->id, 'Edit'); ?> |
<?php echo Html::anchor('item', 'Back'); ?>