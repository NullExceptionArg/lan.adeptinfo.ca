<?php

namespace App\Services\Implementation;

use App\Model\Role;
use App\Repositories\Implementation\LanRepositoryImpl;
use App\Repositories\Implementation\RoleRepositoryImpl;
use App\Services\RoleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class RoleServiceImpl implements RoleService
{
    protected $roleRepository;
    protected $lanRepository;

    /**
     * LanServiceImpl constructor.
     * @param RoleRepositoryImpl $roleRepository
     * @param LanRepositoryImpl $lanRepository
     */
    public function __construct(RoleRepositoryImpl $roleRepository, LanRepositoryImpl $lanRepository)
    {
        $this->roleRepository = $roleRepository;
        $this->lanRepository = $lanRepository;
    }

    public function create(Request $input): Role
    {

        $lan = null;
        if ($input->input('lan_id') == null) {
            $lan = $this->lanRepository->getCurrent();
            $input['lan_id'] = $lan != null ? $lan->id : null;
        }

        // TODO ajouter la liste des
        $roleValidator = Validator::make([
            'lan_id' => $input->input('lan_id'),
            'name' => $input->input('name'),
            'en_display_name' => $input->input('en_display_name'),
            'en_description' => $input->input('en_description'),
            'fr_display_name' => $input->input('fr_display_name'),
            'fr_description' => $input->input('fr_description')
        ], [
            'lan_id' => 'integer|exists:lan,id,deleted_at,NULL',
            'name' => 'required|string|max:50',
            'en_display_name' => 'required|string|max:70',
            'en_description' => 'required|string|max:1000',
            'fr_display_name' => 'required|string|max:70',
            'fr_description' => 'required|string|max:1000',
        ]);

        if ($roleValidator->fails()) {
            throw new BadRequestHttpException($roleValidator->errors());
        }

        return $this->roleRepository->create(
            $input->input('name'),
            $input->input('en_display_name'),
            $input->input('en_description'),
            $input->input('fr_display_name'),
            $input->input('fr_description')
        );
    }

}