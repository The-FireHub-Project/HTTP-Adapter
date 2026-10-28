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

namespace FireHub\Adapter\HTTP\Header\Language;

namespace FireHub\Adapter\HTTP\Header\Language;

/**
 * ### Supported HTTP language tags
 *
 * Represents common language tags used in HTTP content negotiation.
 *
 * Language tags follow BCP 47 conventions and are used by the
 * Accept-Language request header.
 *
 * @since 1.0.0
 */
enum Type:string {


    /* * ============================================================
     * English
     * ============================================================ */

    /**
     * ### English
     * @since 1.0.0
     */
    case EN = 'en';

    /**
     * ### English (United States)
     * @since 1.0.0
     */
    case EN_US = 'en-US';

    /**
     * ### English (United Kingdom)
     * @since 1.0.0
     */
    case EN_GB = 'en-GB';

    /**
     * ### English (Australia)
     * @since 1.0.0
     */
    case EN_AU = 'en-AU';

    /**
     * ### English (Canada)
     * @since 1.0.0
     */
    case EN_CA = 'en-CA';


    /* * ============================================================
     * Croatian
     * ============================================================ */

    /**
     * ### Croatian
     * @since 1.0.0
     */
    case HR = 'hr';

    /**
     * ### Croatian (Croatia)
     * @since 1.0.0
     */
    case HR_HR = 'hr-HR';

    /**
     * ### Croatian (Bosnia and Herzegovina)
     * @since 1.0.0
     */
    case HR_BA = 'hr-BA';


    /* * ============================================================
     * Bosnian
     * ============================================================ */

    /**
     * ### Bosnian
     * @since 1.0.0
     */
    case BS = 'bs';

    /**
     * ### Bosnian (Bosnia and Herzegovina)
     * @since 1.0.0
     */
    case BS_BA = 'bs-BA';


    /* * ============================================================
     * Serbian
     * ============================================================ */

    /**
     * ### Serbian
     * @since 1.0.0
     */
    case SR = 'sr';

    /**
     * ### Serbian (Serbia)
     * @since 1.0.0
     */
    case SR_RS = 'sr-RS';

    /**
     * ### Serbian (Latin script)
     * @since 1.0.0
     */
    case SR_LATN = 'sr-Latn';

    /**
     * ### Serbian (Cyrillic script)
     * @since 1.0.0
     */
    case SR_CYRL = 'sr-Cyrl';


    /* * ============================================================
     * German
     * ============================================================ */

    /**
     * ### German
     * @since 1.0.0
     */
    case DE = 'de';

    /**
     * ### German (Germany)
     * @since 1.0.0
     */
    case DE_DE = 'de-DE';

    /**
     * ### German (Austria)
     * @since 1.0.0
     */
    case DE_AT = 'de-AT';

    /**
     * ### German (Switzerland)
     * @since 1.0.0
     */
    case DE_CH = 'de-CH';


    /* * ============================================================
     * French
     * ============================================================ */

    /**
     * ### French
     * @since 1.0.0
     */
    case FR = 'fr';

    /**
     * ### French (France)
     * @since 1.0.0
     */
    case FR_FR = 'fr-FR';

    /**
     * ### French (Canada)
     * @since 1.0.0
     */
    case FR_CA = 'fr-CA';

    /**
     * ### French (Belgium)
     * @since 1.0.0
     */
    case FR_BE = 'fr-BE';

    /**
     * ### French (Switzerland)
     * @since 1.0.0
     */
    case FR_CH = 'fr-CH';


    /* * ============================================================
     * Spanish
     * ============================================================ */

    /**
     * ### Spanish
     * @since 1.0.0
     */
    case ES = 'es';

    /**
     * ### Spanish (Spain)
     * @since 1.0.0
     */
    case ES_ES = 'es-ES';

    /**
     * ### Spanish (Mexico)
     * @since 1.0.0
     */
    case ES_MX = 'es-MX';

    /**
     * ### Spanish (Argentina)
     * @since 1.0.0
     */
    case ES_AR = 'es-AR';

    /**
     * ### Spanish (Colombia)
     * @since 1.0.0
     */
    case ES_CO = 'es-CO';

    /**
     * ### Spanish (United States)
     * @since 1.0.0
     */
    case ES_US = 'es-US';


    /* * ============================================================
     * Italian
     * ============================================================ */

    /**
     * ### Italian
     * @since 1.0.0
     */
    case IT = 'it';

    /**
     * ### Italian (Italy)
     * @since 1.0.0
     */
    case IT_IT = 'it-IT';

    /**
     * ### Italian (Switzerland)
     * @since 1.0.0
     */
    case IT_CH = 'it-CH';


    /* * ============================================================
     * Portuguese
     * ============================================================ */

    /**
     * ### Portuguese
     * @since 1.0.0
     */
    case PT = 'pt';

    /**
     * ### Portuguese (Portugal)
     * @since 1.0.0
     */
    case PT_PT = 'pt-PT';

    /**
     * ### Portuguese (Brazil)
     * @since 1.0.0
     */
    case PT_BR = 'pt-BR';


    /* * ============================================================
     * Other Languages
     * ============================================================ */

    /**
     * ### Dutch
     * @since 1.0.0
     */
    case NL = 'nl';

    /**
     * ### Dutch (Netherlands)
     * @since 1.0.0
     */
    case NL_NL = 'nl-NL';

    /**
     * ### Dutch (Belgium)
     * @since 1.0.0
     */
    case NL_BE = 'nl-BE';

    /**
     * ### Polish
     * @since 1.0.0
     */
    case PL = 'pl';

    /**
     * ### Czech
     * @since 1.0.0
     */
    case CS = 'cs';

    /**
     * ### Slovak
     * @since 1.0.0
     */
    case SK = 'sk';

    /**
     * ### Slovenian
     * @since 1.0.0
     */
    case SL = 'sl';

    /**
     * ### Hungarian
     * @since 1.0.0
     */
    case HU = 'hu';

    /**
     * ### Romanian
     * @since 1.0.0
     */
    case RO = 'ro';

    /**
     * ### Bulgarian
     * @since 1.0.0
     */
    case BG = 'bg';

    /**
     * ### Greek
     * @since 1.0.0
     */
    case EL = 'el';

    /**
     * ### Turkish
     * @since 1.0.0
     */
    case TR = 'tr';

    /**
     * ### Russian
     * @since 1.0.0
     */
    case RU = 'ru';

    /**
     * ### Ukrainian
     * @since 1.0.0
     */
    case UK = 'uk';

    /**
     * ### Swedish
     * @since 1.0.0
     */
    case SV = 'sv';

    /**
     * ### Norwegian Bokmål
     * @since 1.0.0
     */
    case NB = 'nb';

    /**
     * ### Norwegian Nynorsk
     * @since 1.0.0
     */
    case NN = 'nn';

    /**
     * ### Danish
     * @since 1.0.0
     */
    case DA = 'da';

    /**
     * ### Finnish
     * @since 1.0.0
     */
    case FI = 'fi';

    /**
     * ### Icelandic
     * @since 1.0.0
     */
    case IS = 'is';

    /**
     * ### Estonian
     * @since 1.0.0
     */
    case ET = 'et';

    /**
     * ### Latvian
     * @since 1.0.0
     */
    case LV = 'lv';

    /**
     * ### Lithuanian
     * @since 1.0.0
     */
    case LT = 'lt';

    /**
     * ### Albanian
     * @since 1.0.0
     */
    case SQ = 'sq';

    /**
     * ### Macedonian
     * @since 1.0.0
     */
    case MK = 'mk';

    /**
     * ### Arabic
     * @since 1.0.0
     */
    case AR = 'ar';

    /**
     * ### Hebrew
     * @since 1.0.0
     */
    case HE = 'he';

    /**
     * ### Persian
     * @since 1.0.0
     */
    case FA = 'fa';

    /**
     * ### Hindi
     * @since 1.0.0
     */
    case HI = 'hi';

    /**
     * ### Bengali
     * @since 1.0.0
     */
    case BN = 'bn';

    /**
     * ### Urdu
     * @since 1.0.0
     */
    case UR = 'ur';

    /**
     * ### Chinese
     * @since 1.0.0
     */
    case ZH = 'zh';

    /**
     * ### Chinese (China)
     * @since 1.0.0
     */
    case ZH_CN = 'zh-CN';

    /**
     * ### Chinese (Taiwan)
     * @since 1.0.0
     */
    case ZH_TW = 'zh-TW';

    /**
     * ### Chinese (Simplified script)
     * @since 1.0.0
     */
    case ZH_HANS = 'zh-Hans';

    /**
     * ### Chinese (Traditional script)
     * @since 1.0.0
     */
    case ZH_HANT = 'zh-Hant';

    /**
     * ### Japanese
     * @since 1.0.0
     */
    case JA = 'ja';

    /**
     * ### Korean
     * @since 1.0.0
     */
    case KO = 'ko';

    /**
     * ### Vietnamese
     * @since 1.0.0
     */
    case VI = 'vi';

    /**
     * ### Thai
     * @since 1.0.0
     */
    case TH = 'th';

    /**
     * ### Indonesian
     * @since 1.0.0
     */
    case ID = 'id';

    /**
     * ### Malay
     * @since 1.0.0
     */
    case MS = 'ms';

    /**
     * ### Filipino
     * @since 1.0.0
     */
    case FIL = 'fil';

    /**
     * ### Swahili
     * @since 1.0.0
     */
    case SW = 'sw';

    /**
     * ### Afrikaans
     * @since 1.0.0
     */
    case AF = 'af';

}