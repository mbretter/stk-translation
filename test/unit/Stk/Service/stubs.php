<?php

namespace Stk\Service {

    function glob($pattern, $flags = null)
    {
        if ($pattern === 'not-a-readable-file/*.de.json') {
            return [
                'not-a-readable-file'
            ];
        }

        if ($pattern === 'invalid-json/*.de.json') {
            return [
                'invalid-json',
            ];
        }

        if ($pattern === 'my-translations/*.de.json') {
            return [
                'de-json',
            ];
        }

        if ($pattern === 'my-translations/*.en.json') {
            return [
                'en-json',
            ];
        }

        return [];
    }

    function file_get_contents($file)
    {
        if ($file === 'not-a-readable-file') {
            return false;
        }

        if ($file === 'invalid-json') {
            return '"';
        }

        if ($file === 'de-json') {
            return json_encode([
                'user' => [
                    'validation' => [
                        'username_required' => 'Der Benutzername ist erforderlich!'
                    ]
                ]
            ]);
        }

        if ($file === 'en-json') {
            return json_encode([
                'user' => [
                    'validation' => [
                        'username_required' => 'The username is mandatory!'
                    ]
                ]
            ]);
        }

        if ($file === 'single-file') {
            return json_encode([
                'de' => [
                    'user' => [
                        'validation' => [
                            'username_required' => 'Der Benutzername ist erforderlich!'
                        ]
                    ]
                ],
                'en' => [
                    'user' => [
                        'validation' => [
                            'username_required' => 'The username is mandatory!'
                        ]
                    ]
                ]
            ]);
        }

        return false;
    }
}

