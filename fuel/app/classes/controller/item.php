<?php
class Controller_Item extends Controller_Template
{

	public function action_test()
	{
		// Fetch the parent item to test lazy loading:
		$item_lazy = Model_Item::find_by_title('Parent Item');

		// If the item doesn't exist, create it:
		if (!$item_lazy)
		{
			// Create parent item:
			$item = Model_Item::forge(array('title' => 'Parent Item'));

			// Create two child items:
			$child1 = Model_Item::forge(array('title' => 'Child Item 1'));
			$child2 = Model_Item::forge(array('title' => 'Child Item 2'));

			$child1->save();
			$child2->save();

			// Add the two child items in reverse order (ie. child2 first):
			// (order is set by the 'after_save' method)
			$item->children[] = $child2;
			$item->children[] = $child1;

			$item->save();
		}
		
		// Fetch the parent item again to test eager loading:
		$item_eager = Model_Item::query()
			->related('children')
			//->related('children2') // causes a Fuel error
			->related('children3')
			->where('title', '=', 'Parent Item')
			->get_one();

		// 'children' is the correct method (as per Fuel's documentation),
		// but the results are unsorted:
		echo 'Eager loading - children';
		var_dump($item_eager->children);

		echo 'Lazy loading - children';
		var_dump($item_lazy->children);

		// 'children3' is a mysterious alternative approach that actually
		// now seems to work, but is not in line with the ORM's docs:
		echo 'Eager loading - children3';
		var_dump($item_eager->children3);

		echo 'Lazy loading - children3';
		var_dump($item_lazy->children3);

		exit;
	}

	public function action_index()
	{
		$data['items'] = Model_Item::find('all');
		$this->template->title = "Items";
		$this->template->content = View::forge('item/index', $data);

	}

	public function action_view($id = null)
	{
		is_null($id) and Response::redirect('item');

		if ( ! $data['item'] = Model_Item::find($id))
		{
			Session::set_flash('error', 'Could not find item #'.$id);
			Response::redirect('item');
		}

		$this->template->title = "Item";
		$this->template->content = View::forge('item/view', $data);

	}

	public function action_create()
	{
		if (Input::method() == 'POST')
		{
			$val = Model_Item::validate('create');

			if ($val->run())
			{
				$item = Model_Item::forge(array(
					'title' => Input::post('title'),
				));

				if ($item and $item->save())
				{
					Session::set_flash('success', 'Added item #'.$item->id.'.');

					Response::redirect('item');
				}

				else
				{
					Session::set_flash('error', 'Could not save item.');
				}
			}
			else
			{
				Session::set_flash('error', $val->error());
			}
		}

		$this->template->title = "Items";
		$this->template->content = View::forge('item/create');

	}

	public function action_edit($id = null)
	{
		is_null($id) and Response::redirect('item');

		if ( ! $item = Model_Item::find($id))
		{
			Session::set_flash('error', 'Could not find item #'.$id);
			Response::redirect('item');
		}

		$val = Model_Item::validate('edit');

		if ($val->run())
		{
			$item->title = Input::post('title');

			if ($item->save())
			{
				Session::set_flash('success', 'Updated item #' . $id);

				Response::redirect('item');
			}

			else
			{
				Session::set_flash('error', 'Could not update item #' . $id);
			}
		}

		else
		{
			if (Input::method() == 'POST')
			{
				$item->title = $val->validated('title');

				Session::set_flash('error', $val->error());
			}

			$this->template->set_global('item', $item, false);
		}

		$this->template->title = "Items";
		$this->template->content = View::forge('item/edit');

	}

	public function action_delete($id = null)
	{
		is_null($id) and Response::redirect('item');

		if ($item = Model_Item::find($id))
		{
			$item->delete();

			Session::set_flash('success', 'Deleted item #'.$id);
		}

		else
		{
			Session::set_flash('error', 'Could not delete item #'.$id);
		}

		Response::redirect('item');

	}

}
