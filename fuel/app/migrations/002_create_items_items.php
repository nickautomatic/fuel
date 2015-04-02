<?php

namespace Fuel\Migrations;

class Create_items_items
{
	public function up()
	{
		\DBUtil::create_table('items_items', array(
			'parent_id' => array('constraint' => 11, 'type' => 'int'),
			'child_id' => array('constraint' => 11, 'type' => 'int'),
			'order' => array('constraint' => 11, 'type' => 'int'),
		));

		// Create primary key based on parent_id / child_id:
		\DB::query(
			'ALTER TABLE `items_items` ADD PRIMARY KEY ( `parent_id` , `child_id` );'
		)->execute();
	}

	public function down()
	{
		\DBUtil::drop_table('items_items');
	}
}