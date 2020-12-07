<?php


namespace Lukaswhite\PodcastFeedValidator;

/**
 * Class Validator
 * @package Lukaswhite\PodcastFeedValidator
 */
class Result
{
    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @var array
     */
    protected $warnings = [];

    /**
     * @var array
     */
    protected $recommendations = [];

    /**
     * @var array
     */
    protected $itemResults = [];

    public function addItemResult(ItemResult $result)
    {
        $this->itemResults[] = $result;
    }

    public function episodes(): array
    {
        return $this->itemResults;
    }

    /**
     * A simple flag to indicate that validation has failed
     *
     * @var bool
     */
    protected $failed;

    /**
     * Whether the file exists.
     *
     * Note that this is irrelevant if the feed's raw content is provided.
     *
     * @var bool
     */
    protected $fileExists = true;

    /**
     * @var bool
     */
    protected $isBlank = true;

    /**
     * @var bool
     */
    protected $isXml = true;

    /**
     * @var bool
     */
    protected $isRss = true;

    /**
     * @var bool
     */
    protected $hasChannel = true;

    /***
     * @var int
     */
    protected $itemsCount = 0;

    /**
     * @return bool
     */
    public function passes(): bool
    {
        return ! $this->failed;
    }

    /**
     * Whether there are any errors
     *
     * @return bool
     */
    public function hasErrors(): bool
    {
        return count($this->errors) > 0;
    }

    /**
     * Whether there are any warnings
     *
     * @return bool
     */
    public function hasWarnings(): bool
    {
        return count($this->warnings) > 0;
    }

    /**
     * Whether there are any recommendations
     *
     * @return bool
     */
    public function hasRecommendations(): bool
    {
        return count($this->recommendations) > 0;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return array
     */
    public function getWarnings()
    {
        return $this->warnings;
    }

    /**
     * @return array
     */
    public function getRecommendations()
    {
        return $this->recommendations;
    }

    /**
     * Whether a particular error is present
     *
     * @param string $key
     * @return bool
     */
    public function hasError(string $key): bool
    {
        return isset($this->errors[$key]);
    }

    /**
     * Whether a particular warning is present
     *
     * @param string $key
     * @return bool
     */
    public function hasWarning(string $key): bool
    {
        return isset($this->warnings[$key]);
    }

    /**
     * Whether a particular recommendation is present
     *
     * @param string $key
     * @return bool
     */
    public function hasRecommendation(string $key): bool
    {
        return isset($this->recommendations[$key]);
    }

    /**
     * @return bool
     */
    public function found(): bool
    {
        return $this->fileExists;
    }

    /**
     * @return bool
     */
    public function hasContent(): bool
    {
        return !! $this->isBlank;
    }

    /**
     * @return bool
     */
    public function isRss(): bool
    {
        return $this->isRss;
    }

    /**
     * @return bool
     */
    public function hasChannel(): bool
    {
        return $this->hasChannel;
    }

    /**
     * Indicate that the file could not be found.
     *
     * @return self
     */
    public function notFound(): self
    {
        $this->fileExists = false;
        return $this->fail();
    }

    /**
     * Indicate that the file is blank
     *
     * @return self
     */
    public function blank(): self
    {
        $this->isBlank = true;
        return $this->fail();
    }

    /**
     * Indicate that the file is not XML
     *
     * @return self
     */
    public function notXml(): self
    {
        $this->isXml = false;
        return $this->fail();
    }

    /**
     * Indicate that the file is not RSS
     *
     * @return self
     */
    public function notRSS(): self
    {
        $this->isRss = false;
        return $this->fail();
    }

    /**
     * Indicate that the file has no channel
     *
     * @return self
     */
    public function noChannel(): self
    {
        $this->hasChannel = false;
        return $this->fail();
    }

    /**
     * @param int $items
     * @return $this
     */
    public function setNumberOfItems(int $items): self
    {
        $this->itemsCount = $items;
        if ( $this->itemsCount === 0 ) {
            $this->addWarning(Warning::NO_ITEMS);
        }
        return $this;
    }

    /**
     * Add a warning
     *
     * @param string $key
     * @return $this
     */
    public function addWarning(string $key): self
    {
        $this->warnings[$key] = $key;
        return $this;
    }

    /**
     * Add an error
     *
     * @param string $key
     * @return $this
     */
    public function addError(string $key): self
    {
        $this->errors[$key] = $key;
        return $this;
    }

    /**
     * Add a recommendation
     *
     * @param string $key
     * @return $this
     */
    public function addRecommendation(string $key): self
    {
        $this->recommendations[$key] = $key;
        return $this;
    }

    /**
     * @return self
     */
    public function fail(): self
    {
        $this->failed = true;
        return $this;
    }

    /**
     * @return self
     */
    public function pass(): self
    {
        $this->failed = false;
        return $this;
    }

}