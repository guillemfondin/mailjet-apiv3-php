<?php
/**
 * MailJet Api
 *
 * Copyright (c) 2013, Mailjet.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 *     * Redistributions of source code must retain the above copyright notice,
 *       this list of conditions and the following disclaimer.
 *
 *     * Redistributions in binary form must reproduce the above copyright notice,
 *       this list of conditions and the following disclaimer in the documentation
 *       and/or other materials provided with the distribution.
 *
 *     * Neither the name of Zend Technologies USA, Inc. nor the names of its
 *       contributors may be used to endorse or promote products derived from this
 *       software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace Mailjet\Hydrator\Strategy;

use Mailjet\Model;
use Zend\Stdlib\Hydrator\Strategy\ClosureStrategy;
use Mailjet\Api\AbstractApi;
use Mailjet\Model\ModelInterface;

class ModelStrategy extends ClosureStrategy
{
    /**
     /* Constructor
     *
     * @param AbstractApi $dataMapper
     * @param string      $apiName    Api name providing data
     */
    public function __construct(AbstractApi $dataMapper, $apiName)
    {
        $extractFunc = function ($value) use ($dataMapper, $apiName) {
            $api = $dataMapper->api($apiName);
            if ($value instanceof ModelInterface) {
                $hydrator = $api->getHydrator();
                $value = $hydrator->extract($value);

                return $value;
            }

            return $value;
        };

        $hydrateFunc = function ($value) use ($dataMapper, $apiName) {
            $api = $dataMapper->api($apiName);
            $hydrator = $api->getHydrator();
            $objectPrototype = $api->getResultSetPrototype()->getObjectPrototype();
            if (is_array($value)) {
                return $hydrator->hydrate($value, clone $objectPrototype);
            } elseif (is_int($value)) {
                return $api->lazyLoadModelClosure($apiName, $value);
            }

            return $value;
        };

        parent::__construct($extractFunc, $hydrateFunc);
    }

}
