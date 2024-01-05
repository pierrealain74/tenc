<?php

namespace MyListing\Utils;

/**
 * Generate a random unique string.
 *
 * @link http://stackoverflow.com/a/13733588/1056679
 * @link https://gist.github.com/raveren/5555297
 */
class Random_Id {
    protected static
        $pool,
        $pool_length,
        $pools = [
            'alnum' => '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
            'alpha' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
            'hexdec' => '0123456789abcdef',
            'numeric' => '0123456789',
        ];

    /**
     * Set the list of characters to be used in the generated string.
     *
     * @param string $type
     */
    public static function set_pool( $type = 'alnum' ) {
        if ( in_array( $type, array_keys( self::$pools ) ) ) {
            self::$pool = self::$pools[ $type ];
        } elseif ( strlen( $type ) ) {
            self::$pool = $type;
        } else {
            self::$pool = self::$pools[ 'alnum' ];
        }

        self::$pool_length = strlen( self::$pool );
    }

    /**
     * Generate random string.
     *
     * @param  int $length
     * @return string
     */
    public static function generate( $length = 12, $pool = 'alnum' ) {
        self::set_pool( $pool );

        $token = '';
        for ( $i = 0; $i < $length; $i++ ) {
            $random_key = self::get_random_integer( 0, self::$pool_length );
            $token .= self::$pool[ $random_key ];
        }

        return $token;
    }

    /**
     * Generate a random number.
     *
     * @param  int $min
     * @param  int $max
     * @return int
     */
    protected static function get_random_integer( $min, $max ) {
        $range = ($max - $min);
        if ($range < 0) {
            // Not so random...
            return $min;
        }

        $log = log($range, 2);

        // Length in bytes.
        $bytes = (int) ($log / 8) + 1;

        // Length in bits.
        $bits = (int) $log + 1;

        // Set all lower bits to 1.
        $filter = (int) (1 << $bits) - 1;

        do {
            $rnd = hexdec( bin2hex( openssl_random_pseudo_bytes( $bytes ) ) );

            // Discard irrelevant bits.
            $rnd = $rnd & $filter;
        } while ( $rnd >= $range );

        return ($min + $rnd);
    }
}