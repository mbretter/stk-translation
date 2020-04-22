<?php

namespace StkTest\Service;

require_once __DIR__ . '/stubs.php';

use Stk\Service\Translation;
use PHPUnit\Framework\TestCase;

class TranslationTest extends TestCase
{
    protected Translation $tr;

    public function testLoad()
    {
        $tr = Translation::loadFromJson(['my-translations'], ['de', 'en']);
        $this->assertEquals([
            'de' => [
                'user' => [
                    'validation' => [
                        'username_required' => 'Der Benutzername is erforderlich!',
                    ],
                ],
            ],
            'en' => [
                'user' => [
                    'validation' => [
                        'username_required' => 'The username is mandatory!',
                    ],
                ],
            ],
        ], $tr->getTranslations());
    }

    public function testLoadFromJsonNoFiles()
    {
        $tr = Translation::loadFromJson(['/src'], ['de', 'en']);
        $this->assertEquals([
            'de' => [],
            'en' => [],
        ], $tr->getTranslations());
    }

    public function testLoadFromJsonUnableToLoadFile()
    {
        $tr = Translation::loadFromJson(['not-a-readable-file'], ['de', 'en']);
        $this->assertEquals([
            'de' => [],
            'en' => [],
        ], $tr->getTranslations());
    }

    public function testLoadInvalidJson()
    {
        $tr = Translation::loadFromJson(['invalid-json'], ['de', 'en']);
        $this->assertEquals([
            'de' => [],
            'en' => [],
        ], $tr->getTranslations());
    }

    public function testGet()
    {
        $tr = Translation::loadFromJson(['my-translations'], ['de', 'en']);
        $this->assertEquals('The username is mandatory!', $tr->get('user', 'validation', 'username_required'));
        $tr->setLanguage('de');
        $this->assertEquals('Der Benutzername is erforderlich!', $tr->get('user', 'validation', 'username_required'));
    }

    public function testGetNotFound()
    {
        $tr = Translation::loadFromJson(['my-translations'], ['de', 'en']);
        $this->assertEquals('en.xxx', $tr->get('xxx'));
        $tr->setLanguage('de');
        $this->assertEquals('de.xxx', $tr->get('xxx'));
        $this->assertEquals('de.user.validation.lastname_required', $tr->get('user', 'validation', 'lastname_required'));
    }

    public function testMerge()
    {
        $tr    = Translation::loadFromJson(['my-translations'], ['de', 'en']);
        $newTr = $tr->merge([
            'en' => [
                'user' => [
                    'firstname' => 'Firstname'
                ]
            ]
        ]);
        $this->assertEquals([
            'de' => [
                'user' => [
                    'validation' => [
                        'username_required' => 'Der Benutzername is erforderlich!',
                    ],
                ],
            ],
            'en' => [
                'user' => [
                    'validation' => [
                        'username_required' => 'The username is mandatory!',
                    ],
                    'firstname'  => 'Firstname',
                ],
            ],
        ], $newTr->getTranslations());
    }
}
