<?php

namespace PslSearchFormTest\Controller;

use PslSearchFormTest\Controller\PslSearchFormControllerTestCase;

class IndexControllerTest extends PslSearchFormControllerTestCase
{
    protected $site;

    public function setUp()
    {
        parent::setUp();

        $response = $this->api()->create('sites', [
            'o:title' => 'Test site',
            'o:slug' => 'test',
            'o:theme' => 'default',
        ]);

        $this->site = $response->getContent();

        $this->resetApplication();
    }

    public function tearDown()
    {
        $this->api()->delete('sites', $this->site->id());

        parent::tearDown();
    }

    public function testSearchAction()
    {
        $this->dispatch('/s/test/test/search');
        $this->assertResponseStatusCode(200);

        $this->assertQuery('input[name="q"]');
        $this->assertNotQuery('.search-results');
    }

    public function testSearchWithParamsAction()
    {
        $this->dispatch('/s/test/test/search', 'GET', ['q' => 'test']);
        $this->assertResponseStatusCode(200);
        $this->assertQuery('.search-results');
        $this->assertQuery('input[name="q"][value="test"]');
    }
}
