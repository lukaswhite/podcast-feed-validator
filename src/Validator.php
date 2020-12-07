<?php


namespace Lukaswhite\PodcastFeedValidator;

use Lukaswhite\PodcastFeedParser\Category;
use Lukaswhite\PodcastFeedParser\Config;
use Lukaswhite\PodcastFeedParser\Episode;
use Lukaswhite\PodcastFeedParser\Exceptions\FileNotFoundException;
use Lukaswhite\PodcastFeedParser\Exceptions\InvalidXmlException;
use Lukaswhite\PodcastFeedParser\Parser;
use Lukaswhite\PodcastFeedParser\Podcast;
use Lukaswhite\ItunesCategories\Categories;

/**
 * Class Validator
 * @package Lukaswhite\PodcastFeedValidator
 */
class Validator
{
    const RSS_VERSION = '2.0';

    /**
     * @var string
     */
    protected $filepath;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var Parser
     */
    protected $parser;

    /**
     * @var Podcast
     */
    protected $podcast;

    /**
     * @var Categories
     */
    protected $categories;

    /**
     * Validator constructor.
     */
    public function __construct()
    {
        $config = new Config();
        $config->descriptionOnly();
        $config->dontDefaultToToday();
        $this->parser = new Parser($config);
        $this->categories = new Categories();
    }

    /**
     * Set the raw content
     *
     * @param string $content
     * @return $this
     */
    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Load the feed from a file
     *
     * @param string $content
     * @return $this
     */
    public function load(string $filepath): self
    {
        $this->filepath = $filepath;
        return $this;
    }

    /**
     * Run the validation process
     *
     * @return Result
     */
    public function run(): Result
    {
        $result = new Result();

        // First check whether the file exists. If it doesn't,
        // we have to bail at this early stage.
        if ( $this->filepath ) {
            try {
                $this->parser->load($this->filepath);
            } catch (FileNotFoundException $e) {
                return $result->notFound();
            } catch(InvalidXmlException $e) {
                return $result->notXml();
            }
        }

        // Check whether the file is blank; bail if it is
        if ( $this->filepath ) {
            try {
                $xml = simplexml_load_file($this->filepath);
            } catch (\Exception $e) {
                if ($e->getCode() === 2) {
                    return $result->blank();
                }
            }
        }

        if ($this->content) {
            try {
                $xml = simplexml_load_string($this->content);
            } catch (\Exception $e) {
                if ($e->getCode() === 2) {
                    return $result->blank();
                }
            }
        }

        // If it's not RSS, bail. This also sets specific errors
        if(!$this->isRss($xml,$result)) {
            return $result->notRSS();
        }

        // Ensure that a channel exists
        if ( ! $xml->channel ) {
            return $result->noChannel();
        }

        // Get the namespaces
        $namespaces = array_flip($xml->getDocNamespaces(true));

        // Ensure that the iTunes namespace is present
        if (!isset($namespaces[Parser::NS_ITUNES])) {
            $result->addError(Error::MISSING_ITUNES_NS);
            return $result->fail();
        }

        // Check the namespaces
        $this->checkNamespaces($xml);

        //var_dump($xml->getDocNamespaces());

        //var_dump($xml->getNamespaces(true));

        // Now we can start parsing
        $podcast = $this->parser->run();

        // Set the number of items; if there aren't any then this will add the appropriate
        // warning
        $result->setNumberOfItems($podcast->getEpisodes()->count());

        /**
         * Check the existence of fields required by iTunes in the channel
         */
        if (!$podcast->getTitle()) {
            $result->addWarning(Warning::NO_TITLE);
        }
        if (!$podcast->getDescription()) {
            $result->addWarning(Warning::NO_DESCRIPTION);
        }
        if (!$podcast->getLanguage()) {
            $result->addWarning(Warning::NO_LANGUAGE);
        }
        if (!$podcast->getArtwork()) {
            $result->addWarning(Warning::NO_ARTWORK);
        }

        // Warn if there are no categories
        if (!$podcast->getCategories()||count($podcast->getCategories())===0) {
            $result->addWarning(Warning::NO_CATEGORIES);
        } else {
            foreach($podcast->getCategories(Category::ITUNES) as $category) {
                if (!$this->categories->has($category->getKey())) {
                    $result->addWarning(Warning::INVALID_CATEGORY);
                }
            }
        }

        if (!$podcast->getExplicit()) {
            $result->addWarning(Warning::NO_EXPLICIT);
        }

        /**
         * Check the existence of fields recommended by iTunes
         */
        if (!$podcast->getAuthor()) {
            $result->addRecommendation(Recommendation::ADD_AUTHOR);
        }
        if (!$podcast->getLink()) {
            $result->addRecommendation(Recommendation::ADD_LINK);
        }
        if (!$podcast->getOwner()) {
            $result->addRecommendation(Recommendation::ADD_OWNER);
        } elseif(!$podcast->getOwner()->getEmail()){
            $result->addRecommendation(Recommendation::ADD_OWNER_EMAIL);
        }

        // Now validate the episodes
        foreach( $podcast->getEpisodes() as $episode)
        {
            $result->addItemResult($this->validateEpisode($episode));
        }

        return $result->pass();
    }

    /**
     * @param \SimpleXMLElement $xml
     * @param Result $result
     * @return bool
     */
    protected function isRss(\SimpleXMLElement $xml, Result $result)
    {
        $rss = true;
        if ($xml->getName()!=='rss'){
            $result->addError(Error::TAG_NOT_RSS);
            $rss = false;
        }
        if (!isset($xml->attributes()['version'])) {
            $result->addError(Error::MISSING_RSS_VERSION);
            $rss = false;
        } elseif((string)$xml->attributes()['version']!==self::RSS_VERSION) {
            $result->addError(Error::WRONG_RSS_VERSION);
            $rss = false;
        }
        return $rss;
    }

    /**
     * Validate an episode
     *
     * @param Episode $episode
     * @return ItemResult
     */
    public function validateEpisode(Episode $episode)
    {
        $result = new ItemResult();
        // Check the fields required by iTunes
        if (!$episode->getTitle()) {
            $result->addWarning(Warning::NO_TITLE);
        }
        if (!$episode->getMedia()) {
            $result->addWarning(Warning::NO_MEDIA);
        }

        // Check the fields recommended by iTunes
        if (!$episode->getDescription()) {
            $result->addRecommendation(Recommendation::ADD_DESCRIPTION);
        }
        if (!$episode->getPublishedDate()) {
            $result->addRecommendation(Recommendation::ADD_PUB_DATE);
        }
        if (!$episode->getGuid()) {
            $result->addRecommendation(Recommendation::ADD_GUID);
        }
        if (!$episode->getExplicit()) {
            $result->addRecommendation(Recommendation::ADD_EXPLICIT);
        }
        if (!$episode->getDuration()) {
            $result->addRecommendation(Recommendation::ADD_DURATION);
        }
        if (!$episode->getLink()) {
            $result->addRecommendation(Recommendation::ADD_LINK);
        }
        if (!$episode->getArtwork()) {
            $result->addRecommendation(Recommendation::ADD_ARTWORK);
        }
        $result->pass();
        return $result;
    }

    /**
     * @param \SimpleXMLElement $xml
     */
    protected function checkNamespaces(\SimpleXMLElement $xml)
    {
        $namespaces = array_values($xml->getNamespaces(true));

    }

}