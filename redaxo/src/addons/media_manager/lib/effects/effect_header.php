<?php

/**
 * @package redaxo\media-manager
 */
class rex_effect_header extends rex_effect_abstract
{
    public function execute()
    {
        if ('no_cache' == $this->params['cache']) {
            $this->media->setHeader('Cache-Control', 'must-revalidate, proxy-revalidate, private, no-cache, max-age=0');
            $this->media->setHeader('Expires', 'Sat, 26 Jul 1997 05:00:00 GMT'); // in the past
        } elseif ('cache' !== $this->params['cache'] /* bc */ && 'unspecified' !== $this->params['cache']) {
            $seconds = match ($this->params['cache']) {
                'max-age: 1 min' => 60,
                'max-age: 1 hour' => 60 * 60,
                'max-age: 1 day' => 60 * 60 * 24,
                'max-age: 1 week' => 60 * 60 * 24 * 7,
                'max-age: 1 month' => 60 * 60 * 24 * 30,
                'max-age: 1 year', 'immutable' => 60 * 60 * 24 * 365,
                default => throw new LogicException(sprintf('Unsupported cache duration "%s".', $this->params['cache'])),
            };

            $cacheControl = 'proxy-revalidate, private, max-age='.$seconds;

            if ('immutable' === $this->params['cache']) {
                $cacheControl .= ', immutable';
            }

            $this->media->setHeader('Cache-Control', $cacheControl);
        }

        $disposition = 'download' == $this->params['download'] ? 'attachment' : 'inline';
        $disposition .= '; filename="' . rex_path::basename($this->media->getMediaFilename()) . '"';

        if ('originalname' == $this->params['filename']) {
            $disposition .= "; filename*=utf-8''" . rawurldecode(rex_media::get($this->media->getMediaFilename())->getOriginalFileName());
        }

        if ('noindex' === ($this->params['index'] ?? null)) {
            $this->media->setHeader('X-Robots-Tag', 'noindex');
        }

        $this->media->setHeader('Content-Disposition', $disposition);

        /*
         header("Pragma: public"); // required
         header("Expires: 0");
         header("Content-Transfer-Encoding: binary");
         header("Content-Length: ".$fsize);
         */
    }

    public function getName()
    {
        return rex_i18n::msg('media_manager_effect_header');
    }

    public function getParams()
    {
        return [
            [
                'label' => rex_i18n::msg('media_manager_effect_header_download'),
                'name' => 'download',
                'type' => 'select',
                'options' => ['open_media', 'download'],
                'default' => 'open_media',
            ],
            [
                'label' => rex_i18n::msg('media_manager_effect_header_cache'),
                'name' => 'cache',
                'type' => 'select',
                'options' => ['no_cache', 'unspecified', 'max-age: 1 min', 'max-age: 1 hour', 'max-age: 1 day', 'max-age: 1 week', 'max-age: 1 month', 'max-age: 1 year', 'immutable'],
                'default' => 'no_cache',
            ],
            [
                'label' => rex_i18n::msg('media_manager_effect_header_filename'),
                'name' => 'filename',
                'type' => 'select',
                'options' => ['filename', 'originalname'],
                'default' => 'filename',
                'notice' => rex_i18n::msg('media_manager_effect_header_filename_notice'),
            ],
            [
                'label' => rex_i18n::msg('media_manager_effect_header_index'),
                'name' => 'index',
                'type' => 'select',
                'options' => ['index', 'noindex'],
                'default' => 'index',
                'notice' => rex_i18n::msg('media_manager_effect_header_index_notice'),
            ],
        ];
    }
}
