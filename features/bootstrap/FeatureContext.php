<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    protected $client = null;
    protected $results = null;
    protected $params = [];
    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct(array $parameters = [])
    {
        $this->params = $parameters;
        $this->client = new \Github\Client();
    }

    /**
     * @Given I am an authenticated user
     */
    public function iAmAnAuthenticatedUser()
    {
        $this->client->authenticate(
            $this->params['github_token'], null, Github\AuthMethod::ACCESS_TOKEN
        );
    }

    /**
     * @When I request a list of my repositories
     */
    public function iRequestAListOfMyRepositories()
    {
        $repositories = $this->client->api('current_user')->repositories();

        $this->checkResponseCode(200);

        $this->results = $repositories;
    }

    /**
     * @Then the results should include a repository named :arg1
     */
    public function theResultsShouldIncludeARepositoryNamed($arg1)
    {
        if (!$this->repositoryExists($this->results, $arg1)) {
            throw new Exception("Expected to find a repository called '$arg1' but it doesn't exist.");
        }
    }
    
    protected function checkResponseCode($expected)
    {
        $statusCode   = $this->client->getLastResponse()->getStatusCode();

        if ($expected != $statusCode) {
            throw new Exception("Expected a $expected status code but got $statusCode instead!");
        }
    }

    protected function repositoryExists($repoArray, $repoName)
    {
        $repositories = array_column($repoArray, 'name', 'name');

        return isset($repositories[$repoName]);
    }
}
