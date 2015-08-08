<?php namespace JobBrander\Jobs\Client\Providers;

use JobBrander\Jobs\Client\Job;

class AuthenticJobs extends AbstractProvider
{
    /**
     * Api key
     *
     * @var string
     */
    protected $key;

    /**
     * Map of setter methods to search parameters
     *
     * @var array
     */
    protected $searchMap = [
        'setCategory' => 'category',
        'setType' => 'type',
        'setSort' => 'sort',
        'setCompany' => 'company',
        'setLocation' => 'location',
        'setTelecommuting' => 'telecommuting',
        'setKeywords' => 'keywords',
        'setBeginDate' => 'begin_date',
        'setEndDate' => 'end_date',
        'setPage' => 'page',
        'setPerPage' => 'perpage',
    ];

    /**
     * Current search parameters
     *
     * @var array
     */
    protected $searchParameters = [
        'category' => null,
        'type' => null,
        'sort' => null,
        'company' => null,
        'location' => null,
        'telecommuting' => null,
        'keywords' => null,
        'begin_date' => null,
        'end_date' => null,
        'page' => null,
        'perpage' => null,
    ];

    /**
     * Create new authentic jobs client.
     *
     * @param array $parameters
     */
    public function __construct($parameters = [])
    {
        parent::__construct($parameters);
        array_walk($parameters, [$this, 'updateQuery']);
    }

    /**
     * Magic method to handle get and set methods for properties
     *
     * @param  string $method
     * @param  array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (isset($this->searchMap[$method], $parameters[0])) {
            $this->updateQuery($parameters[0], $this->searchMap[$method]);
        }

        return parent::__call($method, $parameters);
    }

    /**
     * Returns the standardized job object.
     *
     * @param array $payload
     *
     * @return \JobBrander\Jobs\Client\Job
     */
    public function createJobObject($payload)
    {
        $job = new Job;

        $map = [
            'setSourceId' => '@attributes.id',
            'setName' => '@attributes.title',
            'setDescription' => '@attributes.description',
            'setJobBenefits' => '@attributes.perks',
            'setDatePostedAsString' => '@attributes.post_date',
            'setUrl' => '@attributes.url',
            'setOccupationalCategory' => 'category.@attributes.name',
            'setWorkHours' => 'type.@attributes.name',
            'setCompanyName' => 'company.@attributes.name',
            'setCompanyLogo' => 'company.@attributes.logo',
            'setCompanyUrl' => 'company.@attributes.url',
            'setCompanyDescription' => 'company.@attributes.tagline',
            'setCity' => 'company.location.@attributes.city',
            'setCountry' => 'company.location.@attributes.country',
        ];

        array_walk($map, function ($path, $setter) use ($payload, &$job) {
            try {
                $value = static::getValue(explode('.', $path), $payload);
                $job->$setter($value);
            } catch (\OutOfRangeException $e) {
                // do nothing
            }
        });

        return $job;
    }

    /**
     * Get data format.
     *
     * @return string
     */
    public function getFormat()
    {
        return 'xml';
    }

    /**
     * Get listings path.
     *
     * @return  string
     */
    public function getListingsPath()
    {
        return 'listings.listing';
    }

    /**
     * Retrieves query string.
     *
     * @return string
     */
    protected function getQueryString()
    {
        $query = http_build_query($this->searchParameters);

        if ($query) {
            $query = '&' . $query;
        }

        return $query;
    }

    /**
     * Get url.
     *
     * @return  string
     */
    public function getUrl()
    {
        return 'http://www.authenticjobs.com/api/?method=aj.jobs.search'.
            '&format='.$this->getFormat().
            '&api_key='.$this->key.
            $this->getQueryString();
    }

    /**
     * Get http verb.
     *
     * @return  string
     */
    public function getVerb()
    {
        return 'GET';
    }

    /**
     * Attempts to update current query parameters.
     *
     * @param  string  $value
     * @param  string  $key
     *
     * @return AuthenticJobs
     */
    protected function updateQuery($value, $key)
    {
        if (array_key_exists($key, $this->searchParameters)) {
            $this->searchParameters[$key] = $value;
        }

        return $this;
    }
}
