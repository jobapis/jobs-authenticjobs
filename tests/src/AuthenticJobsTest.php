<?php namespace JobBrander\Jobs\Client\Providers\Test;

use DateTime;
use JobBrander\Jobs\Client\Job;
use JobBrander\Jobs\Client\Providers\AuthenticJobs;
use Mockery as m;

class AuthenticJobsTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->params = [
            'key' => 'mock_key',
        ];

        $this->client = new AuthenticJobs($this->params);
    }

    public function testClientUsesListingsPath()
    {
        $listingsPath = $this->client->getListingsPath();

        $this->assertEquals('listings.listing', $listingsPath);
    }

    public function testClientUsesGetMethod()
    {
        $verb = $this->client->getVerb();

        $this->assertEquals('GET', $verb);
    }

    public function testUrlContainsSearchMethod()
    {
        $url = $this->client->getUrl();

        $this->assertContains('method=aj.jobs.search', $url);
    }

    public function testUrlContainsXmlFormat()
    {
        $format = $this->client->getFormat();
        $url = $this->client->getUrl();

        $this->assertContains('format='.$format, $url);
    }

    public function testUrlContainsKeyWhenProvided()
    {
        $url = $this->client->getUrl();

        $this->assertContains('api_key='.$this->params['key'], $url);
    }

    public function testUrlContainsSearchParametersWhenProvided()
    {
        $client = new \ReflectionClass(AuthenticJobs::class);
        $property = $client->getProperty("searchMap");
        $property->setAccessible(true);
        $searchMap = $property->getValue($this->client);

        $searchParameters = array_values($searchMap);
        $params = [];

        array_map(function ($item) use (&$params) {
            $params[$item] = uniqid();
        }, $searchParameters);

        $newClient = new AuthenticJobs(array_merge($this->params, $params));

        $url = $newClient->getUrl();

        array_walk($params, function ($v, $k) use ($url) {
            $this->assertContains($k.'='.$v, $url);
        });
    }

    public function testUrlContainsSearchParametersWhenSet()
    {
        $client = new \ReflectionClass(AuthenticJobs::class);
        $property = $client->getProperty("searchMap");
        $property->setAccessible(true);
        $searchMap = $property->getValue($this->client);

        array_walk($searchMap, function ($v, $k) {
            $value = uniqid();
            $url = $this->client->$k($value)->getUrl();

            $this->assertContains($v.'='.$value, $url);
        });
    }

    public function testCreateJobObject()
    {
        $xml = $this->getListingXml();
        $payload = json_decode(json_encode(simplexml_load_string($xml, null, LIBXML_NOCDATA)), true);

        $job = $this->client->createJobObject($payload);

        $this->assertInstanceOf(Job::class, $job);
        $this->assertEquals($payload['@attributes']['id'], $job->getSourceId());
        $this->assertEquals($payload['@attributes']['title'], $job->getName());
        $this->assertEquals($payload['@attributes']['description'], $job->getDescription());
        $this->assertEquals($payload['@attributes']['perks'], $job->getJobBenefits());
        $this->assertEquals(new DateTime($payload['@attributes']['post_date']), $job->getDatePosted());
        $this->assertEquals($payload['@attributes']['url'], $job->getUrl());
        $this->assertEquals($payload['category']['@attributes']['name'], $job->getOccupationalCategory());
        $this->assertEquals($payload['type']['@attributes']['name'], $job->getWorkHours());
        $this->assertEquals($payload['company']['@attributes']['name'], $job->getCompanyName());
        $this->assertEquals($payload['company']['@attributes']['url'], $job->getCompanyUrl());
        $this->assertEquals($payload['company']['@attributes']['logo'], $job->getCompanyLogo());
        $this->assertEquals($payload['company']['@attributes']['tagline'], $job->getCompanyDescription());
        $this->assertEquals($payload['company']['@attributes']['name'], $job->getHiringOrganization()->getName());
        $this->assertEquals($payload['company']['@attributes']['url'], $job->getHiringOrganization()->getUrl());
        $this->assertEquals($payload['company']['@attributes']['logo'], $job->getHiringOrganization()->getLogo());
        $this->assertEquals($payload['company']['@attributes']['tagline'], $job->getHiringOrganization()->getDescription());
        $this->assertEquals($payload['company']['location']['@attributes']['city'], $job->getCity());
        $this->assertEquals($payload['company']['location']['@attributes']['country'], $job->getCountry());
        $this->assertEquals($payload['company']['location']['@attributes']['city'], $job->getJobLocation()->getAddress()->getAddressLocality());
        $this->assertEquals($payload['company']['location']['@attributes']['country'], $job->getJobLocation()->getAddress()->getAddressCountry());
    }

    protected function getListingXml()
    {
        return "<listing id=\"25277\" title=\"Lead Theme Designer\" description=\"&lt;p&gt;At Bandzoogle, we build tools that help bands succeed online. Our app powers tens of thousands of artist websites and helps them make a living by selling their music and growing their fan base. We've been &quot;bootstrapped, profitable, and proud&quot; since 2003 and are growing fast.&lt;br /&gt;&lt;br /&gt;As Bandzoogle's Lead Theme Designer, you'll design and implement themes that will be the foundation for thousands of musician websites. We believe that with a well crafted theme, our members can build websites that rival the best custom designs. Lucky for you, we've just built a powerful new theme engine that is waiting for you to create the next generation of Bandzoogle themes.&lt;br /&gt;&lt;br /&gt;Responsibilitites&lt;br /&gt;We&amp;rsquo;re looking for an experienced designer who can:
            &lt;ul&gt;
            &lt;li&gt;Create flexible, modern themes for musicians.&lt;/li&gt;
            &lt;li&gt;Own the creative process, from idea to implementation.&lt;/li&gt;
            &lt;li&gt;Manage external design teams and curate their work.&lt;/li&gt;
            &lt;/ul&gt;
            Requirements
            &lt;ul&gt;
            &lt;li&gt;Able to code clean, responsive, cross-platform HTML5/CSS.&lt;/li&gt;
            &lt;li&gt;Experience with SASS and LESS.&lt;/li&gt;
            &lt;li&gt;Experience using Git or a similar source control system.&lt;/li&gt;
            &lt;li&gt;Comfortable working with front end JavaScript.&lt;/li&gt;
            &lt;li&gt;Strong web typography skills.&lt;/li&gt;
            &lt;li&gt;Productive in a distributed team environment. Our 18-member team is spread across Canada, Europe and the US; we work where we love to be.&lt;/li&gt;
            &lt;li&gt;Portfolio of modern, responsive, CMS-powered websites.&lt;/li&gt;
            &lt;li&gt;Bonus points if you've created commercially available themes on any platform.&lt;/li&gt;
            &lt;/ul&gt;&lt;/p&gt;\" perks=\"A family-friendly schedule — no overtime or weekends.

            Health insurance for US and Canadian employees.

            Reimbursement of home office expenses, computer, and co-working spaces.

            Time and resources for learning, including reimbursement of books and conferences.

            Yearly meet-ups in fun locations, family included!\" howto_apply=\"Email jobs@bandzoogle.com with the subject &quot;Lead theme designer&quot; (this is important, we filter out the rest). In your email, let us know why you’d be a good fit at Bandzoogle, and include links to CMS-powered websites you have designed.\" post_date=\"2015-08-07 12:32:42\" relocation_assistance=\"0\" telecommuting=\"1\" keywords=\"theme,themes,websites,their,designer,team,thousands,responsive,modern,weve,lead,design,build,bandzoogle,growing,html5cssexperience,sass,lessexperience,similar,source,control,using,workrequirementsable,from,idea,process,creative,musiciansown,implementation\" apply_email=\"jobs@bandzoogle.com\" url=\"http://www.authenticjobs.com/jobs/25277/lead-theme-designer\">
                        <category id=\"3\" name=\"UI Design\" />
                        <type id=\"1\" name=\"Full-time\" />
                        <company id=\"bandzoogle\" name=\"Bandzoogle\" url=\"http://bandzoogle.com\" type=\"3\" logo=\"http://www.authenticjobs.com/logos/bandzoogle_1438968613.png\" tagline=\"Helping bands build websites that work.\">
                            <location id=\"anywherewereinmontrealca\" name=\"Anywhere (we're in Montreal), CA\" city=\"Anywhere (we're in Montreal)\" country=\"CA\" lat=\"45.5588\" lng=\"-73.6913\" />
                        </company>
                    </listing>";
    }
}
