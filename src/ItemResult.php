<?php


namespace Lukaswhite\PodcastFeedValidator;

use phpDocumentor\Reflection\Types\Boolean;

/**
 * Class Validator
 * @package Lukaswhite\PodcastFeedValidator
 */
class ItemResult extends Result
{
    const WARNING_NO_MEDIA = 'no_media';

    const RECOMMENDATION_DESCRIPTION = 'recommendation_description';
    const RECOMMENDATION_GUID = 'recommendation_guid';
    const RECOMMENDATION_PUB_DATE = 'recommendation_pub_date';
    const RECOMMENDATION_DURATION = 'recommendation_duration';
    const RECOMMENDATION_EPISODE_ARTWORK = 'recommendation_episode_artwork';
    const RECOMMENDATION_EPISODE_EXPLICIT = 'recommendation_episode_explicit';

}