<?php
class Controller_Item extends Controller_Template
{

	public function action_test()
	{
		$item = Model_Item::find_by_title('Parent Item');

		if (!$item)
		{
			// Create parent item:
			$item = Model_Item::forge(array('title' => 'Parent Item'));

			// Add two child items:
			$item->children[] = Model_Item::forge(array('title' => 'Child Item 1'));
			$item->children[] = Model_Item::forge(array('title' => 'Child Item 2'));
			
			$item->save();
		}
		
		var_dump($item->children); exit;
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
