<?php
/**
* Cakephp Auth Helper
*
* Copyright (c) 2012, M@kSEO (http://makseo.ru)
*
* Licensed under The MIT License
* Redistributions of files must retain the above copyright notice.
*
* @author M@kSEO
* @copyright Copyright (c) 2012, M@kSEO (http://makseo.ru)
* @license http://www.opensource.org/licenses/mit-license.php MIT
*/
    class AuthHelper extends AppHelper
    {
    /**
    * Utilize session helper
    *
    * @var array
    */
        public $helpers = array('Session');
        /**
        * Session key name where current user is stored
        *
        * @var string
        */

        public static $sessionKey = 'Auth.User';
        /**
        * Check whether the user is authorized
        *
        * @return boolean - true if user authorized and false otherwise
        */

        public function loggedIn() {
            return $this->Session->check(self::$sessionKey.'.id');
        }
        /**
        * Return a single field of user record
        *
        * @return int
        */

        /*public function get($key) {
            $data = $this->Session->read(self::$sessionKey.'.'.$key);
            return (isset($data[$key])) ? $data[$key] : false;
        }*/

        public function get($key) {
            $data = $this->Session->read(self::$sessionKey.'.'.$key);
            return $data;
        }

        
        /**
        * Return user id
        *
        * @return int
        */

        public function getUserId() {
            return $this->get('id');
        }
        /**
        * Return user information
        *
        * @return array
        */

        public function user() {
            return $this->Session->read(self::$sessionKey);
        }
    }