<?php

namespace Zenstruck\MediaBundle\Media;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class File
{
    protected $file;

    public function __construct(\SplFileInfo $file)
    {
        $this->file = $file;
    }

    public function __call($method, $args)
    {
        if (!method_exists($this->file, $method)) {
            throw new \Exception(sprintf('Method "%s" does not exist for "%s"', $method, get_class($this->file)));
        }

        return call_user_func_array(array($this->file, $method), $args);
    }

    /**
     * @ref http://jeffreysambells.com/2012/10/25/human-readable-filesize-php
     *
     * @param int $decimals
     *
     * @return string
     */
    public function getHumanFileSize($decimals = 0)
    {
        $bytes = $this->file->getSize();

        $size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f %s", $bytes / pow(1024, $factor), @$size[$factor]);
    }
}