<?php

namespace Stk\Service;

use Stk\Immutable\Map;

class Translation implements Injectable
{
    /**
     * The current active language
     *
     * @var string
     */
    protected string $language = 'en';

    /**
     * the current translations
     *
     * @var Map
     */
    protected Map $translations;

    public function __construct(array $translations)
    {
        $this->translations = new Map($translations);
    }

    /**
     * set the current active language
     *
     * @param string $lang
     *
     * @return static
     */
    public function setLanguage(string $lang)
    {
        $this->language = $lang;

        return $this;
    }

    /**
     * load translations from json files
     *
     * @param array $scandirs
     * @param array $languages
     *
     * @return Translation
     */
    public static function loadFromJson(array $scandirs, array $languages)
    {
        $translations = [];
        foreach ($languages as $lang) {
            if (!isset($translations[$lang])) {
                $translations[$lang] = [];
            }

            foreach ($scandirs as $d) {
                $glob = rtrim($d, '/') . "/*.$lang.json";

                $files = glob($glob);
                if ($files === false) {
                    continue;
                }

                foreach ($files as $file) {
                    $data = @file_get_contents($file);
                    if ($data === false) {
                        continue;
                    }

                    $tr = json_decode($data, true);
                    if ($tr === null) {
                        continue;
                    }
                    $translations = array_merge_recursive($translations, [$lang => $tr]);
                }
            }
        }

        return new self($translations);
    }

    /**
     * @param string $filename
     * @return Translation
     */
    public static function loadFromFile(string $filename)
    {
        $translations = null;
        $data = @file_get_contents($filename);
        if ($data !== false) {
            $translations = json_decode($data, true);
        }

        return new self($translations === null ? [] : $translations);
    }

    /**
     * merge existing translations, return a new clone
     *
     * @param array $translations
     *
     * @return Translation
     */
    public function merge(array $translations)
    {
        $new               = clone $this;
        $new->translations = new Map(array_merge_recursive($this->translations->get(), $translations));

        return $new;
    }

    /**
     * @param mixed ...$path
     *
     * @return mixed|string
     */
    public function get(...$path)
    {
        array_unshift($path, $this->language);

        return call_user_func_array([$this, 'getLang'], $path);
    }

    /**
     * get translation, return path concatenated with dots as string if no translation was found
     *
     * @param string $language
     * @param mixed ...$path
     *
     * @return mixed|string
     */
    public function getLang(string $language, ...$path)
    {
        array_unshift($path, $language);

        $text = $this->translations->getIn($path);
        if ($text === null) {
            return implode('.', $path);
        }

        return $text;
    }

    /**
     * @return array
     */
    public function getTranslations(): array
    {
        return $this->translations->get();
    }
}
