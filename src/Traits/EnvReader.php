<?php

namespace Orphans\Satellite\Traits;

trait EnvReader
{
    /**
     * Supports various website implementations (notably different versions of Bedrock) where
     * env() is/isn't namespaced
     */
    private function env(string $key)
    {
        if (class_exists('Env') && method_exists('Env', 'env')) {
            return Env\env($key);
        } elseif (function_exists('env')) {
            return env($key);
        }
        return null;
    }
}
