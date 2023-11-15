<?php
/**
 * The below solution for autoloading fits perfect for our needs
 * and the scope of this project.
 */

class WooSupercharge_Autoload {

	/** @var string $class */
	private $class;

	/** @var string $file */
	private $file;

	/**
	 * WooSupercharge_Autoload constructor.
	 *
	 * @param $class
	 */
	public function __construct(
		$class
	) {
		$this->class = $class;
	}

	/**
	 * Build filepath for requested class.
	 */
	public function load() {
		$path       = explode( '_', $this->class );
		$this->file = '';
		$i          = 0;

		if ( count( $path ) > 1 ) {
			array_shift( $path );
		}
		end( $path );

		/**
		 * Build directory path.
		 */
		while ( $i < key( $path ) ) {
			$this->build( $path[ $i ], '', '/' );

			$i++;
		}

		/**
		 * Build filename.
		 */
		$this->build( $path[ $i ], 'class', '.php' );

		return $this->file;
	}

	/**
	 * Checks if $path is written uppercase entirely, otherwise it'll split $path up and build a string glued with
	 * dashes.
	 *
	 * @param        $path
	 * @param string $prefix
	 * @param string $suffix
	 */
	private function build( $path, $prefix = '', $suffix = '/' ) {
		if ( ctype_upper( $path ) ) {
			$this->file .= ( $prefix ? $prefix . '-' : '' ) . strtolower( $path ) . $suffix;
		} else {
			$parts       = preg_split( '/(?=[A-Z])/', lcfirst( $path ) );
			$this->file .= ( $prefix ? $prefix . '-' : '' ) . strtolower( implode( '-', $parts ) ) . $suffix;
		}
	}
}

/**
 * Takes care of loading classes on demand.
 *
 * @param $class
 *
 * @return mixed|void
 */
function woosupercharge_autoloader( $class ) {
	$path = explode( '_', $class );

	if ( $path[0] != 'WooSupercharge' ) {
		return;
	}

	$autoload = new WooSupercharge_Autoload( $class );

	return include WOOSUPERCHARGE_PLUGIN_DIR . 'includes/' . $autoload->load();
}

spl_autoload_register( 'woosupercharge_autoloader' );
