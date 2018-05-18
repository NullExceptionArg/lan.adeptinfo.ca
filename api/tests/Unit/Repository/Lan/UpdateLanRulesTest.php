<?php

namespace Tests\Unit\Repository\Lan;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class UpdateLanRulesTest extends TestCase
{
    use DatabaseMigrations;

    protected $lanRepository;

    protected $paramsContent = [
        'text' => "☭"
    ];

    public function setUp()
    {
        parent::setUp();
        $this->lanRepository = $this->app->make('App\Repositories\Implementation\LanRepositoryImpl');
    }

    public function testUpdateLanRules()
    {
        $lan = factory('App\Model\Lan')->create();
        $this->lanRepository->updateLanRules($lan, $this->paramsContent['text']);

        $this->seeInDatabase('lan', ['rules' => $this->paramsContent['text']]);
    }
}
