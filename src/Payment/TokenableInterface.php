<?php
/**
 * @author: adavydov
 * @since: 10.11.2020
 */

namespace App\Payment;


interface TokenableInterface
{
    function getToken(): string;
}