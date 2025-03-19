<?php 
	namespace App\Libraries;

class Cart
{
    public $productIdRules = '\.a-z0-9_-';
    public $productNameRules = '\w \-\.\:';
    public $productNameSafe = true;
    protected $cartContents = [];
    protected $session;

    public function __construct()
    {
        $this->session = session();

        $this->cartContents = $this->session->get('cart_contents');
        if ( $this->cartContents === null ) {
            $this->cartContents = [ 'cart_total' => 0, 'total_items' => 0 ];
        }

        log_message('info', 'Cart Class Initialized');
    }

	public function checkin($ref_code = 'ref_code'): bool
    {
		$this->session->set('ref_code', $ref_code);
		return true;
	}

	public function checkout(): void
    {
        $this->session->remove('ref_code');
    }

	public function ref_code()
    {
        return $this->session->get('ref_code');
    }

    public function insert($items = [], $row_id = false): bool
    {
        if ( ! is_array($items) || count($items) === 0 ) {
            log_message('error', 'The insert method must be passed an array containing data.');
            return false;
        }

        $save_cart = false;
        if ( isset($items[ 'id' ]) ) {
            if ( ( $rowid = $this->_insert($items, $row_id) ) ) {
                $save_cart = true;
            }
        } else {
            foreach ( $items as $val ) {
                if ( is_array($val) && isset($val[ 'id' ]) && $this->_insert($val, $row_id) ) {
                    $save_cart = true;
                }
            }
        }

        if ( $save_cart === true ) {
            $this->saveCart();
            return $rowid ?? true;
        }

        return false;
    }

    protected function _insert($items = [], $row_id = false)
    {
        if ( ! is_array($items) || count($items) === 0 ) {
            log_message('error', 'The insert method must be passed an array containing data.');
            return false;
        }

        if ( ! isset($items[ 'id' ], $items[ 'qty' ], $items[ 'price' ], $items[ 'name' ]) ) {
            log_message('error', 'The cart array must contain a product ID, quantity, price, and name.');
            return false;
        }

        $items[ 'qty' ] = (float)$items[ 'qty' ];

        if ( $items[ 'qty' ] === 0 ) {
            return false;
        }

        if ( ! preg_match('/^[' . $this->productIdRules . ']+$/i', $items[ 'id' ]) ) {
            log_message('error', 'Invalid product ID.  The product ID can only contain alpha-numeric characters, dashes, and underscores');
            return false;
        }

        if ( $this->productNameSafe && ! preg_match('/^[' . $this->productNameRules . ']+$/i' . ( true ? 'u' : '' ), $items[ 'name' ]) ) {
            log_message('error', 'An invalid name was submitted as the product name: ' . $items[ 'name' ] . ' The name can only contain alpha-numeric characters, dashes, underscores, colons, and spaces');
            return false;
        }

        $items[ 'price' ] = (float)$items[ 'price' ];

		if($row_id){
			$rowid = $row_id;
		} else {
			if ( isset($items[ 'options' ]) && count($items[ 'options' ]) > 0 ) {
				$rowid = md5($items[ 'id' ] . serialize($items[ 'options' ]));
			} else {
				$rowid = md5($items[ 'id' ]);
			}
		}

        $old_quantity = isset($this->cartContents[ $rowid ][ 'qty' ]) ? (int)$this->cartContents[ $rowid ][ 'qty' ] : 0;

        $items[ 'rowid' ] = $rowid;
        $items[ 'qty' ] += $old_quantity;
        $this->cartContents[ $rowid ] = $items;

        return $rowid;
    }

    public function update($items = []): bool
    {
        if ( ! is_array($items) || count($items) === 0 ) {
            return false;
        }

        $save_cart = false;
        if ( isset($items[ 'rowid' ]) ) {
            if ( $this->_update($items) === true ) {
                $save_cart = true;
            }
        } else {
            foreach ( $items as $val ) {
                if ( is_array($val) && isset($val[ 'rowid' ]) && $this->_update($val) === true ) {
                    $save_cart = true;
                }
            }
        }

        if ( $save_cart === true ) {
            $this->saveCart();
            return true;
        }

        return false;
    }

    protected function _update($items = []): bool
    {
        if ( ! isset($items[ 'rowid' ], $this->cartContents[ $items[ 'rowid' ] ]) ) {
            return false;
        }

        if ( isset($items[ 'qty' ]) ) {
            $items[ 'qty' ] = (float)$items[ 'qty' ];
            if ( $items[ 'qty' ] === 0 ) {
                unset($this->cartContents[ $items[ 'rowid' ] ]);
                return true;
            }
        }

        $keys = array_intersect(array_keys($this->cartContents[ $items[ 'rowid' ] ]), array_keys($items));
        if ( isset($items[ 'price' ]) ) {
            $items[ 'price' ] = (float)$items[ 'price' ];
        }

        foreach ( array_diff($keys, [ 'id', 'name' ]) as $key ) {
            $this->cartContents[ $items[ 'rowid' ] ][ $key ] = $items[ $key ];
        }

        return true;
    }

    protected function saveCart(): bool
    {
        $this->cartContents[ 'total_items' ] = $this->cartContents[ 'cart_total' ] = 0;
        foreach ( $this->cartContents as $key => $val ) {
            if ( ! is_array($val) || ! isset($val[ 'price' ], $val[ 'qty' ]) ) {
                continue;
            }

            $this->cartContents[ 'cart_total' ] += ( $val[ 'price' ] * $val[ 'qty' ] );
            $this->cartContents[ 'total_items' ] += $val[ 'qty' ];
            $this->cartContents[ $key ][ 'subtotal' ] = ( $this->cartContents[ $key ][ 'price' ] * $this->cartContents[ $key ][ 'qty' ] );
        }

        if ( count($this->cartContents) <= 2 ) {
            $this->session->remove('cart_contents');

            return false;
        }

        $this->session->set('cart_contents', $this->cartContents);

        return true;
    }

    public function total()
    {
        return $this->cartContents[ 'cart_total' ];
    }

    public function remove($rowid): bool
    {
        unset($this->cartContents[ $rowid ]);
        $this->saveCart();
        return true;
    }

    public function totalItems()
    {
        return $this->cartContents[ 'total_items' ];
    }

    public function contents($newest_first = false): array
    {
        $cart = ( $newest_first ) ? array_reverse($this->cartContents) : $this->cartContents;
        unset($cart[ 'total_items' ], $cart[ 'cart_total' ]);

        return $cart;
    }

    public function getItem($row_id)
    {
        return ( in_array($row_id, [ 'total_items', 'cart_total' ], true) OR ! isset($this->cartContents[ $row_id ]) )
            ? false
            : $this->cartContents[ $row_id ];
    }

    public function hasOptions($row_id = ''): bool
    {
        return ( isset($this->cartContents[ $row_id ][ 'options' ]) && count($this->cartContents[ $row_id ][ 'options' ]) !== 0 );
    }

    public function productOptions($row_id = '')
    {
        return $this->cartContents[ $row_id ][ 'options' ] ?? [];
    }

    public function formatNumber($n = ''): string
    {
        return ( $n === '' ) ? '' : number_format((float)$n, 2);
    }

    public function destroy(): void
    {
        $this->cartContents = [ 'cart_total' => 0, 'total_items' => 0 ];
        $this->session->remove('cart_contents');
    }

}