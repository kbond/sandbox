<?php

namespace Zenstruck\MediaBundle\Media\Alert;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface AlertProviderInterface
{
    public function addAlert($message, $type);
}