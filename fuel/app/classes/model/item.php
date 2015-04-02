<?php
use Orm\Model;

class Model_Item extends Model
{
	protected static $_many_many = array(
		// This should be the correct way of setting 'order_by' on a relation
		// (cf. http://fuelphp.com/docs/packages/orm/relations/many_many.html)
		// 
		// ...it doesn't seem to work, though.
		'children' => array(
			'table_through'    => 'items_items',
			'key_through_from' => 'parent_id',
			'key_through_to'   => 'child_id',
			'model_to'         => 'Model_Item',
			'order_by' => array(
				'items_items.order' => 'ASC'	// define custom through table ordering
			),
		),
		// nb. 	this is a hack: we shouldn't be using 't0_through',
		//		but this allows sorting on lazy-loaded items. It breaks eager-loading, though.
		//		cf. http://stackoverflow.com/q/10788210/180500
		'children2' => array(
			'table_through'    => 'items_items',
			'key_through_from' => 'parent_id',
			'key_through_to'   => 'child_id',
			'model_to'         => 'Model_Item',
			'conditions'			=> array(
				'order_by' 			=> array(
					't0_through.order' => 'ASC' // custom through table ordering
				),
			),
		),
	);

	protected static $_properties = array(
		'id',
		'title',
		'created_at',
		'updated_at',
	);

	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'mysql_timestamp' => false,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_save'),
			'mysql_timestamp' => false,
		),
	);

	public static function validate($factory)
	{
		$val = Validation::forge($factory);
		$val->add_field('title', 'Title', 'required|max_length[255]');

		return $val;
	}

}
