# Podcast Feed Validator

A PHP library for validating podcast feeds.

Checks the validity of the feed; for example whether it's XML, RSS, whether it has the appropriate namespace.

It warns if certain fields are missing; for example, the fields that iTunes requires.

It also recommends fields that iTunes recommend, but which are not required.

> Note that I deliberately haven't added a method to fetch a remote feed, so as not to add additional dependencies; I'd recommend using Guzzle. 

## Installation

```bash
composer require lukaswhite/podcast-feed-validator
```

## Usage

```php
use Lukaswhite\PodcastFeedValidator\Validator;

$validator = new Validator();

$validator->load('/path/to/feed.rss');
// or
$validator->setContent(/** raw content */);

$result = $validator->run();
        
if ($result->fails()) {
    // ...do something
}
```

## The Result

The return value is an object that encapsulates the errors, warnings and recommendations.

Each of these are just strings, and are defined as constants in the relevant classes. 

Note that typically, if an error has occurred then validation has failed before it starts generating warnings or recommendations. For example if the provided feed isn't XML, then it cannot check for the existence of certain fields. 

```php
if ($result->hasErrors()) {
    foreach ($result->getErrors() as $error) {
    
    }
}

if ($result->hasWarnings()) {
    foreach ($result->getWarnings() as $error) {
    
    }
}

if ($result->hasRecommendations()) {
    foreach ($result->getRecommendations() as $error) {
    
    }
}
```

The result object also includes individual results for the episodes.

## Example

Suppose you provide the following XML:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd"
xmlns:rawvoice="http://www.rawvoice.com/rawvoiceRssModule/"
xmlns:googleplay="http://www.google.com/schemas/play-podcasts/1.0"
>
<channel>
	<title>Minimal Example</title>
	<link>https://example.com</link>
	<description>Just an example.</description>
	<item>
		<title>Episode One</title>
	</item>
</channel>
</rss>

```

This will return `FALSE`:

```php
$result->hasErrors();
```

The following will all return `TRUE`:

```php
$result->hasWarnings();
$result->hasWarning(Warning::NO_LANGUAGE);
$result->hasWarning(Warning::NO_ARTWORK);
$result->hasWarning(Warning::NO_CATEGORIES);
$result->hasWarning(Warning::NO_EXPLICIT);
```

It will also make recommendations, so the following will also return `TRUE`:

```php
$result->hasRecommendations();
$result->hasRecommendation(Recommendation::ADD_AUTHOR);
$result->hasRecommendation(Recommendation::ADD_OWNER);
```

For the episode:

```php
$result = $validator->run()->episodes()[0];
$result->hasWarning(Warning::NO_MEDIA);
$result->hasRecommendation(Recommendation::ADD_LINK);
$result->hasRecommendation(Recommendation::ADD_GUID);
$result->hasRecommendation(Recommendation::ADD_PUB_DATE);
$result->hasRecommendation(Recommendation::ADD_DESCRIPTION);
$result->hasRecommendation(Recommendation::ADD_EXPLICIT);
$result->hasRecommendation(Recommendation::ADD_ARTWORK);
$result->hasRecommendation(Recommendation::ADD_DURATION);
```

See the tests for more information.