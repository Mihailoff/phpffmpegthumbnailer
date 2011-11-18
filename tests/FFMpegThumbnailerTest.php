<?php

require_once __DIR__.'/../src/FFMpegThumbnailer/FFMpegThumbnailer.php';

/**
 * UnitTests of FFMpegThumbnailer\FFMpegThumbnailer PHP class
 *
 * @author George Mihailov <mihailoff@gmail.com>
 */
class FFMpegThumbnailerTest extends PHPUnit_Framework_TestCase
{	
	/**
	* @expectedException InvalidArgumentException
	* @dataProvider provideIncorrectOutputSizes
	*/
	function testSetOutputSize( $size )
	{
		$fft = new FFMpegThumbnailer\FFMpegThumbnailer( 'foo' );
		$fft->setOutputSize( $size );
	}
	
	function provideIncorrectOutputSizes()
	{
		return array(
		    array(''),
		    array('foo'),
		    array(10.25),
		    array(-10)
		);
	}
	
	/**
	* @expectedException InvalidArgumentException
	* @dataProvider provideIncorrectTime
	*/
	function testSetSeekTime( $time )
	{
		$fft = new FFMpegThumbnailer\FFMpegThumbnailer( 'foo' );
		$fft->setSeekTime($time);
	}
	
	function provideIncorrectTime()
	{
		return array(
		    array( -1 ),
		    array( 101 ),
		    array( '' ),
		    array( 'str' ),
		    array( '11:22' ),
		);
	}
	
	/**
	* @expectedException InvalidArgumentException
	* @dataProvider provideIncorrectQuality
	*/
	function testSetQuality( $quality )
	{
		$fft = new FFMpegThumbnailer\FFMpegThumbnailer( 'foo' );
		$fft->setQuality($quality);
	}
	
	function provideIncorrectQuality()
	{
		return array(
		    array( -1 ),
		    array( 11 ),
		    array( '' ),
		    array( 'str' )
		);
	}
	
	/**
	* @expectedException InvalidArgumentException
	* @dataProvider provideIncorrectImageFormats
	*/
	function testImageFormat( $format )
	{
		$fft = new FFMpegThumbnailer\FFMpegThumbnailer( 'foo' );
		$fft->setImageFormat($format);
	}
	
	function provideIncorrectImageFormats()
	{
		return array(
		    array( 1 ),
		    array( '' ),
		    array( 'str' ),
		    array( 'gif' )
		);
	}
	
	function testSimplestRunCase()
	{
		$mock = $this->getMock( 'FFMpegThumbnailer\FFMpegThumbnailer', array('execute'), array('foo') );
		
		$mock ->expects($this->once())
				->method('execute')
				->with($this->equalTo( "ffmpegthumbnailer -i 'foo' -o 'bar'") );
		
		$mock->setOutput( 'bar' );
		$mock->run();
	}
	
	function testComplexRunCase()
	{
		$mock = $this->getMock( 'FFMpegThumbnailer\FFMpegThumbnailer', array('execute'), array('foo') );
		
		$mock ->expects($this->once())
				->method('execute')
				->with($this->equalTo( "ffmpegthumbnailer -i 'foo' -o 'bar' -s '100' -t '50' -q '2' -c 'jpeg' -a '1' -w '1'") );
		
		$mock->setOutput( 'bar' )
			  ->setOutputSize( 100 )
			  ->setSeekTime( 50 )
			  ->setQuality( 2 )
			  ->setImageFormat( 'jpeg' )
			  ->setIgnoreAspectRatio( 'yes' )
			  ->setCompatabilityMode( 'yes' );
		
		$mock->run();
	}
	
	/**
	 * @expectedException LogicException
	 */
	function testClearOutputOptionOnClone() //COOOC ;)
	{
		$fft = new FFMpegThumbnailer\FFMpegThumbnailer( 'foo' );
		$fft->setOutput( 'bar' );
		
		$fft2 = clone $fft;
		$fft2->run();
	}
}