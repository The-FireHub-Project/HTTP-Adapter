<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.1
 * @package Adapter\HTTP
 */

namespace FireHub\Adapter\HTTP\Header\Disposition;

/**
 * ### Known Content-Disposition Types
 *
 * Defines commonly used content disposition types.
 *
 * <code>
 * use FireHub\Adapter\HTTP\Header\Disposition\KnownType;
 *
 * KnownType::INLINE->value;     // inline
 * KnownType::ATTACHMENT->value; // attachment
 * KnownType::FORM_DATA->value;  // form-data
 * </code>
 *
 * @since 1.0.0
 */
enum KnownType:string {

    /**
     * ### Indicates that the content should be displayed inline
     * @since 1.0.0
     */
    case INLINE = 'inline';

    /**
     * ### Indicates that the content should be handled as an attachment
     * @since 1.0.0
     */
    case ATTACHMENT = 'attachment';

    /**
     * ### Identifies a field within multipart/form-data content
     * @since 1.0.0
     */
    case FORM_DATA = 'form-data';

}