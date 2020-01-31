<?php

namespace calderawp\calderaforms\Tests\Integration;

use Caldera_Forms_Transient;

class Caldera_Forms_TransientTest extends TestCase
{
	/**
	 *
	 * @since  1.8.0
	 *
	 * @covers \Caldera_Forms_Transient::set_transient()
	 */
	public function testSet_transient()
	{
		$id = uniqid('cf' );
		$value = 'foo';
		\Caldera_Forms_Transient::set_transient($id,$value,500 );
		$this->assertSame( get_option('cftransdata_' . $id), $value );

	}
	/**
	 *
	 * @since  1.8.0
	 *
	 * @covers \Caldera_Forms_Transient::get_transient()
	 */
	public function testGet_transient()
	{
		$id = uniqid('cf' );
		$value = 'foo';
		\Caldera_Forms_Transient::set_transient($id,$value,500 );
		$this->assertSame( \Caldera_Forms_Transient::get_transient($id), $value );
	}

	/**
	 *
	 * @since  1.8.0
	 *
	 * @covers \Caldera_Forms_Transient::delete_transient()
	 */
	public function testDelete_transient()
	{
		$id = uniqid('cf' );
		$value = 'foo';
		\Caldera_Forms_Transient::set_transient($id,$value,500 );
		\Caldera_Forms_Transient::delete_transient($id );
		$this->assertFalse( get_option('cftransdata_' . $id), $value );
		$this->assertFalse( Caldera_Forms_Transient::get_transient($id) );
	}


	/**
	 *
	 * @since  1.8.0
	 *
	 * @covers \Caldera_Forms_Transient::cron_callback()
	 */
	public function testCron_callback()
	{
		$id = uniqid('cf' );
		$value = 'foo';
		\Caldera_Forms_Transient::set_transient($id,$value,500 );
		\Caldera_Forms_Transient::cron_callback([$id]);
		$this->assertFalse( Caldera_Forms_Transient::get_transient($id) );
	}

	/**
	 *
	 * @since  1.8.0
	 *
	 * @covers \Caldera_Forms_Transient::delete_at_submission_complete()
	 * @covers \Caldera_Forms_Transient::submission_complete()
	 */
	public function testDelete_at_submission_complete()
	{

		$id = uniqid('cf' );
		$value = 'foo';
		$id2 = uniqid('cf' );
		$value2 = 'bar';
		\Caldera_Forms_Transient::set_transient($id,$value,500 );
		\Caldera_Forms_Transient::set_transient($id2,$value2,500 );

		\Caldera_Forms_Transient::delete_at_submission_complete($id);
		\Caldera_Forms_Transient::delete_at_submission_complete($id2);
		\Caldera_Forms_Transient::submission_complete($id);
		$this->assertFalse( Caldera_Forms_Transient::get_transient($id) );
		$this->assertFalse( Caldera_Forms_Transient::get_transient($id2) );
	}

	/**
	 * @since 1.8.0
	 *
	 * @covers Caldera_Forms_Transient::get_all()
	 */
	public function testGetAllWhenThereAreNone()
	{
		$this->assertSame( 0, count( Caldera_Forms_Transient::get_all() ) );
	}

	/**
	 * @since 1.8.0
	 *
	 * @covers Caldera_Forms_Transient::get_all()
	 */
	public function testGetAllWhenThereAreSomeToGet()
	{
		$id = 'r2345';
		$value = [9,0, new \stdClass() ];
		\Caldera_Forms_Transient::set_transient($id,$value,1500 );
		$transients = Caldera_Forms_Transient::get_all();
		$this->assertSame( 1, count( $transients ) );
		$this->assertSame( $transients[0], 'cftransdata_' . $id );
		\Caldera_Forms_Transient::set_transient('cf222',[87],1500 );
		$this->assertSame( 2, count( Caldera_Forms_Transient::get_all() ) );

	}

}


