<?php

require_once __DIR__.'/../src/FFMpegThumbnailer/FFMpegThumbnailer.php';
require_once __DIR__.'/../src/FFMpegThumbnailer/Batch.php';

/**
 * UnitTests of FFMpegThumbnailer\Batch PHP class
 *
 * @author George Mihailov <mihailoff@gmail.com>
 */
class BatchTest extends PHPUnit_Framework_TestCase
{
	function testConstructor()
	{
		$att1 = new FFMpegThumbnailer\FFMpegThumbnailer( 'xfoo' );
		$att1->setOutput( 'xbar' );
		$att2 = new FFMpegThumbnailer\FFMpegThumbnailer( 'zfoo' );
		$att2->setOutput( 'zbar' );
		
		$mock = $this->getMock( 'FFMpegThumbnailer\\Batch', array('execute'), array( array($att1, $att2) ) );
		
		$mock ->expects($this->once())
				->method('execute')
				->with($this->equalTo( "ffmpegthumbnailer -i 'xfoo' -o 'xbar' && ffmpegthumbnailer -i 'zfoo' -o 'zbar'") );
		
		$mock->run();
	}
	
	function testAttach()
	{
		$mock = $this->getMock( 'FFMpegThumbnailer\\Batch', array('execute') );
		
		$att1 = new FFMpegThumbnailer\FFMpegThumbnailer( 'xfoo' );
		$att1->setOutput( 'xbar' );
		$att2 = new FFMpegThumbnailer\FFMpegThumbnailer( 'zfoo' );
		$att2->setOutput( 'zbar' );
		
		$mock->attach( $att1 )
			  ->attach( $att2 );
		
		$mock ->expects($this->once())
				->method('execute')
				->with($this->equalTo( "ffmpegthumbnailer -i 'xfoo' -o 'xbar' && ffmpegthumbnailer -i 'zfoo' -o 'zbar'") );
		
		$mock->run();
	}

//	
//	function testDeattachObjectsOnClone()
//	{
//		$mock = $this->getMock( 'FFMpegThumbnailer', array('execute'), array('foo') );
//		$mock->setOutput( 'bar' );
//		
//		$att1 = new FFMpegThumbnailer( 'xfoo' );
//		$att1->setOutput( 'xbar' );
//		$att2 = new FFMpegThumbnailer( 'zfoo' );
//		$att2->setOutput( 'zbar' );
//		
//		$mock->setOutputSize( 200 )
//			  ->attach( $att1 )
//			  ->attach( $att2 );
//		
//		$mock2 = clone $mock;
//		$mock2->setOutput( 'bar' );
//		
//		$mock2->expects($this->once())
//				->method('execute')
//				->with($this->equalTo( "ffmpegthumbnailer -i 'foo' -s '200' -o 'bar'") );
//		
//		$mock2->run();
//	}
	
}