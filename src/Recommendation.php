<?php


namespace Lukaswhite\PodcastFeedValidator;

/**
 * Class Recommendation
 *
 * @package Lukaswhite\PodcastFeedValidator
 */
class Recommendation
{
    const ADD_AUTHOR = 'add_author';
    const ADD_LINK = 'add_link';
    const ADD_OWNER = 'add_owner';
    const ADD_OWNER_EMAIL = 'add_owner_email';

    // Episode specific
    const ADD_GUID = 'add_guid';
    const ADD_PUB_DATE = 'add_pub_date';
    const ADD_DESCRIPTION = 'add_description';
    const ADD_EXPLICIT = 'add_explicit';
    const ADD_ARTWORK = 'add_artwork';
    const ADD_DURATION = 'add_duration';
}