<?php

namespace App\Modules\CompanyProfile\Controllers;

use App\Models\Media;
use App\Modules\CompanyProfile\Requests\StoreMediaRequest;
use App\Modules\CompanyProfile\Requests\UpdateMediaRequest;
use App\Services\BaseCrud\BaseWebCrud;
use App\Services\OptimizedImageService;
use Illuminate\Http\Request;

class MediaController extends BaseWebCrud
{
    public $model = Media::class;

    public $modelKey = 'uuid';

    public $searchAble = ['original_name', 'alt_text', 'caption'];

    public $storeValidator = StoreMediaRequest::class;

    public $updateValidator = UpdateMediaRequest::class;

    public $viewPath = 'adminpanel.pages.company-profile.media';

    public $redirectSuccessStore = 'company-profile.media.index';

    public $redirectSuccessUpdate = 'company-profile.media.index';

    public $successStoreMsg;

    public $successUpdateMsg;

    public $successDestroyMsg;

    public $defaultOrder = 'updated_at';

    public $defaultSort = 'desc';

    public $relationList = ['variants'];

    public $abilityPolicyIndex = 'view_media';

    public $abilityPolicyShow = 'show_media';

    public $abilityPolicyStore = 'create_media';

    public $abilityPolicyUpdate = 'update_media';

    public $abilityPolicyDelete = 'delete_media';

    public $enableBulkDelete = false;

    public $lockRelationParam = true;

    public function __construct(private readonly OptimizedImageService $optimizedImageService)
    {
        $this->successStoreMsg = __('admin.media.success_uploaded');
        $this->successUpdateMsg = __('admin.media.success_updated');
        $this->successDestroyMsg = __('admin.media.success_deleted');
    }

    public function store(Request $request)
    {
        return $this->DBSafe(function () {
            $validatedRequest = app(StoreMediaRequest::class);
            $this->requestData = $validatedRequest;
            $this->row = $this->optimizedImageService->storeResponsiveContent(
                image: $validatedRequest->file('image'),
                directory: 'company-profile/media',
                altText: $validatedRequest->string('alt_text')->toString(),
                uploadedBy: auth()->id(),
            );
            $this->row->forceFill([
                'caption' => $validatedRequest->validated('caption'),
            ])->save();

            return $this->__successStore();
        });
    }

    /** @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function __prepareDataUpdate($data): array
    {
        return $data;
    }
}
