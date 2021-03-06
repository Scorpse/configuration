<?php

namespace Supervisor\Configuration\Loader;

use Indigo\Ini\Exception\ParserException;
use League\Flysystem\Filesystem;
use Supervisor\Configuration\Configuration;
use Supervisor\Configuration\Exception\LoaderException;

/**
 * Read a file from any filesystem and parse it as INI string.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
final class IniFileLoader extends AbstractLoader
{
    /**
     * @var string
     */
    protected $file;

    /**
     * @param string $file
     */
    public function __construct(string $file)
    {
        $this->file = $file;
    }

    /**
     * {@inheritdoc}
     */
    public function load(Configuration $configuration = null): Configuration
    {
        if (!file_exists($this->file)) {
            throw new LoaderException(sprintf('File "%s" not found', $this->file));
        }

        if (!$fileContents = file_get_contents($this->file)) {
            throw new LoaderException(sprintf('Reading file "%s" failed', $this->file));
        }

        try {
            $ini = $this->getParser()->parse($fileContents);
        } catch (ParserException $e) {
            throw new LoaderException('Cannot parse INI', 0, $e);
        }

        return $this->parseSections($ini, $configuration);
    }
}
