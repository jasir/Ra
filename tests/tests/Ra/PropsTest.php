<?php
namespace Ra;

class PropsTest extends \BaseTestCase
{

	public function testPropsCreation()
	{
		$props = new Props([
			'prop1' => 1,
			'prop2' => 2,
		]);

		$this->assertEquals(['prop1', 'prop2'], $props->keys());

		//default state is immutable
		$this->assertFalse($props->immutable());

		//accessing properties
		$this->assertEquals(1, $props->prop1);
		$this->assertEquals(1, $props->get('prop1'));

		$this->assertException(function () use ($props) {
			$props->nogood;
		}, 'Ra\PropNotFoundException');

		$props->computed('sum', function ($props) {
			return $props->prop1 + $props->prop2;
		});

		$this->assertEquals(3, $props->sum);
		$this->assertEquals(3, $props->get('sum'));


		// setting props

		$props->newProp = 4;
		$this->assertEquals(4, $props->newProp);

		$this->assertException(function () use ($props) {
			$props->newProp = 5;
		}, 'Ra\PropExistsException');

		$this->assertTrue($props->immutable(true));

		// when in immutable, throws
		$this->assertException(function () use ($props) {
			$props->moreProperty = 5;
		}, 'Ra\PropsImmutableException');


		$this->assertFalse($props->immutable(false));
		$this->assertFalse($props->immutable());


		//$prop->keys - keys are sorted
		$this->assertEquals(['prop1', 'prop2', 'newProp', 'sum'], $props->keys());
	}


	public function testCreate()
	{
		$props = new Props([
			'a' => 5,
			'b' => 1000,
			'c' => 'c',
		]);

		$props->computed('sum', function ($props) {
			return $props->a + $props->b;
		});
		$this->assertEquals(['a', 'b', 'c', 'sum'], $props->keys());

		$clone1 = $props->create(['a', 'sum'], ['extra' => 'extra_value']);
		$this->assertFalse($clone1->immutable());

		$this->assertEquals(['extra', 'a', 'sum'], $clone1->keys());

		$clone1->computed('b', function ($props) {
			return $props->a * 100;
		});


		$this->assertEquals(['extra', 'a', 'sum', 'b'], $clone1->keys());

		$this->assertEquals(500, $clone1->b);
		$this->assertEquals(505, $clone1->sum);

		$this->assertException(function () use ($props) {
			$props->create(['a', 'b'], ['a' => 3]);
		}, 'Ra\PropExistsException');
	}

	public function testCall()
	{
		$props = new Props();
		$props->callme = function($a, $b) {
			//dump($this);
			return $a + $b;
		};

		$this->assertEquals(5, $props->callme(2, 3));
	}

}