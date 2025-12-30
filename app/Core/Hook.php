<?php

namespace App\Core;

class Hook
{
    private static $hooks = [];

    // Registrar una función a un evento
    public static function add(string $name, callable $callback, int $priority = 10)
    {
        self::$hooks[$name][] = [
            'callback' => $callback,
            'priority' => $priority
        ];
        
        // Ordenar por prioridad
        usort(self::$hooks[$name], function($a, $b) {
            return $a['priority'] <=> $b['priority'];
        });
    }

    // Ejecutar el evento (puede actuar como filtro si se pasa $value)
    public static function call(string $name, $value = null, ...$args)
    {
        if (empty(self::$hooks[$name])) {
            return $value;
        }

        foreach (self::$hooks[$name] as $hook) {
            // El resultado de la función pasa a ser el valor para la siguiente (Pipeline)
            $value = call_user_func($hook['callback'], $value, ...$args);
        }

        return $value;
    }
}
