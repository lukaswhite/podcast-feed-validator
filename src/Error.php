<?php


namespace Lukaswhite\PodcastFeedValidator;

/**
 * Class Error
 *
 * @package Lukaswhite\PodcastFeedValidator
 */
class Error
{
    const TAG_NOT_RSS = 'tag_not_rss';
    const MISSING_RSS_VERSION = 'missing_rss_version';
    const WRONG_RSS_VERSION = 'wrong_rss_version';
    const MISSING_ITUNES_NS = 'missing_itunes_ns';
}