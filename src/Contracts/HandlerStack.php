<?php

namespace Modulate\Artisan\Interceptor\Contracts;

interface HandlerStack
{

    /**
     * Add a new handler to the stack
     *
     * @param Handler|ArtisanHandler $handler
     * @return HandlerStack
     */
    public function push(Handler|ArtisanHandler $handler): HandlerStack;

    /**
     * Checks if the stack contains any handlers
     *
     * @return boolean
     */
    public function empty(): bool;

    /**
     * Return the current handler from the stack
     *
     * @return void
     */
    public function current();

    /**
     * Move the pointer to the next handler in the stack
     * The method should return false if the stack has no more handlers
     *
     * @return Handler|ArtisanHandler|false
     */
    public function next(): Handler|ArtisanHandler|bool;

    /**
     * Reset the pointer to the start of the stack
     *
     * @return void
     */
    public function reset(): void;

    /**
     * Remove all handlers from the stack
     */
    public function flush(): HandlerStack;
}
