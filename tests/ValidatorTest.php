<?php

use Lukaswhite\PodcastFeedValidator\Validator;
use Lukaswhite\PodcastFeedValidator\Result;
use Lukaswhite\PodcastFeedValidator\ItemResult;
use Lukaswhite\PodcastFeedValidator\Error;
use Lukaswhite\PodcastFeedValidator\Warning;
use Lukaswhite\PodcastFeedValidator\Recommendation;

class ValidatorTest extends \PHPUnit\Framework\TestCase
{
    public function test_fails_if_file_not_found()
    {
        $validator = new Validator();
        $validator->load('./tests/fixtures/not-exist.rss');
        $result = $validator->run();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertFalse($result->passes());
        $this->assertFalse($result->found());
    }

    public function test_fails_if_file_is_blank()
    {
        $validator = new Validator();
        $validator->load('./tests/fixtures/blank.rss');
        $result = $validator->run();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertFalse($result->passes());
        $this->assertTrue($result->found());
    }

    public function test_fails_if_file_is_not_xml()
    {
        $validator = new Validator();
        $validator->load('./tests/fixtures/not-xml.rss');
        $result = $validator->run();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertTrue($result->found());
        $this->assertFalse($result->passes());
    }

    public function test_fails_if_string_is_not_xml()
    {
        $validator = new Validator();
        $validator->setContent('i am not xml');
        $result = $validator->run();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertTrue($result->found());
        $this->assertFalse($result->passes());
    }

    public function test_fails_if_file_is_not_valid_xml()
    {
        $validator = new Validator();
        $validator->load('./tests/fixtures/invalid-xml.rss');
        $result = $validator->run();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertTrue($result->found());
        $this->assertFalse($result->passes());
    }

    public function test_fails_if_file_is_not_rss()
    {
        $validator = new Validator();
        $validator->load('./tests/fixtures/not-rss.xml');
        $result = $validator->run();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertTrue($result->found());
        $this->assertFalse($result->passes());
        $this->assertFalse($result->isRss());
        $this->assertTrue($result->hasErrors());
        $this->assertTrue($result->hasError(Error::TAG_NOT_RSS));
        $this->assertTrue($result->hasError(Error::MISSING_RSS_VERSION));
        $this->assertTrue(is_array($result->getErrors()));
        $this->assertEquals(2,count($result->getErrors()));
    }

    public function ___test_fails_if_rss_version_missing()
    {
        $validator = new Validator();
        $validator->load('./tests/fixtures/missing-rss-version.rss');
        $result = $validator->run();
        $this->assertInstanceOf(Result::class, $result);
        var_dump($result);
        $this->assertTrue($result->found());
        $this->assertFalse($result->passes());
        //$this->assertFalse($result->isRss());
        $this->assertTrue($result->hasError(Error::MISSING_RSS_VERSION));
    }

    public function ___test_fails_if_rss_version_wrong()
    {
        $validator = new Validator();
        $validator->load('./tests/fixtures/wrong-rss-version.rss');
        $result = $validator->run();
        $this->assertInstanceOf(Result::class, $result);
        var_dump($result);
        $this->assertTrue($result->found());
        $this->assertFalse($result->passes());
        $this->assertFalse($result->isRss());
        $this->assertTrue($result->hasError(Error::WRONG_RSS_VERSION));
    }

    public function test_fails_if_itunes_namespace_missing()
    {
        $validator = new Validator();
        $validator->load('./tests/fixtures/missing-itunes-namespace.rss');
        $result = $validator->run();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertTrue($result->found());
        $this->assertFalse($result->passes());
        $this->assertTrue($result->hasError(Error::MISSING_ITUNES_NS));
    }

    public function test_fails_if_file_has_no_channel()
    {
        $validator = new Validator();
        $validator->load('./tests/fixtures/no-channel.rss');
        $result = $validator->run();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertFalse($result->passes());
        $this->assertTrue($result->found());
        $this->assertTrue($result->isRss());
        $this->assertFalse($result->hasChannel());
    }

    public function test_warns_if_itunes_categories_are_invalid()
    {
        $validator = new Validator();
        $validator->load('./tests/fixtures/invalid-itunes-categories.rss');
        $result = $validator->run();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertTrue($result->passes());
        $this->assertTrue($result->hasWarnings());
        $this->assertTrue($result->hasWarning(Warning::INVALID_CATEGORY));
        $this->assertTrue(is_array($result->getWarnings()));
        $this->assertEquals(1,count($result->getWarnings()));
    }

    public function test_warns_if_no_items()
    {
        $validator = new Validator();
        $validator->load('./tests/fixtures/no-items.rss');
        $result = $validator->run();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertTrue($result->passes());
        $this->assertTrue($result->hasWarnings());
        $this->assertTrue($result->hasWarning(Warning::NO_ITEMS));
    }

    public function test_warns_if_no_title()
    {
        $validator = new Validator();
        $validator->load('./tests/fixtures/no-title.rss');
        $result = $validator->run();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertTrue($result->passes());
        $this->assertTrue($result->hasWarnings());
        $this->assertTrue($result->hasWarning(Warning::NO_TITLE));
    }

    public function test_warns_if_no_description()
    {
        $validator = new Validator();
        $validator->load('./tests/fixtures/no-description.rss');
        $result = $validator->run();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertTrue($result->passes());
        $this->assertTrue($result->hasWarnings());
        $this->assertTrue($result->hasWarning(Warning::NO_DESCRIPTION));
    }

    public function test_warns_if_no_language()
    {
        $validator = new Validator();
        $validator->load('./tests/fixtures/no-language.rss');
        $result = $validator->run();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertTrue($result->passes());
        $this->assertTrue($result->hasWarnings());
        $this->assertTrue($result->hasWarning(Warning::NO_LANGUAGE));
    }

    public function test_warns_if_no_artwork()
    {
        $validator = new Validator();
        $validator->load('./tests/fixtures/no-artwork.rss');
        $result = $validator->run();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertTrue($result->passes());
        $this->assertTrue($result->hasWarnings());
        $this->assertTrue($result->hasWarning(Warning::NO_ARTWORK));
    }

    public function test_warns_if_no_categories()
    {
        $validator = new Validator();
        $validator->load('./tests/fixtures/no-categories.rss');
        $result = $validator->run();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertTrue($result->passes());
        $this->assertTrue($result->hasWarnings());
        $this->assertTrue($result->hasWarning(Warning::NO_CATEGORIES));
    }

    public function test_warns_if_no_explicit()
    {
        $validator = new Validator();
        $validator->load('./tests/fixtures/no-explicit.rss');
        $result = $validator->run();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertTrue($result->passes());
        $this->assertTrue($result->hasWarnings());
        $this->assertTrue($result->hasWarning(Warning::NO_EXPLICIT));
    }

    public function test_recommends_author()
    {
        $validator = new Validator();
        $validator->load('./tests/fixtures/no-author.rss');
        $result = $validator->run();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertTrue($result->passes());
        $this->assertTrue($result->hasRecommendations());
        $this->assertTrue($result->hasRecommendation(Recommendation::ADD_AUTHOR));
        $this->assertTrue(is_array($result->getRecommendations()));
        $this->assertEquals(1,count($result->getRecommendations()));
    }

    public function test_recommends_link()
    {
        $validator = new Validator();
        $validator->load('./tests/fixtures/no-link.rss');
        $result = $validator->run();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertTrue($result->passes());
        $this->assertTrue($result->hasRecommendations());
        $this->assertTrue($result->hasRecommendation(Recommendation::ADD_LINK));
    }

    public function test_recommends_owner()
    {
        $validator = new Validator();
        $validator->load('./tests/fixtures/no-owner.rss');
        $result = $validator->run();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertTrue($result->passes());
        $this->assertTrue($result->hasRecommendations());
        $this->assertTrue($result->hasRecommendation(Recommendation::ADD_OWNER));
    }

    public function test_recommends_owner_email()
    {
        $validator = new Validator();
        $validator->load('./tests/fixtures/no-owner-email.rss');
        $result = $validator->run();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertTrue($result->passes());
        $this->assertTrue($result->hasRecommendations());
        $this->assertTrue($result->hasRecommendation(Recommendation::ADD_OWNER_EMAIL));
    }

    public function test_warns_if_episode_has_no_title()
    {
        $validator = new Validator();
        $validator->load('./tests/fixtures/episode-without-title.rss');
        /** @var \Lukaswhite\PodcastFeedValidator\ItemResult $result */
        $result = $validator->run()->episodes()[0];
        $this->assertTrue($result->hasWarnings());
        $this->assertTrue($result->hasWarning(Warning::NO_TITLE));
    }

    public function test_warns_if_episode_has_no_media()
    {
        $validator = new Validator();
        $validator->load('./tests/fixtures/episode-without-enclosure.rss');
        /** @var \Lukaswhite\PodcastFeedValidator\ItemResult $result */
        $result = $validator->run()->episodes()[0];
        $this->assertTrue($result->hasWarnings());
        $this->assertTrue($result->hasWarning(Warning::NO_MEDIA));
    }

    public function test_recommends_episode_fields()
    {
        $validator = new Validator();
        $validator->load('./tests/fixtures/episodes-without-recommended-felds.rss');
        /** @var \Lukaswhite\PodcastFeedValidator\ItemResult $result */
        $result = $validator->run()->episodes()[0];
        $this->assertTrue($result->hasRecommendations());
        $this->assertTrue($result->hasRecommendation(Recommendation::ADD_LINK));
        $this->assertTrue($result->hasRecommendation(Recommendation::ADD_GUID));
        $this->assertTrue($result->hasRecommendation(Recommendation::ADD_PUB_DATE));
        $this->assertTrue($result->hasRecommendation(Recommendation::ADD_DESCRIPTION));
        $this->assertTrue($result->hasRecommendation(Recommendation::ADD_EXPLICIT));
        $this->assertTrue($result->hasRecommendation(Recommendation::ADD_ARTWORK));
        $this->assertTrue($result->hasRecommendation(Recommendation::ADD_DURATION));

    }

    public function test_passes_valid_feed()
    {
        $validator = new Validator();
        $validator->load('./tests/fixtures/full.rss');
        $result = $validator->run();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertTrue($result->passes());
        $this->assertTrue($result->found());
        $this->assertTrue($result->isRss());
        $this->assertTrue($result->hasChannel());

        foreach( $result->episodes() as $itemResult) {
            /** @var ItemResult $itemResult */
            $this->assertTrue($itemResult->passes());
            $this->assertFalse($itemResult->hasWarnings());
            $this->assertFalse($itemResult->hasRecommendations());
        }
    }
}