<?php

namespace App\Models\Soap;


use Illuminate\Database\Eloquent\Model;

use Artisaninweb\SoapWrapper\Extension\SoapService;


class SOAP_Soap extends SoapService
{

        protected $name = 'servis';

        /**
         * @var string
         */

        protected $wsdl = '/var/www/laravel/public/rs.wsdl';

        /**
         * @var boolean
         */
        protected $trace = true;

        /**
         * Get all the available functions
         *
         * @return mixed
         */
        public function functions()
        {
            return $this->getFunctions();
        }
}
