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

namespace FireHub\Adapter\HTTP\Header\Media;

/**
 * ### Supported HTTP media types
 *
 * Represents common Internet media types used throughout the FireHub HTTP Adapter.
 *
 * This enumeration provides a type-safe representation of media types and prevents the usage of raw media type
 * strings when configuring HTTP request and response headers.
 *
 * Media ranges such as <code>*&#47;*</code> and <code>application&#47;*</code> are intentionally excluded because
 * they do not represent concrete media types.
 * @since 1.0.0
 */
enum Type:string {


    /* * ============================================================
     * Application
     * ============================================================ */

    /**
     * ### JSON media type
     * @since 1.0.0
     */
    case APPLICATION_JSON = 'application/json';

    /**
     * ### XML media type
     * @since 1.0.0
     */
    case APPLICATION_XML = 'application/xml';

    /**
     * ### Form URL-encoded media type
     * @since 1.0.0
     */
    case APPLICATION_FORM_URLENCODED = 'application/x-www-form-urlencoded';

    /**
     * ### Binary data media type
     * @since 1.0.0
     */
    case APPLICATION_OCTET_STREAM = 'application/octet-stream';

    /**
     * ### PDF document media type
     * @since 1.0.0
     */
    case APPLICATION_PDF = 'application/pdf';

    /**
     * ### ZIP archive media type
     * @since 1.0.0
     */
    case APPLICATION_ZIP = 'application/zip';

    /**
     * ### GZIP compressed data media type
     * @since 1.0.0
     */
    case APPLICATION_GZIP = 'application/gzip';

    /**
     * ### JavaScript media type
     * @since 1.0.0
     */
    case APPLICATION_JAVASCRIPT = 'application/javascript';

    /**
     * ### JSON Lines media type
     * @since 1.0.0
     */
    case APPLICATION_JSONL = 'application/jsonl';

    /**
     * ### JSON Patch media type
     * @since 1.0.0
     */
    case APPLICATION_JSON_PATCH = 'application/json-patch+json';

    /**
     * ### Merge Patch media type
     * @since 1.0.0
     */
    case APPLICATION_MERGE_PATCH = 'application/merge-patch+json';

    /**
     * ### Problem Details JSON media type
     * @since 1.0.0
     */
    case APPLICATION_PROBLEM_JSON = 'application/problem+json';

    /**
     * ### Problem Details XML media type
     * @since 1.0.0
     */
    case APPLICATION_PROBLEM_XML = 'application/problem+xml';

    /**
     * ### JSON Web Token media type
     * @since 1.0.0
     */
    case APPLICATION_JWT = 'application/jwt';

    /**
     * ### Protocol Buffers media type
     * @since 1.0.0
     */
    case APPLICATION_PROTOBUF = 'application/protobuf';

    /**
     * ### CBOR media type
     * @since 1.0.0
     */
    case APPLICATION_CBOR = 'application/cbor';

    /**
     * ### WebAssembly media type
     * @since 1.0.0
     */
    case APPLICATION_WASM = 'application/wasm';

    /**
     * ### RTF document media type
     * @since 1.0.0
     */
    case APPLICATION_RTF = 'application/rtf';

    /**
     * ### SQL media type
     * @since 1.0.0
     */
    case APPLICATION_SQL = 'application/sql';

    /**
     * ### GraphQL response JSON media type
     * @since 1.0.0
     */
    case APPLICATION_GRAPHQL_RESPONSE_JSON = 'application/graphql-response+json';


    /* * ============================================================
     * Text
     * ============================================================ */

    /**
     * ### Plain text media type
     * @since 1.0.0
     */
    case TEXT_PLAIN = 'text/plain';

    /**
     * ### HTML document media type
     * @since 1.0.0
     */
    case TEXT_HTML = 'text/html';

    /**
     * ### CSS stylesheet media type
     * @since 1.0.0
     */
    case TEXT_CSS = 'text/css';

    /**
     * ### CSV document media type
     * @since 1.0.0
     */
    case TEXT_CSV = 'text/csv';

    /**
     * ### JavaScript media type
     * @since 1.0.0
     */
    case TEXT_JAVASCRIPT = 'text/javascript';

    /**
     * ### XML document media type
     * @since 1.0.0
     */
    case TEXT_XML = 'text/xml';

    /**
     * ### Markdown document media type
     * @since 1.0.0
     */
    case TEXT_MARKDOWN = 'text/markdown';

    /**
     * ### Calendar data media type
     * @since 1.0.0
     */
    case TEXT_CALENDAR = 'text/calendar';

    /**
     * ### Tab-separated values media type
     * @since 1.0.0
     */
    case TEXT_TAB_SEPARATED_VALUES = 'text/tab-separated-values';

    /**
     * ### Event stream media type
     * @since 1.0.0
     */
    case TEXT_EVENT_STREAM = 'text/event-stream';

    /**
     * ### vCard contact data media type
     * @since 1.0.0
     */
    case TEXT_VCARD = 'text/vcard';


    /* * ============================================================
     * Image
     * ============================================================ */

    /**
     * ### JPEG image media type
     * @since 1.0.0
     */
    case IMAGE_JPEG = 'image/jpeg';

    /**
     * ### PNG image media type
     * @since 1.0.0
     */
    case IMAGE_PNG = 'image/png';

    /**
     * ### GIF image media type
     * @since 1.0.0
     */
    case IMAGE_GIF = 'image/gif';

    /**
     * ### WebP image media type
     * @since 1.0.0
     */
    case IMAGE_WEBP = 'image/webp';

    /**
     * ### SVG image media type
     * @since 1.0.0
     */
    case IMAGE_SVG = 'image/svg+xml';

    /**
     * ### AVIF image media type
     * @since 1.0.0
     */
    case IMAGE_AVIF = 'image/avif';

    /**
     * ### BMP image media type
     * @since 1.0.0
     */
    case IMAGE_BMP = 'image/bmp';

    /**
     * ### TIFF image media type
     * @since 1.0.0
     */
    case IMAGE_TIFF = 'image/tiff';

    /**
     * ### Icon image media type
     * @since 1.0.0
     */
    case IMAGE_ICON = 'image/vnd.microsoft.icon';

    /**
     * ### APNG image media type
     * @since 1.0.0
     */
    case IMAGE_APNG = 'image/apng';


    /* * ============================================================
     * Audio
     * ============================================================ */

    /**
     * ### MPEG audio media type
     * @since 1.0.0
     */
    case AUDIO_MPEG = 'audio/mpeg';

    /**
     * ### Ogg audio media type
     * @since 1.0.0
     */
    case AUDIO_OGG = 'audio/ogg';

    /**
     * ### WAV audio media type
     * @since 1.0.0
     */
    case AUDIO_WAV = 'audio/wav';

    /**
     * ### WebM audio media type
     * @since 1.0.0
     */
    case AUDIO_WEBM = 'audio/webm';

    /**
     * ### AAC audio media type
     * @since 1.0.0
     */
    case AUDIO_AAC = 'audio/aac';

    /**
     * ### FLAC audio media type
     * @since 1.0.0
     */
    case AUDIO_FLAC = 'audio/flac';

    /**
     * ### MP4 audio media type
     * @since 1.0.0
     */
    case AUDIO_MP4 = 'audio/mp4';


    /* * ============================================================
     * Video
     * ============================================================ */

    /**
     * ### MP4 video media type
     * @since 1.0.0
     */
    case VIDEO_MP4 = 'video/mp4';

    /**
     * ### MPEG video media type
     * @since 1.0.0
     */
    case VIDEO_MPEG = 'video/mpeg';

    /**
     * ### WebM video media type
     * @since 1.0.0
     */
    case VIDEO_WEBM = 'video/webm';

    /**
     * ### Ogg video media type
     * @since 1.0.0
     */
    case VIDEO_OGG = 'video/ogg';

    /**
     * ### QuickTime video media type
     * @since 1.0.0
     */
    case VIDEO_QUICKTIME = 'video/quicktime';

    /**
     * ### AVI video media type
     * @since 1.0.0
     */
    case VIDEO_AVI = 'video/x-msvideo';

    /**
     * ### 3GPP video media type
     * @since 1.0.0
     */
    case VIDEO_3GPP = 'video/3gpp';


    /* * ============================================================
     * Multipart
     * ============================================================ */

    /**
     * ### Multipart form data media type
     * @since 1.0.0
     */
    case MULTIPART_FORM_DATA = 'multipart/form-data';

    /**
     * ### Multipart mixed content media type
     * @since 1.0.0
     */
    case MULTIPART_MIXED = 'multipart/mixed';

    /**
     * ### Multipart alternative content media type
     * @since 1.0.0
     */
    case MULTIPART_ALTERNATIVE = 'multipart/alternative';

    /**
     * ### Multipart related content media type
     * @since 1.0.0
     */
    case MULTIPART_RELATED = 'multipart/related';

    /**
     * ### Multipart byte ranges media type
     * @since 1.0.0
     */
    case MULTIPART_BYTERANGES = 'multipart/byteranges';


    /* * ============================================================
     * Font
     * ============================================================ */

    /**
     * ### WOFF font media type
     * @since 1.0.0
     */
    case FONT_WOFF = 'font/woff';

    /**
     * ### WOFF2 font media type
     * @since 1.0.0
     */
    case FONT_WOFF2 = 'font/woff2';

    /**
     * ### TrueType font media type
     * @since 1.0.0
     */
    case FONT_TTF = 'font/ttf';

    /**
     * ### OpenType font media type
     * @since 1.0.0
     */
    case FONT_OTF = 'font/otf';


    /* * ============================================================
     * Message
     * ============================================================ */

    /**
     * ### HTTP message media type
     * @since 1.0.0
     */
    case MESSAGE_HTTP = 'message/http';

    /**
     * ### RFC 822 message media type
     * @since 1.0.0
     */
    case MESSAGE_RFC822 = 'message/rfc822';

}