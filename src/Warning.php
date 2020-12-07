<?php


namespace Lukaswhite\PodcastFeedValidator;

/**
 * Class Warning
 *
 * @package Lukaswhite\PodcastFeedValidator
 */
class Warning
{
    const NO_ITEMS = 'no_items';
    const NO_TITLE = 'no_title';
    const NO_LANGUAGE = 'no_language';
    const NO_DESCRIPTION = 'no_description';
    const NO_ARTWORK = 'no_artwork';
    const NO_CATEGORIES = 'no_categories';
    const NO_EXPLICIT = 'no_explicit';
    const INVALID_CATEGORY = 'invalid_category';

    // Category-specific
    const NO_MEDIA = 'no_media';
}