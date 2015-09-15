<?php
namespace Ra;

class Dependency1
{
}

class Dependency2
{
	public $lorem = "Lorem ipsum...";
}

class SearchManagerControl extends BaseControl
{
	/** @var Dependency1 @prop(name=dependency1)*/
	public $dep;

	/** @var  Dependency2 @prop */
	public $dependency2;

}


class SearchFormControl extends BaseControl
{
	/** @var Dependency2 @prop */
	private $dependency2;


}

class DescriptionFormControl extends BaseControl
{

}


class RaControlTest extends \BaseTestCase
{
	public function testUsage()
	{
		/*
		 * Komponenty
		 * SearchManager
		 *    SearchForm
		 *    SearchResult
		 *    SearchSettings
		 */


		//Defines props. Components has to decide, if it will use it (default)
		//or will use their oru value

		$dependency1 = new Dependency1();
		$dependency2 = new Dependency2();

		$propsSearchManager = new Props([
			'searchForm.PlaceHolder' => 'Default placeholder',
			'searchResult.ActiveColor' => '#red',
			'searchSettings.ShowGlobal' => false,
			'dependency1' => $dependency1,
			'dependency2' => $dependency2,
			'renderMethod' => function () {
				echo "Hello world";
			},
			'compontentFactory' => function($name, $props	) {
				switch($name) {
					case 'search':
						return new SearchFormControl($this->props);
				}

			}
		]);

		//$control = new SearchManagerControl($propsSearchManager)

		//In search manager, who builds 3 components, search manager creates
		//


		// in SearchManager, creating SearchForm

		$searchFormProps = $propsSearchManager->create([
			'searchForm.PlaceHolder',
		]);
		$searchFormProps->{"searchForm.ShowDescription"} = true;


		$searchControl = new SearchManagerControl($propsSearchManager);

		$this->assertSame($dependency1, $searchControl->dep);

	}
}

