<?php
namespace FFMpegThumbnailer;

/**
 * Description of Batch
 *
 * @author George Mihailov <mihailoff@gmail.com>
 */
class Batch extends FFMpegThumbnailer
{
		
	/**
	 * Other cmds to make one batch execution
	 * 
	 * @var \SplFixedArray
	 */
	protected $attaches;
	
	public function __construct( array $list = array() )
	{
		array_map( function ($item) { 
			if ( !($item instanceof FFMpegThumbnailer)) 
				throw new \InvalidArgumentException( 'Excpecting list of FFMpegThumbnailer\FFMpegThumbnailer objects only' );
		}, $list);
		$this->attaches = \SplFixedArray::fromArray( $list, false );
	}
	
	/**
	 * Input video filename
	 * 
	 * @param string $filename
	 * @return FFMpegThumbnailer Fluent interface
	 */
	public function setInput( $filename )
	{
		foreach( $this->attaches as $ffmpegthumbnailer )
			$ffmpegthumbnailer->setInput( $filename );
		return $this;
	}
	
	public function setOutput( $filename )
	{
		throw new \LogicException( 'Cant set same output to different thumbnails' );
	}
	
	public function setOutputSize( $size )
	{
		foreach( $this->attaches as $ffmpegthumbnailer )
			$ffmpegthumbnailer->setOutputSize( $size );
		return $this;
	}
	
	public function setOutputOriginalSize()
	{
		foreach( $this->attaches as $ffmpegthumbnailer )
			$ffmpegthumbnailer->setOutputSize( 0 );
		return $this;
	}
	
	public function setSeekTime( $time )
	{
		foreach( $this->attaches as $ffmpegthumbnailer )
			$ffmpegthumbnailer->setSeekTime( $time );
		return $this;
	}
	
	public function setQuality( $quality )
	{
		foreach( $this->attaches as $ffmpegthumbnailer )
			$ffmpegthumbnailer->setQuality( $quality );
		return $this;
	}
	
	public function setImageFormat( $format )
	{
		foreach( $this->attaches as $ffmpegthumbnailer )
			$ffmpegthumbnailer->setImageFormat( $format );
		return $this;
	}

	public function setIgnoreAspectRatio( $flag )
	{	
		foreach( $this->attaches as $ffmpegthumbnailer )
			$ffmpegthumbnailer->setIgnoreAspectRatio( $flag );
		return $this;
	}
	
	public function setCompatabilityMode( $flag )
	{	
		foreach( $this->attaches as $ffmpegthumbnailer )
			$ffmpegthumbnailer->setCompatabilityMode( $flag );
		return $this;
	}
	
	/**
	 * Attach other object to be executed in batch mode (make only one system call e.g. shell>cmd && cmd1 && cmdx)
	 * 
	 * @param FFMpegThumbnailer $fft
	 * @return FFMpegThumbnailer Fluent interface
	 */
	public function attach( FFMpegThumbnailer $fft )
	{
		$size = $this->attaches->getSize();
		$this->attaches->setSize( ++$size );
		$this->attaches[--$size] = $fft;
		return $this;
	}
	
	public function buildCmd()
	{
		$cmd_arr = array();
		foreach( $this->attaches as $ffmpegthumbnailer )
			$cmd_arr[] = $ffmpegthumbnailer->buildCmd();
		
		return (implode( ' && ', $cmd_arr));
	}
}