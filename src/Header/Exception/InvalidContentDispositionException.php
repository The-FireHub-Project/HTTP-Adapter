<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.3
 * @package Adapter\HTTP
 */

namespace FireHub\Adapter\HTTP\Header\Exception;

use FireHub\Core\Exception\Runtime\InvalidArgumentException;

/**
 * ### Invalid Content-Disposition exception
 * @since 1.0.0
 */
final class InvalidContentDispositionException extends InvalidArgumentException {

    /**
     * @inheritdoc
     *
     * @since 1.0.0
     */
    protected const string DEFAULT_MESSAGE = 'Invalid HTTP content disposition.';

}