<?php

/**
 * Sample test
 *
 */

 class Sample_Test extends WC_REST_Unit_Test_Case {
	/**
	 * Test that installation without permission is unauthorized.
	 */
	public function test_example() {
        $number = 100;
		$this->assertEquals( 100, $number );
	}
 }