<?php
class testOpenGraphControl extends PHPUnit_Framework_TestCase {
	function setUp() {
		parent::setUp();
		\WP_Mock::setUp();
	}

	function tearDown() {
		parent::tearDown();
		\WP_Mock::tearDown();
	}

	function testGetUTMDataExpectsArrayReturned() {
		// arrange
		\WP_Mock::wpFunction(
			'get_post_meta', 
			array(
				'times' => 3, 
				'return' => $this->stringContains('something'),
				)
			);
		// act
		$actual = \CFPB\SimpleOpenGraph::get_utm_data();
		// assert
		$this->assertEquals('web', $actual['medium']);
		$this->assertEquals('consumerfinance.gov', $actual['source']);
	}

	function testGetUTMDataPassedSourceExpectsGivenSourceReturned() {
		// arrange
		\WP_Mock::wpFunction(
			'get_post_meta', 
			array(
				'times' => 3,
				'return' => $this->stringContains('something'),
				)
			);
		$expected = 'twitter';
		//act
		$actual = \CFPB\SimpleOpenGraph::get_utm_data('twitter');
		// assert
		$this->assertEquals($expected, $actual['medium']);
	}

	function testOpenGraphURLExpectsURLFragments() {
		// arrange
		$utm_data = array(
			'campaign' => 'string',
			'term' => 'other',
			'content' => 'utm_string',
		);
		$expected = '&utm_campaign=string&utm_term=other&utm_content=utm_string';
		//act
		$url = \CFPB\SimpleOpenGraph::utm_url($utm_data);

		//assert
		$this->assertEquals($expected, $url);
		
	}

	function testOpenGraphURLGivenFalseValuesExpectsNoURLFragments() {
		// arrange
		$utm_data = array(
			'campaign' => false,
			'term' => false,
			'content' => false,
		);
		$expected = '';

		// act
		$url = \CFPB\SimpleOpenGraph::utm_url($utm_data);

		// assert
		$this->assertEquals($expected, $url);
	}

	function testOpenGraphURLGivenSomeValuesExpectsCorrectURLFragments(){
		// arrange
		$utm_data = array(
			'campaign' => 'campaign string',
			'term' => false,
			'content' => 'something',
		);
		$expected = '&utm_campaign=campaign+string&utm_content=something';

		// act
		$url = \CFPB\SimpleOpenGraph::utm_url($utm_data);

		// assert
		$this->assertEquals($expected, $url);
	}

	function testGetOGDataExpectsArrayReturned() {
		// arrange
		global $post;
		$post = new StdClass();
		$post->ID = $this->greaterThan(1);
		\WP_Mock::wpFunction('get_post_meta', array('times' => 2, 'return' => $this->stringContains('Post meta')));
		$expected = array(
			'image' => $this->stringContains('Post meta'),
			'title' => $this->stringContains('Post meta'),
		);

		// act
		$data = \CFPB\SimpleOpenGraph::get_og_data($post);
		// assert
		$this->assertEquals($expected, $data);
	}

}