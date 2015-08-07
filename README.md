# AuthenticJobs Jobs Client

[![Latest Version](https://img.shields.io/github/release/JobBrander/jobs-authenticjobs.svg?style=flat-square)](https://github.com/JobBrander/jobs-authenticjobs/releases)
[![Software License](https://img.shields.io/badge/license-APACHE%202.0-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/JobBrander/jobs-authenticjobs/master.svg?style=flat-square&1)](https://travis-ci.org/JobBrander/jobs-authenticjobs)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/JobBrander/jobs-authenticjobs.svg?style=flat-square)](https://scrutinizer-ci.com/g/JobBrander/jobs-authenticjobs/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/JobBrander/jobs-authenticjobs.svg?style=flat-square)](https://scrutinizer-ci.com/g/JobBrander/jobs-authenticjobs)
[![Total Downloads](https://img.shields.io/packagist/dt/jobbrander/jobs-authenticjobs.svg?style=flat-square)](https://packagist.org/packages/jobbrander/jobs-authenticjobs)

This package provides AuthenticJobs Jobs API support for the JobBrander's [Jobs Client](https://github.com/JobBrander/jobs-common).

## Installation

To install, use composer:

```
composer require jobbrander/jobs-authenticjobs
```

## Usage

Usage is the same as Job Branders's Jobs Client, using `\JobBrander\Jobs\Client\Provider\AuthenticJobs` as the provider.

```php
$client = new JobBrander\Jobs\Client\Provider\AuthenticJobs([
    'key' => 'YOUR API KEY',
]);

// Search for 200 job listings for 'project manager' in Chicago, IL
$jobs = $client->setKeywords('designer') // Keywords to look for in the title or description of the job posting. Separate multiple keywords with commas. Multiple keywords will be treated as an OR
    ->setCategory('UI Design')     // The id of a job category to limit to. See aj.categories.getList
    ->setType('Freelance')         // The id of a job type to limit to. See aj.types.getList
    ->setSort('date-posted-asc')   // Accepted values are: date-posted-desc (the default) and date-posted-asc
    ->setCompany('Apple')          // Free-text matching against company names. Suggested values are the ids from aj.jobs.getCompanies
    ->setLocation('Pasadena, CA')  // Free-text matching against company location names. Suggested values are the ids from aj.jobs.getLocation
    ->setTelecommuting(1)          // Set to 1 if you only want telecommuting jobs
    ->setBeginDate(1438819200)     // Unix timestamp. Listings posted before this time will not be returned
    ->setEndDate(1441497600)       // Unix timestamp. Listings posted after this time will not be returned
    ->setPage(2)                   // The page of listings to return. Defaults to 1.
    ->setPerPage(20)               // The number of listings per page. The default value is 10. The maximum value is 100.
    ->getJobs();
```

The `getJobs` method will return a [Collection](https://github.com/JobBrander/jobs-common/blob/master/src/Collection.php) of [Job](https://github.com/JobBrander/jobs-common/blob/master/src/Job.php) objects.

## Testing

``` bash
$ ./vendor/bin/phpunit
```

## Contributing

Please see [CONTRIBUTING](https://github.com/jobbrander/jobs-authenticjobs/blob/master/CONTRIBUTING.md) for details.


## Credits

- [Steven Maguire](https://github.com/stevenmaguire)
- [All Contributors](https://github.com/jobbrander/jobs-authenticjobs/contributors)


## License

The Apache 2.0. Please see [License File](https://github.com/jobbrander/jobs-authenticjobs/blob/master/LICENSE) for more information.
