<?php
namespace FFMpegThumbnailer;

/**
 * PHP class warpper to ffmpegthumbnailer utility
 *
 * @version 0.1 (november 2011)
 * @author George Mihailov <mihailoff@gmail.com>
 */
class FFMpegThumbnailer
{
	/**
	 * Path to ffmpegthumbnailer utility
	 * You need to be careful with $PATH, in some cases it is need the full path
	 * 
	 * @var string
	 */
	protected $bin = 'ffmpegthumbnailer';
	
	/**
	 * Options container
	 * 
	 * @var \stdClass
	 */
	protected $options;
	
	/**
	 *
	 * @param string $input_filename Input video filename
	 * @param string $bin Path to ffmpegthumbnailer utility
	 */
	public function __construct( $input_filename, $bin = 'ffmpegthumbnailer' )
	{
		$this->bin = $bin;
		$this->options = new \stdClass;
		$this->options->i = $input_filename;
	}
	
	/**
	 * Input video filename
	 * 
	 * @param string $filename
	 * @return FFMpegThumbnailer Fluent interface
	 */
	public function setInput( $filename )
	{
		$this->options->i = $filename;
		return $this;
	}
	
	/**
	 * Output filename of the generated image file (filename ending with .jpg or .jpeg will be in jpeg format, otherwise png is used)
	 * 
	 * @param string $filename
	 * @return FFMpegThumbnailer Fluent interface
	 */
	public function setOutput( $filename )
	{
		$this->options->o = $filename;
		return $this;
	}
	
	/**
	 * Size of the generated thumbnail in pixels (use 0 for original size) (default value: 128)
	 * 
	 * @param integer $size 
	 * @return FFMpegThumbnailer Fluent interface
	 * @throws \InvalidArgumentException
	 */
	public function setOutputSize( $size )
	{
		if ( !is_int( $size ) or $size < 0)
			throw new \InvalidArgumentException( 'Given value ['.$size.'] of size option is incorrect');
		
		$this->options->s = $size;
		return $this;
	}
	
	/**
	 *
	 * @return FFMpegThumbnailer Fluent interface
	 * @throws \InvalidArgumentException
	 */
	public function setOutputOriginalSize()
	{
		return $this->setOutputSize( 0 );
	}
	
	/**
	 * Time to seek to (percentage or absolute time hh:mm:ss) (default: 10)
	 * 
	 * @param integer|string $time
	 * @return FFMpegThumbnailer Fluent interface
	 * @throws \InvalidArgumentException
	 */
	public function setSeekTime( $time )
	{
		if ( is_int( $time ) && ( $time < 0 or $time > 100 ) )
			throw new \InvalidArgumentException( "Invalid percentage [{$time}] for seek time" );
		elseif ( !is_int( $time ) && !preg_match( '#^\d\d:\d\d:\d\d$#', $time ) )
			  throw new \InvalidArgumentException( "Invalid time value [{$time}], must be in hh:mm:ss format" );
		
		$this->options->t = $time;
		return $this;
	}
	
	/**
	 * Image quality (0 = bad, 10 = best) (default: 8) only applies to jpeg output
	 * 
	 * @param integer $quality
	 * @return FFMpegThumbnailer Fluent interface
	 * @throws \InvalidArgumentException
	 */
	public function setQuality( $quality )
	{
		if ( !is_int( $quality ) or $quality < 0 or $quality > 10 )
			throw new \InvalidArgumentException( "Invalid quality value [{$quality}], must be between 0..10" );
		
		$this->options->q = $quality;
		return $this;
	}
	
	/**
	 * Override image format (jpeg or png) (default: determined by filename)
	 * 
	 * @param string $format
	 * @return FFMpegThumbnailer Fluent interface
	 * @throws \InvalidArgumentException
	 */
	public function setImageFormat( $format )
	{
		$format = strtolower( $format );
		if ( $format != 'png' && $format != 'jpeg' )
			throw new \InvalidArgumentException( "Invalid image format [{$format}], must be 'jpeg' or 'png'" );
			
		$this->options->c = $format;
		return $this;
	}
	
	/**
	 * Ignore aspect ratio and generate square thumbnail
	 * 
	 * @param boolean $flag
	 * @return FFMpegThumbnailer Fluent interface
	 */
	public function setIgnoreAspectRatio( $flag )
	{	
		$this->options->a = (int)(bool)$flag;
		return $this;
	}
	
	/**
	 * Workaround some issues in older versions of ffmpeg (only use if you experience problems like 100% cpu usage on certain files)
	 * 
	 * @param boolean $flag
	 * @return FFMpegThumbnailer Fluent interface
	 */
	public function setCompatabilityMode( $flag )
	{	
		$this->options->w = (int)(bool)$flag;
		return $this;
	}
	
	public function run()
	{
		return $this->execute( $this->buildCmd() );
	}
	
	/**
	 *
	 * @return string 
	 * @throws \LogicException
	 */
	public function buildCmd()
	{
		if ( !property_exists( $this->options, 'o' ) )
			throw new \LogicException ( 'Output file must be defined' );
		
		$cmd = $this->bin;
		foreach ( get_object_vars( $this->options ) as $cmd_arg => $cmd_arg_val )
			$cmd .= ' -'.$cmd_arg.' '.escapeshellarg ($cmd_arg_val);
		
		return $cmd;
	}
	
	/**
	 *
	 * @param string $cmd
	 * @return string The last line of the command output
	 * @throws \RuntimeException
	 */
	protected function execute( $cmd )
	{
		$status = system( $cmd, $exec_status ) ;
		
		if ( false === $status )
			throw new \RuntimeException ( 'Unable to execute command ['.$cmd.']' );
		elseif ( 0 !== $exec_status )
			throw new \RuntimeException( 'Executed command ['.$cmd.'] returned status code ['.$exec_status.'], last output - '.$status );
		
		return $status;
	}
	
	public function __clone()
	{
		unset( $this->options->o );
	}
}