<?php

namespace Tests\Unit\Repository\Lan;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class UpdateLanRulesTest extends TestCase
{
    use DatabaseMigrations;

    protected $lanRepository;

    protected $lan;

    protected $paramsContent = [
        'text' => "☭"
    ];

    public function setUp()
    {
        parent::setUp();
        $this->lanRepository = $this->app->make('App\Repositories\Implementation\LanRepositoryImpl');
        $this->lan = factory('App\Model\Lan')->create();
    }

    public function testUpdateLanRules()
    {
        $this->lanRepository->updateLanRules($this->lan, $this->paramsContent['text']);

        $this->seeInDatabase('lan', ['rules' => $this->paramsContent['text']]);
    }
}
