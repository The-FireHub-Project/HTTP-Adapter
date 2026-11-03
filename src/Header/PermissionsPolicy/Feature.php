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

namespace FireHub\Adapter\HTTP\Header\PermissionsPolicy;

/**
 * ### Permissions Policy Feature
 *
 * Defines browser features that can be controlled through the Permissions-Policy HTTP response header.
 *
 * Feature availability and support depend on the browser.
 * @since 1.0.0
 */
enum Feature:string {

    /**
     * ### Accelerometer
     *
     * Controls access to device acceleration information.
     * @since 1.0.0
     */
    case ACCELEROMETER = 'accelerometer';

    /**
     * ### Autoplay
     *
     * Controls automatic playback of media.
     * @since 1.0.0
     */
    case AUTOPLAY = 'autoplay';

    /**
     * ### Camera
     *
     * Controls access to camera devices.
     * @since 1.0.0
     */
    case CAMERA = 'camera';

    /**
     * ### Display Capture
     *
     * Controls access to screen capture capabilities.
     * @since 1.0.0
     */
    case DISPLAY_CAPTURE = 'display-capture';

    /**
     * ### Encrypted Media
     *
     * Controls access to encrypted media playback APIs.
     * @since 1.0.0
     */
    case ENCRYPTED_MEDIA = 'encrypted-media';

    /**
     * ### Fullscreen
     *
     * Controls access to fullscreen presentation.
     * @since 1.0.0
     */
    case FULLSCREEN = 'fullscreen';

    /**
     * ### Geolocation
     *
     * Controls access to geographic location information.
     * @since 1.0.0
     */
    case GEOLOCATION = 'geolocation';

    /**
     * ### Gyroscope
     *
     * Controls access to device rotation information.
     * @since 1.0.0
     */
    case GYROSCOPE = 'gyroscope';

    /**
     * ### Magnetometer
     *
     * Controls access to magnetic field information.
     * @since 1.0.0
     */
    case MAGNETOMETER = 'magnetometer';

    /**
     * ### Microphone
     *
     * Controls access to microphone devices.
     * @since 1.0.0
     */
    case MICROPHONE = 'microphone';

    /**
     * ### MIDI
     *
     * Controls access to MIDI devices.
     * @since 1.0.0
     */
    case MIDI = 'midi';

    /**
     * ### Payment
     *
     * Controls access to the Payment Request API.
     * @since 1.0.0
     */
    case PAYMENT = 'payment';

    /**
     * ### Picture In Picture
     *
     * Controls access to picture-in-picture video playback.
     * @since 1.0.0
     */
    case PICTURE_IN_PICTURE = 'picture-in-picture';

    /**
     * ### Public Key Credentials Get
     *
     * Controls access to credential retrieval through WebAuthn.
     * @since 1.0.0
     */
    case PUBLICKEY_CREDENTIALS_GET = 'publickey-credentials-get';

    /**
     * ### Screen Wake Lock
     *
     * Controls access to the Screen Wake Lock API.
     * @since 1.0.0
     */
    case SCREEN_WAKE_LOCK = 'screen-wake-lock';

    /**
     * ### USB
     *
     * Controls access to USB devices through the WebUSB API.
     * @since 1.0.0
     */
    case USB = 'usb';

    /**
     * ### Web Share
     *
     * Controls access to the Web Share API.
     * @since 1.0.0
     */
    case WEB_SHARE = 'web-share';

    /**
     * ### Window Management
     *
     * Controls access to window management capabilities.
     * @since 1.0.0
     */
    case WINDOW_MANAGEMENT = 'window-management';

    /**
     * ### XR Spatial Tracking
     *
     * Controls access to spatial tracking for WebXR.
     * @since 1.0.0
     */
    case XR_SPATIAL_TRACKING = 'xr-spatial-tracking';

}